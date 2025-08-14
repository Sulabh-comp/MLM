<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ValidHierarchyLevel;
use App\Rules\ValidHierarchyParent;
use App\Rules\HierarchyDepthLimit;
use App\Services\HierarchyPermissionService;

class CreateManagerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->getAuthenticatedUser();
        
        if (!$user) {
            return false;
        }

        // Admin can always create managers
        if (get_class($user) === 'App\Models\Admin') {
            return true;
        }

        // Only managers can create other managers
        if (get_class($user) !== 'App\Models\Manager') {
            return false;
        }

        // Check if manager has permission to create subordinates
        $permissionService = new HierarchyPermissionService();
        
        if ($this->has('level_name') && get_class($user) === 'App\Models\Manager') {
            return $permissionService->canCreateSubordinate($user, $this->level_name);
        }

        // If no level specified yet, allow the request to proceed to validation
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->getAuthenticatedUser();
        
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:managers'],
            'phone' => ['required', 'string', 'max:20'],
            'designation' => ['required', 'string', 'max:255'],
            'level_name' => [
                'required',
                'string',
                'exists:manager_levels,name',
                new ValidHierarchyLevel($user, 'create')
            ],
            'parent_id' => [
                'nullable',
                'exists:managers,id',
                new ValidHierarchyParent($user),
                new HierarchyDepthLimit(10)
            ],
            'region_id' => ['required', 'exists:regions,id'],
            'territory_name' => ['nullable', 'string', 'max:255'],
            'territory_description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Manager name is required.',
            'email.required' => 'Email address is required.',
            'email.unique' => 'This email address is already in use.',
            'phone.required' => 'Phone number is required.',
            'designation.required' => 'Designation is required.',
            'level_name.required' => 'Manager level is required.',
            'level_name.exists' => 'Invalid manager level selected.',
            'parent_id.exists' => 'Invalid parent manager selected.',
            'region_id.required' => 'Region is required.',
            'region_id.exists' => 'Invalid region selected.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'level_name' => 'manager level',
            'parent_id' => 'parent manager',
            'region_id' => 'region',
            'designation' => 'designation',
            'territory_name' => 'territory name',
            'territory_description' => 'territory description',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $user = $this->getAuthenticatedUser();
            
            if ($user && get_class($user) === 'App\Models\Manager') {
                // Additional business logic validation
                $permissionService = new HierarchyPermissionService();
                
                // Type cast to Manager for proper type checking
                /** @var \App\Models\Manager $managerUser */
                $managerUser = $user;
                $validationErrors = $permissionService->validateManagerCreation($managerUser, $this->all());
                
                foreach ($validationErrors as $field => $message) {
                    $validator->errors()->add($field, $message);
                }

                // Check if parent_id and level_name are compatible
                if ($this->parent_id && $this->level_name) {
                    $parentManager = \App\Models\Manager::find($this->parent_id);
                    $targetLevel = \App\Models\ManagerLevel::where('name', $this->level_name)->first();
                    
                    if ($parentManager && $targetLevel) {
                        // Child level must be greater than parent level
                        if ($targetLevel->hierarchy_level <= $parentManager->hierarchy_level) {
                            $validator->errors()->add('level_name', 'Child manager level must be below parent manager level.');
                        }
                    }
                }
            }
        });
    }

    /**
     * Get the authenticated user from any guard
     */
    private function getAuthenticatedUser()
    {
        if (auth()->guard('manager')->check()) {
            return auth()->guard('manager')->user();
        }

        if (auth()->guard('admin')->check()) {
            return auth()->guard('admin')->user();
        }

        return null;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Clean and prepare data
        if ($this->has('phone')) {
            $this->merge([
                'phone' => preg_replace('/[^0-9+\-\s]/', '', $this->phone)
            ]);
        }

        // Auto-assign parent based on hierarchy rules
        $this->autoAssignParentByHierarchy();

        // Set default parent if not specified and user is manager
        $user = $this->getAuthenticatedUser();
        if (!$this->parent_id && $user && get_class($user) === 'App\Models\Manager') {
            $this->merge(['parent_id' => $user->id]);
        }
        if ($user && get_class($user) === 'App\Models\Manager') {
            $this->merge(['region_id' => $user->region_id]);
        }
    }

    /**
     * Auto-assign parent based on hierarchy level rules
     */
    private function autoAssignParentByHierarchy(): void
    {
        if (!$this->level_name || $this->parent_id) {
            return; // Skip if level not selected or parent already assigned
        }

        $selectedLevel = \App\Models\ManagerLevel::where('name', $this->level_name)->first();
        if (!$selectedLevel) {
            return; // Invalid level
        }

        $targetHierarchyLevel = $selectedLevel->hierarchy_level;

        // Level 1 doesn't need a parent (top level)
        if ($targetHierarchyLevel == 1) {
            return;
        }

        // Only allow immediate senior (level-1) as parent
        $assignedParent = $this->findImmediateSeniorParent($targetHierarchyLevel);
        if ($assignedParent) {
            $this->merge(['parent_id' => $assignedParent->id]);
        }
    }

    /**
     * Find appropriate parent manager based on hierarchy rules
     */
    /**
     * Only allow immediate senior (level-1) as parent. No same-level parent allowed.
     */
    private function findImmediateSeniorParent(int $targetLevel): ?\App\Models\Manager
    {
        $immediateParentLevel = $targetLevel - 1;
        // Only allow managers from immediate senior level
        return \App\Models\Manager::whereHas('managerLevel', function($query) use ($immediateParentLevel) {
                $query->where('hierarchy_level', $immediateParentLevel)
                      ->where('status', true); // Active level
            })
            ->where('status', 1) // Active manager
            ->first();
    }
}
