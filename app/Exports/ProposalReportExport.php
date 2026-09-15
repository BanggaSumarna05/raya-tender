<?php

namespace App\Exports;

use App\Services\ReportService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProposalReportExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithTitle,
    WithEvents,
    WithColumnWidths
{
    private int   $totalRows = 0;
    private array $stats     = [];

    private const ROW_HEADER    = 12;
    private const ROW_DATA_START = 13;
    private const LAST_COL       = 'J';

    public function __construct(
        private readonly array $filters = [],
        private readonly ReportService $reportService = new ReportService()
    ) {}

    public function collection()
    {
        $this->stats     = $this->reportService->getProposalReport($this->filters);
        $collection      = $this->stats['proposals'];
        $collection->load(['tender.client', 'pic']);
        $this->totalRows = $collection->count();
        return $collection;
    }

    public function headings(): array
    {
        return ['No.', 'Kode Proposal', 'Judul Proposal', 'Kode Tender', 'Klien', 'PIC', 'Status', 'Versi', 'Deadline', 'Tgl. Dibuat'];
    }

    public function map($p): array
    {
        static $no = 0; $no++;
        return [
            $no,
            $p->code,
            $p->title,
            $p->tender?->code          ?? '-',
            $p->tender?->client?->name ?? '-',
            $p->pic?->name             ?? '-',
            $p->status?->label()       ?? (string) $p->status,
            'v' . $p->current_version,
            $p->deadline?->format('d/m/Y')   ?? '-',
            $p->created_at?->format('d/m/Y') ?? '-',
        ];
    }

    public function title(): string { return 'Laporan Proposal'; }

    public function columnWidths(): array
    {
        return ['A'=>5, 'B'=>19, 'C'=>40, 'D'=>17, 'E'=>26, 'F'=>22, 'G'=>16, 'H'=>8, 'I'=>13, 'J'=>13];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $ws     = $event->sheet->getDelegate();
                $lc     = self::LAST_COL;
                $hRow   = self::ROW_HEADER;
                $dStart = self::ROW_DATA_START;
                $dEnd   = max($dStart + $this->totalRows - 1, $dStart);

                $ws->insertNewRowBefore(1, $hRow - 1);

                // ── Rows 1-3: Logo + Title ──
                $ws->getRowDimension(1)->setRowHeight(18);
                $ws->getRowDimension(2)->setRowHeight(18);
                $ws->getRowDimension(3)->setRowHeight(18);

                $ws->mergeCells("C1:{$lc}2");
                $ws->setCellValue('C1', 'LAPORAN PROPOSAL');
                $ws->getStyle('C1')->applyFromArray([
                    'font'      => ['name' => 'Eina01', 'bold' => true, 'size' => 18, 'color' => ['rgb' => '101828']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $ws->mergeCells("C3:{$lc}3");
                $ws->setCellValue('C3', 'PT. RAYA KONSTRUKSI INTERNASIONAL');
                $ws->getStyle('C3')->applyFromArray([
                    'font'      => ['name' => 'Eina01', 'size' => 9, 'color' => ['rgb' => '667085']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $logoPath = public_path('img/Raya - Logo.png');
                if (file_exists($logoPath)) {
                    $drawing = new Drawing();
                    $drawing->setName('Logo');
                    $drawing->setPath($logoPath);
                    $drawing->setHeight(48);
                    $drawing->setCoordinates('A1');
                    $drawing->setOffsetX(6);
                    $drawing->setOffsetY(6);
                    $drawing->setWorksheet($ws);
                }

                // ── Row 4: Divider ──
                $ws->mergeCells("A4:{$lc}4");
                $ws->getStyle('A4')->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '101828']],
                ]);
                $ws->getRowDimension(4)->setRowHeight(3);

                // ── Rows 5-6: Meta info ──
                $period = !empty($this->filters['start_date'])
                    ? $this->filters['start_date'] . '  –  ' . ($this->filters['end_date'] ?? now()->format('Y-m-d'))
                    : 'Semua periode';

                foreach ([5 => ['Periode', $period], 6 => ['Tanggal Export', now()->format('d F Y, H:i')]] as $r => [$lbl, $val]) {
                    $ws->mergeCells("A{$r}:B{$r}");
                    $ws->setCellValue("A{$r}", $lbl);
                    $ws->getStyle("A{$r}")->applyFromArray(['font' => ['name' => 'Eina01', 'bold' => true, 'size' => 9, 'color' => ['rgb' => '344054']], 'alignment' => ['vertical' => Alignment::VERTICAL_CENTER]]);
                    $ws->mergeCells("C{$r}:{$lc}{$r}");
                    $ws->setCellValue("C{$r}", $val);
                    $ws->getStyle("C{$r}")->applyFromArray(['font' => ['name' => 'Eina01', 'size' => 9, 'color' => ['rgb' => '344054']], 'alignment' => ['vertical' => Alignment::VERTICAL_CENTER]]);
                    $ws->getRowDimension($r)->setRowHeight(15);
                }

                // ── Row 7: Spacer ──
                $ws->mergeCells("A7:{$lc}7");
                $ws->getRowDimension(7)->setRowHeight(8);

                // ── Rows 8-9: Stats (spread across J cols — 2 cols each, last 2 cols) ──
                $statRanges = [
                    ['A8:B8', 'A9:B9'],
                    ['C8:D8', 'C9:D9'],
                    ['E8:F8', 'E9:F9'],
                    ['G8:H8', 'G9:H9'],
                    ['I8:J8', 'I9:J9'],
                ];
                $statsData = [
                    ['TOTAL',           $this->stats['total'],           'F2F4F7', '344054'],
                    ['DRAFT',           $this->stats['draft'],           'F2F4F7', '667085'],
                    ['INTERNAL REVIEW', $this->stats['internal_review'], 'FFFAEB', 'B54708'],
                    ['FINAL',           $this->stats['final'],           'EFF8FF', '1570EF'],
                    ['SUBMITTED',       $this->stats['submitted'],       'ECFDF3', '027A48'],
                ];

                foreach ($statsData as $i => [$label, $value, $bg, $fg]) {
                    [$lr, $vr] = $statRanges[$i];
                    [$lc2] = explode(':', $lr);
                    [$vc]  = explode(':', $vr);

                    $ws->mergeCells($lr);
                    $ws->setCellValue($lc2, $label);
                    $ws->getStyle($lr)->applyFromArray([
                        'font'      => ['name' => 'Eina01', 'bold' => true, 'size' => 8, 'color' => ['rgb' => $fg]],
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'borders'   => ['top' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E4E7EC']], 'left' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E4E7EC']], 'right' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E4E7EC']]],
                    ]);
                    $ws->mergeCells($vr);
                    $ws->setCellValue($vc, $value);
                    $ws->getStyle($vr)->applyFromArray([
                        'font'      => ['name' => 'Eina01', 'bold' => true, 'size' => 20, 'color' => ['rgb' => $fg]],
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'borders'   => ['bottom' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E4E7EC']], 'left' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E4E7EC']], 'right' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E4E7EC']]],
                    ]);
                }
                $ws->getRowDimension(8)->setRowHeight(14);
                $ws->getRowDimension(9)->setRowHeight(34);

                // ── Row 10: Spacer ──
                $ws->mergeCells("A10:{$lc}10");
                $ws->getRowDimension(10)->setRowHeight(8);

                // ── Row 11: Section label ──
                $ws->mergeCells("A11:{$lc}11");
                $ws->setCellValue('A11', 'DAFTAR PROPOSAL');
                $ws->getStyle('A11')->applyFromArray([
                    'font'      => ['name' => 'Eina01', 'bold' => true, 'size' => 8, 'color' => ['rgb' => '98A2B3']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $ws->getRowDimension(11)->setRowHeight(12);

                // ── Row 12: Table header ──
                $lc = self::LAST_COL;
                $ws->getStyle("A{$hRow}:{$lc}{$hRow}")->applyFromArray([
                    'font'      => ['name' => 'Eina01', 'bold' => true, 'size' => 9, 'color' => ['rgb' => 'FFFFFF']],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1D2939']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '344054']]],
                ]);
                $ws->getRowDimension($hRow)->setRowHeight(22);

                // ── Data rows ──
                if ($this->totalRows > 0) {
                    for ($row = $dStart; $row <= $dEnd; $row++) {
                        $bg = ($row - $dStart) % 2 === 0 ? 'FFFFFF' : 'F9FAFB';
                        $ws->getStyle("A{$row}:{$lc}{$row}")->applyFromArray([
                            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                            'font'      => ['name' => 'Eina01', 'size' => 9],
                            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                            'borders'   => ['bottom' => ['borderStyle' => Border::BORDER_HAIR, 'color' => ['rgb' => 'E4E7EC']]],
                        ]);
                        $ws->getRowDimension($row)->setRowHeight(16);
                    }
                    $ws->getStyle("A{$dStart}:A{$dEnd}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $ws->getStyle("B{$dStart}:B{$dEnd}")->applyFromArray(['font' => ['name' => 'Eina01', 'size' => 8, 'color' => ['rgb' => '667085']]]);
                    $ws->getStyle("C{$dStart}:C{$dEnd}")->getFont()->setName('Eina01')->setBold(true);
                    $ws->getStyle("D{$dStart}:D{$dEnd}")->applyFromArray(['font' => ['name' => 'Eina01', 'size' => 8, 'color' => ['rgb' => '667085']]]);
                    foreach (['H', 'I', 'J'] as $c) {
                        $ws->getStyle("{$c}{$dStart}:{$c}{$dEnd}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    }
                    $ws->getStyle("A{$hRow}:{$lc}{$dEnd}")->applyFromArray([
                        'borders' => ['outline' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => 'D0D5DD']]],
                    ]);
                }

                // ── Footer ──
                $footerRow = $dEnd + 2;
                $ws->mergeCells("A{$footerRow}:{$lc}{$footerRow}");
                $ws->setCellValue("A{$footerRow}", 'Sumber data: Raya Tender Management System');
                $ws->getStyle("A{$footerRow}")->applyFromArray([
                    'font'      => ['name' => 'Eina01', 'size' => 8, 'italic' => true, 'color' => ['rgb' => 'B0B7C3']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $ws->getRowDimension($footerRow)->setRowHeight(14);

                $ws->freezePane("A{$dStart}");
                $event->sheet->getTabColor()->setRGB('039855');
            },
        ];
    }
}
