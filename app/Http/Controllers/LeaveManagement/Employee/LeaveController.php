<?php

namespace App\Http\Controllers\LeaveManagement\Employee;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class LeaveController extends Controller
{
    /**
     * Display leave history - matches Route::get('/leave-history')
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get filters from request
        $status = $request->status;
        $leaveType = $request->leave_type;
        $monthYear = $request->month_year;

        // Get leave requests with filters
        $leaveRequests = LeaveRequest::with('leaveType')
            ->where('user_id', $user->id)
            ->when($status && $status !== 'all', function($query) use ($status) {
                return $query->where('status', $status);
            })
            ->when($leaveType && $leaveType !== 'all', function($query) use ($leaveType) {
                return $query->where('leave_type_id', $leaveType);
            })
            ->when($monthYear, function($query) use ($monthYear) {
                return $query->whereYear('start_date', substr($monthYear, 0, 4))
                           ->whereMonth('start_date', substr($monthYear, 5, 2));
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Summary counts
        $summary = [
            'pending' => LeaveRequest::where('user_id', $user->id)->where('status', 'pending')->count(),
            'approved' => LeaveRequest::where('user_id', $user->id)->where('status', 'approved')->count(),
            'rejected' => LeaveRequest::where('user_id', $user->id)->where('status', 'rejected')->count(),
            'total_days' => LeaveRequest::where('user_id', $user->id)->where('status', 'approved')->sum('total_days'),
        ];

        $leaveTypes = LeaveType::all();

        return view('modules.Leave-management.employee.index', compact('leaveRequests', 'summary', 'leaveTypes'));
    }

    /**
     * Show leave application form - matches Route::get('/apply-leave')
     */
    public function create()
    {
        $leaveTypes = LeaveType::all();
        $user = Auth::user();
        
        // Calculate balances for each leave type
        $balances = [];
        foreach ($leaveTypes as $type) {
            $usedDays = LeaveRequest::where('user_id', $user->id)
                ->where('leave_type_id', $type->id)
                ->where('status', 'approved')
                ->sum('total_days');
            
            $balances[$type->id] = $type->max_days ? ($type->max_days - $usedDays) : null;
        }

        return view('modules.Leave-management.employee.create', compact('leaveTypes', 'balances'));
    }

    /**
     * Store new leave application - we need to add this route
     */
public function store(Request $request)
{
    Log::info('=== LEAVE STORE METHOD CALLED ===');
    Log::info('Request Method: ' . $request->method());
    Log::info('Request Headers:', $request->headers->all());
    Log::info('Form Data:', $request->all());
    Log::info('CSRF Token matches: ' . ($request->header('X-CSRF-TOKEN') === csrf_token() ? 'YES' : 'NO'));

    try {
        // Validate the request
        $validated = $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|min:10|max:1000',
            'contact_number' => 'required|string|max:20',
            'emergency_contact' => 'nullable|string|max:20',
            'handover_notes' => 'nullable|string|max:500',
        ]);

        Log::info('Validation passed successfully');

        // Calculate working days
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $totalDays = $this->calculateWorkingDays($startDate, $endDate);

        Log::info("Date range: {$startDate} to {$endDate}");
        Log::info("Calculated total days: " . $totalDays);

        if ($totalDays === 0) {
            Log::warning('Total days calculated as 0 - only weekends selected');
            return back()->withErrors([
                'end_date' => 'The selected dates include only weekends. Please select dates that include working days.'
            ])->withInput();
        }

        // Check leave balance
        $leaveType = LeaveType::find($request->leave_type_id);
        $user = Auth::user();
        
        $usedDays = LeaveRequest::where('user_id', $user->id)
            ->where('leave_type_id', $request->leave_type_id)
            ->where('status', 'approved')
            ->sum('total_days');
        
        $availableDays = $leaveType->max_days ? ($leaveType->max_days - $usedDays) : null;

        Log::info("Leave type: {$leaveType->name}, Available days: " . ($availableDays ?? 'Unlimited'));

        if ($availableDays !== null && $totalDays > $availableDays) {
            Log::warning("Insufficient leave balance. Available: {$availableDays}, Requested: {$totalDays}");
            return back()->withErrors([
                'end_date' => "You only have {$availableDays} days remaining for {$leaveType->name}. You requested {$totalDays} days."
            ])->withInput();
        }

