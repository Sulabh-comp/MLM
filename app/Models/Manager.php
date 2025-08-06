<?php

namespace App\Models;

use Illuminate\Support\Str;
use App\Mail\UserCreationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Manager extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $guard = 'manager';

    protected $fillable = [
        'name', 'email', 'phone', 'designation', 'region_id', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    // Get all employees in the same region
    public function employees()
    {
        return Employee::where('region_id', $this->region_id);
    }

    // Get all agencies in the same region through employees
    public function agencies()
    {
        return Agency::whereHas('employee', function($query) {
            $query->where('region_id', $this->region_id);
        });
    }

    // Get all customers in the same region through agencies
    public function customers()
    {
        return Customer::whereHas('agency.employee', function($query) {
            $query->where('region_id', $this->region_id);
        });
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class,'user_id','id')->where('model', self::class);
    }

    /**
     * Generate unique code: MAN + First 2 letters of first name + First 2 letters of last name + Zero-padded ID
     */
    public function generateCode()
    {
        $nameParts = explode(' ', $this->name ?? '');
        $firstName = strtoupper(substr($nameParts[0] ?? '', 0, 2));
        $lastName = strtoupper(substr($nameParts[1] ?? '', 0, 2));
        $paddedId = str_pad($this->id, 4, '0', STR_PAD_LEFT);
        
        return 'MAN' . $firstName . $lastName . $paddedId;
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

        static::created(function ($manager) {
            $manager->update(['code' => $manager->generateCode()]);
        });

        static::creating(function ($model) {
            $password = Str::random(10);
            $model->password = bcrypt($password);

            // send email to manager
            Mail::to($model->email)->send(new UserCreationMail($model, $password, 'manager'));
        });

        static::updating(function($model) {
            if ($model->isDirty('status') && $model->status == 1) {
                // generate password
                $password = Str::random(10);
                $model->password = bcrypt($password);

                // send email to manager
                Mail::to($model->email)->send(new UserCreationMail($model, $password, 'manager'));
            }
        });
    }
}
