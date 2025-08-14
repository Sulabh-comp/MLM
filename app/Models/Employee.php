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
        'name', 'email', 'phone', 'password', 'manager_id',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Get the manager that this employee reports to
     */
    public function manager()
    {
        return $this->belongsTo(Manager::class);
    }

    /**
     * Get all managers accessible to this employee (through hierarchy)
     */
    public function accessibleManagers()
    {
        if (!$this->manager) {
            return collect();
        }

        // Employee can access their direct manager only (not subordinate managers)
        return collect([$this->manager]);
    }

    public function agencies()
    {
        return $this->hasMany(Agency::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class,'user_id','id')->where('model', self::class);
    }

    /**
     * Get customers accessible through this employee's agencies
     */
    public function customers()
    {
        return Customer::whereHas('agency', function($query) {
            $query->where('employee_id', $this->id);
        });
    }

    /**
     * Check if employee can access a specific manager
     */
    public function canAccessManager(Manager $manager)
    {
        return $this->accessibleManagers()->contains('id', $manager->id);
    }

    /**
     * Check if employee can access a specific agency
     */
    public function canAccessAgency(Agency $agency)
    {
        return $this->agencies()->where('id', $agency->id)->exists();
    }

    /**
     * Check if employee can access a specific customer
     */
    public function canAccessCustomer(Customer $customer)
    {
        return $this->customers()->where('id', $customer->id)->exists();
    }

    /**
     * Get the territorial scope based on manager hierarchy
     */
    public function getTerritorialScope()
    {
        if (!$this->manager) {
            return [
                'agencies' => collect(),
                'customers' => collect(),
                'managers' => collect()
            ];
        }

        return [
            'agencies' => $this->agencies,
            'customers' => $this->customers()->get(),
            'managers' => $this->accessibleManagers()
        ];
    }

    /**
     * Get employees under the same manager (colleagues)
     */
    public function colleagues()
    {
        if (!$this->manager_id) {
            return $this->newQuery()->whereNull('id'); // Return empty query
        }

        return $this->newQuery()
            ->where('manager_id', $this->manager_id)
            ->where('id', '!=', $this->id);
    }

    /**
     * Scope: Filter by manager
     */
    public function scopeByManager($query, $managerId)
    {
        return $query->where('manager_id', $managerId);
    }

    /**
     * Scope: Filter by accessible managers for an employee
     */
    public function scopeAccessibleTo($query, Employee $employee)
    {
        $managerIds = $employee->accessibleManagers()->pluck('id')->toArray();
        return $query->whereIn('manager_id', $managerIds);
    }

    /**
     * Scope: Active employees
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Generate unique code: EMP + First 2 letters of first name + First 2 letters of last name + Zero-padded ID
     */
    public function generateCode()
    {
        $nameParts = explode(' ', $this->name ?? '');
        $firstName = strtoupper(substr($nameParts[0] ?? '', 0, 2));
        $lastName = strtoupper(substr($nameParts[1] ?? '', 0, 2));
        $paddedId = str_pad($this->id, 4, '0', STR_PAD_LEFT);
        
        return 'EMP' . $firstName . $lastName . $paddedId;
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

        static::created(function ($employee) {
            $employee->update(['code' => $employee->generateCode()]);
        });

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
