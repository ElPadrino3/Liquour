<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Liquour — Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<style>
  *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

  :root {
    --carbon:    #1A1A1A;
    --gold:      #C5A059;
    --gold-lt:   #D4B577;
    --gold-dk:   #9A7A3F;
    --oxford:    #4A4A4A;
    --cream:     #F5F5DC;
    --surface:   #242424;
    --surface2:  #2E2E2E;
    --border:    rgba(197,160,89,.14);
    --border-md: rgba(197,160,89,.28);
  }

  html, body { height: 100%; background: var(--carbon); color: var(--cream); font-family: 'Montserrat', sans-serif; font-size: 13px; }

  /* ─── LAYOUT ─────────────────────────────────── */
  .app { display: grid; grid-template-columns: 220px 1fr 250px; grid-template-rows: 58px 1fr; height: 100vh; overflow: hidden; }

  /* ─── SIDEBAR ─────────────────────────────────── */
  .sidebar {
    grid-row: 1 / 3;
    background: #0f0f0f;
    border-right: 1px solid var(--border);
    display: flex; flex-direction: column;
    padding: 24px 16px;
    gap: 2px;
  }

  .logo { display: flex; align-items: center; gap: 12px; padding: 0 6px; margin-bottom: 28px; }
  .logo-ring {
    width: 44px; height: 44px; border-radius: 50%;
    border: 1.5px solid var(--gold);
    display: flex; align-items: center; justify-content: center;
    font-family: 'Cormorant Garamond', serif; font-size: 20px; color: var(--gold);
    flex-shrink: 0;
  }
  .logo-text { font-family: 'Cormorant Garamond', serif; font-size: 18px; letter-spacing: 3px; color: var(--cream); }
  .logo-sub  { font-size: 8px; letter-spacing: 3px; color: var(--gold-dk); text-transform: uppercase; }

  .nav-label { font-size: 8px; letter-spacing: 3px; color: var(--oxford); text-transform: uppercase; padding: 14px 10px 4px; }

  .nav-item {
    display: flex; align-items: center; gap: 12px;
    padding: 9px 12px; border-radius: 7px; cursor: pointer;
    font-size: 11px; letter-spacing: 1px; color: rgba(245,245,220,.45);
    text-transform: uppercase; border: 1px solid transparent;
    transition: background .2s, color .2s;
  }
  .nav-item:hover { background: rgba(197,160,89,.07); color: rgba(245,245,220,.8); }
  .nav-item.active { background: rgba(197,160,89,.12); color: var(--gold); border-color: rgba(197,160,89,.22); }

  .nav-dot { width: 5px; height: 5px; border-radius: 50%; background: var(--oxford); flex-shrink: 0; }
  .nav-item.active .nav-dot { background: var(--gold); }

  .sidebar-spacer { flex: 1; }

  .profile { display: flex; align-items: center; gap: 10px; padding: 12px; border-radius: 8px; background: var(--surface); border: 1px solid var(--border); }
  .profile-avatar { width: 34px; height: 34px; border-radius: 50%; background: rgba(197,160,89,.18); border: 1px solid var(--gold-dk); display: flex; align-items: center; justify-content: center; font-size: 10px; color: var(--gold); font-weight: 600; flex-shrink: 0; }
  .profile-name { font-size: 11px; color: var(--cream); font-weight: 500; }
  .profile-role { font-size: 9px; color: var(--oxford); letter-spacing: 1px; margin-top: 1px; }

  /* ─── TOPBAR ─────────────────────────────────── */
  .topbar {
    background: #0f0f0f;
    border-bottom: 1px solid var(--border);
    display: flex; align-items: center; justify-content: space-between;
    padding: 0 26px;
  }
  .page-title { font-family: 'Cormorant Garamond', serif; font-size: 22px; font-weight: 400; letter-spacing: 2px; }
  .page-title em { color: var(--gold); font-style: normal; }

  .topbar-right { display: flex; align-items: center; gap: 10px; }

  .search {
    background: var(--surface); border: 1px solid var(--border);
    border-radius: 18px; padding: 6px 14px; width: 190px;
    font-family: 'Montserrat', sans-serif; font-size: 11px;
    color: var(--cream); outline: none;
  }
  .search::placeholder { color: var(--oxford); }

  .pill { background: rgba(197,160,89,.1); border: 1px solid var(--border-md); border-radius: 18px; padding: 5px 12px; font-size: 9px; letter-spacing: 1px; color: var(--gold); }

  .icon-btn {
    width: 33px; height: 33px; border-radius: 50%;
    background: var(--surface); border: 1px solid var(--border);
    display: flex; align-items: center; justify-content: center; cursor: pointer; position: relative;
  }
  .icon-btn svg { width: 14px; height: 14px; }
  .badge-dot { position: absolute; top: 5px; right: 5px; width: 7px; height: 7px; background: var(--gold); border-radius: 50%; border: 1.5px solid #0f0f0f; }

  /* ─── MAIN ─────────────────────────────────── */
  .main { overflow-y: auto; padding: 22px 22px; display: flex; flex-direction: column; gap: 18px; }

  /* KPI */
  .kpi-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; }
  .kpi {
    background: var(--surface); border: 1px solid var(--border); border-radius: 10px;
    padding: 18px 20px; position: relative; overflow: hidden;
    transition: border-color .2s;
  }
  .kpi:hover { border-color: var(--border-md); }
  .kpi::after { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px; background: linear-gradient(90deg,transparent,var(--gold),transparent); opacity: .4; }
  .kpi-label { font-size: 8px; letter-spacing: 3px; color: var(--oxford); text-transform: uppercase; margin-bottom: 8px; }
  .kpi-value { font-family: 'Cormorant Garamond', serif; font-size: 32px; font-weight: 400; color: var(--cream); line-height: 1; }
  .kpi-value sup { font-size: 16px; color: var(--gold); vertical-align: top; margin-top: 5px; }
  .kpi-meta { font-size: 10px; color: var(--oxford); margin-top: 5px; }
  .kpi-tag { display: inline-block; margin-top: 6px; background: rgba(197,160,89,.12); border: 1px solid rgba(197,160,89,.22); border-radius: 10px; padding: 2px 8px; font-size: 9px; letter-spacing: 1px; color: var(--gold); }

  /* CHARTS */
  .charts-row { display: grid; grid-template-columns: 1.65fr 1fr; gap: 14px; }
  .card { background: var(--surface); border: 1px solid var(--border); border-radius: 10px; padding: 18px 20px; }
  .card-title { font-size: 8px; letter-spacing: 3px; color: var(--oxford); text-transform: uppercase; margin-bottom: 14px; display: flex; justify-content: space-between; align-items: center; }
  .legend { display: flex; gap: 14px; }
  .leg-item { display: flex; align-items: center; gap: 5px; font-size: 9px; color: var(--oxford); }
  .leg-dot { width: 6px; height: 6px; border-radius: 50%; }

  /* TABLE */
  .tbl-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; }
  .tbl-btn { font-size: 9px; color: var(--gold); letter-spacing: 1px; text-transform: uppercase; border: 1px solid var(--border-md); padding: 4px 10px; border-radius: 12px; cursor: pointer; background: none; font-family: inherit; }
  table { width: 100%; border-collapse: collapse; }
  thead tr { border-bottom: 1px solid rgba(197,160,89,.09); }
  th { font-size: 8px; letter-spacing: 2px; color: var(--oxford); text-transform: uppercase; padding: 0 12px 9px; text-align: left; font-weight: 400; }
  td { font-size: 11px; color: rgba(245,245,220,.7); padding: 9px 12px; border-bottom: 1px solid rgba(255,255,255,.035); }
  tbody tr:last-child td { border-bottom: none; }
  tbody tr:hover td { color: var(--cream); background: rgba(197,160,89,.04); }
  .avatar-sm { display: inline-flex; align-items: center; justify-content: center; width: 26px; height: 26px; border-radius: 50%; background: rgba(197,160,89,.1); border: 1px solid var(--border-md); font-size: 8px; color: var(--gold); margin-right: 8px; vertical-align: middle; font-weight: 600; }
  .price { color: var(--gold); font-weight: 500; }
  .cat { background: rgba(197,160,89,.08); border: 1px solid var(--border); border-radius: 10px; padding: 2px 7px; font-size: 9px; color: var(--gold-dk); }
  .unit-wrap { display: flex; align-items: center; gap: 8px; }
  .bar-bg { flex: 1; height: 3px; background: rgba(255,255,255,.05); border-radius: 2px; }
  .bar-fg { height: 100%; background: var(--gold); border-radius: 2px; opacity: .65; }

  /* ─── RIGHT PANEL ─────────────────────────────────── */
  .panel {
    background: #0f0f0f;
    border-left: 1px solid var(--border);
    display: flex; flex-direction: column; gap: 18px;
    padding: 22px 16px;
    overflow-y: auto;
  }

  .panel-title { font-size: 8px; letter-spacing: 3px; color: var(--oxford); text-transform: uppercase; margin-bottom: 10px; }

  /* stock list */
  .stock-box { background: rgba(197,160,89,.06); border: 1px solid rgba(197,160,89,.18); border-radius: 9px; padding: 12px 14px; }
  .stock-box-title { font-size: 11px; color: var(--gold); font-weight: 500; margin-bottom: 10px; }
  .stock-row { display: flex; align-items: center; justify-content: space-between; padding: 7px 0; border-bottom: 1px solid rgba(197,160,89,.06); }
  .stock-row:last-child { border-bottom: none; }
  .stock-info { display: flex; align-items: center; gap: 8px; }
  .status-dot { width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; }
  .red    { background: #c05050; }
  .amber  { background: var(--gold); }
  .green  { background: #5a9c6e; }
  .stock-name { font-size: 10px; color: var(--cream); }
  .stock-sub  { font-size: 9px; color: var(--oxford); margin-top: 1px; }
  .stock-qty  { font-family: 'Cormorant Garamond', serif; font-size: 18px; color: var(--gold); }

  /* mini grid */
  .mini-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
  .mini-card { background: var(--surface2); border-radius: 8px; padding: 10px 12px; border: 1px solid rgba(255,255,255,.04); }
  .mini-lbl { font-size: 8px; letter-spacing: 2px; color: var(--oxford); text-transform: uppercase; margin-bottom: 4px; }
  .mini-val { font-family: 'Cormorant Garamond', serif; font-size: 20px; color: var(--cream); }

  /* activity */
  .activity { display: flex; flex-direction: column; gap: 8px; }
  .act-item { display: flex; align-items: flex-start; gap: 10px; padding: 9px 10px; background: var(--surface); border-radius: 8px; border: 1px solid rgba(255,255,255,.04); }
  .act-icon { width: 28px; height: 28px; border-radius: 50%; background: rgba(197,160,89,.1); border: 1px solid var(--border-md); display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 11px; color: var(--gold); }
  .act-text { font-size: 10px; color: rgba(245,245,220,.65); line-height: 1.5; }
  .act-time { font-size: 9px; color: var(--oxford); margin-top: 2px; }

  .divider { height: 1px; background: var(--border); }

  /* scrollbars */
  ::-webkit-scrollbar { width: 4px; }
  ::-webkit-scrollbar-track { background: transparent; }
  ::-webkit-scrollbar-thumb { background: rgba(197,160,89,.2); border-radius: 4px; }
</style>
</head>
<body>
<div class="app">

  <!-- ═══ SIDEBAR ═══ -->
  <aside class="sidebar">
    <div class="logo">
      <div class="logo-ring">L</div>
      <div>
        <div class="logo-text">LIQUOUR</div>
        <div class="logo-sub">Premium Spirits</div>
      </div>
    </div>

    <div class="nav-label">Principal</div>
    <div class="nav-item active"><div class="nav-dot"></div>Dashboard</div>
    <div class="nav-item"><div class="nav-dot"></div>Inventario</div>
    <div class="nav-item"><div class="nav-dot"></div>Ventas</div>
    <div class="nav-item"><div class="nav-dot"></div>Reservas</div>

    <div class="nav-label">Gestión</div>
    <div class="nav-item"><div class="nav-dot"></div>Clientes</div>
    <div class="nav-item"><div class="nav-dot"></div>Proveedores</div>
    <div class="nav-item"><div class="nav-dot"></div>Reportes</div>

    <div class="nav-label">Sistema</div>
    <div class="nav-item"><div class="nav-dot"></div>Configuración</div>

    <div class="sidebar-spacer"></div>

    <div class="profile">
      <div class="profile-avatar">AG</div>
      <div>
        <div class="profile-name">Admin General</div>
        <div class="profile-role">Gerente · En línea</div>
      </div>
    </div>
  </aside>

  <!-- ═══ TOPBAR ═══ -->
  <header class="topbar">
    <div class="page-title">Panel de <em>Control</em></div>
    <div class="topbar-right">
      <input class="search" type="text" placeholder="Buscar productos…" />
      <div class="pill">Mar 19, 2026</div>
      <div class="icon-btn">
        <svg viewBox="0 0 24 24" fill="none" stroke="#C5A059" stroke-width="1.6" stroke-linecap="round">
          <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>
        </svg>
        <div class="badge-dot"></div>
      </div>
      <div class="icon-btn">
        <svg viewBox="0 0 24 24" fill="none" stroke="#C5A059" stroke-width="1.6" stroke-linecap="round">
          <circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/>
        </svg>
      </div>
    </div>
  </header>

  <!-- ═══ MAIN ═══ -->
  <main class="main">

    <!-- KPI -->
    <div class="kpi-row">
      <div class="kpi">
        <div class="kpi-label">Ventas Hoy</div>
        <div class="kpi-value"><sup>$</sup>245.50</div>
        <div class="kpi-meta">32 transacciones</div>
        <div class="kpi-tag">↑ 12% vs ayer</div>
      </div>
      <div class="kpi">
        <div class="kpi-label">Última Semana</div>
        <div class="kpi-value"><sup>$</sup>1,580.75</div>
        <div class="kpi-meta">224 transacciones</div>
        <div class="kpi-tag">↑ 8% vs anterior</div>
      </div>
      <div class="kpi">
        <div class="kpi-label">Este Mes</div>
        <div class="kpi-value"><sup>$</sup>3,674.25</div>
        <div class="kpi-meta">Meta: $2,980.06</div>
        <div class="kpi-tag">✓ Meta superada</div>
      </div>
    </div>

    <!-- CHARTS -->
    <div class="charts-row">
      <div class="card">
        <div class="card-title">
          Ventas Mensuales
          <div class="legend">
            <div class="leg-item"><div class="leg-dot" style="background:var(--gold)"></div>Este mes</div>
            <div class="leg-item"><div class="leg-dot" style="background:var(--oxford)"></div>Mes pasado</div>
          </div>
        </div>
        <canvas id="lineChart" height="110"></canvas>
      </div>
      <div class="card">
        <div class="card-title">Ventas Semanales</div>
        <canvas id="barChart" height="110"></canvas>
      </div>
    </div>

    <!-- TABLE -->
    <div class="card">
      <div class="tbl-header">
        <div class="card-title" style="margin:0">5 Productos Más Vendidos</div>
        <button class="tbl-btn">Ver todos</button>
      </div>
      <table>
        <thead>
          <tr>
            <th>Producto</th>
            <th>Precio</th>
            <th>Categoría</th>
            <th>Unidades vendidas</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><span class="avatar-sm">JD</span>Jack Daniel's</td>
            <td class="price">$20.00</td>
            <td><span class="cat">Whiskey</span></td>
            <td><div class="unit-wrap">120<div class="bar-bg"><div class="bar-fg" style="width:100%"></div></div></div></td>
          </tr>
          <tr>
            <td><span class="avatar-sm">JW</span>Johnnie Walker Black</td>
            <td class="price">$30.00</td>
            <td><span class="cat">Whiskey</span></td>
            <td><div class="unit-wrap">95<div class="bar-bg"><div class="bar-fg" style="width:79%"></div></div></div></td>
          </tr>
          <tr>
            <td><span class="avatar-sm">AV</span>Absolut Vodka</td>
            <td class="price">$18.00</td>
            <td><span class="cat">Vodka</span></td>
            <td><div class="unit-wrap">87<div class="bar-bg"><div class="bar-fg" style="width:72%"></div></div></div></td>
          </tr>
          <tr>
            <td><span class="avatar-sm">BC</span>Bacardí Carta Blanca</td>
            <td class="price">$15.00</td>
            <td><span class="cat">Ron</span></td>
            <td><div class="unit-wrap">74<div class="bar-bg"><div class="bar-fg" style="width:62%"></div></div></div></td>
          </tr>
          <tr>
            <td><span class="avatar-sm">MC</span>Moët &amp; Chandon</td>
            <td class="price">$55.00</td>
            <td><span class="cat">Champagne</span></td>
            <td><div class="unit-wrap">60<div class="bar-bg"><div class="bar-fg" style="width:50%"></div></div></div></td>
          </tr>
        </tbody>
      </table>
    </div>

  </main>

  <!-- ═══ RIGHT PANEL ═══ -->
  <aside class="panel">

    <div>
      <div class="panel-title">Stock Crítico</div>
      <div class="stock-box">
        <div class="stock-box-title">Próximos a Agotarse</div>
        <div class="stock-row">
          <div class="stock-info">
            <div class="status-dot red"></div>
            <div><div class="stock-name">Absolut Vodka</div><div class="stock-sub">Última unidad</div></div>
          </div>
          <div class="stock-qty">6</div>
        </div>
        <div class="stock-row">
          <div class="stock-info">
            <div class="status-dot red"></div>
            <div><div class="stock-name">Chivas Regal 18</div><div class="stock-sub">Stock mínimo</div></div>
          </div>
          <div class="stock-qty">11</div>
        </div>
        <div class="stock-row">
          <div class="stock-info">
            <div class="status-dot amber"></div>
            <div><div class="stock-name">Buchanan's 12</div><div class="stock-sub">Reponer pronto</div></div>
          </div>
          <div class="stock-qty">8</div>
        </div>
        <div class="stock-row">
          <div class="stock-info">
            <div class="status-dot green"></div>
            <div><div class="stock-name">Moët &amp; Chandon</div><div class="stock-sub">Nivel aceptable</div></div>
          </div>
          <div class="stock-qty">66</div>
        </div>
      </div>
    </div>

    <div class="divider"></div>

    <div>
      <div class="panel-title">Resumen Rápido</div>
      <div class="mini-grid">
        <div class="mini-card"><div class="mini-lbl">Productos</div><div class="mini-val">148</div></div>
        <div class="mini-card"><div class="mini-lbl">Categorías</div><div class="mini-val">12</div></div>
        <div class="mini-card"><div class="mini-lbl">Clientes</div><div class="mini-val">340</div></div>
        <div class="mini-card"><div class="mini-lbl">Pedidos</div><div class="mini-val">27</div></div>
      </div>
    </div>

    <div class="divider"></div>

    <div>
      <div class="panel-title">Actividad Reciente</div>
      <div class="activity">
        <div class="act-item">
          <div class="act-icon">↑</div>
          <div>
            <div class="act-text">Venta: Chivas Regal 18 × 2 confirmada</div>
            <div class="act-time">Hace 5 minutos</div>
          </div>
        </div>
        <div class="act-item">
          <div class="act-icon">!</div>
          <div>
            <div class="act-text">Stock bajo en 3 productos detectado</div>
            <div class="act-time">Hace 22 minutos</div>
          </div>
        </div>
        <div class="act-item">
          <div class="act-icon">✓</div>
          <div>
            <div class="act-text">Pedido #1042 entregado correctamente</div>
            <div class="act-time">Hace 1 hora</div>
          </div>
        </div>
        <div class="act-item">
          <div class="act-icon">+</div>
          <div>
            <div class="act-text">Nuevo cliente registrado: R. Morales</div>
            <div class="act-time">Hace 2 horas</div>
          </div>
        </div>
      </div>
    </div>

  </aside>

</div><!-- /.app -->

<script>
  Chart.defaults.color = '#4A4A4A';
  Chart.defaults.font.family = 'Montserrat';
  Chart.defaults.font.size = 10;

  const gold   = '#C5A059';
  const oxford = '#4A4A4A';

  /* Line chart */
  new Chart(document.getElementById('lineChart'), {
    type: 'line',
    data: {
      labels: ['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'],
      datasets: [
        {
          label: 'Este mes',
          data: [420, 580, 510, 690, 750, 870, 920],
          borderColor: gold,
          backgroundColor: 'rgba(197,160,89,.08)',
          borderWidth: 2,
          pointBackgroundColor: gold,
          pointRadius: 3.5,
          tension: 0.42,
          fill: true
        },
        {
          label: 'Mes pasado',
          data: [350, 490, 430, 600, 620, 710, 780],
          borderColor: oxford,
          backgroundColor: 'transparent',
          borderWidth: 1.5,
          borderDash: [4, 4],
          pointBackgroundColor: oxford,
          pointRadius: 2,
          tension: 0.42
        }
      ]
    },
    options: {
      responsive: true,
      plugins: { legend: { display: false } },
      scales: {
        x: { grid: { color: 'rgba(255,255,255,.03)' }, ticks: { color: oxford } },
        y: { grid: { color: 'rgba(255,255,255,.03)' }, ticks: { color: oxford, callback: v => '$'+v } }
      }
    }
  });

  /* Bar chart */
  new Chart(document.getElementById('barChart'), {
    type: 'bar',
    data: {
      labels: ['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'],
      datasets: [{
        data: [180, 240, 210, 290, 320, 410, 380],
        backgroundColor: 'rgba(197,160,89,.22)',
        borderColor: gold,
        borderWidth: 1,
        borderRadius: 4
      }]
    },
    options: {
      responsive: true,
      plugins: { legend: { display: false } },
      scales: {
        x: { grid: { display: false }, ticks: { color: oxford } },
        y: { grid: { color: 'rgba(255,255,255,.03)' }, ticks: { color: oxford } }
      }
    }
  });
</script>
</body>
</html>