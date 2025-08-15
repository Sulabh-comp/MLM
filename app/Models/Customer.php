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

    /**
     * Generate unique code: CUS + First 2 letters of first name + First 2 letters of last name + Zero-padded ID
     */
    public function generateCode()
    {
        $firstName = strtoupper(substr($this->first_name ?? '', 0, 2));
        $lastName = strtoupper(substr($this->last_name ?? '', 0, 2));
        $random = rand(10000, 99999);
        $code = 'CUS' . $firstName . $lastName . $random;
        if (self::where('code', $code)->exists()) {
            return $this->generateCode(); // Recursively generate a new code
        }

        return $code;
    }

    protected static function booted()
    {
        static::addGlobalScope('search', function ($builder) {
            if ($search = request()->query('q')) {
                $builder->where('first_name', 'like', '%' . $search . '%')
                        ->orWhere('last_name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
            }
        });

        static::created(function ($customer) {
            $customer->update(['code' => $customer->generateCode()]);
        });
    }
}
