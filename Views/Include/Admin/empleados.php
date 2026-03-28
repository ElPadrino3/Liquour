<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Liquour — Empleados</title>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../../../Assets/CSS/style.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body>

<?php include '../../Layout/header_admin.php'; ?>

<div class="page">
  <div class="page-heading">Gestión de Empleados</div>

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
  { nombre:'Juan Perez',  rol:'Vendedor Senior', ingreso:'2021-03-15', turno:'Mañana',  ventas:'$4,218', ordenes:48, meta:84, estado:'Activo' },
  { nombre:'Marp Perez',  rol:'Vendedor',        ingreso:'2022-07-01', turno:'Tarde',   ventas:'$3,100', ordenes:35, meta:62, estado:'Activo' },
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