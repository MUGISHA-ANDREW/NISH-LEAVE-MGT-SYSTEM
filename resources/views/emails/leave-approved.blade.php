@component('mail::message')
# Leave Application Approved

Your leave application has been approved.

**Details:**

*   **Leave Type:** {{ $leaveRequest->leaveType->name }}
*   **From:** {{ $leaveRequest->start_date->format('Y-m-d') }}
*   **To:** {{ $leaveRequest->end_date->format('Y-m-d') }}
*   **Total Days:** {{ $leaveRequest->total_days }}

@component('mail::button', ['url' => route('employee.leave.history')])
View Leave History
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
