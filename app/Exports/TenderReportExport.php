<?php

namespace App\Exports;

use App\Http\Controllers\ReportController;
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

class TenderReportExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithTitle,
    WithEvents,
    WithColumnWidths
{
    private int   $totalRows  = 0;
    private array $stats      = [];
    private array $colKeys    = [];   // kolom yang dipakai (termasuk 'no' di posisi 0)
    private int   $numCols    = 0;
    private string $lastCol   = 'A';

    // Layout constants
    private const ROW_LOGO_TOP    = 1;
    private const ROW_LOGO_BOTTOM = 3;
    private const ROW_DIVIDER     = 4;
    private const ROW_PERIOD      = 5;
    private const ROW_EXPORT_DATE = 6;
    private const ROW_SPACER1     = 7;
    private const ROW_STAT_LABEL  = 8;
    private const ROW_STAT_VALUE  = 9;
    private const ROW_SPACER2     = 10;
    private const ROW_SECTION_LBL = 11;
    private const ROW_HEADER      = 12;
    private const ROW_DATA_START  = 13;

    /** Lebar kolom default per key */
    private const COL_WIDTHS = [
        'no'                  => 5,
        'code'                => 17,
        'title'               => 38,
        'client'              => 26,
        'category'            => 20,
        'status'              => 16,
        'priority'            => 14,
        'pic'                 => 22,
        'backup_pic'          => 22,
        'location'            => 24,
        'source'              => 18,
        'received_date'       => 14,
        'submission_deadline' => 16,
        'project_start_date'  => 16,
        'project_end_date'    => 16,
        'estimated_value'     => 20,
        'description'         => 40,
        'notes'               => 32,
        'created_at'          => 13,
    ];

    /** Label heading per key */
    private const COL_LABELS = [
        'no'                  => 'No.',
        'code'                => 'Kode Tender',
        'title'               => 'Nama Tender',
        'client'              => 'Klien',
        'category'            => 'Kategori',
        'status'              => 'Status',
        'priority'            => 'Prioritas',
        'pic'                 => 'PIC',
        'backup_pic'          => 'Backup PIC',
        'location'            => 'Lokasi',
        'source'              => 'Sumber',
        'received_date'       => 'Tgl. Diterima',
        'submission_deadline' => 'Deadline',
        'project_start_date'  => 'Tgl. Mulai',
        'project_end_date'    => 'Tgl. Selesai',
        'estimated_value'     => 'Nilai Estimasi',
        'description'         => 'Deskripsi',
        'notes'               => 'Catatan',
        'created_at'          => 'Tgl. Dibuat',
    ];

    public function __construct(
        private readonly array $filters = [],
        private readonly array $selectedColumns = [],
        private readonly array $exportStatuses = [],   // [] = semua, array berisi = filter spesifik
        private readonly ReportService $reportService = new ReportService()
    ) {
        // Selalu awali dengan nomor urut, lalu kolom yang dipilih
        $cols = empty($this->selectedColumns)
            ? ReportController::defaultTenderColumns()
            : $this->selectedColumns;

        $this->colKeys  = array_merge(['no'], $cols);
        $this->numCols  = count($this->colKeys);
        $this->lastCol  = $this->colIndexToLetter($this->numCols - 1);
    }

    public function collection()
    {
        // Terapkan filter status dari modal jika ada
        $filters = $this->filters;
        if (!empty($this->exportStatuses)) {
            $filters['statuses'] = $this->exportStatuses;
        }

        $this->stats     = $this->reportService->getTenderReport($filters);
        $collection      = $this->stats['tenders'];
        $this->totalRows = $collection->count();
        return $collection;
    }

    public function headings(): array
    {
        return array_map(fn($k) => self::COL_LABELS[$k] ?? $k, $this->colKeys);
    }

    public function map($t): array
    {
        static $no = 0;
        $no++;

        $row = [];
        foreach ($this->colKeys as $key) {
            $row[] = match ($key) {
                'no'                  => $no,
                'code'                => $t->code,
                'title'               => $t->title,
                'client'              => $t->client?->name ?? '-',
                'category'            => $t->category?->name ?? '-',
                'status'              => $t->status?->label() ?? (string) $t->status,
                'priority'            => $t->priority?->label() ?? (string) $t->priority,
                'pic'                 => $t->pic?->name ?? '-',
                'backup_pic'          => $t->backupPic?->name ?? '-',
                'location'            => $t->location ?? '-',
                'source'              => $t->source ?? '-',
                'received_date'       => $t->received_date?->format('d/m/Y') ?? '-',
                'submission_deadline' => $t->submission_deadline?->format('d/m/Y') ?? '-',
                'project_start_date'  => $t->project_start_date?->format('d/m/Y') ?? '-',
                'project_end_date'    => $t->project_end_date?->format('d/m/Y') ?? '-',
                'estimated_value'     => $t->estimated_value ?? '-',
                'description'         => $t->description ?? '-',
                'notes'               => $t->notes ?? '-',
                'created_at'          => $t->created_at?->format('d/m/Y') ?? '-',
                default               => '-',
            };
        }

        return $row;
    }

    public function title(): string { return 'Laporan Tender'; }

    public function columnWidths(): array
    {
        $widths = [];
        foreach ($this->colKeys as $i => $key) {
            $letter = $this->colIndexToLetter($i);
            $widths[$letter] = self::COL_WIDTHS[$key] ?? 18;
        }
        return $widths;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $ws     = $event->sheet->getDelegate();
                $lc     = $this->lastCol;
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

                $titleCol = $this->numCols >= 3 ? 'C' : 'B';

                $ws->mergeCells("{$titleCol}1:{$lc}2");
                $ws->setCellValue("{$titleCol}1", 'LAPORAN TENDER');
                $ws->getStyle("{$titleCol}1")->applyFromArray([
                    'font'      => ['name' => 'Eina01', 'bold' => true, 'size' => 18, 'color' => ['rgb' => '101828']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT,
                                    'vertical'   => Alignment::VERTICAL_CENTER],
                ]);

                $ws->mergeCells("{$titleCol}3:{$lc}3");
                $ws->setCellValue("{$titleCol}3", 'PT. RAYA KONSTRUKSI INTERNASIONAL');
                $ws->getStyle("{$titleCol}3")->applyFromArray([
                    'font'      => ['name' => 'Eina01', 'size' => 9, 'color' => ['rgb' => '667085']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT,
                                    'vertical'   => Alignment::VERTICAL_CENTER],
                ]);

                // Logo — columns A-B, rows 1-3
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
                        'font'      => ['name' => 'Eina01', 'bold' => true, 'size' => 9, 'color' => ['rgb' => '344054']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);
                    $ws->mergeCells("C{$r}:{$lc}{$r}");
                    $ws->setCellValue("C{$r}", $val);
                    $ws->getStyle("C{$r}")->applyFromArray([
                        'font'      => ['name' => 'Eina01', 'size' => 9, 'color' => ['rgb' => '344054']],
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
                // ROWS 8-9 : STATS BLOCK (spread across all cols)
                // ═══════════════════════════════════════════
                $statsData = [
                    ['TOTAL',    $this->stats['total'],              'F2F4F7', '344054'],
                    ['AKTIF',    $this->stats['active'],             'EFF8FF', '1570EF'],
                    ['MENANG',   $this->stats['won'],                'ECFDF3', '027A48'],
                    ['KALAH',    $this->stats['lost'],               'FEF3F2', 'B42318'],
                    ['WIN RATE', $this->stats['win_rate'] . '%',     'EEF2FF', '3538CD'],
                ];

                $statRanges = $this->buildStatRanges($this->numCols);

                foreach ($statsData as $i => [$label, $value, $bg, $fg]) {
                    if (!isset($statRanges[$i])) continue;
                    [$labelRange, $valueRange] = $statRanges[$i];

                    $ws->mergeCells($labelRange);
                    [$lCell] = explode(':', $labelRange);
                    $ws->setCellValue($lCell, $label);
                    $ws->getStyle($labelRange)->applyFromArray([
                        'font'      => ['name' => 'Eina01', 'bold' => true, 'size' => 8, 'color' => ['rgb' => $fg]],
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
                        'font'      => ['name' => 'Eina01', 'bold' => true, 'size' => 20, 'color' => ['rgb' => $fg]],
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
                    'font'      => ['name' => 'Eina01', 'bold' => true, 'size' => 8, 'color' => ['rgb' => '98A2B3']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $ws->getRowDimension(11)->setRowHeight(12);

                // ═══════════════════════════════════════════
                // ROW 12 : TABLE HEADER
                // ═══════════════════════════════════════════
                $ws->getStyle("A{$hRow}:{$lc}{$hRow}")->applyFromArray([
                    'font'      => ['name' => 'Eina01', 'bold' => true, 'size' => 9, 'color' => ['rgb' => 'FFFFFF']],
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
                            'font'      => ['name' => 'Eina01', 'size' => 9],
                            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                            'borders'   => ['bottom' => ['borderStyle' => Border::BORDER_HAIR, 'color' => ['rgb' => 'E4E7EC']]],
                        ]);
                        $ws->getRowDimension($row)->setRowHeight(16);
                    }

                    // Col A (No) — center
                    $ws->getStyle("A{$dStart}:A{$dEnd}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    // Styling khusus per kolom
                    foreach ($this->colKeys as $i => $key) {
                        $letter = $this->colIndexToLetter($i);
                        switch ($key) {
                            case 'code':
                                $ws->getStyle("{$letter}{$dStart}:{$letter}{$dEnd}")->applyFromArray([
                                    'font' => ['name' => 'Eina01', 'size' => 8, 'color' => ['rgb' => '667085']],
                                ]);
                                break;
                            case 'title':
                                $ws->getStyle("{$letter}{$dStart}:{$letter}{$dEnd}")->getFont()
                                   ->setName('Eina01')->setBold(true);
                                break;
                            case 'received_date':
                            case 'submission_deadline':
                            case 'project_start_date':
                            case 'project_end_date':
                            case 'created_at':
                                $ws->getStyle("{$letter}{$dStart}:{$letter}{$dEnd}")
                                   ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                                break;
                            case 'estimated_value':
                                $ws->getStyle("{$letter}{$dStart}:{$letter}{$dEnd}")
                                   ->getNumberFormat()->setFormatCode('#,##0');
                                $ws->getStyle("{$letter}{$dStart}:{$letter}{$dEnd}")
                                   ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                                break;
                        }
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
                    'font'      => ['name' => 'Eina01', 'size' => 8, 'italic' => true, 'color' => ['rgb' => 'B0B7C3']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $ws->getRowDimension($footerRow)->setRowHeight(14);

                // ── Freeze & Tab ──
                $ws->freezePane("A{$dStart}");
                $event->sheet->getTabColor()->setRGB('465FFF');
            },
        ];
    }

    // ──────────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────────

    /**
     * Konversi indeks kolom (0-based) ke huruf Excel (A, B, ..., Z, AA, ...).
     */
    private function colIndexToLetter(int $index): string
    {
        $letter = '';
        $index++;  // 1-based
        while ($index > 0) {
            $mod    = ($index - 1) % 26;
            $letter = chr(65 + $mod) . $letter;
            $index  = (int)(($index - $mod) / 26);
        }
        return $letter;
    }

    /**
     * Bangun range cells untuk 5 stat boxes, tersebar merata di $totalCols kolom.
     * Setiap stat mendapat 2 baris (label row 8, value row 9).
     * Returns array of [labelRange, valueRange].
     */
    private function buildStatRanges(int $totalCols): array
    {
        $numStats   = 5;
        $base       = (int)floor($totalCols / $numStats);
        $remainder  = $totalCols % $numStats;

        $ranges = [];
        $colIdx = 0;
        for ($i = 0; $i < $numStats; $i++) {
            $span  = $base + ($i < $remainder ? 1 : 0);
            $start = $this->colIndexToLetter($colIdx);
            $end   = $this->colIndexToLetter($colIdx + $span - 1);
            $ranges[] = ["{$start}8:{$end}8", "{$start}9:{$end}9"];
            $colIdx += $span;
        }

        return $ranges;
    }
}
