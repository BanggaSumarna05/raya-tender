<?php

namespace App\Exports;

use App\Models\Proposal;
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
 * Raw / data export — semua kolom untuk analisis pivot/filter di Excel.
 * Data finansial (bid_value) tidak disertakan by policy.
 */
class ProposalRawExport implements
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
        $collection = Proposal::with(['tender.client', 'pic'])
            ->when(isset($this->filters['start_date']), fn($q) => $q->whereDate('created_at', '>=', $this->filters['start_date']))
            ->when(isset($this->filters['end_date']),   fn($q) => $q->whereDate('created_at', '<=', $this->filters['end_date']))
            ->when(isset($this->filters['status']) && $this->filters['status'], fn($q) => $q->where('status', $this->filters['status']))
            ->when(isset($this->filters['pic_id']) && $this->filters['pic_id'], fn($q) => $q->where('pic_id', $this->filters['pic_id']))
            ->orderBy('created_at', 'desc')
            ->get();

        $this->totalRows = $collection->count();
        return $collection;
    }

    public function headings(): array
    {
        return [
            'id', 'code', 'title',
            'tender_code', 'tender_title',
            'client_name', 'client_code',
            'pic_name',
            'status', 'status_label',
            'current_version',
            'deadline', 'submitted_at',
            'created_at', 'updated_at',
        ];
    }

    public function map($p): array
    {
        return [
            $p->id,
            $p->code,
            $p->title,
            $p->tender?->code            ?? '',
            $p->tender?->title           ?? '',
            $p->tender?->client?->name   ?? '',
            $p->tender?->client?->code   ?? '',
            $p->pic?->name               ?? '',
            $p->status?->value           ?? '',
            $p->status?->label()         ?? '',
            $p->current_version,
            $p->deadline?->format('Y-m-d')     ?? '',
            $p->submitted_at?->format('Y-m-d H:i:s') ?? '',
            $p->created_at?->format('Y-m-d H:i:s') ?? '',
            $p->updated_at?->format('Y-m-d H:i:s') ?? '',
        ];
    }

    public function title(): string { return 'Data Proposal'; }

    public function styles(Worksheet $sheet): array { return []; }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $ws      = $event->sheet->getDelegate();
                $lastCol = 'O';
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

                // ── Data rows ──
                if ($this->totalRows > 0) {
                    $ws->getStyle("A{$dStart}:{$lastCol}{$dEnd}")->applyFromArray([
                        'font'      => ['size' => 9],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                        'borders'   => ['horizontal' => ['borderStyle' => Border::BORDER_HAIR, 'color' => ['rgb' => 'EAECF0']]],
                    ]);
                    $ws->freezePane('A2');
                    $ws->setAutoFilter("A{$hRow}:{$lastCol}{$hRow}");
                    $ws->getColumnDimension('A')->setWidth(8);
                }

                // ── Footer note ──
                $note = $dEnd + 2;
                $ws->mergeCells("A{$note}:{$lastCol}{$note}");
                $ws->setCellValue("A{$note}", 'Sumber data: Raya Tender Management System  ·  Raw data export — gunakan fitur pivot/filter Excel untuk analisis lanjutan.');
                $ws->getStyle("A{$note}")->applyFromArray([
                    'font' => ['size' => 8, 'italic' => true, 'color' => ['rgb' => '98A2B3']],
                ]);

                $event->sheet->getTabColor()->setRGB('667085');
            },
        ];
    }
}
