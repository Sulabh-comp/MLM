<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ValidEmployeeManager;
use App\Services\HierarchyPermissionService;

class CreateEmployeeRequest extends FormRequest
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

        // Admin can always create employees
        if (get_class($user) === 'App\Models\Admin') {
            return true;
        }

        // Managers can create employees
        if (get_class($user) === 'App\Models\Manager') {
            $permissionService = new HierarchyPermissionService();
            return $permissionService->canCreateEmployee($user);
        }

        // Employees cannot create other employees
        return false;
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
            'email' => ['required', 'string', 'email', 'max:255', 'unique:employees'],
            'phone' => ['required', 'string', 'max:20'],
            'designation' => ['required', 'string', 'max:255'],
            'manager_id' => [
                'required',
                'exists:managers,id',
                new ValidEmployeeManager($user)
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Employee name is required.',
            'email.required' => 'Email address is required.',
            'email.unique' => 'This email address is already in use.',
            'phone.required' => 'Phone number is required.',
            'designation.required' => 'Designation is required.',
            'manager_id.required' => 'Manager assignment is required.',
            'manager_id.exists' => 'Invalid manager selected.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'manager_id' => 'assigned manager',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $user = $this->getAuthenticatedUser();
            
            // Additional business logic validation
            if ($this->manager_id) {
                $manager = \App\Models\Manager::find($this->manager_id);
                
                if ($manager) {
                    // Check if manager has capacity for more employees
                    $currentEmployees = $manager->employees()->count();
                    $maxEmployees = $this->getMaxEmployeesPerManager($manager);
                    
                    if ($currentEmployees >= $maxEmployees) {
                        $validator->errors()->add('manager_id', "Selected manager has reached maximum employee limit ({$maxEmployees}).");
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
        if (auth()->guard('admin')->check()) {
            return auth()->guard('admin')->user();
        }

        if (auth()->guard('manager')->check()) {
            return auth()->guard('manager')->user();
        }

        if (auth()->guard('employee')->check()) {
            return auth()->guard('employee')->user();
        }

        return null;
    }

    /**
     * Get maximum employees per manager based on their level
     */
    private function getMaxEmployeesPerManager($manager): int
    {
        // Define limits based on manager level
        $limits = [
            1 => 50,  // CEO - 50 direct employees
            2 => 40,  // Regional Manager - 40 direct employees
            3 => 30,  // Area Manager - 30 direct employees
            4 => 25,  // Zone Manager - 25 direct employees
            5 => 20,  // Team Leader - 20 direct employees
            6 => 15,  // Manager - 15 direct employees
        ];

        return $limits[$manager->hierarchy_level] ?? 10;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Clean phone number
        if ($this->has('phone')) {
            $this->merge([
                'phone' => preg_replace('/[^0-9+\-\s]/', '', $this->phone)
            ]);
        }

        // Set default manager if not specified and user is manager
        $user = $this->getAuthenticatedUser();
        if (!$this->manager_id && $user && get_class($user) === 'App\Models\Manager') {
            $this->merge(['manager_id' => $user->id]);
        }
    }
}
