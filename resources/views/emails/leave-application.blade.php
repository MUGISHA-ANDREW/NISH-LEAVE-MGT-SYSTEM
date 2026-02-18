@component('mail::message')
# New Leave Application

A new leave application has been submitted by **{{ $leaveRequest->user->first_name }} {{ $leaveRequest->user->last_name }}**.

**Details:**

*   **Leave Type:** {{ $leaveRequest->leaveType->name }}
*   **From:** {{ $leaveRequest->start_date->format('Y-m-d') }}
*   **To:** {{ $leaveRequest->end_date->format('Y-m-d') }}
*   **Total Days:** {{ $leaveRequest->total_days }}
*   **Reason:** {{ $leaveRequest->reason }}

@component('mail::button', ['url' => route('head.leaves.pending')])
View Application
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
