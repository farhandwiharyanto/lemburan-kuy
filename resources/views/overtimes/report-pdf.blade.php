<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Lembur - {{ $user->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            color: #333;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 5px;
            border: 1px solid #ddd;
        }
        .info-table .label {
            background-color: #f8f9fa;
            font-weight: bold;
            width: 30%;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .data-table th {
            background-color: #343a40;
            color: white;
            padding: 8px;
            text-align: left;
            border: 1px solid #454d55;
        }
        .data-table td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        .data-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .total-row {
            background-color: #e9ecef !important;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #666;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .status-approved {
            color: #28a745;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN LEMBUR</h1>
        <p>Periode: {{ now()->format('d F Y') }}</p>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Nama Karyawan</td>
            <td>{{ $user->name }}</td>
        </tr>
        <tr>
            <td class="label">Departemen</td>
            <td>{{ $user->department }}</td>
        </tr>
        <tr>
            <td class="label">Total Lembur Disetujui</td>
            <td>{{ $overtimes->count() }} kali</td>
        </tr>
        <tr>
            <td class="label">Total Jam Lembur</td>
            <td>{{ $totalHours }} jam</td>
        </tr>
    </table>

    @if($overtimes->count() > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Jam Mulai</th>
                    <th>Jam Selesai</th>
                    <th>Durasi (Jam)</th>
                    <th>Deskripsi Tugas</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($overtimes as $index => $overtime)
                @php
                    $start = \Carbon\Carbon::parse($overtime->start_time);
                    $end = \Carbon\Carbon::parse($overtime->end_time);
                    $duration = round($end->diffInMinutes($start) / 60, 2); // Dalam jam dengan 2 decimal
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $overtime->date->format('d/m/Y') }}</td>
                    <td>{{ $overtime->start_time }}</td>
                    <td>{{ $overtime->end_time }}</td>
                    <td class="text-center">{{ $duration }} jam</td>
                    <td>{{ $overtime->task_description }}</td>
                    <td class="status-approved">DISETUJUI</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="4" class="text-right"><strong>TOTAL</strong></td>
                    <td class="text-center"><strong>{{ $totalHours }} jam</strong></td>
                    <td colspan="2"></td>
                </tr>
            </tbody>
        </table>
    @else
        <div style="text-align: center; padding: 40px; color: #666;">
            <p><strong>Tidak ada data lembur yang disetujui</strong></p>
            <p>Belum ada pengajuan lembur yang telah disetujui oleh pimpinan.</p>
        </div>
    @endif

    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d F Y H:i:s') }}</p>
        <p>Lemburan-Kuy &copy; {{ date('Y') }}</p>
    </div>
</body>
</html>