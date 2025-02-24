<?php

namespace App\Models;

use App\Mail\UserCreationMail;
use Illuminate\Database\Eloquent\Model;
use Mail;
use Str;

class Agency extends Model
{
    protected $guarded = [];

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function($agency) {
            $password ='zndbchskbc a';
            $agency->password = bcrypt($password);
        });

        static::updating(function($agency) {
            if ($agency->status->isDirty() && $agency->status == 1) {
                // generate password
                $password = Str::random(10);
                $agency->password = bcrypt($password);

                // send email to agency
                Mail::to($agency->email)->send(new UserCreationMail($agency, $password, 'agency'));
            }
        });
    }
}
