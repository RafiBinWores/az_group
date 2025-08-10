<?php

namespace App\Exports;

use App\Models\Finishing;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class FinisingExport implements FromCollection, WithHeadings, WithEvents, WithTitle
{
     protected $finishing;

    public function __construct($finishing)
    {
        $this->finishing = $finishing;
    }

    public function collection()
    {
        $rows = [];
        $serial = 1;
        $finishingQty = 0;
        // Use color_qty if that's your JSON/array field
        foreach ($this->finishing as $row) {
            $rows[] = [
                'Serial No' => $serial++,
                'Style No' => $this->finishing->style_no ?? 'N/A',
                'Color' => $row['color'] ?? '',
                'finishing Quantity' => $row['qty'] ?? 0,
                'Remarks' => $row['remarks'] ?? '',
            ];
            $finishingQty += (int) ($row['qty'] ?? 0);
        }

        // Add Total row at the end
        $rows[] = [
            'Serial No' => '',
            'Style No' => '',
            'Color' => 'Total',
            'finishing Quantity' => $finishingQty,
            'Remarks' => '',
        ];

        return collect($rows);
    }

    public function headings(): array
    {
        $date = $this->finishing->created_at
            ? Carbon::parse($this->finishing->created_at)->format('d-m-Y')
            : '';
        // Render garment types as comma-separated
        $garmentTypes = $this->finishing->garmentTypes->pluck('name')->join(', ') ?: 'N/A';

        return [
            ['A.Z Group'],
            ['295/JA/4/A, Rayer Bazar, Dhaka-1209'],
            ['finishing Report'],
            ["Garment Types: {$garmentTypes}"],
            ['', '', '', '', 'Date: ' . $date],
            ['Serial No', 'Style No', 'Color', 'finishing Quantity', 'Remarks'],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Merge and style headers
                foreach (['A1:E1', 'A2:E2', 'A3:E3', 'A4:E4'] as $range) {
                    $event->sheet->mergeCells($range);
                }
                foreach (['A1', 'A2', 'A3', 'A4'] as $cell) {
                    $event->sheet->getStyle($cell)->applyFromArray([
                        'font' => [
                            'bold' => true,
                            'size' => ($cell === 'A1' ? 16 : ($cell === 'A3' ? 14 : 12)),
                        ],
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        ],
                    ]);
                }
                $event->sheet->getStyle('E5')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                    ],
                ]);
                $event->sheet->getStyle('A6:E6')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                ]);
                // Bfinishings for table
                $rowCount = count($this->finishing->color_qty ?? []);
                $totalRow = 6 + $rowCount + 1; // 6 heading rows, then data, then total row
                $lastRow = $totalRow;
                $cellRange = "A6:E{$lastRow}";
                $event->sheet->getStyle($cellRange)->applyFromArray([
                    'bfinishings' => [
                        'allBfinishings' => [
                            'bfinishingStyle' => \PhpOffice\PhpSpreadsheet\Style\Bfinishing::Bfinishing_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);
                // Bold the total row (A to E)
                $event->sheet->getStyle("A{$totalRow}:E{$totalRow}")->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                ]);
            }
        ];
    }

    public function title(): string
    {
        return 'finishing Report';
    }
}
