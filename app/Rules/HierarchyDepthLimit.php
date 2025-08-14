<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Manager;

class HierarchyDepthLimit implements ValidationRule
{
    protected $maxDepth;

    public function __construct(int $maxDepth = 10)
    {
        $this->maxDepth = $maxDepth;
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$value) {
            return; // Allow null parent (root level)
        }

        $parentManager = Manager::find($value);
        
        if (!$parentManager) {
            return; // Let other validation handle invalid manager
        }

        // Calculate what the depth would be
        $newDepth = $parentManager->depth + 1;

        if ($newDepth > $this->maxDepth) {
            $fail("Maximum hierarchy depth of {$this->maxDepth} levels would be exceeded.");
            return;
        }
    }
}
