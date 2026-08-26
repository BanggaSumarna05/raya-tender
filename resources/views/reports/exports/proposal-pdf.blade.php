<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Laporan Proposal</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }

  body {
    font-family: 'Helvetica Neue', Arial, sans-serif;
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
  .c-blue   .stat-val { color: #465fff; }
  .c-orange .stat-val { color: #dc6803; }
  .c-green  .stat-val { color: #039855; }
  .c-navy   .stat-val { color: #262e89; }

  /* ── CHARTS ROW ── */
  .charts-row { display: table; width: 100%; margin-bottom: 22px; }
  .chart-box  {
    display: table-cell; border: 1px solid #e4e7ec;
    border-radius: 8px; padding: 12px 14px; vertical-align: top;
  }
  .chart-box.wide   { width: 62%; padding-right: 10px; }
  .chart-box.narrow { width: 38%; padding-left: 10px; }
  .chart-title {
    font-size: 8.5px; font-weight: 700; text-transform: uppercase;
    letter-spacing: .08em; color: #98a2b3; margin-bottom: 10px;
  }

  /* ── BAR CHART ── */
  .bar-row   { display: table; width: 100%; margin-bottom: 6px; }
  .bar-label { display: table-cell; width: 120px; font-size: 8.5px; color: #344054; vertical-align: middle; padding-right: 6px; white-space: nowrap; }
  .bar-track { display: table-cell; vertical-align: middle; }
  .bar-bg    { background: #f2f4f7; border-radius: 3px; height: 10px; }
  .bar-fill  { background: #465fff; border-radius: 3px; height: 10px; }
  .bar-val   { display: table-cell; width: 28px; font-size: 8px; font-weight: 700; color: #344054; vertical-align: middle; padding-left: 5px; text-align: right; }

  /* ── DONUT / LEGEND ── */
  .donut-wrap  { text-align: center; margin-bottom: 8px; }
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
  .td-code  { font-family: 'Courier New', monospace; font-size: 8px; color: #667085; }
  .td-title { font-weight: 600; color: #101828; }
  .td-muted { color: #98a2b3; font-size: 8.5px; }

  /* ── BADGES ── */
  .badge {
    display: inline-block; padding: 2px 7px; border-radius: 20px;
    font-size: 7px; font-weight: 700; text-transform: uppercase; letter-spacing: .03em; white-space: nowrap;
  }
  .s-draft           { background:#f2f4f7; color:#667085; }
  .s-internal_review { background:#eff8ff; color:#1570ef; }
  .s-client_review   { background:#f0f9ff; color:#026aa2; }
  .s-final           { background:#fff4ed; color:#c4320a; }
  .s-submitted       { background:#ecfdf3; color:#027a48; }
  .s-won             { background:#d1fadf; color:#05603a; }
  .s-lost            { background:#fef3f2; color:#b42318; }
  .s-cancelled       { background:#f9f5ff; color:#6941c6; }
  .s-default         { background:#f2f4f7; color:#667085; }

  .version-pill {
    display: inline-block; padding: 1px 6px; border-radius: 4px;
    background: #f2f4f7; color: #344054;
    font-family: 'Courier New', monospace; font-size: 8.5px; font-weight: 700;
  }

  .empty-row td { text-align:center; color:#98a2b3; padding:24px 0; font-size:10px; }

  /* ── FOOTER ── */
  .footer { margin-top: 24px; padding-top: 10px; border-top: 1px solid #eaecf0; display: table; width: 100%; }
  .footer-left, .footer-right { display: table-cell; font-size: 8px; color: #98a2b3; vertical-align: middle; }
  .footer-right { text-align: right; font-style: italic; }
</style>
</head>
<body>

@php
  /* ── Precompute chart data ── */
  $proposals = $data['proposals'];

  $statusColors = [
    'draft'           => '#94a3b8',
    'internal_review' => '#60a5fa',
    'client_review'   => '#38bdf8',
    'final'           => '#fb923c',
    'submitted'       => '#34d399',
    'won'             => '#16a34a',
    'lost'            => '#ef4444',
    'cancelled'       => '#c084fc',
  ];
  $statusLabels = [
    'draft'           => 'Draft',
    'internal_review' => 'Internal Review',
    'client_review'   => 'Client Review',
    'final'           => 'Final',
    'submitted'       => 'Submitted',
    'won'             => 'Won',
    'lost'            => 'Lost',
    'cancelled'       => 'Cancelled',
  ];

  $statusDist = [];
  foreach ($proposals as $p) {
    $k = $p->status?->value ?? 'unknown';
    $statusDist[$k] = ($statusDist[$k] ?? 0) + 1;
  }
  arsort($statusDist);
  $totalForPct = array_sum($statusDist) ?: 1;
  $barMax = max(array_values($statusDist) ?: [1]);

  /* Client distribution (top 6) */
  $clientDist = [];
  foreach ($proposals as $p) {
    $name = $p->tender?->client?->name ?? 'Tidak diketahui';
    $clientDist[$name] = ($clientDist[$name] ?? 0) + 1;
  }
  arsort($clientDist);
  $clientDist = array_slice($clientDist, 0, 6, true);
  $clientMax  = max(array_values($clientDist) ?: [1]);

  $clientColors = ['#465fff','#38bdf8','#34d399','#fb923c','#a78bfa','#fbbf24'];

  /* SVG donut */
  function donutSlicesP(array $dist, array $colors, int $cx, int $cy, int $r, int $innerR): string {
    $total = array_sum($dist) ?: 1;
    $angle = -90; $svg = ''; $i = 0;
    foreach ($dist as $key => $val) {
      $pct   = $val / $total;
      $sweep = 360 * $pct;
      if ($sweep >= 359.9) $sweep = 359.9;
      $r1 = deg2rad($angle); $r2 = deg2rad($angle + $sweep);
      $x1 = $cx + $r * cos($r1); $y1 = $cy + $r * sin($r1);
      $x2 = $cx + $r * cos($r2); $y2 = $cy + $r * sin($r2);
      $xi1 = $cx + $innerR * cos($r1); $yi1 = $cy + $innerR * sin($r1);
      $xi2 = $cx + $innerR * cos($r2); $yi2 = $cy + $innerR * sin($r2);
      $large = $sweep > 180 ? 1 : 0;
      $color = $colors[$i % count($colors)];
      $svg  .= "<path d=\"M{$x1},{$y1} A{$r},{$r} 0 {$large},1 {$x2},{$y2} L{$xi2},{$yi2} A{$innerR},{$innerR} 0 {$large},0 {$xi1},{$yi1} Z\" fill=\"{$color}\" />";
      $angle += $sweep; $i++;
    }
    return $svg;
  }
@endphp

{{-- ── HEADER ── --}}
<div class="header">
  <div class="header-left">
    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('img/Raya - Logo.png'))) }}" alt="Raya Tender" />
  </div>
  <div class="header-right">
    <div class="doc-title">Laporan Proposal</div>
    <div class="doc-sub">
      PT. Raya Konstruksi Internasional
      <span>·</span>
      Dicetak: {{ now()->format('d F Y, H:i') }}
      @if(!empty($filters['start_date']))
        <span>·</span> Periode: {{ $filters['start_date'] }} s/d {{ $filters['end_date'] ?? now()->format('Y-m-d') }}
      @endif
      @if(!empty($filters['status']))
        <span>·</span> Status: {{ strtoupper($filters['status']) }}
      @endif
    </div>
  </div>
</div>

{{-- ── TABLE ── --}}
<div class="section-label">Daftar Proposal</div>
<table class="data">
  <thead>
    <tr>
      <th style="width:82px">Kode</th>
      <th>Judul Proposal</th>
      <th style="width:78px">Tender</th>
      <th>Klien</th>
      <th>PIC</th>
      <th>Status</th>
      <th style="width:36px">Versi</th>
      <th style="width:62px">Deadline</th>
    </tr>
  </thead>
  <tbody>
    @forelse($data['proposals'] as $p)
    @php
      $sk = 's-' . ($p->status?->value ?? 'default');
    @endphp
    <tr>
      <td class="td-code">{{ $p->code }}</td>
      <td class="td-title">{{ $p->title }}</td>
      <td class="td-code">{{ $p->tender?->code ?? '—' }}</td>
      <td>{{ $p->tender?->client?->name ?? '—' }}</td>
      <td>{{ $p->pic?->name ?? '—' }}</td>
      <td><span class="badge {{ $sk }}">{{ $p->status?->label() }}</span></td>
      <td><span class="version-pill">v{{ $p->current_version }}</span></td>
      <td class="td-muted">{{ $p->deadline?->format('d/m/Y') ?? '—' }}</td>
    </tr>
    @empty
    <tr class="empty-row"><td colspan="8">Tidak ada data proposal</td></tr>
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
      <div class="stat-val">{{ $data['draft'] }}</div>
      <div class="stat-lbl">Draft</div>
    </td>
    <td class="stats-cell c-orange">
      <div class="stat-val">{{ $data['internal_review'] }}</div>
      <div class="stat-lbl">Review</div>
    </td>
    <td class="stats-cell c-navy">
      <div class="stat-val">{{ $data['submitted'] }}</div>
      <div class="stat-lbl">Submitted</div>
    </td>
    <td class="stats-cell c-green">
      <div class="stat-val">{{ $data['final'] ?? 0 }}</div>
      <div class="stat-lbl">Final</div>
    </td>
  </tr>
</table>

{{-- ── CHARTS ── --}}
<div class="charts-row">

  {{-- Bar chart: Status Distribution --}}
  <div class="chart-box wide">
    <div class="chart-title">Distribusi Status Proposal</div>
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

  {{-- Donut: Top Clients --}}
  <div class="chart-box narrow">
    <div class="chart-title">Top Klien</div>
    @php
      $svgSize = 110; $cx = 55; $cy = 55; $r = 46; $innerR = 28;
      $totalC = array_sum($clientDist) ?: 1;
      $donutSvg = donutSlicesP($clientDist, $clientColors, $cx, $cy, $r, $innerR);
    @endphp
    <div class="donut-wrap">
      <svg width="{{ $svgSize }}" height="{{ $svgSize }}" viewBox="0 0 {{ $svgSize }} {{ $svgSize }}">
        {!! $donutSvg !!}
        <text x="{{ $cx }}" y="{{ $cy - 5 }}" text-anchor="middle" font-size="13" font-weight="700" fill="#101828">{{ $totalC }}</text>
        <text x="{{ $cx }}" y="{{ $cy + 9 }}" text-anchor="middle" font-size="7" fill="#98a2b3">proposal</text>
      </svg>
    </div>
    @foreach($clientDist as $name => $cnt)
    @php $color = $clientColors[array_search($name, array_keys($clientDist)) % count($clientColors)]; @endphp
    <div class="legend-item">
      <div class="legend-dot"><span style="background:{{ $color }};"></span></div>
      <div class="legend-name">{{ Str::limit($name, 20) }}</div>
      <div class="legend-cnt">{{ $cnt }} <span style="font-weight:400;color:#98a2b3">({{ round($cnt/$totalC*100) }}%)</span></div>
    </div>
    @endforeach
  </div>

</div>

{{-- ── FOOTER ── --}}
<div class="footer">
  <div class="footer-left">Sumber data: Raya Tender Management System</div>
  <div class="footer-right">Dokumen internal — data finansial tidak ditampilkan</div>
</div>

</body>
</html>
