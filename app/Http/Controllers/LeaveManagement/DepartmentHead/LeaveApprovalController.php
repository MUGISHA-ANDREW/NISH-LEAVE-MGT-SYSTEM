<?php

namespace App\Http\Controllers\LeaveManagement\DepartmentHead;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LeaveRequest;
use App\Models\Department;
use App\Models\User;
use Carbon\Carbon;

class LeaveApprovalController extends Controller
{
    /**
     * Display pending leaves for approval
     */
   public function pending()
{
    $user = Auth::user();
    $department = $user->department;
    
    // Only show leaves that DON'T have department head approval yet
    $pendingLeaves = LeaveRequest::with(['user', 'leaveType', 'approvals'])
        ->whereHas('user', function($query) use ($department) {
            $query->where('department_id', $department->id);
        })
        ->where('status', 'pending')
        ->whereDoesntHave('approvals', function($query) {
            $query->where('level', 'department_head');
        })
        ->orderBy('created_at', 'desc')
        ->paginate(10);

    return view('modules.Leave-management.department_head.pending', compact('pendingLeaves', 'department'));
}

    /**
     * Display leave history for the department
     */
   public function history()
{
    $user = Auth::user();
    $department = $user->department;
    
    // Get leaves where department head has taken action
    $leaveHistory = LeaveRequest::with(['user', 'leaveType', 'approvals'])
        ->whereHas('user', function($query) use ($department) {
            $query->where('department_id', $department->id);
        })
        ->whereHas('approvals', function($query) {
            $query->where('level', 'department_head')
                  ->where('approver_id', Auth::id());
        })
        ->orderBy('created_at', 'desc')
        ->paginate(15);

    return view('modules.leave-management.department_head.history', compact('leaveHistory', 'department'));
}

    /**
     * Approve a leave request
     */
   public function approve(Request $request, $id)
{
    \Log::info('=== APPROVE METHOD STARTED ===');
    \Log::info('Leave ID: ' . $id);
    \Log::info('User ID: ' . Auth::id());
    \Log::info('Request Data: ', $request->all());
    
    try {
        \Log::info('Looking for leave request with ID: ' . $id);
        $leaveRequest = LeaveRequest::with('user')->find($id);
        
        if (!$leaveRequest) {
            \Log::error('Leave request not found with ID: ' . $id);
            return response()->json([
                'success' => false,
                'message' => 'Leave request not found.'
            ], 404);
        }
        
        \Log::info('Leave request found:', [
            'id' => $leaveRequest->id,
            'user_id' => $leaveRequest->user_id,
            'user_department_id' => $leaveRequest->user->department_id,
            'status' => $leaveRequest->status
        ]);

        $user = Auth::user();
        \Log::info('Current user:', [
            'id' => $user->id,
            'department_id' => $user->department_id,
            'name' => $user->name
        ]);
        
        // Check if the leave request belongs to user's department
        if ($leaveRequest->user->department_id !== $user->department_id) {
            \Log::error('Department mismatch:', [
                'leave_user_department' => $leaveRequest->user->department_id,
                'current_user_department' => $user->department_id
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Access denied. This leave request does not belong to your department.'
            ], 403);
        }

        // Check if already processed
        if ($leaveRequest->status !== 'pending') {
            \Log::warning('Leave already processed:', [
                'current_status' => $leaveRequest->status
            ]);
            return response()->json([
                'success' => false,
                'message' => 'This leave request has already been processed.'
            ], 400);
        }

        // Check if department head already approved this
        $existingApproval = \App\Models\Approval::where('leave_request_id', $id)
            ->where('level', 'department_head')
            ->first();
            
        if ($existingApproval) {
            \Log::warning('Department head already approved this request:', [
                'approval_status' => $existingApproval->status
            ]);
            return response()->json([
                'success' => false,
                'message' => 'You have already processed this leave request.'
            ], 400);
        }

        \Log::info('Creating department head approval record');
        
        // Create approval record instead of updating leave request status
        $approval = \App\Models\Approval::create([
            'leave_request_id' => $leaveRequest->id,
            'approver_id' => $user->id,
            'level' => 'department_head',
            'status' => 'approved',
            'remarks' => $request->head_remarks ?? 'Approved by department head - Waiting for HR approval'
        ]);
        
        \Log::info('Approval record created:', [
            'approval_id' => $approval->id,
            'level' => $approval->level,
            'status' => $approval->status
        ]);

        \Log::info('=== APPROVE METHOD COMPLETED SUCCESSFULLY ===');
        return response()->json([
            'success' => true,
            'message' => 'Leave request approved by department head. Now waiting for HR approval.'
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Error in approve method: ' . $e->getMessage());
        \Log::error('Stack trace: ' . $e->getTraceAsString());
        
        return response()->json([
            'success' => false,
            'message' => 'Error approving leave request: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Reject a leave request
     */
    public function reject(Request $request, $id)
{
    \Log::info('=== REJECT METHOD STARTED ===');
    \Log::info('Leave ID: ' . $id);
    \Log::info('User ID: ' . Auth::id());
    \Log::info('Request Data: ', $request->all());
    
    try {
        \Log::info('Looking for leave request with ID: ' . $id);
        $leaveRequest = LeaveRequest::with('user')->find($id);
        
        if (!$leaveRequest) {
            \Log::error('Leave request not found with ID: ' . $id);
            return response()->json([
                'success' => false,
                'message' => 'Leave request not found.'
            ], 404);
        }
        
        $user = Auth::user();
        
        // Check if the leave request belongs to user's department
        if ($leaveRequest->user->department_id !== $user->department_id) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied.'
            ], 403);
        }

        // Check if already processed
        if ($leaveRequest->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'This leave request has already been processed.'
            ], 400);
        }

        // Check if department head already processed this
        $existingApproval = \App\Models\Approval::where('leave_request_id', $id)
            ->where('level', 'department_head')
            ->first();
            
        if ($existingApproval) {
            return response()->json([
                'success' => false,
                'message' => 'You have already processed this leave request.'
            ], 400);
        }

        // Validate rejection reason
        $validated = $request->validate([
            'reason' => 'required|string|min:5|max:500'
        ]);

        // Create department head rejection approval
        $approval = \App\Models\Approval::create([
            'leave_request_id' => $leaveRequest->id,
            'approver_id' => $user->id,
            'level' => 'department_head',
            'status' => 'rejected',
            'remarks' => $request->reason
        ]);
        
        // Update leave request status to rejected (department head can reject fully)
        $leaveRequest->update([
            'status' => 'rejected',
            'rejection_reason' => $request->reason
        ]);

        \Log::info('=== REJECT METHOD COMPLETED SUCCESSFULLY ===');
        return response()->json([
            'success' => true,
            'message' => 'Leave request rejected.'
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Error in reject method: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error rejecting leave request: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->pending();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $leaveRequest = LeaveRequest::with(['user', 'leaveType'])->findOrFail($id);
            
            // Check if the leave request belongs to user's department
            if ($leaveRequest->user->department_id !== Auth::user()->department_id) {
                return redirect()->back()->with('error', 'Access denied.');
            }

            return view('modules.leave-management.department_head.leave-details', compact('leaveRequest'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Leave request not found.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}