        // Create the leave request with all required fields
        $leaveData = [
            'user_id' => $user->id,
            'leave_type_id' => $request->leave_type_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'total_days' => $totalDays,
            'reason' => $request->reason,
            'contact_number' => $request->contact_number,
            'emergency_contact' => $request->emergency_contact,
            'handover_notes' => $request->handover_notes,
            'status' => 'pending',
        ];

        Log::info('Creating leave request with data:', $leaveData);

        $leaveRequest = LeaveRequest::create($leaveData);

        Log::info('Leave request created successfully with ID: ' . $leaveRequest->id);

        return redirect()->route('employee.leave.history')->with('success', 'Leave application submitted successfully! It is now pending approval.');

    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::error('Validation failed: ' . json_encode($e->errors()));
        throw $e; // Let Laravel handle validation exceptions
    } catch (\Exception $e) {
        Log::error('Error creating leave request: ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());
        
        return back()->withErrors([
            'error' => 'There was an error submitting your leave application. Please try again. Error: ' . $e->getMessage()
        ])->withInput();
    }
}

    /**
     * Calculate working days excluding weekends
     */
    private function calculateWorkingDays($startDate, $endDate)
    {
        $totalDays = 0;
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            // Skip weekends (Saturday = 6, Sunday = 0)
            if (!$currentDate->isWeekend()) {
                $totalDays++;
            }
            $currentDate->addDay();
        }

        return $totalDays;
    }

    /**
 * Cancel a pending leave request
 */
public function cancel($id)
{
    $leaveRequest = LeaveRequest::where('user_id', Auth::id())
        ->where('status', 'pending')
        ->findOrFail($id);

    $leaveRequest->update(['status' => 'cancelled']);

    return redirect()->route('employee.leave.history')->with('success', 'Leave request cancelled successfully.');
}

// Add these methods to your existing LeaveController

/**
 * Show the form for editing the specified resource.
 */
public function edit($id)
{
    $leaveRequest = LeaveRequest::with('leaveType')
        ->where('user_id', Auth::id())
        ->where('status', 'pending') // Only allow editing pending requests
        ->findOrFail($id);

    $leaveTypes = LeaveType::all();
    $user = Auth::user();
    
    // Calculate balances
    $balances = [];
    foreach ($leaveTypes as $type) {
        $usedDays = LeaveRequest::where('user_id', $user->id)
            ->where('leave_type_id', $type->id)
            ->where('status', 'approved')
            ->sum('total_days');
        
        $balances[$type->id] = $type->max_days ? ($type->max_days - $usedDays) : null;
    }

    return view('modules.Leave-management.employee.edit', compact('leaveRequest', 'leaveTypes', 'balances'));
}

/**
 * Display the specified leave request.
 */
public function show($id)
{
    $leaveRequest = LeaveRequest::with(['leaveType', 'user', 'standInEmployee'])
        ->where('user_id', Auth::id())
        ->findOrFail($id);

    return view('modules.Leave-management.employee.show', compact('leaveRequest'));
}

/**
 * Update the specified resource in storage.
 */
public function update(Request $request, $id)
{
    $leaveRequest = LeaveRequest::where('user_id', Auth::id())
        ->where('status', 'pending') // Only allow updating pending requests
        ->findOrFail($id);

    // Validate the request
    $request->validate([
        'leave_type_id' => 'required|exists:leave_types,id',
        'start_date' => 'required|date|after_or_equal:today',
        'end_date' => 'required|date|after_or_equal:start_date',
        'reason' => 'required|string|min:10|max:1000',
        'contact_number' => 'required|string|max:20',
        'emergency_contact' => 'nullable|string|max:20',
        'handover_notes' => 'nullable|string|max:500',
    ]);

    // Calculate working days (excluding weekends)
    $startDate = Carbon::parse($request->start_date);
    $endDate = Carbon::parse($request->end_date);
    $totalDays = $this->calculateWorkingDays($startDate, $endDate);

    if ($totalDays === 0) {
        return back()->withErrors([
            'end_date' => 'The selected dates include only weekends. Please select dates that include working days.'
        ])->withInput();
    }

    // Check leave balance (exclude current request from calculation)
    $leaveType = LeaveType::find($request->leave_type_id);
    $user = Auth::user();
    
    $usedDays = LeaveRequest::where('user_id', $user->id)
        ->where('leave_type_id', $request->leave_type_id)
        ->where('status', 'approved')
        ->sum('total_days');
    
    $availableDays = $leaveType->max_days ? ($leaveType->max_days - $usedDays) : null;

    // If changing leave type, check balance for the new type
    if ($availableDays !== null && $totalDays > $availableDays) {
        return back()->withErrors([
            'end_date' => "You only have {$availableDays} days remaining for {$leaveType->name}. You requested {$totalDays} days."
        ])->withInput();
    }

    // Update the leave request
    $leaveRequest->update([
        'leave_type_id' => $request->leave_type_id,
        'start_date' => $request->start_date,
        'end_date' => $request->end_date,
        'total_days' => $totalDays,
        'reason' => $request->reason,
        'contact_number' => $request->contact_number,
        'emergency_contact' => $request->emergency_contact,
        'handover_notes' => $request->handover_notes,
    ]);

    return redirect()->route('employee.leave.history')->with('success', 'Leave request updated successfully!');
}

