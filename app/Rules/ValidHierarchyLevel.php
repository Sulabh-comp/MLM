<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Manager;
use App\Models\ManagerLevel;

class ValidHierarchyLevel implements ValidationRule
{
    protected $currentUser;
    protected $operation;

    public function __construct($currentUser, string $operation = 'create')
    {
        $this->currentUser = $currentUser;
        $this->operation = $operation;
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Admin can create at any level
        if (get_class($this->currentUser) === 'App\Models\Admin') {
            return;
        }

        // Only managers can create other managers
        if (get_class($this->currentUser) !== 'App\Models\Manager') {
            $fail('Only managers can create other managers.');
            return;
        }

        $targetLevel = ManagerLevel::where('name', $value)->first();
        
        if (!$targetLevel) {
            $fail('Invalid manager level.');
            return;
        }

        // Manager can only create subordinates at levels below their own
        if ($this->currentUser->hierarchy_level >= $targetLevel->hierarchy_level) {
            $fail('You can only create managers at levels below your current level.');
            return;
        }
    }
}
