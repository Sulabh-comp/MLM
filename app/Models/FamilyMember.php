<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FamilyMember extends Model
{
    protected $guarded = [];

    protected $table = "family-members";

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    
    protected static function booted()
    {
        static::addGlobalScope('search', function ($builder) {
            if ($search = request()->query('q')) {
                $builder->where('name', 'like', '%' . $search . '%');
            }
        });
    }
}
