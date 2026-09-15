<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Laporan Tender</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }

  @font-face {
    font-family: 'Eina01';
    src: url('data:font/opentype;base64,{{ base64_encode(file_get_contents(public_path('Eina-Font/Eina-Font/OTF/Eina-01-Regular.otf'))) }}') format('opentype');
    font-weight: 400; font-style: normal;
  }
  @font-face {
    font-family: 'Eina01';
    src: url('data:font/opentype;base64,{{ base64_encode(file_get_contents(public_path('Eina-Font/Eina-Font/OTF/Eina-01-Regular-Italic.otf'))) }}') format('opentype');
    font-weight: 400; font-style: italic;
  }
  @font-face {
    font-family: 'Eina01';
    src: url('data:font/opentype;base64,{{ base64_encode(file_get_contents(public_path('Eina-Font/Eina-Font/OTF/Eina-01-Semi-Bold.otf'))) }}') format('opentype');
    font-weight: 600; font-style: normal;
  }
  @font-face {
    font-family: 'Eina01';
    src: url('data:font/opentype;base64,{{ base64_encode(file_get_contents(public_path('Eina-Font/Eina-Font/OTF/Eina-01-Semibold-Italic.otf'))) }}') format('opentype');
    font-weight: 600; font-style: italic;
  }
  @font-face {
    font-family: 'Eina01';
    src: url('data:font/opentype;base64,{{ base64_encode(file_get_contents(public_path('Eina-Font/Eina-Font/OTF/Eina-01-Bold.otf'))) }}') format('opentype');
    font-weight: 700; font-style: normal;
  }
  @font-face {
    font-family: 'Eina01';
    src: url('data:font/opentype;base64,{{ base64_encode(file_get_contents(public_path('Eina-Font/Eina-Font/OTF/Eina-01-Bold-Italic.otf'))) }}') format('opentype');
    font-weight: 700; font-style: italic;
  }

  body {
    font-family: 'Eina01', sans-serif;
    font-size: 10px;
    color: #1d2939;
    background: #ffffff;
    padding: 36px 40px 28px;
  }

  /* ── HEADER ── */
  .header {
    display: table;
    width: 100%;
    border-bottom: 2px solid #101828;
    padding-bottom: 14px;
    margin-bottom: 22px;
  }
  .header-left  { display: table-cell; vertical-align: middle; width: 160px; }
  .header-left img { height: 34px; width: auto; }
  .header-right { display: table-cell; vertical-align: middle; text-align: right; }
  .doc-title    { font-size: 16px; font-weight: 700; color: #101828; letter-spacing: -.01em; }
  .doc-sub      { font-size: 8.5px; color: #667085; margin-top: 4px; }
  .doc-sub span { margin: 0 4px; color: #d0d5dd; }

  /* ── STATS ── */
  .stats { display: table; width: 100%; border-collapse: separate; border-spacing: 7px 0; margin: 0 -7px 22px; }
  .stats-cell {
    display: table-cell; border: 1px solid #e4e7ec;
    border-radius: 8px; padding: 10px 6px; text-align: center; width: 20%;
  }
  .stat-val { font-size: 20px; font-weight: 700; color: #101828; line-height: 1.1; }
  .stat-lbl { font-size: 7.5px; font-weight: 600; color: #98a2b3; text-transform: uppercase; letter-spacing: .06em; margin-top: 3px; }
  .c-blue  .stat-val { color: #465fff; }
  .c-green .stat-val { color: #039855; }
  .c-red   .stat-val { color: #d92d20; }
  .c-navy  .stat-val { color: #262e89; }

  /* ── CHARTS ROW ── */
  .charts-row { display: table; width: 100%; margin-bottom: 22px; border-spacing: 10px 0; }
  .chart-box  {
    display: table-cell; border: 1px solid #e4e7ec;
    border-radius: 8px; padding: 12px 14px; vertical-align: top;
  }
  .chart-box.wide  { width: 62%; }
  .chart-box.narrow { width: 38%; }
  .chart-title {
    font-size: 8.5px; font-weight: 700; text-transform: uppercase;
    letter-spacing: .08em; color: #98a2b3; margin-bottom: 10px;
  }

  /* ── BAR CHART ── */
  .bar-row { display: table; width: 100%; margin-bottom: 5px; }
  .bar-label { display: table-cell; width: 100px; font-size: 8.5px; color: #344054; vertical-align: middle; padding-right: 6px; white-space: nowrap; overflow: hidden; }
  .bar-track { display: table-cell; vertical-align: middle; }
  .bar-bg    { background: #f2f4f7; border-radius: 3px; height: 10px; width: 100%; position: relative; }
  .bar-fill  { background: #465fff; border-radius: 3px; height: 10px; }
  .bar-val   { display: table-cell; width: 28px; font-size: 8px; font-weight: 700; color: #344054; vertical-align: middle; padding-left: 5px; text-align: right; }

  /* ── DONUT / LEGEND ── */
  .donut-wrap { text-align: center; margin-bottom: 8px; }
  .legend-item { display: table; width: 100%; margin-bottom: 4px; }
  .legend-dot  { display: table-cell; width: 18px; vertical-align: middle; }
  .legend-dot span { display: inline-block; width: 8px; height: 8px; border-radius: 50%; }
  .legend-name { display: table-cell; font-size: 8.5px; color: #344054; vertical-align: middle; }
  .legend-cnt  { display: table-cell; font-size: 8.5px; font-weight: 700; color: #101828; text-align: right; vertical-align: middle; }

  /* ── SECTION LABEL ── */
  .section-label {
    font-size: 8.5px; font-weight: 700; text-transform: uppercase;
    letter-spacing: .09em; color: #98a2b3; margin-bottom: 7px;
  }

  /* ── TABLE ── */
  table.data { width: 100%; border-collapse: collapse; }
  table.data thead tr { background: #101828; }
  table.data th {
    padding: 8px 9px; text-align: left; font-size: 7.5px; font-weight: 700;
    color: #fff; text-transform: uppercase; letter-spacing: .05em; white-space: nowrap;
  }
  table.data th:first-child { border-radius: 4px 0 0 0; }
  table.data th:last-child  { border-radius: 0 4px 0 0; }
  table.data td {
    padding: 6px 9px; border-bottom: 1px solid #eaecf0;
    vertical-align: top; font-size: 9px; color: #344054;
  }
  table.data tbody tr:nth-child(even) td { background: #f9fafb; }
  .td-no    { text-align: center; color: #98a2b3; font-size: 8px; width: 28px; }
  .td-code  { font-family: 'Courier New', monospace; font-size: 8px; color: #667085; white-space: nowrap; }
  .td-title { font-weight: 600; color: #101828; }
  .td-muted { color: #98a2b3; font-size: 8.5px; }
  .td-date  { text-align: center; white-space: nowrap; color: #667085; }
  .td-num   { text-align: right; font-variant-numeric: tabular-nums; }
  .td-desc  { max-width: 180px; overflow: hidden; }

  /* ── BADGES ── */
  .badge {
    display: inline-block; padding: 2px 7px; border-radius: 20px;
    font-size: 7px; font-weight: 700; text-transform: uppercase; letter-spacing: .03em; white-space: nowrap;
  }
  .s-draft        { background:#f2f4f7; color:#667085; }
  .s-identified   { background:#eff8ff; color:#1570ef; }
  .s-qualification{ background:#f0f9ff; color:#026aa2; }
  .s-preparation  { background:#fff4ed; color:#c4320a; }
  .s-submitted    { background:#ecfdf3; color:#027a48; }
  .s-evaluation   { background:#fdf4ff; color:#6941c6; }
  .s-clarification{ background:#fffaeb; color:#b54708; }
  .s-negotiation  { background:#f0fdf4; color:#166534; }
  .s-won          { background:#d1fadf; color:#05603a; }
  .s-lost         { background:#fef3f2; color:#b42318; }
  .s-cancelled    { background:#f9f5ff; color:#6941c6; }
  .s-completed    { background:#f0fdf4; color:#166534; }
  .s-default      { background:#f2f4f7; color:#667085; }

  .p-low    { background:#f9fafb; color:#667085; }
  .p-medium { background:#fffaeb; color:#b54708; }
  .p-high   { background:#fff4ed; color:#c4320a; }
  .p-urgent { background:#fef3f2; color:#b42318; }

  .empty-row td { text-align:center; color:#98a2b3; padding:24px 0; font-size:10px; }

  /* ── FOOTER ── */
  .footer { margin-top: 24px; padding-top: 10px; border-top: 1px solid #eaecf0; display: table; width: 100%; }
  .footer-left, .footer-right { display: table-cell; font-size: 8px; color: #98a2b3; vertical-align: middle; }
  .footer-right { text-align: right; font-style: italic; }
</style>
</head>
<body>

@php
  /* ── Kolom yang dipilih ── */
  // $columns  = array of column keys, e.g. ['code','title','client',...]
  // $columnLabels = array of key => label
  // $exportStatuses = array of status values yang dipilih ([] = semua)

  $tenders = $data['tenders'];

  /* ── Label mapping ── */
  $defaultLabels = [
    'code'                => 'Kode',
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
  $labels = array_merge($defaultLabels, $columnLabels ?? []);

  /* ── Kolom yang aktif ── */
  $activeColumns = $columns ?? ['code','title','client','category','pic','status','priority','submission_deadline','location','created_at'];

  /* ── Status distribution ── */
  $statusColors = [
    'draft'          => '#94a3b8',
    'identified'     => '#60a5fa',
    'qualification'  => '#38bdf8',
    'preparation'    => '#fb923c',
    'submitted'      => '#34d399',
    'evaluation'     => '#a78bfa',
    'clarification'  => '#fbbf24',
    'negotiation'    => '#4ade80',
    'won'            => '#16a34a',
    'lost'           => '#ef4444',
    'cancelled'      => '#c084fc',
    'completed'      => '#059669',
  ];
  $statusLabels = [
    'draft'=>'Draft','identified'=>'Identified','qualification'=>'Qualification',
    'preparation'=>'Preparation','submitted'=>'Submitted','evaluation'=>'Evaluation',
    'clarification'=>'Clarification','negotiation'=>'Negotiation',
    'won'=>'Won','lost'=>'Lost','cancelled'=>'Cancelled','completed'=>'Completed',
  ];

  $statusDist = [];
  foreach ($tenders as $t) {
    $k = $t->status?->value ?? 'unknown';
    $statusDist[$k] = ($statusDist[$k] ?? 0) + 1;
  }
  arsort($statusDist);
  $totalForPct = array_sum($statusDist) ?: 1;

  /* ── Priority distribution ── */
  $priorityColors = ['low'=>'#94a3b8','medium'=>'#fbbf24','high'=>'#fb923c','urgent'=>'#ef4444'];
  $priorityDist = [];
  foreach ($tenders as $t) {
    $k = $t->priority?->value ?? 'unknown';
    $priorityDist[$k] = ($priorityDist[$k] ?? 0) + 1;
  }
  arsort($priorityDist);
  $totalPri = array_sum($priorityDist) ?: 1;

  $barMax = max(array_values($statusDist) ?: [1]);

  /* ── SVG donut helper ── */
  function donutSlices(array $dist, array $colors, int $cx, int $cy, int $r, int $innerR): string {
    $total = array_sum($dist) ?: 1;
    $angle = -90; $svg = '';
    foreach ($dist as $key => $val) {
      $pct   = $val / $total;
      $sweep = 360 * $pct;
      if ($sweep >= 359.9) $sweep = 359.9;
      $r1 = deg2rad($angle);
      $r2 = deg2rad($angle + $sweep);
      $x1 = $cx + $r * cos($r1); $y1 = $cy + $r * sin($r1);
      $x2 = $cx + $r * cos($r2); $y2 = $cy + $r * sin($r2);
      $xi1 = $cx + $innerR * cos($r1); $yi1 = $cy + $innerR * sin($r1);
      $xi2 = $cx + $innerR * cos($r2); $yi2 = $cy + $innerR * sin($r2);
      $large = $sweep > 180 ? 1 : 0;
      $color = $colors[$key] ?? '#cbd5e1';
      $svg  .= "<path d=\"M{$x1},{$y1} A{$r},{$r} 0 {$large},1 {$x2},{$y2} L{$xi2},{$yi2} A{$innerR},{$innerR} 0 {$large},0 {$xi1},{$yi1} Z\" fill=\"{$color}\" />";
      $angle += $sweep;
    }
    return $svg;
  }

  /* ── Helper: nilai sel per kolom ── */
  function tenderCellValue($t, string $col): string {
    return match ($col) {
      'code'                => $t->code ?? '—',
      'title'               => $t->title ?? '—',
      'client'              => $t->client?->name ?? '—',
      'category'            => $t->category?->name ?? '—',
      'pic'                 => $t->pic?->name ?? '—',
      'backup_pic'          => $t->backupPic?->name ?? '—',
      'location'            => $t->location ?? '—',
      'source'              => $t->source ?? '—',
      'received_date'       => $t->received_date?->format('d/m/Y') ?? '—',
      'submission_deadline' => $t->submission_deadline?->format('d/m/Y') ?? '—',
      'project_start_date'  => $t->project_start_date?->format('d/m/Y') ?? '—',
      'project_end_date'    => $t->project_end_date?->format('d/m/Y') ?? '—',
      'estimated_value'     => $t->estimated_value ? number_format($t->estimated_value, 0, ',', '.') : '—',
      'description'         => $t->description ?? '—',
      'notes'               => $t->notes ?? '—',
      'created_at'          => $t->created_at?->format('d/m/Y') ?? '—',
      default               => '—',
    };
  }

  /* ── Helper: CSS class per kolom ── */
  function tenderCellClass(string $col): string {
    return match ($col) {
      'code'                              => 'td-code',
      'title'                             => 'td-title',
      'category','backup_pic','source'    => 'td-muted',
      'received_date','submission_deadline',
      'project_start_date','project_end_date',
      'created_at'                        => 'td-date',
      'estimated_value'                   => 'td-num',
      'description','notes'              => 'td-desc td-muted',
      default                             => '',
    };
  }
@endphp

{{-- ── HEADER ── --}}
<div class="header">
  <div class="header-left">
    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('img/Raya - Logo.png'))) }}" alt="Raya Tender" />
  </div>
  <div class="header-right">
    <div class="doc-title">Laporan Tender</div>
    <div class="doc-sub">
      PT. Raya Konstruksi Internasional
      <span>·</span>
      Dicetak: {{ now()->format('d F Y, H:i') }}
      @if(!empty($filters['start_date']))
        <span>·</span> Periode: {{ $filters['start_date'] }} s/d {{ $filters['end_date'] ?? now()->format('Y-m-d') }}
      @endif
      @if(!empty($filters['status']))
        <span>·</span> Status: {{ strtoupper($filters['status']) }}
      @elseif(!empty($exportStatuses))
        @php
          $statusLabelsMap = collect(\App\Enums\TenderStatus::cases())->mapWithKeys(fn($s) => [$s->value => $s->label()])->all();
          $pickedLabels = array_map(fn($s) => $statusLabelsMap[$s] ?? $s, $exportStatuses);
        @endphp
        <span>·</span> Status: {{ implode(', ', $pickedLabels) }}
      @endif
    </div>
  </div>
</div>

{{-- ── TABLE ── --}}
<div class="section-label">Daftar Tender</div>
<table class="data">
  <thead>
    <tr>
      <th style="width:28px">#</th>
      @foreach($activeColumns as $col)
        @php
          $w = match($col) {
            'code'                => 'width:82px',
            'submission_deadline',
            'received_date',
            'project_start_date',
            'project_end_date',
            'created_at'          => 'width:62px',
            'status','priority'   => 'width:72px',
            'estimated_value'     => 'width:80px',
            default               => '',
          };
        @endphp
        <th @if($w) style="{{ $w }}" @endif>{{ $labels[$col] ?? $col }}</th>
      @endforeach
    </tr>
  </thead>
  <tbody>
    @forelse($data['tenders'] as $i => $t)
    @php
      $sk = 's-' . ($t->status?->value ?? 'default');
      $pk = 'p-' . ($t->priority?->value ?? 'low');
    @endphp
    <tr>
      <td class="td-no">{{ $i + 1 }}</td>
      @foreach($activeColumns as $col)
        @if($col === 'status')
          <td><span class="badge {{ $sk }}">{{ $t->status?->label() }}</span></td>
        @elseif($col === 'priority')
          <td><span class="badge {{ $pk }}">{{ $t->priority?->label() }}</span></td>
        @else
          <td class="{{ tenderCellClass($col) }}">{{ tenderCellValue($t, $col) }}</td>
        @endif
      @endforeach
    </tr>
    @empty
    <tr class="empty-row"><td colspan="{{ count($activeColumns) + 1 }}">Tidak ada data tender</td></tr>
    @endforelse
  </tbody>
</table>

<br>

{{-- ── STATS ── --}}
<table class="stats">
  <tr>
    <td class="stats-cell">
      <div class="stat-val">{{ $data['total'] }}</div>
      <div class="stat-lbl">Total</div>
    </td>
    <td class="stats-cell c-blue">
      <div class="stat-val">{{ $data['active'] }}</div>
      <div class="stat-lbl">Aktif</div>
    </td>
    <td class="stats-cell c-green">
      <div class="stat-val">{{ $data['won'] }}</div>
      <div class="stat-lbl">Menang</div>
    </td>
    <td class="stats-cell c-red">
      <div class="stat-val">{{ $data['lost'] }}</div>
      <div class="stat-lbl">Kalah</div>
    </td>
    <td class="stats-cell c-navy">
      <div class="stat-val">{{ $data['win_rate'] }}%</div>
      <div class="stat-lbl">Win Rate</div>
    </td>
  </tr>
</table>

{{-- ── CHARTS ── --}}
<div class="charts-row">

  {{-- Bar chart: Status Distribution --}}
  <div class="chart-box wide">
    <div class="chart-title">Distribusi Status Tender</div>
    @foreach($statusDist as $key => $cnt)
    @php $pct = $barMax > 0 ? round(($cnt / $barMax) * 100) : 0; $color = $statusColors[$key] ?? '#94a3b8'; @endphp
    <div class="bar-row">
      <div class="bar-label">{{ $statusLabels[$key] ?? ucfirst($key) }}</div>
      <div class="bar-track">
        <div class="bar-bg">
          <div class="bar-fill" style="width:{{ $pct }}%; background:{{ $color }};"></div>
        </div>
      </div>
      <div class="bar-val">{{ $cnt }}</div>
    </div>
    @endforeach
  </div>

  {{-- Donut: Priority Distribution --}}
  <div class="chart-box narrow">
    <div class="chart-title">Distribusi Prioritas</div>
    @php
      $svgSize = 110; $cx = 55; $cy = 55; $r = 46; $innerR = 28;
      $donutSvg = donutSlices($priorityDist, $priorityColors, $cx, $cy, $r, $innerR);
    @endphp
    <div class="donut-wrap">
      <svg width="{{ $svgSize }}" height="{{ $svgSize }}" viewBox="0 0 {{ $svgSize }} {{ $svgSize }}">
        {!! $donutSvg !!}
        <text x="{{ $cx }}" y="{{ $cy - 5 }}" text-anchor="middle" font-size="13" font-weight="700" fill="#101828">{{ $totalPri }}</text>
        <text x="{{ $cx }}" y="{{ $cy + 9 }}" text-anchor="middle" font-size="7" fill="#98a2b3">tender</text>
      </svg>
    </div>
    @foreach($priorityDist as $key => $cnt)
    @php $color = $priorityColors[$key] ?? '#cbd5e1'; @endphp
    <div class="legend-item">
      <div class="legend-dot"><span style="background:{{ $color }};"></span></div>
      <div class="legend-name">{{ ucfirst($key) }}</div>
      <div class="legend-cnt">{{ $cnt }} <span style="font-weight:400;color:#98a2b3">({{ round($cnt/$totalPri*100) }}%)</span></div>
    </div>
    @endforeach
  </div>

</div>

{{-- ── FOOTER ── --}}
<div class="footer">
  <div class="footer-left">Sumber data: Raya Tender Management System</div>
  <div class="footer-right">
    @if(in_array('estimated_value', $activeColumns))
      Dokumen internal — data finansial ditampilkan sesuai akses
    @else
      Dokumen internal — data finansial tidak ditampilkan
    @endif
  </div>
</div>

</body>
</html>
