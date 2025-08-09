<?php

namespace App\Exports;

use App\Models\Production;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class ProductionExport implements FromCollection, WithHeadings, WithTitle, WithEvents
{
    protected $production;

    public function __construct(Production $production)
    {
        $this->production = $production;
    }

    public function collection()
    {
        $rows = [];
        $serial = 1;
        $totalOrderQty = 0;
        $totalCuttingQty = 0;
        $inputQty = 0;
        $totalInput = 0;
        $outputQty = 0;
        $totalOutput = 0;
        $totalBalance = 0;

        foreach ($this->production->production_data as $row) {
            $orderQty = (int)($row['order_qty'] ?? 0);
            $cuttingQty = (int)($row['cutting_qty'] ?? 0);
            $factory = ($row['factory'] ?? 'N/A');
            $line = ($row['line'] ?? 'N/A');
            $input = (int)($row['input'] ?? 0);
            $total_input = (int)($row['total_input'] ?? 0);
            $output = (int)($row['output'] ?? 0);
            $total_output = (int)($row['total_output'] ?? 0);
            $balance = $total_input - $total_output;

            $rows[] = [
                'Serial No'         => $serial++,
                'Buyer'             => $this->production->order->buyer_name ?? 'N/A',
                'Style No'          => $this->production->order->style_no ?? 'N/A',
                'Garment Type'      => $this->production->garment_type ?? 'N/A',
                'Color'             => $row['color'] ?? '',
                'Order Quantity'    => $orderQty,
                'Cutting Quantity'  => $cuttingQty,
                'Factory'           => $factory,
                'Line'              => $line,
                'Input'             => $input,
                'Total Input'              => $total_input,
                'Output'              => $output,
                'Total Output'           => $total_output,
                'Balance'           => $balance,
                'Remarks'           => $row['remarks'] ?? '',
            ];

            $totalOrderQty += $orderQty;
            $totalCuttingQty += $cuttingQty;
            $inputQty += $input;
            $totalInput += $total_input;
            $outputQty += $output;
            $totalOutput += $total_output;
            $totalBalance += $balance;
        }

        // Add total row at the end
        $rows[] = [
            'Serial No'         => '',
            'Buyer'         => '',
            'Style No'          => '',
            'Garment Type'      => '',
            'Color'             => 'Total',
            'Order Quantity'    => $totalOrderQty,
            'Cutting Quantity'  => $totalCuttingQty,
            'Factory'           =>'',
            'Line'              =>'',
            'Input'              => $inputQty,
            'Total Input'           => $totalInput,
            'Output'              => $outputQty,
            'Total Output'           => $totalOutput,
            'Balance'           => $totalBalance,
            'Remarks'           => '',
        ];

        return collect($rows);
    }

    public function headings(): array
    {
        $date = Carbon::parse($this->production->date)->format('d-m-Y');
        return [
            ['A.Z Group'],
            ['295/JA/4/A, Rayer Bazar, Dhaka-1209'],
            ['Daily Production Report'],
            [],
            ['', '', '', '', '', '', '', '', '', '', '', 'Date: ' . $date],
            [
                'SL',
                'Buyer',
                'Style No',
                'Garment Type',
                'Color',
                'Order Quantity',
                'Cutting Quantity',
                'Factory',
                'Line',
                'Input',
                'Total Input',
                'Output',
                'Total Output',
                'Balance',
                'Remarks'
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Merge and style headers
                foreach (['A1:O1', 'A2:O2', 'A3:O3'] as $range) {
                    $event->sheet->mergeCells($range);
                }
                foreach (['A1', 'A2', 'A3'] as $cell) {
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
                $event->sheet->getStyle('O5')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                    ],
                ]);
                $event->sheet->getStyle('A6:O6')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                ]);
                // Borders for table
                $rowCount = count($this->production->production_data);
                $totalRow = 6 + $rowCount + 1; // 6 heading rows, then data, then total row
                $lastRow = $totalRow;
                $cellRange = "A6:O{$lastRow}";
                $event->sheet->getStyle($cellRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);
                // Bold the total row
                $event->sheet->getStyle("A{$totalRow}:O{$totalRow}")->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                ]);

                // Auto-size columns so header fits
                 foreach (range('A', 'O') as $col) {
                    $event->sheet->getDelegate()->getColumnDimension($col)->setAutoSize(true);
                }
               
            }
        ];
    }

    public function title(): string
    {
        return 'Production production';
    }
}
