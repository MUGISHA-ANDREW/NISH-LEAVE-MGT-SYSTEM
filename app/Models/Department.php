<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'manager_id',
        'is_active',
    ];

    /**
     * Get the users belonging to this department
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the manager of this department
     */
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Get the leave requests for this department through users
     */
    public function leaveRequests()
    {
        return $this->hasManyThrough(
            LeaveRequest::class,
            User::class,
            'department_id', // Foreign key on users table...
            'user_id', // Foreign key on leave_requests table...
            'id', // Local key on departments table...
            'id' // Local key on users table...
        );
    }
}