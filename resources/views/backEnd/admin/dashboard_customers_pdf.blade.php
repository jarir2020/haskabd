<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customers Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 13px; color: #333; }
        h2 { text-align: center; margin-bottom: 4px; }
        p.meta { text-align: center; color: #888; font-size: 11px; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #6366f1; color: #fff; padding: 8px 10px; text-align: left; }
        td { padding: 7px 10px; border-bottom: 1px solid #eee; }
        tr:nth-child(even) td { background: #f9f9f9; }
        @media print { .no-print { display: none; } }
        .no-print { text-align: center; margin-bottom: 16px; }
        .no-print button { padding: 8px 20px; background: #6366f1; color: #fff; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; margin-right: 8px; }
        .no-print a { padding: 8px 20px; background: #e5e7eb; color: #333; border-radius: 6px; text-decoration: none; font-size: 14px; }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()">🖨 Print / Save as PDF</button>
        <a href="{{ route('dashboard') }}">← Back to Dashboard</a>
    </div>
    <h2>Customers Report</h2>
    <p class="meta">Generated: {{ now()->format('d M Y, H:i') }} &nbsp;|&nbsp; Rows: {{ count($customers) }}</p>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($customers as $i => $c)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $c->name }}</td>
                <td>{{ $c->phone ?? '—' }}</td>
                <td>{{ $c->email ?? '—' }}</td>
                <td>{{ $c->status ?? 'active' }}</td>
                <td>{{ $c->created_at->format('d M Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <script>
        // Auto-trigger print dialog -- added 2026-05-02
        window.addEventListener('load', function(){ window.print(); });
    </script>
</body>
</html>
