<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Mail\UserCreationMail;
use Str;
use Mail;

class Employee extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $guard = 'employee';

    protected $fillable = [
        'name', 'email', 'phone', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function agencies()
    {
        return $this->hasMany(Agency::class);
    }

    protected static function boot(){
        parent::boot();

        static::creating(function ($model) {
            $password = Str::random(10);
            $model->password = bcrypt($password);

            // send email to employee
            Mail::to($model->email)->send(new UserCreationMail($model, $password, 'employee'));
        });
    }
}