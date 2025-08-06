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
        $paddedId = str_pad($this->id, 4, '0', STR_PAD_LEFT);
        
        return 'CUS' . $firstName . $lastName . $paddedId;
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
