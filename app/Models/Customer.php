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
}
