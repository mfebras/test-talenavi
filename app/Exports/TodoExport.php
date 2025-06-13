<?php

namespace App\Exports;

use App\Models\Todo;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TodoExport implements FromCollection, WithHeadings, WithStyles, WithEvents, ShouldAutoSize
{
    public function __construct(
        protected $data,
        protected $total,
        protected $totalTimeTracked
    ) {}

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'Title',
            'Assignee',
            'Due Date',
            'Time Tracked',
            'Status',
            'Priority',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
           // Header style the first row as bold text.
           1 => ['font' => ['bold' => true]],
           // Total item style
           count($this->data) + 3 => ['font' => ['bold' => true]],
           // Total time tracked style
           count($this->data) + 4 => ['font' => ['bold' => true]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Get total row data
                $lastRow = count($this->data) + 1;

                // Add new row for total
                $event->sheet->setCellValue('A' . ($lastRow + 2), 'Total Item');
                $event->sheet->setCellValue('F' . ($lastRow + 2), $this->total);

                // Add new row for total time tracked
                $event->sheet->setCellValue('A' . ($lastRow + 3), 'Total Time Tracked');
                $event->sheet->setCellValue('F' . ($lastRow + 3), $this->totalTimeTracked);

                // Merge label cell
                $event->sheet->mergeCells('A' . ($lastRow + 2) . ':E' . ($lastRow + 2));
                $event->sheet->mergeCells('A' . ($lastRow + 3) . ':E' . ($lastRow + 3));
            },
        ];
    }
}
