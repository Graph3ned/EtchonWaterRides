<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #00A3E0;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            color: #00A3E0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            background-color: #00A3E0;
            color: white;
            padding: 8px 12px;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .metrics {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        .metric-row {
            display: table-row;
        }
        .metric-label {
            display: table-cell;
            font-weight: bold;
            padding: 5px 10px;
            width: 40%;
        }
        .metric-value {
            display: table-cell;
            padding: 5px 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table th {
            background-color: #f0f0f0;
            padding: 8px;
            text-align: left;
            border: 1px solid #ddd;
            font-weight: bold;
            font-size: 11px;
        }
        table td {
            padding: 6px 8px;
            border: 1px solid #ddd;
            font-size: 11px;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .text-right {
            text-align: right;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>Period: {{ $period }}</p>
        <p>Generated on: {{ $generated_at }}</p>
    </div>

    <div class="section">
        <div class="section-title">Key Metrics</div>
        <div class="metrics">
            <div class="metric-row">
                <div class="metric-label">Total Revenue:</div>
                <div class="metric-value">P{{ number_format($totalRevenue, 2) }}</div>
            </div>
            <div class="metric-row">
                <div class="metric-label">Total Rentals:</div>
                <div class="metric-value">{{ $totalRentals }}</div>
            </div>
            <div class="metric-row">
                <div class="metric-label">Average Transaction:</div>
                <div class="metric-value">P{{ number_format($averageTransaction, 2) }}</div>
            </div>
        </div>
    </div>

    @if($revenueByRideType->count() > 0)
    <div class="section">
        <div class="section-title">Revenue by Ride Type</div>
        <table>
            <thead>
                <tr>
                    <th>Ride Type</th>
                    <th class="text-right">Revenue</th>
                </tr>
            </thead>
            <tbody>
                @foreach($revenueByRideType as $rideType => $revenue)
                <tr>
                    <td>{{ $rideType }}</td>
                    <td class="text-right">P{{ number_format($revenue, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($revenueByStaff->count() > 0)
    <div class="section">
        <div class="section-title">Revenue by Staff</div>
        <table>
            <thead>
                <tr>
                    <th>Staff Member</th>
                    <th class="text-right">Revenue</th>
                </tr>
            </thead>
            <tbody>
                @foreach($revenueByStaff as $staff => $revenue)
                <tr>
                    <td>{{ $staff }}</td>
                    <td class="text-right">P{{ number_format($revenue, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($rentals->count() > 0)
    <div class="section">
        <div class="section-title">Detailed Transactions</div>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Staff</th>
                    <th>Ride Type</th>
                    <th>Classification</th>
                    <th>Ride ID</th>
                    <th class="text-right">Duration</th>
                    <th class="text-right">Life Jackets</th>
                    <th class="text-right">Total Price</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Note</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rentals as $rental)
                <tr>
                    <td>{{ $rental['date'] }}</td>
                    <td>{{ $rental['staff'] }}</td>
                    <td>{{ $rental['ride_type'] }}</td>
                    <td>{{ $rental['classification'] }}</td>
                    <td>{{ $rental['ride_identifier'] }}</td>
                    <td class="text-right">{{ $rental['duration'] }} min</td>
                    <td class="text-right">{{ $rental['life_jackets'] }}</td>
                    <td class="text-right">P{{ number_format($rental['total_price'], 2) }}</td>
                    <td>{{ $rental['start_time'] }}</td>
                    <td>{{ $rental['end_time'] }}</td>
                    <td>{{ $rental['note'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        <p>This report was generated automatically by Etchon Water Rides Management System</p>
    </div>
</body>
</html>

