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

    protected static function boot()
    {
        parent::boot();

        static::creating(function($agency) {
            $password ='zndbchskbca';
            $agency->password = bcrypt($password);
        });

        static::updating(function($agency) {
            if ($agency->isDirty('status') && $agency->status == 1) {
                // generate password
                $password = Str::random(10);
                $agency->password = bcrypt($password);

                // send email to agency
                Mail::to($agency->email)->send(new UserCreationMail($agency, $password, 'agency'));

                Notification::create([
                    'user_id' => $agency->employee->id,
                    'model' => Employee::class,
                    'title' => 'Agency ' . $agency->name . ' Approved',
                    'url' => route('employee.agencies.show', $agency->id),
                    'message' => 'Agency ' . $agency->name . ' has been approved by ' . auth()->guard('admin')->user()->name .''. $agency->email .'',
                ]);

                // generate notifications to all admins
                $admins = Admin::all();
                foreach ($admins as $admin) {
                    $notification = new Notification();
                    $notification->user_id = $admin->id;
                    $notification->model = Admin::class;
                    $notification->title = 'Agency ' . $agency->name . ' Approved';
                    $notification->message = 'Agency ' . $agency->name . ' has been approved by ' . auth()->guard('admin')->user()->name;
                    $notification->url = route('admin.agencies.show', $agency->id);
                    $notification->save();
                }
            }
        });
    }
}
