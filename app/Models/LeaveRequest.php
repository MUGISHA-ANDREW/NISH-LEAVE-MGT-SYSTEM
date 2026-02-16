<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'leave_type_id', 
        'start_date',
        'end_date',
        'total_days',
        'reason',
        'contact_number',
        'emergency_contact',
        'handover_notes',
        'status',
        'rejection_reason',
        'action_by',
        'action_at',
        'stand_in_employee_id'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'action_at' => 'datetime'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function actionBy()
    {
        return $this->belongsTo(User::class, 'action_by');
    }

    /**
     * Get the stand-in employee assigned to cover during this leave
     */
    public function standInEmployee()
    {
        return $this->belongsTo(User::class, 'stand_in_employee_id');
    }

    /**
     * Get the approval records for this leave request
     */
    public function approvals()
    {
        return $this->hasMany(Approval::class);
    }

    /**
     * Get department head approval
     */
    public function departmentHeadApproval()
    {
        return $this->hasOne(Approval::class)->where('level', 'department_head');
    }

    /**
     * Get HR admin approval
     */
    public function hrAdminApproval()
    {
        return $this->hasOne(Approval::class)->where('level', 'hr_admin');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    // Helpers
    public function getStatusBadgeClass()
    {
        return match($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'approved' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
            'cancelled' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Get approval workflow status
     */
    public function getApprovalStatus()
    {
        $departmentApproval = $this->departmentHeadApproval;
        $hrApproval = $this->hrAdminApproval;
        
        if (!$departmentApproval && !$hrApproval) {
            return 'not_started';
        }
        
        if ($departmentApproval && $departmentApproval->status === 'rejected') {
            return 'rejected_by_dept';
        }
        
        if ($hrApproval && $hrApproval->status === 'rejected') {
            return 'rejected_by_hr';
        }
        
        if ($departmentApproval && $departmentApproval->status === 'approved' && 
            $hrApproval && $hrApproval->status === 'approved') {
            return 'fully_approved';
        }
        
        if ($departmentApproval && $departmentApproval->status === 'approved' && !$hrApproval) {
            return 'pending_hr_approval';
        }
        
        if ($departmentApproval && $departmentApproval->status === 'pending') {
            return 'pending_dept_approval';
        }
        
        return 'in_progress';
    }

    /**
     * Check if leave request can be edited
     */
    public function canBeEdited()
    {
        return $this->status === 'pending' && $this->start_date > now();
    }

    /**
     * Check if leave request can be cancelled
     */
    public function canBeCancelled()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if leave request can be retrieved
     */
    public function canBeRetrieved()
    {
        return $this->status === 'cancelled' && $this->start_date > now();
    }

    /**
     * Get the current approver based on workflow
     */
    public function getCurrentApprover()
    {
        $approvalStatus = $this->getApprovalStatus();
        
        switch ($approvalStatus) {
            case 'not_started':
            case 'pending_dept_approval':
                return $this->user->department->head ?? null;
                
            case 'pending_hr_approval':
                return User::whereHas('role', function($query) {
                    $query->where('name', 'hr_admin');
                })->first();
                
            default:
                return null;
        }
    }
}