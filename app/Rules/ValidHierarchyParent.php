<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Manager;

class ValidHierarchyParent implements ValidationRule
{
    protected $currentUser;

    public function __construct($currentUser)
    {
        $this->currentUser = $currentUser;
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$value) {
            return; // Allow null parent for root managers
        }

        $parentManager = Manager::find($value);
        
        if (!$parentManager) {
            $fail('Invalid parent manager.');
            return;
        }

        // Admin can assign any parent
        if (get_class($this->currentUser) === 'App\Models\Admin') {
            return;
        }

        // Only managers can create other managers
        if (get_class($this->currentUser) !== 'App\Models\Manager') {
            $fail('Only managers can create other managers.');
            return;
        }

        // Manager can only assign themselves or their subordinates as parents
        $accessibleManagers = $this->currentUser->allSubordinates();
        $accessibleManagers->prepend($this->currentUser); // Add self

        if (!$accessibleManagers->contains('id', $value)) {
            $fail('You can only assign managers within your hierarchy as parents.');
            return;
        }

        // Prevent circular references
        if ($this->wouldCreateCircularReference($parentManager, $this->currentUser)) {
            $fail('This assignment would create a circular reference in the hierarchy.');
            return;
        }
    }

    private function wouldCreateCircularReference(Manager $parentManager, Manager $currentUser): bool
    {
        // dd($currentUser, $currentUser->allSubordinates(), $parentManager);
        return $currentUser->allSubordinates()->contains('id', $parentManager->id);
    }
}
