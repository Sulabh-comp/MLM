<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $guarded = [];

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    public function familyMembers()
    {
        return $this->hasMany(FamilyMember::class);
    }

    public function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    protected static function booted()
    {
        static::addGlobalScope('search', function ($builder) {
            if ($search = request()->query('q')) {
                $builder->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
            }
        });
    }
}
