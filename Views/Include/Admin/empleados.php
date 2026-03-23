<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Liquour — Empleados</title>
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
    --border:    rgba(197,160,89,.15);
    --border-md: rgba(197,160,89,.30);
  }
  html, body { min-height: 100vh; background: var(--carbon); color: var(--cream); font-family: 'Montserrat', sans-serif; font-size: 13px; }
 
  /* TOPBAR */
  .topbar { background: #0f0f0f; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; padding: 0 32px; height: 62px; }
  .logo { display: flex; align-items: center; gap: 12px; text-decoration: none; }
  .logo-ring { width: 44px; height: 44px; border-radius: 50%; border: 1.5px solid var(--gold); display: flex; align-items: center; justify-content: center; font-family: 'Cormorant Garamond', serif; font-size: 20px; color: var(--gold); }
  .logo-text { font-family: 'Cormorant Garamond', serif; font-size: 18px; letter-spacing: 3px; color: var(--cream); }
  .logo-sub  { font-size: 8px; letter-spacing: 3px; color: var(--gold-dk); text-transform: uppercase; }
  .nav-links { display: flex; gap: 6px; }
  .nav-link { padding: 8px 22px; border: 1px solid var(--border-md); border-radius: 6px; font-size: 10px; letter-spacing: 2px; text-transform: uppercase; color: var(--cream); cursor: pointer; background: none; font-family: 'Montserrat', sans-serif; transition: background .2s, color .2s; }
  .nav-link:hover, .nav-link.active { background: rgba(197,160,89,.15); border-color: var(--gold); color: var(--gold); }
 
  /* PAGE */
  .page { padding: 28px 32px; max-width: 1200px; margin: 0 auto; display: flex; flex-direction: column; gap: 20px; }
 
  .page-heading { font-family: 'Cormorant Garamond', serif; font-size: 26px; letter-spacing: 5px; font-weight: 400; color: var(--gold); text-transform: uppercase; text-align: center; position: relative; margin-bottom: 4px; }
  .page-heading::after { content: ''; display: block; width: 60px; height: 1px; background: var(--gold); opacity: .4; margin: 8px auto 0; }
 
  /* KPI */
  .kpi-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; }
  .kpi { background: var(--surface); border: 1px solid var(--border); border-radius: 10px; padding: 16px 18px; position: relative; overflow: hidden; transition: border-color .2s; }
  .kpi:hover { border-color: var(--border-md); }
  .kpi::after { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px; background: linear-gradient(90deg,transparent,var(--gold),transparent); opacity: .4; }
  .kpi-lbl { font-size: 8px; letter-spacing: 3px; color: var(--oxford); text-transform: uppercase; margin-bottom: 7px; }
  .kpi-val { font-family: 'Cormorant Garamond', serif; font-size: 28px; color: var(--cream); line-height: 1; }
  .kpi-val sup { font-size: 14px; color: var(--gold); vertical-align: top; margin-top: 4px; }
  .kpi-sub { font-size: 9px; color: var(--oxford); margin-top: 5px; }
  .kpi-tag { display: inline-block; margin-top: 5px; background: rgba(197,160,89,.1); border: 1px solid rgba(197,160,89,.2); border-radius: 10px; padding: 2px 8px; font-size: 9px; letter-spacing: 1px; color: var(--gold); }
 
  /* GRID */
  .two-col  { display: grid; grid-template-columns: 1fr 1.4fr; gap: 16px; }
  .three-col { display: grid; grid-template-columns: repeat(3,1fr); gap: 16px; }
 
  /* CARD */
  .card { background: var(--surface); border: 1px solid var(--border); border-radius: 10px; padding: 18px 20px; }
  .card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; }
  .card-title { font-size: 8px; letter-spacing: 3px; color: var(--oxford); text-transform: uppercase; }
  .card-action { font-size: 9px; color: var(--gold); letter-spacing: 1px; text-transform: uppercase; border: 1px solid var(--border-md); padding: 4px 10px; border-radius: 12px; cursor: pointer; background: none; font-family: inherit; transition: background .2s; }
  .card-action:hover { background: rgba(197,160,89,.12); }
 
  /* EMPLEADO CARDS */
  .emp-grid { display: grid; grid-template-columns: repeat(auto-fill,minmax(220px,1fr)); gap: 14px; }
  .emp-card { background: var(--surface); border: 1px solid var(--border); border-radius: 10px; padding: 20px 16px; text-align: center; position: relative; overflow: hidden; transition: border-color .2s, transform .2s; cursor: pointer; }
  .emp-card:hover { border-color: var(--border-md); transform: translateY(-2px); }
  .emp-card::after { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px; background: linear-gradient(90deg,transparent,var(--gold),transparent); opacity: .35; }
  .emp-avatar { width: 58px; height: 58px; border-radius: 50%; background: rgba(197,160,89,.12); border: 2px solid var(--border-md); display: flex; align-items: center; justify-content: center; font-size: 18px; color: var(--gold); font-weight: 600; margin: 0 auto 10px; font-family: 'Cormorant Garamond', serif; }
  .emp-name { font-size: 13px; color: var(--cream); font-weight: 500; margin-bottom: 3px; }
  .emp-role { font-size: 9px; color: var(--oxford); letter-spacing: 2px; text-transform: uppercase; margin-bottom: 10px; }
  .emp-stats { display: flex; justify-content: space-around; padding-top: 10px; border-top: 1px solid var(--border); }
  .emp-stat-lbl { font-size: 8px; color: var(--oxford); letter-spacing: 1px; text-transform: uppercase; margin-bottom: 3px; }
  .emp-stat-val { font-family: 'Cormorant Garamond', serif; font-size: 17px; color: var(--gold); }
  .status-pill { display: inline-block; padding: 2px 10px; border-radius: 10px; font-size: 8px; letter-spacing: 1px; margin-bottom: 8px; }
  .active   { background: rgba(90,156,110,.12); border: 1px solid rgba(90,156,110,.25); color: #7dc896; }
  .inactive { background: rgba(192,80,80,.1);   border: 1px solid rgba(192,80,80,.25);  color: #e08080; }
  .vacation { background: rgba(197,160,89,.12); border: 1px solid rgba(197,160,89,.25); color: var(--gold); }
 
  /* TABLE */
  .tbl-wrap { overflow-x: auto; max-height: 300px; overflow-y: auto; }
  .tbl-wrap::-webkit-scrollbar { width: 4px; }
  .tbl-wrap::-webkit-scrollbar-thumb { background: rgba(197,160,89,.2); border-radius: 4px; }
  table { width: 100%; border-collapse: collapse; min-width: 560px; }
  thead { position: sticky; top: 0; z-index: 2; }
  thead tr { background: var(--surface2); }
  th { font-size: 8px; letter-spacing: 2px; color: var(--oxford); text-transform: uppercase; padding: 10px 14px; text-align: left; font-weight: 500; border-bottom: 1px solid var(--border); white-space: nowrap; }
  td { font-size: 11px; color: rgba(245,245,220,.72); padding: 9px 14px; border-bottom: 1px solid rgba(255,255,255,.035); white-space: nowrap; }
  tbody tr:last-child td { border-bottom: none; }
  tbody tr:hover td { color: var(--cream); background: rgba(197,160,89,.04); }
  .td-gold { color: var(--gold); font-weight: 500; }
  .badge { display: inline-block; padding: 2px 9px; border-radius: 10px; font-size: 9px; letter-spacing: 1px; font-weight: 500; }
 
  /* PROGRESS BAR */
  .prog-wrap { display: flex; align-items: center; gap: 8px; }
  .prog-bg { flex: 1; height: 4px; background: rgba(255,255,255,.06); border-radius: 2px; }
  .prog-fg { height: 100%; background: var(--gold); border-radius: 2px; opacity: .7; }
 
  /* TOOLBAR */
  .toolbar { display: flex; align-items: center; justify-content: space-between; padding: 12px 20px; border-bottom: 1px solid var(--border); gap: 10px; flex-wrap: wrap; }
  .toolbar-left { display: flex; gap: 8px; align-items: center; }
  .search-input { background: var(--surface2); border: 1px solid var(--border); border-radius: 18px; padding: 6px 14px; width: 190px; font-family: 'Montserrat', sans-serif; font-size: 11px; color: var(--cream); outline: none; }
  .search-input::placeholder { color: var(--oxford); }
  .filter-select { background: var(--surface2); border: 1px solid var(--border); border-radius: 18px; padding: 6px 12px; font-family: 'Montserrat', sans-serif; font-size: 10px; color: var(--cream); outline: none; cursor: pointer; letter-spacing: 1px; }
  .filter-select option { background: var(--surface2); }
  .add-btn { background: rgba(197,160,89,.15); border: 1px solid var(--border-md); border-radius: 18px; padding: 6px 14px; font-family: 'Montserrat', sans-serif; font-size: 9px; letter-spacing: 1.5px; color: var(--gold); text-transform: uppercase; cursor: pointer; transition: background .2s; }
  .add-btn:hover { background: rgba(197,160,89,.25); }
 
  /* RANKING */
  .rank-list { display: flex; flex-direction: column; gap: 10px; }
  .rank-item { display: flex; align-items: center; gap: 12px; }
  .rank-num { font-family: 'Cormorant Garamond', serif; font-size: 20px; color: rgba(197,160,89,.35); width: 20px; text-align: center; flex-shrink: 0; }
  .rank-av { width: 30px; height: 30px; border-radius: 50%; background: rgba(197,160,89,.1); border: 1px solid var(--border-md); display: flex; align-items: center; justify-content: center; font-size: 10px; color: var(--gold); font-weight: 600; flex-shrink: 0; }
  .rank-info { flex: 1; }
  .rank-name { font-size: 11px; color: var(--cream); font-weight: 500; }
  .rank-sub  { font-size: 9px; color: var(--oxford); margin-top: 2px; }
  .rank-bar-bg { height: 3px; background: rgba(255,255,255,.05); border-radius: 2px; margin-top: 4px; }
  .rank-bar-fg { height: 100%; background: var(--gold); border-radius: 2px; opacity: .65; }
  .rank-val { font-family: 'Cormorant Garamond', serif; font-size: 16px; color: var(--gold); text-align: right; flex-shrink: 0; }
 
  ::-webkit-scrollbar { width: 4px; }
  ::-webkit-scrollbar-track { background: transparent; }
  ::-webkit-scrollbar-thumb { background: rgba(197,160,89,.2); border-radius: 4px; }
</style>
</head>
<body>
 
<header class="topbar">
  <a class="logo" href="#">
    <div class="logo-ring">L</div>
    <div>
      <div class="logo-text">LIQUOUR</div>
      <div class="logo-sub">Premium Spirits</div>
    </div>
  </a>
  <nav class="nav-links">
    <button class="nav-link">Home</button>
    <button class="nav-link">Compras</button>
    <button class="nav-link active">Empleados</button>
    <button class="nav-link">Reportes</button>
  </nav>
</header>
 
<div class="page">
 
  <div class="page-heading">Gestión de Empleados</div>
 
  <!-- KPIs -->
  <div class="kpi-row">
    <div class="kpi">
      <div class="kpi-lbl">Total Empleados</div>
      <div class="kpi-val">8</div>
      <div class="kpi-sub">6 activos · 1 vacaciones · 1 inactivo</div>
      <div class="kpi-tag">Planilla completa</div>
    </div>
    <div class="kpi">
      <div class="kpi-lbl">Ventas del Equipo (Mes)</div>
      <div class="kpi-val"><sup>$</sup>12,840</div>
      <div class="kpi-sub">Suma total del equipo</div>
      <div class="kpi-tag">↑ 9% vs anterior</div>
    </div>
    <div class="kpi">
      <div class="kpi-lbl">Mejor Vendedor</div>
      <div class="kpi-val">Juan P.</div>
      <div class="kpi-sub">$4,218 este mes</div>
      <div class="kpi-tag">★ Top 1</div>
    </div>
    <div class="kpi">
      <div class="kpi-lbl">Horas Promedio / Sem.</div>
      <div class="kpi-val">38.4</div>
      <div class="kpi-sub">hrs por empleado activo</div>
      <div class="kpi-tag">Estable</div>
    </div>
  </div>
 
  <!-- EMPLOYEE CARDS -->
  <div class="card">
    <div class="card-header">
      <div class="card-title">Equipo</div>
      <button class="add-btn">+ Nuevo Empleado</button>
    </div>
    <div class="emp-grid">
      <div class="emp-card">
        <div class="emp-avatar">JP</div>
        <div class="emp-name">Juan Perez</div>
        <div class="emp-role">Vendedor Senior</div>
        <div class="status-pill active">Activo</div>
        <div class="emp-stats">
          <div><div class="emp-stat-lbl">Ventas</div><div class="emp-stat-val">$4,218</div></div>
          <div><div class="emp-stat-lbl">Órdenes</div><div class="emp-stat-val">48</div></div>
          <div><div class="emp-stat-lbl">Hrs/Sem</div><div class="emp-stat-val">42</div></div>
        </div>
      </div>
      <div class="emp-card">
        <div class="emp-avatar">MP</div>
        <div class="emp-name">Marp Perez</div>
        <div class="emp-role">Vendedor</div>
        <div class="status-pill active">Activo</div>
        <div class="emp-stats">
          <div><div class="emp-stat-lbl">Ventas</div><div class="emp-stat-val">$3,100</div></div>
          <div><div class="emp-stat-lbl">Órdenes</div><div class="emp-stat-val">35</div></div>
          <div><div class="emp-stat-lbl">Hrs/Sem</div><div class="emp-stat-val">40</div></div>
        </div>
      </div>
      <div class="emp-card">
        <div class="emp-avatar">CR</div>
        <div class="emp-name">Carlos Rivas</div>
        <div class="emp-role">Cajero</div>
        <div class="status-pill active">Activo</div>
        <div class="emp-stats">
          <div><div class="emp-stat-lbl">Ventas</div><div class="emp-stat-val">$2,460</div></div>
          <div><div class="emp-stat-lbl">Órdenes</div><div class="emp-stat-val">28</div></div>
          <div><div class="emp-stat-lbl">Hrs/Sem</div><div class="emp-stat-val">38</div></div>
        </div>
      </div>
      <div class="emp-card">
        <div class="emp-avatar">SL</div>
        <div class="emp-name">Sara López</div>
        <div class="emp-role">Supervisora</div>
        <div class="status-pill active">Activo</div>
        <div class="emp-stats">
          <div><div class="emp-stat-lbl">Ventas</div><div class="emp-stat-val">$1,820</div></div>
          <div><div class="emp-stat-lbl">Órdenes</div><div class="emp-stat-val">20</div></div>
          <div><div class="emp-stat-lbl">Hrs/Sem</div><div class="emp-stat-val">40</div></div>
        </div>
      </div>
      <div class="emp-card">
        <div class="emp-avatar">RM</div>
        <div class="emp-name">Roberto Mejía</div>
        <div class="emp-role">Almacenero</div>
        <div class="status-pill vacation">Vacaciones</div>
        <div class="emp-stats">
          <div><div class="emp-stat-lbl">Ventas</div><div class="emp-stat-val">$842</div></div>
          <div><div class="emp-stat-lbl">Órdenes</div><div class="emp-stat-val">10</div></div>
          <div><div class="emp-stat-lbl">Hrs/Sem</div><div class="emp-stat-val">—</div></div>
        </div>
      </div>
      <div class="emp-card">
        <div class="emp-avatar">AG</div>
        <div class="emp-name">Ana Guzmán</div>
        <div class="emp-role">Vendedor</div>
        <div class="status-pill active">Activo</div>
        <div class="emp-stats">
          <div><div class="emp-stat-lbl">Ventas</div><div class="emp-stat-val">$400</div></div>
          <div><div class="emp-stat-lbl">Órdenes</div><div class="emp-stat-val">5</div></div>
          <div><div class="emp-stat-lbl">Hrs/Sem</div><div class="emp-stat-val">36</div></div>
        </div>
      </div>
    </div>
  </div>
 
  <!-- CHART + RANKING -->
  <div class="two-col">
    <div class="card">
      <div class="card-header">
        <div class="card-title">Ventas por Empleado (Este Mes)</div>
      </div>
      <canvas id="empChart" height="170"></canvas>
    </div>
    <div class="card">
      <div class="card-header">
        <div class="card-title">Ranking de Ventas</div>
      </div>
      <div class="rank-list">
        <div class="rank-item">
          <div class="rank-num">1</div>
          <div class="rank-av">JP</div>
          <div class="rank-info">
            <div class="rank-name">Juan Perez</div>
            <div class="rank-sub">48 órdenes · Vendedor Senior</div>
            <div class="rank-bar-bg"><div class="rank-bar-fg" style="width:100%"></div></div>
          </div>
          <div class="rank-val">$4,218</div>
        </div>
        <div class="rank-item">
          <div class="rank-num">2</div>
          <div class="rank-av">MP</div>
          <div class="rank-info">
            <div class="rank-name">Marp Perez</div>
            <div class="rank-sub">35 órdenes · Vendedor</div>
            <div class="rank-bar-bg"><div class="rank-bar-fg" style="width:73%"></div></div>
          </div>
          <div class="rank-val">$3,100</div>
        </div>
        <div class="rank-item">
          <div class="rank-num">3</div>
          <div class="rank-av">CR</div>
          <div class="rank-info">
            <div class="rank-name">Carlos Rivas</div>
            <div class="rank-sub">28 órdenes · Cajero</div>
            <div class="rank-bar-bg"><div class="rank-bar-fg" style="width:58%"></div></div>
          </div>
          <div class="rank-val">$2,460</div>
        </div>
        <div class="rank-item">
          <div class="rank-num">4</div>
          <div class="rank-av">SL</div>
          <div class="rank-info">
            <div class="rank-name">Sara López</div>
            <div class="rank-sub">20 órdenes · Supervisora</div>
            <div class="rank-bar-bg"><div class="rank-bar-fg" style="width:43%"></div></div>
          </div>
          <div class="rank-val">$1,820</div>
        </div>
        <div class="rank-item">
          <div class="rank-num">5</div>
          <div class="rank-av">RM</div>
          <div class="rank-info">
            <div class="rank-name">Roberto Mejía</div>
            <div class="rank-sub">10 órdenes · Almacenero</div>
            <div class="rank-bar-bg"><div class="rank-bar-fg" style="width:20%"></div></div>
          </div>
          <div class="rank-val">$842</div>
        </div>
      </div>
    </div>
  </div>
 
  <!-- TABLE -->
  <div class="card" style="padding:0">
    <div class="toolbar">
      <div class="toolbar-left">
        <input class="search-input" type="text" placeholder="Buscar empleado…" id="searchInput" />
        <select class="filter-select" id="roleFilter">
          <option value="">Todos los roles</option>
          <option value="Vendedor Senior">Vendedor Senior</option>
          <option value="Vendedor">Vendedor</option>
          <option value="Cajero">Cajero</option>
          <option value="Supervisora">Supervisora</option>
          <option value="Almacenero">Almacenero</option>
        </select>
        <select class="filter-select" id="statusFilter">
          <option value="">Todos los estados</option>
          <option value="Activo">Activo</option>
          <option value="Vacaciones">Vacaciones</option>
          <option value="Inactivo">Inactivo</option>
        </select>
      </div>
      <button class="add-btn">+ Agregar</button>
    </div>
    <div class="tbl-wrap">
      <table>
        <thead>
          <tr>
            <th>Empleado</th>
            <th>Rol</th>
            <th>Ingreso</th>
            <th>Turno</th>
            <th>Ventas Mes</th>
            <th>Órdenes</th>
            <th>Progreso Meta</th>
            <th>Estado</th>
          </tr>
        </thead>
        <tbody id="tableBody"></tbody>
      </table>
    </div>
  </div>
 
</div>
 
<script>
const EMP = [
  { nombre:'Juan Perez',    rol:'Vendedor Senior', ingreso:'2021-03-15', turno:'Mañana',  ventas:'$4,218', ordenes:48, meta:84, estado:'Activo' },
  { nombre:'Marp Perez',    rol:'Vendedor',        ingreso:'2022-07-01', turno:'Tarde',   ventas:'$3,100', ordenes:35, meta:62, estado:'Activo' },
  { nombre:'Carlos Rivas',  rol:'Cajero',          ingreso:'2023-01-10', turno:'Mañana',  ventas:'$2,460', ordenes:28, meta:49, estado:'Activo' },
  { nombre:'Sara López',    rol:'Supervisora',     ingreso:'2020-05-20', turno:'Completo',ventas:'$1,820', ordenes:20, meta:36, estado:'Activo' },
  { nombre:'Roberto Mejía', rol:'Almacenero',      ingreso:'2022-11-03', turno:'—',       ventas:'$842',   ordenes:10, meta:17, estado:'Vacaciones' },
  { nombre:'Ana Guzmán',    rol:'Vendedor',        ingreso:'2024-08-15', turno:'Tarde',   ventas:'$400',   ordenes:5,  meta:8,  estado:'Activo' },
  { nombre:'Luis Torres',   rol:'Cajero',          ingreso:'2019-09-01', turno:'—',       ventas:'$0',     ordenes:0,  meta:0,  estado:'Inactivo' },
  { nombre:'Diana Cruz',    rol:'Vendedor',        ingreso:'2023-06-12', turno:'Mañana',  ventas:'$1,200', ordenes:15, meta:24, estado:'Activo' },
];
 
const SBADGE = { Activo:'active', Vacaciones:'vacation', Inactivo:'inactive' };
const SLBL   = { Activo:'Activo', Vacaciones:'Vacaciones', Inactivo:'Inactivo' };
 
function initials(n){ return n.split(' ').map(w=>w[0]).join('').slice(0,2).toUpperCase(); }
 
let search='', roleF='', statusF='';
 
function filtered(){
  return EMP.filter(r=>{
    const q = search.toLowerCase();
    const m = !q || Object.values(r).some(v=>String(v).toLowerCase().includes(q));
    return m && (!roleF||r.rol===roleF) && (!statusF||r.estado===statusF);
  });
}
 
function render(){
  document.getElementById('tableBody').innerHTML = filtered().map(r=>`
    <tr>
      <td>
        <div style="display:flex;align-items:center;gap:9px">
          <div style="width:28px;height:28px;border-radius:50%;background:rgba(197,160,89,.1);border:1px solid rgba(197,160,89,.3);display:flex;align-items:center;justify-content:center;font-size:9px;color:var(--gold);font-weight:600;flex-shrink:0">${initials(r.nombre)}</div>
          <span style="color:var(--cream);font-weight:500">${r.nombre}</span>
        </div>
      </td>
      <td>${r.rol}</td>
      <td class style="color:var(--oxford);font-size:10px;letter-spacing:1px">${r.ingreso}</td>
      <td>${r.turno}</td>
      <td class="td-gold">${r.ventas}</td>
      <td style="text-align:center">${r.ordenes}</td>
      <td>
        <div class="prog-wrap">${r.meta}%
          <div class="prog-bg"><div class="prog-fg" style="width:${r.meta}%"></div></div>
        </div>
      </td>
      <td><span class="badge status-pill ${SBADGE[r.estado]}">${SLBL[r.estado]}</span></td>
    </tr>`).join('');
}
 
document.getElementById('searchInput').addEventListener('input', e=>{search=e.target.value;render();});
document.getElementById('roleFilter').addEventListener('change', e=>{roleF=e.target.value;render();});
document.getElementById('statusFilter').addEventListener('change', e=>{statusF=e.target.value;render();});
render();
 
// Chart
Chart.defaults.color='#4A4A4A'; Chart.defaults.font.family='Montserrat'; Chart.defaults.font.size=10;
new Chart(document.getElementById('empChart'),{
  type:'bar',
  data:{
    labels:['Juan P.','Marp P.','Carlos R.','Sara L.','Diana C.','Roberto M.','Ana G.'],
    datasets:[{
      data:[4218,3100,2460,1820,1200,842,400],
      backgroundColor:'rgba(197,160,89,.2)',
      borderColor:'#C5A059',borderWidth:1,borderRadius:5
    }]
  },
  options:{
    indexAxis:'y',
    responsive:true,
    plugins:{legend:{display:false}},
    scales:{
      x:{grid:{color:'rgba(255,255,255,.03)'},ticks:{color:'#4A4A4A',callback:v=>'$'+v.toLocaleString()}},
      y:{grid:{display:false},ticks:{color:'#9A7A3F'}}
    }
  }
});
</script>
</body>
</html>