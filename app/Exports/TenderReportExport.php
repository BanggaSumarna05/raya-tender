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

class TenderReportExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithTitle,
    WithEvents,
    WithColumnWidths
{
    private int   $totalRows = 0;
    private array $stats     = [];

    // Layout constants
    private const ROW_LOGO_TOP    = 1;  // logo spans rows 1-3
    private const ROW_LOGO_BOTTOM = 3;
    private const ROW_DIVIDER     = 4;  // dark divider line
    private const ROW_PERIOD      = 5;
    private const ROW_EXPORT_DATE = 6;
    private const ROW_SPACER1     = 7;
    private const ROW_STAT_LABEL  = 8;
    private const ROW_STAT_VALUE  = 9;
    private const ROW_SPACER2     = 10;
    private const ROW_SECTION_LBL = 11;
    private const ROW_HEADER      = 12;
    private const ROW_DATA_START  = 13;

    // 11 columns: A-K
    private const LAST_COL = 'K';
    private const NUM_COLS  = 11;

    public function __construct(
        private readonly array $filters = [],
        private readonly ReportService $reportService = new ReportService()
    ) {}

    public function collection()
    {
        $this->stats     = $this->reportService->getTenderReport($this->filters);
        $collection      = $this->stats['tenders'];
        $this->totalRows = $collection->count();
        return $collection;
    }

    public function headings(): array
    {
        return ['No.', 'Kode Tender', 'Nama Tender', 'Klien', 'Kategori', 'PIC', 'Status', 'Prioritas', 'Deadline', 'Lokasi', 'Tgl. Dibuat'];
    }

    public function map($t): array
    {
        static $no = 0; $no++;
        return [
            $no,
            $t->code,
            $t->title,
            $t->client?->name        ?? '-',
            $t->category?->name      ?? '-',
            $t->pic?->name           ?? '-',
            $t->status?->label()     ?? (string) $t->status,
            $t->priority?->label()   ?? (string) $t->priority,
            $t->submission_deadline?->format('d/m/Y') ?? '-',
            $t->location             ?? '-',
            $t->created_at?->format('d/m/Y') ?? '-',
        ];
    }

    public function title(): string { return 'Laporan Tender'; }

    public function columnWidths(): array
    {
        return ['A'=>5, 'B'=>17, 'C'=>38, 'D'=>26, 'E'=>20, 'F'=>22, 'G'=>16, 'H'=>14, 'I'=>13, 'J'=>24, 'K'=>13];
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

                // ── Pre-insert rows so headings land on ROW_HEADER ──
                $ws->insertNewRowBefore(1, $hRow - 1);

                // ═══════════════════════════════════════════
                // ROWS 1-3 : LOGO AREA + TITLE
                // ═══════════════════════════════════════════
                $ws->getRowDimension(1)->setRowHeight(18);
                $ws->getRowDimension(2)->setRowHeight(18);
                $ws->getRowDimension(3)->setRowHeight(18);

                // Title in col C, spans rows 1-2
                $ws->mergeCells("C1:{$lc}2");
                $ws->setCellValue('C1', 'LAPORAN TENDER');
                $ws->getStyle('C1')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 18, 'color' => ['rgb' => '101828']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT,
                                    'vertical'   => Alignment::VERTICAL_CENTER],
                ]);

                // Subtitle in col C, row 3
                $ws->mergeCells("C3:{$lc}3");
                $ws->setCellValue('C3', 'PT. RAYA KONSTRUKSI INTERNASIONAL');
                $ws->getStyle('C3')->applyFromArray([
                    'font'      => ['size' => 9, 'color' => ['rgb' => '667085']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT,
                                    'vertical'   => Alignment::VERTICAL_CENTER],
                ]);

                // Logo — columns A-B, rows 1-3 (height ~54px total)
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

                // ═══════════════════════════════════════════
                // ROW 4 : DARK DIVIDER
                // ═══════════════════════════════════════════
                $ws->mergeCells("A4:{$lc}4");
                $ws->getStyle('A4')->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '101828']],
                ]);
                $ws->getRowDimension(4)->setRowHeight(3);

                // ═══════════════════════════════════════════
                // ROWS 5-6 : META INFO
                // ═══════════════════════════════════════════
                $period = !empty($this->filters['start_date'])
                    ? $this->filters['start_date'] . '  –  ' . ($this->filters['end_date'] ?? now()->format('Y-m-d'))
                    : 'Semua periode';

                $metaRows = [
                    5 => ['Periode',        $period],
                    6 => ['Tanggal Export', now()->format('d F Y, H:i')],
                ];
                foreach ($metaRows as $r => [$lbl, $val]) {
                    $ws->mergeCells("A{$r}:B{$r}");
                    $ws->setCellValue("A{$r}", $lbl);
                    $ws->getStyle("A{$r}")->applyFromArray([
                        'font'      => ['bold' => true, 'size' => 9, 'color' => ['rgb' => '344054']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);
                    $ws->mergeCells("C{$r}:{$lc}{$r}");
                    $ws->setCellValue("C{$r}", $val);
                    $ws->getStyle("C{$r}")->applyFromArray([
                        'font'      => ['size' => 9, 'color' => ['rgb' => '344054']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);
                    $ws->getRowDimension($r)->setRowHeight(15);
                }

                // ═══════════════════════════════════════════
                // ROW 7 : SPACER
                // ═══════════════════════════════════════════
                $ws->mergeCells("A7:{$lc}7");
                $ws->getRowDimension(7)->setRowHeight(8);

                // ═══════════════════════════════════════════
                // ROWS 8-9 : STATS BLOCK (spread across all 11 cols)
                // ═══════════════════════════════════════════
                // Each stat spans ~2 cols; 5 stats × 2 = 10, last one gets 3
                $statRanges = [
                    ['A8:B8', 'A9:B9'],
                    ['C8:D8', 'C9:D9'],
                    ['E8:F8', 'E9:F9'],
                    ['G8:H8', 'G9:H9'],
                    ['I8:K8', 'I9:K9'],
                ];
                $statsData = [
                    ['TOTAL',    $this->stats['total'],           'F2F4F7', '344054'],
                    ['AKTIF',    $this->stats['active'],          'EFF8FF', '1570EF'],
                    ['MENANG',   $this->stats['won'],             'ECFDF3', '027A48'],
                    ['KALAH',    $this->stats['lost'],            'FEF3F2', 'B42318'],
                    ['WIN RATE', $this->stats['win_rate'] . '%',  'EEF2FF', '3538CD'],
                ];

                foreach ($statsData as $i => [$label, $value, $bg, $fg]) {
                    [$labelRange, $valueRange] = $statRanges[$i];

                    $ws->mergeCells($labelRange);
                    [$lCell] = explode(':', $labelRange);
                    $ws->setCellValue($lCell, $label);
                    $ws->getStyle($labelRange)->applyFromArray([
                        'font'      => ['bold' => true, 'size' => 8, 'color' => ['rgb' => $fg]],
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'borders'   => ['top'   => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E4E7EC']],
                                        'left'  => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E4E7EC']],
                                        'right' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E4E7EC']]],
                    ]);

                    $ws->mergeCells($valueRange);
                    [$vCell] = explode(':', $valueRange);
                    $ws->setCellValue($vCell, $value);
                    $ws->getStyle($valueRange)->applyFromArray([
                        'font'      => ['bold' => true, 'size' => 20, 'color' => ['rgb' => $fg]],
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'borders'   => ['bottom' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E4E7EC']],
                                        'left'   => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E4E7EC']],
                                        'right'  => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E4E7EC']]],
                    ]);
                }
                $ws->getRowDimension(8)->setRowHeight(14);
                $ws->getRowDimension(9)->setRowHeight(34);

                // ═══════════════════════════════════════════
                // ROW 10 : SPACER
                // ═══════════════════════════════════════════
                $ws->mergeCells("A10:{$lc}10");
                $ws->getRowDimension(10)->setRowHeight(8);

                // ═══════════════════════════════════════════
                // ROW 11 : SECTION LABEL
                // ═══════════════════════════════════════════
                $ws->mergeCells("A11:{$lc}11");
                $ws->setCellValue('A11', 'DAFTAR TENDER');
                $ws->getStyle('A11')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 8, 'color' => ['rgb' => '98A2B3']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $ws->getRowDimension(11)->setRowHeight(12);

                // ═══════════════════════════════════════════
                // ROW 12 : TABLE HEADER
                // ═══════════════════════════════════════════
                $ws->getStyle("A{$hRow}:{$lc}{$hRow}")->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 9, 'color' => ['rgb' => 'FFFFFF']],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1D2939']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER,
                                    'vertical'   => Alignment::VERTICAL_CENTER,
                                    'wrapText'   => false],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '344054']]],
                ]);
                $ws->getRowDimension($hRow)->setRowHeight(22);

                // ═══════════════════════════════════════════
                // ROWS 13+ : DATA
                // ═══════════════════════════════════════════
                if ($this->totalRows > 0) {
                    for ($row = $dStart; $row <= $dEnd; $row++) {
                        $bg = ($row - $dStart) % 2 === 0 ? 'FFFFFF' : 'F9FAFB';
                        $ws->getStyle("A{$row}:{$lc}{$row}")->applyFromArray([
                            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                            'font'      => ['size' => 9],
                            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                            'borders'   => ['bottom' => ['borderStyle' => Border::BORDER_HAIR, 'color' => ['rgb' => 'E4E7EC']]],
                        ]);
                        $ws->getRowDimension($row)->setRowHeight(16);
                    }
                    // Col A (No) — center
                    $ws->getStyle("A{$dStart}:A{$dEnd}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    // Col B (code) — small gray
                    $ws->getStyle("B{$dStart}:B{$dEnd}")->applyFromArray([
                        'font' => ['size' => 8, 'color' => ['rgb' => '667085']],
                    ]);
                    // Col C (title) — bold
                    $ws->getStyle("C{$dStart}:C{$dEnd}")->getFont()->setBold(true);
                    // Date cols — center
                    foreach (['I', 'K'] as $c) {
                        $ws->getStyle("{$c}{$dStart}:{$c}{$dEnd}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    }
                    // Outer border around table
                    $ws->getStyle("A{$hRow}:{$lc}{$dEnd}")->applyFromArray([
                        'borders' => ['outline' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => 'D0D5DD']]],
                    ]);
                }

                // ═══════════════════════════════════════════
                // FOOTER
                // ═══════════════════════════════════════════
                $footerRow = $dEnd + 2;
                $ws->mergeCells("A{$footerRow}:{$lc}{$footerRow}");
                $ws->setCellValue("A{$footerRow}", 'Sumber data: Raya Tender Management System');
                $ws->getStyle("A{$footerRow}")->applyFromArray([
                    'font'      => ['size' => 8, 'italic' => true, 'color' => ['rgb' => 'B0B7C3']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $ws->getRowDimension($footerRow)->setRowHeight(14);

                // ── Freeze & Tab ──
                $ws->freezePane("A{$dStart}");
                $event->sheet->getTabColor()->setRGB('465FFF');
            },
        ];
    }
}
