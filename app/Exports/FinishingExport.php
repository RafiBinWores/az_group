<?php

namespace App\Exports;

use App\Models\Finishing;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class FinishingExport implements FromCollection, WithHeadings, WithTitle, WithEvents
{
    protected $finishing;

    public function __construct(Finishing $finishing)
    {
        $this->finishing = $finishing->load('order.garmentTypes');
    }

    public function collection()
    {
        // single data row for the process fields
        return collect([[
            'Thread Cutting'   => $this->finishing->thread_cutting ?? 0,
            'QC Check'         => $this->finishing->qc_check ?? 0,
            'Button/Rivet'     => $this->finishing->button_rivet_attach ?? 0,
            'Iron'             => $this->finishing->iron ?? 0,
            'Hangtag'          => $this->finishing->hangtag ?? 0,
            'Poly'             => $this->finishing->poly ?? 0,
            'Carton'           => $this->finishing->carton ?? 0,
            'Today Finishing'  => $this->finishing->today_finishing ?? 0,
            'Total Finishing'  => $this->finishing->total_finishing ?? 0,
            'Plan To Complete' => $this->finishing->plan_to_complete ?? 0,
            'DPI Inline'       => $this->finishing->dpi_inline ?? 0,
            'FRI Final'        => $this->finishing->fri_final ?? 0,
        ]]);
    }

    public function headings(): array
    {
        $order = $this->finishing->order;
        $date  = $this->finishing->date ? Carbon::parse($this->finishing->date)->format('d-m-Y') : '';
        $garmentTypes = $order?->garmentTypes->pluck('name')->join(', ') ?: 'N/A';

        // Top meta section (key/value), then a blank row, then table headers
        return [
            ['A.Z Group'],
            ['295/JA/4/A, Rayer Bazar, Dhaka-1209'],
            ['Finishing Report'],
            [],
            ['Buyer Name:', $order->buyer_name ?? 'N/A'],
            ['Style No:', $order->style_no ?? 'N/A'],
            ['Garment Types:', $garmentTypes],
            ['Order Quantity:', $order->order_qty ?? 'N/A'],
            ['Report Date:', $date],
            [], // spacer
            // Table headers (must match collection() keys order)
            [
                'Thread Cutting',
                'QC Check',
                'Button/Rivet',
                'Iron',
                'Hangtag',
                'Poly',
                'Carton',
                'Today Finishing',
                'Total Finishing',
                'Plan To Complete',
                'DPI Inline',
                'FRI Final',
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;

                // Merge and center title block
                foreach (['A1:L1', 'A2:L2', 'A3:L3'] as $range) {
                    $sheet->mergeCells($range);
                }
                foreach (['A1','A2','A3'] as $cell) {
                    $sheet->getStyle($cell)->applyFromArray([
                        'font' => [
                            'bold' => true,
                            'size' => $cell === 'A1' ? 16 : ($cell === 'A3' ? 14 : 12),
                        ],
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        ],
                    ]);
                }

                // Bold labels in meta rows A5..A9
                for ($r = 5; $r <= 9; $r++) {
                    $sheet->getStyle("A{$r}")->getFont()->setBold(true);
                }

                // Table header row index
                $tableHeadRow = 11;
                $lastCol = 'L'; // 12 columns

                // Style table header
                $sheet->getStyle("A{$tableHeadRow}:{$lastCol}{$tableHeadRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);

                // Data row is tableHeadRow + 1
                $dataRow = $tableHeadRow + 1;

                // Borders around header + data
                $sheet->getStyle("A{$tableHeadRow}:{$lastCol}{$dataRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);

                // Autosize columns A..L
                foreach (range('A', $lastCol) as $col) {
                    $sheet->getDelegate()->getColumnDimension($col)->setAutoSize(true);
                }
            }
        ];
    }

    public function title(): string
    {
        return 'Finishing Report';
    }
}
