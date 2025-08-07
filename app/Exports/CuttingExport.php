<?php

namespace App\Exports;

use App\Models\Cutting;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class CuttingExport implements FromCollection, WithHeadings, WithEvents, WithTitle
{
   protected $cutting;

    public function __construct($cutting)
    {
        $this->cutting = $cutting;
    }

    public function collection()
    {
        $rows = [];
        $serial = 1;
        $orderQty = 0;
        $cuttingQty = 0;
        foreach ($this->cutting->cutting as $row) {
            $rows[] = [
                'Serial No' => $serial++,
                'Style No' => $this->cutting->order->style_no ?? 'N/A',
                'Color' => $row['color'],
                'Order Quantity' => $row['order_qty'],
                'Cutting Quantity' => $row['cutting_qty'],
                'Remarks' => $row['remarks'] ?? '',
            ];
            $orderQty += (int) $row['order_qty'];
            $cuttingQty += (int) $row['cutting_qty'];
        }

        // Add Total row at the end
        $rows[] = [
            'Serial No' => '',
            'Style No' => '',
            'Color' => 'Total',
            'Order Quantity' => $orderQty,
            'Cutting Quantity' => $cuttingQty,
            'Remarks' => '',
        ];

        return collect($rows);
    }

    public function headings(): array
    {
        $date = Carbon::parse($this->cutting->date)->format('d-m-Y');
        $garmentTypes = $this->cutting->garment_type ?: 'N/A';

        return [
            ['A.Z Group'],
            ['295/JA/4/A, Rayer Bazar, Dhaka-1209'],
            ['Daily Cutting Report'],
            ["Garment Types: {$garmentTypes}"],
            [],
            ['', '', '', '','', 'Date: ' . $date],
            ['Serial No', 'Style No', 'Color','Order Quantity', 'Cutting Quantity', 'Remarks'],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Merge and style headers
                foreach(['A1:F1','A2:F2','A3:F3', 'A4:F4'] as $range) {
                    $event->sheet->mergeCells($range);
                }
                foreach(['A1','A2','A3', 'A4'] as $cell) {
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
                $event->sheet->getStyle('E4')->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                    ],
                ]);
                $event->sheet->getStyle('F5')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                    ],
                ]);
                $event->sheet->getStyle('A6:F6')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                    ],
                ]);
                // Borders for table
                $rowCount = count($this->cutting->cutting);
                $totalRow = 7 + $rowCount + 1;
                $lastRow = $totalRow;
                $cellRange = "A7:F{$lastRow}";
                $event->sheet->getStyle($cellRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);
                // Bold the total row
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
        return 'Cutting Report';
    }
}
