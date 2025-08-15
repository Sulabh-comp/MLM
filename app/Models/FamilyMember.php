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

    /**
     * Generate unique code: FAM + First 2 letters of first name + First 2 letters of last name + Zero-padded ID
     */
    public function generateCode()
    {
        $nameParts = explode(' ', $this->name ?? '');
        $firstName = strtoupper(substr($nameParts[0] ?? '', 0, 2));
        $lastName = strtoupper(substr($nameParts[1] ?? '', 0, 2));
        $random = rand(10000, 99999);
        $code = 'FAM' . $firstName . $lastName . $random;
        if (self::where('code', $code)->exists()) {
            return $this->generateCode(); // Recursively generate a new code
        }

        return $code;
    }
    
    protected static function booted()
    {
        static::addGlobalScope('search', function ($builder) {
            if ($search = request()->query('q')) {
                $builder->where('name', 'like', '%' . $search . '%');
            }
        });

        static::created(function ($familyMember) {
            $familyMember->update(['code' => $familyMember->generateCode()]);
        });
    }
}
