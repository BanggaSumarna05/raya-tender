<?php

namespace App\Exports;

use App\Models\Tender;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Raw / data export — semua kolom tersedia untuk analisis lanjutan di Excel/Pivot.
 * Tidak ada finansial (estimated_value dikecualikan by policy).
 */
class TenderRawExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithStyles,
    WithTitle,
    WithEvents
{
    private int $totalRows = 0;

    public function __construct(private readonly array $filters = []) {}

    public function collection()
    {
        $collection = Tender::with(['client', 'category', 'pic'])
            ->when(isset($this->filters['start_date']), fn($q) => $q->whereDate('created_at', '>=', $this->filters['start_date']))
            ->when(isset($this->filters['end_date']),   fn($q) => $q->whereDate('created_at', '<=', $this->filters['end_date']))
            ->when(isset($this->filters['status']) && $this->filters['status'],      fn($q) => $q->where('status', $this->filters['status']))
            ->when(isset($this->filters['client_id']) && $this->filters['client_id'], fn($q) => $q->where('client_id', $this->filters['client_id']))
            ->when(isset($this->filters['category_id']) && $this->filters['category_id'], fn($q) => $q->where('category_id', $this->filters['category_id']))
            ->when(isset($this->filters['pic_id']) && $this->filters['pic_id'],       fn($q) => $q->where('pic_id', $this->filters['pic_id']))
            ->orderBy('created_at', 'desc')
            ->get();

        $this->totalRows = $collection->count();
        return $collection;
    }

    public function headings(): array
    {
        return [
            'id', 'code', 'title', 'client_name', 'client_code',
            'category', 'pic_name', 'status', 'status_label',
            'priority', 'priority_label', 'location',
            'submission_deadline', 'result_date',
            'created_at', 'updated_at',
        ];
    }

    public function map($t): array
    {
        return [
            $t->id,
            $t->code,
            $t->title,
            $t->client?->name ?? '',
            $t->client?->code ?? '',
            $t->category?->name ?? '',
            $t->pic?->name ?? '',
            $t->status?->value ?? '',
            $t->status?->label() ?? '',
            $t->priority?->value ?? '',
            $t->priority?->label() ?? '',
            $t->location ?? '',
            $t->submission_deadline?->format('Y-m-d') ?? '',
            $t->result_date?->format('Y-m-d') ?? '',
            $t->created_at?->format('Y-m-d H:i:s') ?? '',
            $t->updated_at?->format('Y-m-d H:i:s') ?? '',
        ];
    }

    public function title(): string { return 'Data Tender'; }

    public function styles(Worksheet $sheet): array { return []; }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $ws      = $event->sheet->getDelegate();
                $lastCol = 'P';
                $hRow    = 1;
                $dStart  = 2;
                $dEnd    = max($dStart + $this->totalRows - 1, $dStart);

                // ── Header row ──
                $ws->getStyle("A{$hRow}:{$lastCol}{$hRow}")->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 9, 'color' => ['rgb' => 'FFFFFF']],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '344054']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '475467']]],
                ]);
                $ws->getRowDimension($hRow)->setRowHeight(18);

                // ── Data rows — minimal, clean for pivot ──
                if ($this->totalRows > 0) {
                    $ws->getStyle("A{$dStart}:{$lastCol}{$dEnd}")->applyFromArray([
                        'font'      => ['size' => 9],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                        'borders'   => ['horizontal' => ['borderStyle' => Border::BORDER_HAIR, 'color' => ['rgb' => 'EAECF0']]],
                    ]);
                    // Freeze, autofilter
                    $ws->freezePane('A2');
                    $ws->setAutoFilter("A{$hRow}:{$lastCol}{$hRow}");
                    // id col narrow
                    $ws->getColumnDimension('A')->setWidth(8);
                }

                // ── Footer note ──
                $note = $dEnd + 2;
                $ws->mergeCells("A{$note}:{$lastCol}{$note}");
                $ws->setCellValue("A{$note}", 'Sumber data: Raya Tender Management System  ·  Raw data export — gunakan fitur pivot/filter Excel untuk analisis lanjutan.');
                $ws->getStyle("A{$note}")->applyFromArray([
                    'font' => ['size' => 8, 'italic' => true, 'color' => ['rgb' => '98A2B3']],
                ]);

                $event->sheet->getTabColor()->setRGB('344054');
            },
        ];
    }
}
