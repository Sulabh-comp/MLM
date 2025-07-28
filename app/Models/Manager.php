<?php

namespace App\Models;

use App\Mail\UserCreationMail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class Manager extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $guard = 'manager';

    protected $fillable = [
        'name', 'email', 'phone', 'password', 'region_id',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function agencies()
    {
        return $this->hasManyThrough(Agency::class, Employee::class);
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
            $model->email_password = $password;

            Mail::to($model->email)->send(new UserCreationMail($model, $password, 'manager'));
        });

        static::created(function ($model) {
            // Send notification to admin
            $admins = \App\Models\Admin::all();
            foreach ($admins as $admin) {
                \App\Models\Notification::create([
                    'user_id' => $admin->id,
                    'model' => \App\Models\Admin::class,
                    'title' => 'New Manager Created',
                    'message' => "A new manager '{$model->name}' has been created in region '{$model->region->name}'.",
                    'type' => 'info',
                    'is_read' => false
                ]);
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('password')) {
                $model->password = bcrypt($model->password);
            }
        });

        static::updated(function ($model) {
            // Send notification to admin
            $admins = \App\Models\Admin::all();
            foreach ($admins as $admin) {
                \App\Models\Notification::create([
                    'user_id' => $admin->id,
                    'model' => \App\Models\Admin::class,
                    'title' => 'Manager Updated',
                    'message' => "Manager '{$model->name}' has been updated.",
                    'type' => 'info',
                    'is_read' => false
                ]);
            }
        });

        static::deleted(function ($model) {
            // Send notification to admin
            $admins = \App\Models\Admin::all();
            foreach ($admins as $admin) {
                \App\Models\Notification::create([
                    'user_id' => $admin->id,
                    'model' => \App\Models\Admin::class,
                    'title' => 'Manager Deleted',
                    'message' => "Manager '{$model->name}' has been deleted.",
                    'type' => 'warning',
                    'is_read' => false
                ]);
            }
        });
    }
}
