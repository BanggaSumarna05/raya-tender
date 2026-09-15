<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Laporan Keseluruhan</title>
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
    display: table; width: 100%;
    border-bottom: 2px solid #101828;
    padding-bottom: 14px; margin-bottom: 22px;
  }
  .header-left  { display: table-cell; vertical-align: middle; width: 160px; }
  .header-left img { height: 34px; width: auto; }
  .header-right { display: table-cell; vertical-align: middle; text-align: right; }
  .doc-title    { font-size: 16px; font-weight: 700; color: #101828; letter-spacing: -.01em; }
  .doc-subtitle { font-size: 10px; color: #465fff; font-weight: 600; margin-top: 2px; }
  .doc-sub      { font-size: 8.5px; color: #667085; margin-top: 3px; }
  .doc-sub span { margin: 0 4px; color: #d0d5dd; }

  /* ── SECTION HEADER ── */
  .section-header {
    background: #101828;
    color: #ffffff;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .04em;
    text-transform: uppercase;
    padding: 7px 12px;
    border-radius: 6px 6px 0 0;
    margin-top: 26px;
    margin-bottom: 0;
  }
  .section-header.first { margin-top: 0; }
  .section-body {
    border: 1px solid #e4e7ec;
    border-top: none;
    border-radius: 0 0 6px 6px;
    padding: 14px;
    margin-bottom: 0;
  }

  /* ── KPI STATS ── */
  .stats { display: table; width: 100%; border-collapse: separate; border-spacing: 7px 0; margin: 0 -7px 14px; }
  .stats-cell {
    display: table-cell; border: 1px solid #e4e7ec;
    border-radius: 8px; padding: 9px 6px; text-align: center;
  }
  .stat-val { font-size: 18px; font-weight: 700; color: #101828; line-height: 1.1; }
  .stat-lbl { font-size: 7.5px; font-weight: 600; color: #98a2b3; text-transform: uppercase; letter-spacing: .06em; margin-top: 3px; }
  .c-blue   .stat-val { color: #465fff; }
  .c-green  .stat-val { color: #039855; }
  .c-red    .stat-val { color: #d92d20; }
  .c-navy   .stat-val { color: #262e89; }
  .c-orange .stat-val { color: #dc6803; }

  /* ── CHARTS ROW ── */
  .charts-row { display: table; width: 100%; margin-bottom: 14px; border-spacing: 0; }
  .chart-cell {
    display: table-cell; vertical-align: top;
    border: 1px solid #e4e7ec; border-radius: 8px; padding: 10px 12px;
  }
  .chart-cell + .chart-cell { margin-left: 10px; }
  .chart-cell.wide   { width: 60%; }
  .chart-cell.narrow { width: 40%; padding-left: 16px; }
  .chart-title {
    font-size: 8px; font-weight: 700; text-transform: uppercase;
    letter-spacing: .08em; color: #98a2b3; margin-bottom: 8px;
  }

  /* ── BAR CHART ── */
  .bar-row   { display: table; width: 100%; margin-bottom: 5px; }
  .bar-label { display: table-cell; width: 110px; font-size: 8px; color: #344054; vertical-align: middle; padding-right: 5px; white-space: nowrap; }
  .bar-track { display: table-cell; vertical-align: middle; }
  .bar-bg    { background: #f2f4f7; border-radius: 3px; height: 9px; }
  .bar-fill  { border-radius: 3px; height: 9px; }
  .bar-val   { display: table-cell; width: 24px; font-size: 7.5px; font-weight: 700; color: #344054; vertical-align: middle; padding-left: 4px; text-align: right; }

  /* ── DONUT ── */
  .donut-wrap  { text-align: center; margin-bottom: 6px; }
  .legend-item { display: table; width: 100%; margin-bottom: 3px; }
  .legend-dot  { display: table-cell; width: 16px; vertical-align: middle; }
  .legend-dot span { display: inline-block; width: 7px; height: 7px; border-radius: 50%; }
  .legend-name { display: table-cell; font-size: 8px; color: #344054; vertical-align: middle; }
  .legend-cnt  { display: table-cell; font-size: 8px; font-weight: 700; color: #101828; text-align: right; vertical-align: middle; }

  /* ── DATA TABLE ── */
  .section-label {
    font-size: 8px; font-weight: 700; text-transform: uppercase;
    letter-spacing: .09em; color: #98a2b3; margin: 12px 0 6px;
  }
  table.data { width: 100%; border-collapse: collapse; }
  table.data thead tr { background: #101828; }
  table.data th {
    padding: 6px 8px; text-align: left; font-size: 7px; font-weight: 700;
    color: #fff; text-transform: uppercase; letter-spacing: .05em; white-space: nowrap;
  }
  table.data th:first-child { border-radius: 4px 0 0 0; }
  table.data th:last-child  { border-radius: 0 4px 0 0; }
  table.data td {
    padding: 5px 8px; border-bottom: 1px solid #eaecf0;
    vertical-align: top; font-size: 8.5px; color: #344054;
  }
  table.data tbody tr:nth-child(even) td { background: #f9fafb; }
  .td-code  { font-family: 'Courier New', monospace; font-size: 7.5px; color: #667085; }
  .td-title { font-weight: 600; color: #101828; }
  .td-muted { color: #98a2b3; font-size: 8px; }
  .td-right { text-align: right; }

  /* ── BADGES ── */
  .badge {
    display: inline-block; padding: 1px 6px; border-radius: 20px;
    font-size: 6.5px; font-weight: 700; text-transform: uppercase; white-space: nowrap;
  }
  /* tender status */
  .s-draft          { background:#f2f4f7; color:#667085; }
  .s-identified     { background:#eff8ff; color:#1570ef; }
  .s-qualification  { background:#f0f9ff; color:#026aa2; }
  .s-preparation    { background:#fff4ed; color:#c4320a; }
  .s-submitted      { background:#ecfdf3; color:#027a48; }
  .s-evaluation     { background:#fdf4ff; color:#6941c6; }
  .s-clarification  { background:#fffaeb; color:#b54708; }
  .s-negotiation    { background:#f0fdf4; color:#166534; }
  .s-won            { background:#d1fadf; color:#05603a; }
  .s-lost           { background:#fef3f2; color:#b42318; }
  .s-cancelled      { background:#f9f5ff; color:#6941c6; }
  .s-completed      { background:#f0fdf4; color:#166534; }
  .s-default        { background:#f2f4f7; color:#667085; }
  /* priority */
  .p-low    { background:#f9fafb; color:#667085; }
  .p-medium { background:#fffaeb; color:#b54708; }
  .p-high   { background:#fff4ed; color:#c4320a; }
  .p-urgent { background:#fef3f2; color:#b42318; }
  /* proposal status */
  .ps-draft           { background:#f2f4f7; color:#667085; }
  .ps-internal_review { background:#eff8ff; color:#1570ef; }
  .ps-final           { background:#fff4ed; color:#c4320a; }
  .ps-submitted       { background:#ecfdf3; color:#027a48; }
  .ps-revision        { background:#fffaeb; color:#b54708; }
  .ps-won             { background:#d1fadf; color:#05603a; }
  .ps-lost            { background:#fef3f2; color:#b42318; }
  .ps-cancelled       { background:#f9f5ff; color:#6941c6; }

  .version-pill {
    display: inline-block; padding: 1px 5px; border-radius: 3px;
    background: #f2f4f7; color: #344054;
    font-family: 'Courier New', monospace; font-size: 7.5px; font-weight: 700;
  }
  .win-rate-bar { display: inline-block; background: #e4e7ec; border-radius: 3px; width: 48px; height: 6px; vertical-align: middle; margin-right: 4px; }
  .win-rate-fill { display: inline-block; background: #465fff; border-radius: 3px; height: 6px; }

  .empty-row td { text-align:center; color:#98a2b3; padding:20px 0; font-size:9px; }

  /* ── FOOTER ── */
  .footer { margin-top: 24px; padding-top: 10px; border-top: 1px solid #eaecf0; display: table; width: 100%; }
  .footer-left, .footer-right { display: table-cell; font-size: 8px; color: #98a2b3; vertical-align: middle; }
  .footer-right { text-align: right; font-style: italic; }

  /* ── PAGE BREAK ── */
  .page-break { page-break-before: always; }
</style>
</head>
<body>

@php
  /* ────────────── TENDER CHART DATA ────────────── */
  $tenders = $tenderData['tenders'];

  $tStatusColors = [
    'draft'=>'#94a3b8','identified'=>'#60a5fa','qualification'=>'#38bdf8',
    'preparation'=>'#fb923c','submitted'=>'#34d399','evaluation'=>'#a78bfa',
    'clarification'=>'#fbbf24','negotiation'=>'#4ade80',
    'won'=>'#16a34a','lost'=>'#ef4444','cancelled'=>'#c084fc','completed'=>'#059669',
  ];
  $tStatusLabels = [
    'draft'=>'Draft','identified'=>'Identified','qualification'=>'Qualification',
    'preparation'=>'Preparation','submitted'=>'Submitted','evaluation'=>'Evaluation',
    'clarification'=>'Clarification','negotiation'=>'Negotiation',
    'won'=>'Won','lost'=>'Lost','cancelled'=>'Cancelled','completed'=>'Completed',
  ];
  $priorityColors = ['low'=>'#94a3b8','medium'=>'#fbbf24','high'=>'#fb923c','urgent'=>'#ef4444'];

  $tStatusDist = [];
  foreach ($tenders as $t) {
    $k = $t->status?->value ?? 'unknown';
    $tStatusDist[$k] = ($tStatusDist[$k] ?? 0) + 1;
  }
  arsort($tStatusDist);
  $tBarMax = max(array_values($tStatusDist) ?: [1]);

  $tPriorityDist = [];
  foreach ($tenders as $t) {
    $k = $t->priority?->value ?? 'unknown';
    $tPriorityDist[$k] = ($tPriorityDist[$k] ?? 0) + 1;
  }
  arsort($tPriorityDist);
  $tPriorityTotal = array_sum($tPriorityDist) ?: 1;

  /* ────────────── PROPOSAL CHART DATA ────────────── */
  $proposals = $proposalData['proposals'];

  $pStatusColors = [
    'draft'=>'#94a3b8','internal_review'=>'#60a5fa','final'=>'#fb923c',
    'submitted'=>'#34d399','revision'=>'#fbbf24','won'=>'#16a34a',
    'lost'=>'#ef4444','cancelled'=>'#c084fc',
  ];
  $pStatusLabels = [
    'draft'=>'Draft','internal_review'=>'Internal Review','final'=>'Final',
    'submitted'=>'Submitted','revision'=>'Revision','won'=>'Won',
    'lost'=>'Lost','cancelled'=>'Cancelled',
  ];

  $pStatusDist = [];
  foreach ($proposals as $p) {
    $k = $p->status?->value ?? 'unknown';
    $pStatusDist[$k] = ($pStatusDist[$k] ?? 0) + 1;
  }
  arsort($pStatusDist);
  $pBarMax = max(array_values($pStatusDist) ?: [1]);

  $pClientDist = [];
  foreach ($proposals as $p) {
    $name = $p->tender?->client?->name ?? 'Tidak diketahui';
    $pClientDist[$name] = ($pClientDist[$name] ?? 0) + 1;
  }
  arsort($pClientDist);
  $pClientDist  = array_slice($pClientDist, 0, 6, true);
  $pClientTotal = array_sum($pClientDist) ?: 1;
  $clientColors = ['#465fff','#38bdf8','#34d399','#fb923c','#a78bfa','#fbbf24'];

  /* ────────────── SVG DONUT HELPER ────────────── */
  function summaryDonut(array $dist, array $colorMap, bool $indexed = false): string {
    $total = array_sum($dist) ?: 1;
    $angle = -90; $svg = ''; $i = 0;
    foreach ($dist as $key => $val) {
      $pct   = $val / $total;
      $sweep = 360 * $pct;
      if ($sweep >= 359.9) $sweep = 359.9;
      $cx = 50; $cy = 50; $r = 42; $innerR = 26;
      $r1 = deg2rad($angle); $r2 = deg2rad($angle + $sweep);
      $x1 = $cx + $r * cos($r1); $y1 = $cy + $r * sin($r1);
      $x2 = $cx + $r * cos($r2); $y2 = $cy + $r * sin($r2);
      $xi1 = $cx + $innerR * cos($r1); $yi1 = $cy + $innerR * sin($r1);
      $xi2 = $cx + $innerR * cos($r2); $yi2 = $cy + $innerR * sin($r2);
      $large = $sweep > 180 ? 1 : 0;
      $color = $indexed ? ($colorMap[$i % count($colorMap)] ?? '#cbd5e1') : ($colorMap[$key] ?? '#cbd5e1');
      $svg  .= "<path d=\"M{$x1},{$y1} A{$r},{$r} 0 {$large},1 {$x2},{$y2} L{$xi2},{$yi2} A{$innerR},{$innerR} 0 {$large},0 {$xi1},{$yi1} Z\" fill=\"{$color}\" />";
      $angle += $sweep; $i++;
    }
    return $svg;
  }
@endphp

{{-- ═══════════════════════════════════════════════════ --}}
{{-- HEADER                                              --}}
{{-- ═══════════════════════════════════════════════════ --}}
<div class="header">
  <div class="header-left">
    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('img/Raya - Logo.png'))) }}" alt="Raya Tender" />
  </div>
  <div class="header-right">
    <div class="doc-title">Laporan Keseluruhan</div>
    <div class="doc-subtitle">Tender · Proposal · Performa</div>
    <div class="doc-sub">
      PT. Raya Konstruksi Internasional
      <span>·</span>
      Dicetak: {{ now()->format('d F Y, H:i') }}
      @if(!empty($filters['start_date']))
        <span>·</span> Periode: {{ $filters['start_date'] }} s/d {{ $filters['end_date'] ?? now()->format('Y-m-d') }}
      @endif
    </div>
  </div>
</div>


{{-- ═══════════════════════════════════════════════════ --}}
{{-- SECTION 1 — TENDER                                  --}}
{{-- ═══════════════════════════════════════════════════ --}}
<div class="section-header first">01 · Laporan Tender</div>
<div class="section-body">

  {{-- Stats --}}
  <table class="stats">
    <tr>
      <td class="stats-cell"><div class="stat-val">{{ $tenderData['total'] }}</div><div class="stat-lbl">Total</div></td>
      <td class="stats-cell c-blue"><div class="stat-val">{{ $tenderData['active'] }}</div><div class="stat-lbl">Aktif</div></td>
      <td class="stats-cell c-green"><div class="stat-val">{{ $tenderData['won'] }}</div><div class="stat-lbl">Menang</div></td>
      <td class="stats-cell c-red"><div class="stat-val">{{ $tenderData['lost'] }}</div><div class="stat-lbl">Kalah</div></td>
      <td class="stats-cell"><div class="stat-val">{{ $tenderData['draft'] }}</div><div class="stat-lbl">Draft</div></td>
      <td class="stats-cell c-navy"><div class="stat-val">{{ $tenderData['win_rate'] }}%</div><div class="stat-lbl">Win Rate</div></td>
    </tr>
  </table>

  {{-- Tender table --}}
  <div class="section-label">Daftar Tender</div>
  <table class="data">
    <thead>
      <tr>
        <th style="width:78px">Kode</th>
        <th>Nama Tender</th>
        <th>Klien</th>
        <th>Kategori</th>
        <th>PIC</th>
        <th>Status</th>
        <th>Prioritas</th>
        <th style="width:58px">Deadline</th>
      </tr>
    </thead>
    <tbody>
      @forelse($tenderData['tenders'] as $t)
      @php $sk = 's-'.($t->status?->value??'default'); $pk = 'p-'.($t->priority?->value??'low'); @endphp
      <tr>
        <td class="td-code">{{ $t->code }}</td>
        <td class="td-title">{{ $t->title }}</td>
        <td>{{ $t->client?->name ?? '—' }}</td>
        <td class="td-muted">{{ $t->category?->name ?? '—' }}</td>
        <td>{{ $t->pic?->name ?? '—' }}</td>
        <td><span class="badge {{ $sk }}">{{ $t->status?->label() }}</span></td>
        <td><span class="badge {{ $pk }}">{{ $t->priority?->label() }}</span></td>
        <td class="td-muted">{{ $t->submission_deadline?->format('d/m/Y') ?? '—' }}</td>
      </tr>
      @empty
      <tr class="empty-row"><td colspan="8">Tidak ada data tender</td></tr>
      @endforelse
    </tbody>
  </table>

  {{-- Tender Charts --}}
  <div class="section-label" style="margin-top:14px">Analisis Tender</div>
  <table style="width:100%;border-collapse:separate;border-spacing:8px 0;margin:0 -8px;">
    <tr>
      <td style="width:60%;vertical-align:top;border:1px solid #e4e7ec;border-radius:8px;padding:10px 12px;">
        <div class="chart-title">Distribusi Status</div>
        @foreach($tStatusDist as $key => $cnt)
        @php $pct = $tBarMax > 0 ? round(($cnt/$tBarMax)*100) : 0; $color = $tStatusColors[$key] ?? '#94a3b8'; @endphp
        <div class="bar-row">
          <div class="bar-label">{{ $tStatusLabels[$key] ?? ucfirst($key) }}</div>
          <div class="bar-track"><div class="bar-bg"><div class="bar-fill" style="width:{{ $pct }}%;background:{{ $color }};"></div></div></div>
          <div class="bar-val">{{ $cnt }}</div>
        </div>
        @endforeach
      </td>
      <td style="width:40%;vertical-align:top;border:1px solid #e4e7ec;border-radius:8px;padding:10px 12px;">
        <div class="chart-title">Distribusi Prioritas</div>
        <div class="donut-wrap">
          <svg width="100" height="100" viewBox="0 0 100 100">
            {!! summaryDonut($tPriorityDist, $priorityColors) !!}
            <text x="50" y="47" text-anchor="middle" font-size="13" font-weight="700" fill="#101828">{{ $tPriorityTotal }}</text>
            <text x="50" y="59" text-anchor="middle" font-size="7" fill="#98a2b3">tender</text>
          </svg>
        </div>
        @foreach($tPriorityDist as $key => $cnt)
        <div class="legend-item">
          <div class="legend-dot"><span style="background:{{ $priorityColors[$key] ?? '#cbd5e1' }};"></span></div>
          <div class="legend-name">{{ ucfirst($key) }}</div>
          <div class="legend-cnt">{{ $cnt }} <span style="font-weight:400;color:#98a2b3">({{ round($cnt/$tPriorityTotal*100) }}%)</span></div>
        </div>
        @endforeach
      </td>
    </tr>
  </table>

</div>{{-- /section-body tender --}}


{{-- ═══════════════════════════════════════════════════ --}}
{{-- SECTION 2 — PROPOSAL                                --}}
{{-- ═══════════════════════════════════════════════════ --}}
<div class="section-header">02 · Laporan Proposal</div>
<div class="section-body">

  {{-- Proposal table --}}
  <div class="section-label">Daftar Proposal</div>
  <table class="data">
    <thead>
      <tr>
        <th style="width:78px">Kode</th>
        <th>Judul Proposal</th>
        <th style="width:72px">Tender</th>
        <th>Klien</th>
        <th>PIC</th>
        <th>Status</th>
        <th style="width:32px">Versi</th>
        <th style="width:58px">Deadline</th>
      </tr>
    </thead>
    <tbody>
      @forelse($proposalData['proposals'] as $p)
      @php $psk = 'ps-'.($p->status?->value??'default'); @endphp
      <tr>
        <td class="td-code">{{ $p->code }}</td>
        <td class="td-title">{{ $p->title }}</td>
        <td class="td-code">{{ $p->tender?->code ?? '—' }}</td>
        <td>{{ $p->tender?->client?->name ?? '—' }}</td>
        <td>{{ $p->pic?->name ?? '—' }}</td>
        <td><span class="badge {{ $psk }}">{{ $p->status?->label() }}</span></td>
        <td><span class="version-pill">v{{ $p->current_version }}</span></td>
        <td class="td-muted">{{ $p->deadline?->format('d/m/Y') ?? '—' }}</td>
      </tr>
      @empty
      <tr class="empty-row"><td colspan="8">Tidak ada data proposal</td></tr>
      @endforelse
    </tbody>
  </table>

  {{-- Stats --}}
  <table class="stats" style="margin-top:14px">
    <tr>
      <td class="stats-cell"><div class="stat-val">{{ $proposalData['total'] }}</div><div class="stat-lbl">Total</div></td>
      <td class="stats-cell c-blue"><div class="stat-val">{{ $proposalData['draft'] }}</div><div class="stat-lbl">Draft</div></td>
      <td class="stats-cell c-orange"><div class="stat-val">{{ $proposalData['internal_review'] }}</div><div class="stat-lbl">Review</div></td>
      <td class="stats-cell"><div class="stat-val">{{ $proposalData['final'] }}</div><div class="stat-lbl">Final</div></td>
      <td class="stats-cell c-navy"><div class="stat-val">{{ $proposalData['submitted'] }}</div><div class="stat-lbl">Submitted</div></td>
    </tr>
  </table>

  {{-- Proposal Charts --}}
  <div class="section-label" style="margin-top:14px">Analisis Proposal</div>
  <table style="width:100%;border-collapse:separate;border-spacing:8px 0;margin:0 -8px;">
    <tr>
      <td style="width:60%;vertical-align:top;border:1px solid #e4e7ec;border-radius:8px;padding:10px 12px;">
        <div class="chart-title">Distribusi Status</div>
        @foreach($pStatusDist as $key => $cnt)
        @php $pct = $pBarMax > 0 ? round(($cnt/$pBarMax)*100) : 0; $color = $pStatusColors[$key] ?? '#94a3b8'; @endphp
        <div class="bar-row">
          <div class="bar-label">{{ $pStatusLabels[$key] ?? ucfirst($key) }}</div>
          <div class="bar-track"><div class="bar-bg"><div class="bar-fill" style="width:{{ $pct }}%;background:{{ $color }};"></div></div></div>
          <div class="bar-val">{{ $cnt }}</div>
        </div>
        @endforeach
      </td>
      <td style="width:40%;vertical-align:top;border:1px solid #e4e7ec;border-radius:8px;padding:10px 12px;">
        <div class="chart-title">Top Klien</div>
        <div class="donut-wrap">
          <svg width="100" height="100" viewBox="0 0 100 100">
            {!! summaryDonut($pClientDist, $clientColors, true) !!}
            <text x="50" y="47" text-anchor="middle" font-size="13" font-weight="700" fill="#101828">{{ $pClientTotal }}</text>
            <text x="50" y="59" text-anchor="middle" font-size="7" fill="#98a2b3">proposal</text>
          </svg>
        </div>
        @foreach($pClientDist as $name => $cnt)
        @php $ci = array_search($name, array_keys($pClientDist)); $cc = $clientColors[$ci % count($clientColors)]; @endphp
        <div class="legend-item">
          <div class="legend-dot"><span style="background:{{ $cc }};"></span></div>
          <div class="legend-name">{{ Str::limit($name, 18) }}</div>
          <div class="legend-cnt">{{ $cnt }} <span style="font-weight:400;color:#98a2b3">({{ round($cnt/$pClientTotal*100) }}%)</span></div>
        </div>
        @endforeach
      </td>
    </tr>
  </table>

</div>{{-- /section-body proposal --}}


{{-- ── FOOTER ── --}}
<div class="footer">
  <div class="footer-left">Sumber data: Raya Tender Management System</div>
  <div class="footer-right">Dokumen internal — data finansial tidak ditampilkan</div>
</div>

</body>
</html>
