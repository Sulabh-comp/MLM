<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Manager;

class ValidEmployeeManager implements ValidationRule
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
            return; // Allow null manager
        }

        $manager = Manager::find($value);
        
        if (!$manager) {
            $fail('Invalid manager.');
            return;
        }

        // Admin can assign any manager
        if (get_class($this->currentUser) === 'App\Models\Admin') {
            return;
        }

        // Managers can assign themselves or their subordinates
        if (get_class($this->currentUser) === 'App\Models\Manager') {
            $accessibleManagers = $this->currentUser->allSubordinates();
            $accessibleManagers->prepend($this->currentUser); // Add self

            if (!$accessibleManagers->contains('id', $value)) {
                $fail('You can only assign employees to managers within your hierarchy.');
                return;
            }
        }

        // Employees cannot assign managers
        if (get_class($this->currentUser) === 'App\Models\Employee') {
            $fail('Employees cannot assign managers to other employees.');
            return;
        }
    }
}
