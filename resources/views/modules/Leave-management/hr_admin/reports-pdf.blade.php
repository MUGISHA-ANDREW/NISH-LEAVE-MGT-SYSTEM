<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports & Analytics - Nish Auto Limited</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #333;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 15px;
        }

        .company-name {
            font-size: 24pt;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 5px;
        }

        .report-title {
            font-size: 18pt;
            color: #374151;
            margin-bottom: 10px;
        }

        .report-meta {
            font-size: 9pt;
            color: #6b7280;
            margin-top: 10px;
        }

        .filters-section {
            background-color: #f3f4f6;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #d1d5db;
        }

        .filters-title {
            font-weight: bold;
            font-size: 11pt;
            margin-bottom: 10px;
            color: #1f2937;
        }

        .filter-item {
            display: inline-block;
            margin-right: 20px;
            margin-bottom: 5px;
            font-size: 9pt;
        }

        .filter-label {
            font-weight: bold;
            color: #4b5563;
        }

        .filter-value {
            color: #1f2937;
        }

        .section-title {
            font-size: 14pt;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 15px;
            margin-top: 25px;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 5px;
        }

        .stats-section {
            margin-bottom: 25px;
            width: 100%;
        }

        .stat-card {
            background-color: #f9fafb;
            padding: 12px;
            border-radius: 5px;
            text-align: center;
            border: 1px solid #e5e7eb;
            display: inline-block;
            width: 12%;
            margin-right: 0.5%;
            vertical-align: top;
        }

        .stat-number {
            font-size: 18pt;
            font-weight: bold;
            color: #1e40af;
        }

        .stat-number.green { color: #059669; }
        .stat-number.yellow { color: #d97706; }
        .stat-number.red { color: #dc2626; }
        .stat-number.purple { color: #7c3aed; }

        .stat-label {
            font-size: 8pt;
            color: #6b7280;
            margin-top: 5px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 8pt;
        }

        .data-table thead {
            background-color: #1e40af;
            color: white;
        }

        .data-table th {
            padding: 10px 6px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #1e3a8a;
            font-size: 8pt;
        }

        .data-table td {
            padding: 8px 6px;
            border: 1px solid #d1d5db;
            vertical-align: top;
        }

        .data-table tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 7pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-approved {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-rejected {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .status-cancelled {
            background-color: #e5e7eb;
            color: #374151;
        }

        .distribution-table {
            width: 50%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 9pt;
        }

        .distribution-table thead {
            background-color: #7c3aed;
            color: white;
        }

        .distribution-table th {
            padding: 8px 10px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #6d28d9;
        }

        .distribution-table td {
            padding: 6px 10px;
            border: 1px solid #d1d5db;
        }

        .distribution-table tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .dept-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 9pt;
        }

        .dept-table thead {
            background-color: #059669;
            color: white;
        }

        .dept-table th {
            padding: 8px 10px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #047857;
        }

        .dept-table td {
            padding: 6px 10px;
            border: 1px solid #d1d5db;
        }

        .dept-table tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 8pt;
            color: #6b7280;
            padding-top: 15px;
            border-top: 1px solid #d1d5db;
        }

        .page-break {
            page-break-after: always;
        }

        @page {
            margin: 10mm;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #6b7280;
            font-style: italic;
        }

        .employee-name {
            font-weight: bold;
            color: #1f2937;
        }

        .department-name {
            font-size: 7pt;
            color: #6b7280;
        }

        .leave-type {
            font-weight: 600;
            color: #2563eb;
        }

        .reason-text {
            max-width: 200px;
            word-wrap: break-word;
            font-size: 7pt;
        }

        .two-col {
            width: 100%;
        }
        .two-col td {
            vertical-align: top;
            padding: 0 10px 0 0;
            border: none;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="company-name">NISH AUTO LIMITED</div>
        <div class="report-title">Reports & Analytics</div>
        <div class="report-meta">
            Generated on: {{ date('F d, Y \a\t H:i A') }} |
            Total Records: {{ count($leaveRequests) }}
        </div>
    </div>

    <!-- Applied Filters -->
    <div class="filters-section">
        <div class="filters-title">Applied Filters:</div>
        <div class="filter-item">
            <span class="filter-label">Period:</span>
            <span class="filter-value">{{ $filters['period'] }}</span>
        </div>
        <div class="filter-item">
            <span class="filter-label">Department:</span>
            <span class="filter-value">{{ $filters['department'] }}</span>
        </div>
        <div class="filter-item">
            <span class="filter-label">Report Type:</span>
            <span class="filter-value">{{ $filters['report_type'] }}</span>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="section-title">Summary Statistics</div>
    <div class="stats-section">
        <div class="stat-card">
            <div class="stat-number">{{ $stats['total_requests'] }}</div>
            <div class="stat-label">Total Requests</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $stats['total_leave_days'] }}</div>
            <div class="stat-label">Total Leave Days</div>
        </div>
        <div class="stat-card">
            <div class="stat-number green">{{ $stats['approved'] }}</div>
            <div class="stat-label">Approved</div>
        </div>
        <div class="stat-card">
            <div class="stat-number yellow">{{ $stats['pending'] }}</div>
            <div class="stat-label">Pending</div>
        </div>
        <div class="stat-card">
            <div class="stat-number red">{{ $stats['rejected'] }}</div>
            <div class="stat-label">Rejected</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $stats['avg_leave_duration'] }}</div>
            <div class="stat-label">Avg. Duration</div>
        </div>
        <div class="stat-card">
            <div class="stat-number purple">{{ $stats['approval_rate'] }}%</div>
            <div class="stat-label">Approval Rate</div>
        </div>
        <div style="clear:both;"></div>
    </div>

    <!-- Leave Type Distribution & Department Stats side by side -->
    @if(count($leaveDistribution) > 0 || count($departmentStats) > 0)
    <table class="two-col" style="width:100%; margin-bottom: 20px;">
        <tr>
            <td style="width: 45%; vertical-align: top;">
                @if(count($leaveDistribution) > 0)
                <div class="section-title" style="margin-top:0;">Leave Type Distribution</div>
                <table class="distribution-table" style="width:100%;">
                    <thead>
                        <tr>
                            <th>Leave Type</th>
                            <th style="text-align:center;">Count</th>
                            <th style="text-align:center;">Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalDist = array_sum(array_column($leaveDistribution, 'count')); @endphp
                        @foreach($leaveDistribution as $item)
                        <tr>
                            <td>{{ $item['name'] }}</td>
                            <td style="text-align:center; font-weight:bold;">{{ $item['count'] }}</td>
                            <td style="text-align:center;">{{ $totalDist > 0 ? round(($item['count'] / $totalDist) * 100, 1) : 0 }}%</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </td>
            <td style="width: 55%; vertical-align: top;">
                @if(count($departmentStats) > 0)
                <div class="section-title" style="margin-top:0;">Department Comparison</div>
                <table class="dept-table" style="width:100%;">
                    <thead>
                        <tr>
                            <th>Department</th>
                            <th style="text-align:center;">Requests</th>
                            <th style="text-align:center;">Leave Days</th>
                            <th style="text-align:center;">Avg. Duration</th>
                            <th style="text-align:center;">Approved</th>
                            <th style="text-align:center;">Pending</th>
                            <th style="text-align:center;">Rejected</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($departmentStats as $dept)
                        <tr>
                            <td style="font-weight:bold;">{{ $dept['name'] }}</td>
                            <td style="text-align:center;">{{ $dept['total_requests'] }}</td>
                            <td style="text-align:center; font-weight:bold;">{{ $dept['leave_days'] }}</td>
                            <td style="text-align:center;">{{ $dept['avg_duration'] }}</td>
                            <td style="text-align:center; color:#059669;">{{ $dept['approved'] }}</td>
                            <td style="text-align:center; color:#d97706;">{{ $dept['pending'] }}</td>
                            <td style="text-align:center; color:#dc2626;">{{ $dept['rejected'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </td>
        </tr>
    </table>
    @endif

    <!-- Detailed Leave Requests Table -->
    <div class="section-title">Leave Requests Detail</div>
    @if(count($leaveRequests) > 0)
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 12%;">Employee</th>
                <th style="width: 10%;">Department</th>
                <th style="width: 10%;">Leave Type</th>
                <th style="width: 8%;">Start Date</th>
                <th style="width: 8%;">End Date</th>
                <th style="width: 5%;">Days</th>
                <th style="width: 20%;">Reason</th>
                <th style="width: 8%;">Status</th>
                <th style="width: 10%;">Applied Date</th>
                <th style="width: 9%;">Processing</th>
            </tr>
        </thead>
        <tbody>
            @foreach($leaveRequests as $request)
            @php
                $processingDays = 'Pending';
                if ($request->action_at) {
                    $days = \Carbon\Carbon::parse($request->created_at)->diffInDays(\Carbon\Carbon::parse($request->action_at));
                    $processingDays = $days . ' day' . ($days != 1 ? 's' : '');
                }
            @endphp
            <tr>
                <td>
                    <div class="employee-name">
                        {{ $request->user->first_name ?? 'Unknown' }} {{ $request->user->last_name ?? '' }}
                    </div>
                    <div style="font-size: 7pt; color: #6b7280;">
                        ID: {{ $request->user->employee_id ?? 'N/A' }}
                    </div>
                </td>
                <td class="department-name">
                    {{ $request->user->department->name ?? 'N/A' }}
                </td>
                <td class="leave-type">
                    {{ $request->leaveType->name ?? 'N/A' }}
                </td>
                <td>
                    {{ $request->start_date ? \Carbon\Carbon::parse($request->start_date)->format('M d, Y') : 'N/A' }}
                </td>
                <td>
                    {{ $request->end_date ? \Carbon\Carbon::parse($request->end_date)->format('M d, Y') : 'N/A' }}
                </td>
                <td style="text-align: center; font-weight: bold;">
                    {{ $request->total_days ?? 0 }}
                </td>
                <td>
                    <div class="reason-text">
                        {{ Str::limit($request->reason ?? 'No reason provided', 100) }}
                    </div>
                </td>
                <td>
                    <span class="status-badge status-{{ strtolower($request->status) }}">
                        {{ ucfirst($request->status) }}
                    </span>
                </td>
                <td>
                    {{ $request->created_at ? \Carbon\Carbon::parse($request->created_at)->format('M d, Y') : 'N/A' }}<br>
                    <span style="font-size: 7pt; color: #6b7280;">
                        {{ $request->created_at ? \Carbon\Carbon::parse($request->created_at)->format('h:i A') : '' }}
                    </span>
                </td>
                <td style="text-align: center;">
                    {{ $processingDays }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="no-data">
        <p>No leave records found for the selected filters.</p>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p><strong>Nish Auto Limited</strong> - Leave Management System</p>
        <p>This is a computer-generated report and does not require a signature.</p>
        <p>&copy; {{ date('Y') }} Nish Auto Limited. All rights reserved. | Confidential Document</p>
    </div>
</body>
</html>
