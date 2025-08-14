<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ManagerLevel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'code', 'description', 'hierarchy_level', 
        'is_predefined', 'status', 'permissions'
    ];

    protected $casts = [
        'is_predefined' => 'boolean',
        'status' => 'boolean',
        'permissions' => 'array',
    ];

    /**
     * Get managers with this level
     */
    public function managers()
    {
        return $this->hasMany(Manager::class, 'level_name', 'name');
    }

    /**
     * Scope for active levels only
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope for custom (non-predefined) levels
     */
    public function scopeCustom($query)
    {
        return $query->where('is_predefined', false);
    }

    /**
     * Scope for predefined levels
     */
    public function scopePredefined($query)
    {
        return $query->where('is_predefined', true);
    }

    /**
     * Order by hierarchy level (1 = highest)
     */
    public function scopeOrderByHierarchy($query)
    {
        return $query->orderBy('hierarchy_level');
    }

    /**
     * Get predefined manager levels
     */
    public static function getPredefinedLevels()
    {
        return [
            [
                'name' => 'Chief Executive Officer',
                'code' => 'CEO',
                'description' => 'Top executive responsible for overall company operations',
                'hierarchy_level' => 1,
                'is_predefined' => true,
            ],
            [
                'name' => 'Regional Manager',
                'code' => 'RM',
                'description' => 'Manages multiple areas within a region',
                'hierarchy_level' => 2,
                'is_predefined' => true,
            ],
            [
                'name' => 'Area Manager',
                'code' => 'AM',
                'description' => 'Manages multiple zones within an area',
                'hierarchy_level' => 3,
                'is_predefined' => true,
            ],
            [
                'name' => 'Zone Manager',
                'code' => 'ZM',
                'description' => 'Manages a specific zone with multiple teams',
                'hierarchy_level' => 4,
                'is_predefined' => true,
            ],
            [
                'name' => 'Team Leader',
                'code' => 'TL',
                'description' => 'Leads a team of employees',
                'hierarchy_level' => 5,
                'is_predefined' => true,
            ],
            [
                'name' => 'Manager',
                'code' => 'MGR',
                'description' => 'General manager position',
                'hierarchy_level' => 6,
                'is_predefined' => true,
            ],
        ];
    }

    /**
     * Seed predefined levels
     */
    public static function seedPredefinedLevels()
    {
        foreach (self::getPredefinedLevels() as $level) {
            self::updateOrCreate(
                ['code' => $level['code']],
                $level
            );
        }
    }

    protected static function booted()
    {
        static::addGlobalScope('search', function ($builder) {
            if ($search = request()->query('q')) {
                $builder->where('name', 'like', '%' . $search . '%')
                        ->orWhere('code', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%');
            }
        });
    }
}
