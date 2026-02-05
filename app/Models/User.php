<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'employee_id',
        'first_name',
        'last_name',
        'profile_picture',
        'email',
        'password',
        'role_id',
        'department_id',
        'designation',
        'date_of_birth',
        'gender',
        'phone',
        'address',
        'emergency_contact',
        'supervisor_id',
        'employment_type',
        'join_date',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'date_of_birth' => 'date',
        'join_date' => 'date',
        'last_login_at' => 'datetime',
    ];

    /**
     * Get the role that belongs to the user
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the department that the user belongs to
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the supervisor of the user
     */
    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    /**
     * Get the leave requests for the user
     */
    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    /**
     * Get the leave requests acted upon by this user
     */
    public function actedLeaveRequests()
    {
        return $this->hasMany(LeaveRequest::class, 'action_by');
    }

    /**
     * Get full name attribute
     */
    public function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Check if user is an admin
     */
    public function isAdmin()
    {
        $roleName = strtolower($this->role->name ?? 'employee');
        return in_array($roleName, ['admin', 'hr_admin', 'administrator', 'hr', 'system_admin']);
    }

    /**
     * Check if user is a department head
     */
    public function isHead()
    {
        $roleName = strtolower($this->role->name ?? 'employee');
        return in_array($roleName, [
            'department_head', 
            'head', 
            'manager', 
            'supervisor',
            'department head',
            'department-head',
            'dept_head',
            'dept head'
        ]);
    }

    /**
     * Check if user is an employee
     */
    public function isEmployee()
    {
        return !$this->isAdmin() && !$this->isHead();
    }

    /**
     * Get user's role name
     */
    public function getRoleName()
    {
        return $this->role->name ?? 'Employee';
    }

    /**
     * Get pending approvals count for department heads and admins
     */
    public function getPendingApprovalsCount()
    {
        if ($this->isHead() && $this->department) {
            return LeaveRequest::whereHas('user', function($query) {
                $query->where('department_id', $this->department->id);
            })->where('status', 'pending')->count();
        }

        if ($this->isAdmin()) {
            return LeaveRequest::where('status', 'pending')->count();
        }

        return 0;
    }

    /**
     * Generate employee ID
     */
    public static function generateEmployeeId()
    {
        $lastUser = self::orderBy('id', 'desc')->first();
        $nextId = $lastUser ? $lastUser->id + 1 : 1;
        return 'NISH-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Boot method to auto-generate employee ID
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->employee_id)) {
                $user->employee_id = self::generateEmployeeId();
            }
        });
    }
}