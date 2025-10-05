<?php

namespace App\Exports;

use App\Models\Overtime;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OvertimeExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function collection()
    {
        return Overtime::where('user_id', $this->userId)
            ->where('status', 'approved')
            ->orderBy('date', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal Lembur',
            'Jam Mulai',
            'Jam Selesai',
            'Durasi (Jam)',
            'Deskripsi Tugas',
            'Status',
            'Departemen'
        ];
    }

    public function map($overtime): array
{
    $start = \Carbon\Carbon::parse($overtime->start_time);
    $end = \Carbon\Carbon::parse($overtime->end_time);
    $duration = round($end->diffInMinutes($start) / 60, 2); // Dalam jam dengan 2 decimal

    return [
        $overtime->id,
        $overtime->date->format('d/m/Y'),
        $overtime->start_time,
        $overtime->end_time,
        $duration,
        $overtime->task_description,
        'DISETUJUI',
        $overtime->department
    ];
}

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true]],
            
            // Style the header row
            'A1:H1' => [
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF343A40']
                ],
                'font' => [
                    'color' => ['argb' => 'FFFFFFFF']
                ]
            ],
        ];
    }
}