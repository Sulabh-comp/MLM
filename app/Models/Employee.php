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
        'name', 'email', 'phone', 'password', 'manager_id', 'region_id',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function agencies()
    {
        return $this->hasMany(Agency::class);
    }

    public function manager()
    {
        return $this->belongsTo(Manager::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
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

        static::updating(function($model) {
            if ($model->isDirty('status') && $model->status == 1) {
                // generate password
                $password = Str::random(10);
                $model->password = bcrypt($password);

                // send email to agency
                Mail::to($model->email)->send(new UserCreationMail($model, $password, 'employee'));

                // Notification::create([
                //     'user_id' => $model->employee->id,
                //     'model' => Employee::class,
                //     'title' => 'Agency ' . $model->name . ' Approved',
                //     'url' => route('employee.agencies.show', $agency->id),
                //     'message' => 'Agency ' . $model->name . ' has been approved by ' . auth()->guard('admin')->user()->name .''. $model->email .'',
                // ]);

                // // generate notifications to all admins
                // $admins = Admin::all();
                // foreach ($admins as $admin) {
                //     $notification = new Notification();
                //     $notification->user_id = $admin->id;
                //     $notification->model = Admin::class;
                //     $notification->title = 'Agency ' . $agency->name . ' Approved';
                //     $notification->message = 'Agency ' . $agency->name . ' has been approved by ' . auth()->guard('admin')->user()->name;
                //     $notification->url = route('admin.agencies.show', $agency->id);
                //     $notification->save();
                // }
            }
        });
    }
}
