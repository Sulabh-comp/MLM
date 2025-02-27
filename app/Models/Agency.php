<?php

namespace App\Models;

use App\Mail\UserCreationMail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Mail;
use Str;

class Agency extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $guard = 'agency';

    protected $guarded = ['id'];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function notifications()
    {
        return Notification::where('model', self::class)->where(function ($query) {
            $query->where('user_id', $this->id)->orWhere('user_id', 0);
        });
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function($agency) {
            $password ='zndbchskbca';
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
