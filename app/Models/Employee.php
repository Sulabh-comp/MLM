<?php

namespace App\Models;

use Illuminate\Support\Str;
use App\Mail\UserCreationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

    public function notifications()
    {
        return $this->hasMany(Notification::class,'user_id','id')->where('model', self::class);
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