/**
 * Retrieve a cancelled leave request
 */
public function retrieve($id)
{
    $leaveRequest = LeaveRequest::where('user_id', Auth::id())
        ->where('status', 'cancelled')
        ->findOrFail($id);

    // Check if the leave dates are still valid (not in the past)
    if ($leaveRequest->start_date < today()) {
        return redirect()->route('employee.leave.history')
            ->with('error', 'Cannot retrieve leave request that has already started.');
    }

    $leaveRequest->update(['status' => 'pending']);

    return redirect()->route('employee.leave.history')
        ->with('success', 'Leave request retrieved successfully! It is now pending approval.');
}

/**
 * Export leave history to Excel (CSV format)
 */
public function export(Request $request)
{
    $user = Auth::user();
    
    // Get filters from request
    $status = $request->status;
    $leaveType = $request->leave_type;
    $monthYear = $request->month_year;

    // Get leave requests with filters (no pagination for export)
    $leaveRequests = LeaveRequest::with(['leaveType', 'user'])
        ->where('user_id', $user->id)
        ->when($status && $status !== 'all', function($query) use ($status) {
            return $query->where('status', $status);
        })
        ->when($leaveType && $leaveType !== 'all', function($query) use ($leaveType) {
            return $query->where('leave_type_id', $leaveType);
        })
        ->when($monthYear, function($query) use ($monthYear) {
            return $query->whereYear('start_date', substr($monthYear, 0, 4))
                       ->whereMonth('start_date', substr($monthYear, 5, 2));
        })
        ->orderBy('created_at', 'desc')
        ->get();

    // Generate filename with current date
    $filename = 'my_leave_history_' . date('Y-m-d_His') . '.csv';

    // Set headers for CSV download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');

    // Open output stream
    $output = fopen('php://output', 'w');

    // Add UTF-8 BOM for Excel compatibility
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

    // Add CSV headers
    fputcsv($output, [
        'Employee Name',
        'Leave Type',
        'Start Date',
        'End Date',
        'Total Days',
        'Reason',
        'Status',
        'Applied Date',
        'Contact Number',
        'Emergency Contact',
        'Handover Notes',
        'Reviewed By',
        'Review Date',
        'Review Comments'
    ]);

    // Add data rows
    foreach ($leaveRequests as $leave) {
        fputcsv($output, [
            $leave->user->name ?? 'N/A',
            $leave->leaveType->name ?? 'N/A',
            $leave->start_date ? Carbon::parse($leave->start_date)->format('M d, Y') : 'N/A',
            $leave->end_date ? Carbon::parse($leave->end_date)->format('M d, Y') : 'N/A',
            $leave->total_days ?? 0,
            $leave->reason ?? '',
            ucfirst($leave->status ?? 'pending'),
            $leave->created_at ? Carbon::parse($leave->created_at)->format('M d, Y h:i A') : 'N/A',
            $leave->contact_number ?? '',
            $leave->emergency_contact ?? '',
            $leave->handover_notes ?? '',
            $leave->reviewed_by ?? '',
            $leave->reviewed_at ? Carbon::parse($leave->reviewed_at)->format('M d, Y h:i A') : '',
            $leave->reviewer_comments ?? ''
        ]);
    }

    fclose($output);
    exit();
}
}