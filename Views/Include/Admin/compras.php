<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Liquour — Compras</title>
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



  /* PAGE */
  .page { padding: 28px 32px; max-width: 1200px; margin: 0 auto; display: flex; flex-direction: column; gap: 20px; }

  .page-heading { font-family: 'Cormorant Garamond', serif; font-size: 26px; letter-spacing: 5px; font-weight: 400; color: var(--gold); text-transform: uppercase; text-align: center; position: relative; margin-bottom: 4px; }
  .page-heading::after { content: ''; display: block; width: 60px; height: 1px; background: var(--gold); opacity: .4; margin: 8px auto 0; }

  /* KPI ROW */
  .kpi-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; }
  .kpi { background: var(--surface); border: 1px solid var(--border); border-radius: 10px; padding: 16px 18px; position: relative; overflow: hidden; transition: border-color .2s; }
  .kpi:hover { border-color: var(--border-md); }
  .kpi::after { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px; background: linear-gradient(90deg,transparent,var(--gold),transparent); opacity: .4; }
  .kpi-lbl { font-size: 8px; letter-spacing: 3px; color: var(--oxford); text-transform: uppercase; margin-bottom: 7px; }
  .kpi-val { font-family: 'Cormorant Garamond', serif; font-size: 28px; color: var(--cream); line-height: 1; }
  .kpi-val sup { font-size: 14px; color: var(--gold); vertical-align: top; margin-top: 4px; }
  .kpi-sub { font-size: 9px; color: var(--oxford); margin-top: 5px; }
  .kpi-tag { display: inline-block; margin-top: 5px; background: rgba(197,160,89,.1); border: 1px solid rgba(197,160,89,.2); border-radius: 10px; padding: 2px 8px; font-size: 9px; letter-spacing: 1px; color: var(--gold); }
  .kpi-tag.warn { background: rgba(192,80,80,.1); border-color: rgba(192,80,80,.3); color: #e08080; }

  /* TWO COL */
  .two-col { display: grid; grid-template-columns: 1.5fr 1fr; gap: 16px; }

  /* CARD */
  .card { background: var(--surface); border: 1px solid var(--border); border-radius: 10px; padding: 18px 20px; }
  .card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; }
  .card-title { font-size: 8px; letter-spacing: 3px; color: var(--oxford); text-transform: uppercase; }
  .card-action { font-size: 9px; color: var(--gold); letter-spacing: 1px; text-transform: uppercase; border: 1px solid var(--border-md); padding: 4px 10px; border-radius: 12px; cursor: pointer; background: none; font-family: inherit; transition: background .2s; }
  .card-action:hover { background: rgba(197,160,89,.12); }

  /* STATUS BADGE */
  .badge { display: inline-block; padding: 2px 9px; border-radius: 10px; font-size: 9px; letter-spacing: 1px; font-weight: 500; }
  .badge-ok      { background: rgba(90,156,110,.12); border: 1px solid rgba(90,156,110,.25); color: #7dc896; }
  .badge-pending { background: rgba(197,160,89,.12); border: 1px solid rgba(197,160,89,.25); color: var(--gold); }
  .badge-cancel  { background: rgba(192,80,80,.1);   border: 1px solid rgba(192,80,80,.25);  color: #e08080; }

  /* TABLE */
  .tbl-wrap { overflow-x: auto; max-height: 340px; overflow-y: auto; }
  .tbl-wrap::-webkit-scrollbar { width: 4px; height: 4px; }
  .tbl-wrap::-webkit-scrollbar-thumb { background: rgba(197,160,89,.2); border-radius: 4px; }
  table { width: 100%; border-collapse: collapse; min-width: 600px; }
  thead { position: sticky; top: 0; z-index: 2; }
  thead tr { background: var(--surface2); }
  th { font-size: 8px; letter-spacing: 2px; color: var(--oxford); text-transform: uppercase; padding: 10px 14px; text-align: left; font-weight: 500; border-bottom: 1px solid var(--border); white-space: nowrap; cursor: pointer; }
  th:hover { color: var(--gold-lt); }
  td { font-size: 11px; color: rgba(245,245,220,.72); padding: 9px 14px; border-bottom: 1px solid rgba(255,255,255,.035); white-space: nowrap; }
  tbody tr:last-child td { border-bottom: none; }
  tbody tr:hover td { color: var(--cream); background: rgba(197,160,89,.04); }
  .td-id    { color: var(--gold-lt); font-weight: 500; }
  .td-price { color: var(--gold); font-weight: 500; }
  .td-date  { color: var(--oxford); font-size: 10px; letter-spacing: 1px; }

  /* TOOLBAR */
  .toolbar { display: flex; align-items: center; justify-content: space-between; padding: 12px 20px; border-bottom: 1px solid var(--border); gap: 10px; flex-wrap: wrap; }
  .toolbar-left { display: flex; gap: 8px; align-items: center; }
  .search-input { background: var(--surface2); border: 1px solid var(--border); border-radius: 18px; padding: 6px 14px; width: 190px; font-family: 'Montserrat', sans-serif; font-size: 11px; color: var(--cream); outline: none; }
  .search-input::placeholder { color: var(--oxford); }
  .filter-select { background: var(--surface2); border: 1px solid var(--border); border-radius: 18px; padding: 6px 12px; font-family: 'Montserrat', sans-serif; font-size: 10px; color: var(--cream); outline: none; cursor: pointer; letter-spacing: 1px; }
  .filter-select option { background: var(--surface2); }
  .export-btn { background: rgba(197,160,89,.12); border: 1px solid var(--border-md); border-radius: 18px; padding: 6px 14px; font-family: 'Montserrat', sans-serif; font-size: 9px; letter-spacing: 1.5px; color: var(--gold); text-transform: uppercase; cursor: pointer; transition: background .2s; }
  .export-btn:hover { background: rgba(197,160,89,.22); }

  /* PAGINATION */
  .pagination { display: flex; align-items: center; justify-content: space-between; padding: 10px 20px; border-top: 1px solid var(--border); flex-wrap: wrap; gap: 8px; }
  .page-info { font-size: 9px; color: var(--oxford); letter-spacing: 1px; }
  .page-btns { display: flex; gap: 4px; }
  .page-btn { width: 30px; height: 30px; border-radius: 6px; background: none; border: 1px solid var(--border); color: rgba(245,245,220,.5); font-size: 10px; cursor: pointer; font-family: 'Montserrat', sans-serif; display: flex; align-items: center; justify-content: center; transition: all .2s; }
  .page-btn:hover { border-color: var(--gold); color: var(--gold); }
  .page-btn.active { background: rgba(197,160,89,.15); border-color: var(--gold); color: var(--gold); }
  .page-btn:disabled { opacity: .25; cursor: default; }

  /* PROVEEDOR LIST */
  .prov-list { display: flex; flex-direction: column; gap: 8px; }
  .prov-item { display: flex; align-items: center; justify-content: space-between; padding: 10px 12px; background: var(--surface2); border-radius: 8px; border: 1px solid rgba(255,255,255,.04); transition: border-color .2s; }
  .prov-item:hover { border-color: var(--border); }
  .prov-left { display: flex; align-items: center; gap: 10px; }
  .prov-av { width: 32px; height: 32px; border-radius: 50%; background: rgba(197,160,89,.1); border: 1px solid var(--border-md); display: flex; align-items: center; justify-content: center; font-size: 10px; color: var(--gold); font-weight: 600; flex-shrink: 0; }
  .prov-name { font-size: 11px; color: var(--cream); font-weight: 500; }
  .prov-cat  { font-size: 9px; color: var(--oxford); margin-top: 1px; letter-spacing: 1px; }
  .prov-right { text-align: right; }
  .prov-total { font-family: 'Cormorant Garamond', serif; font-size: 18px; color: var(--gold); }
  .prov-orders { font-size: 9px; color: var(--oxford); }

  /* CHART */
  .chart-wrap { position: relative; }

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
    <button class="nav-link active">Compras</button>
    <button class="nav-link">Empleados</button>
    <button class="nav-link">Reportes</button>
  </nav>
</header>

<div class="page">

  <div class="page-heading">Gestión de Compras</div>

  <!-- KPIs -->
  <div class="kpi-row">
    <div class="kpi">
      <div class="kpi-lbl">Total Comprado (Mes)</div>
      <div class="kpi-val"><sup>$</sup>18,450.00</div>
      <div class="kpi-sub">32 órdenes este mes</div>
      <div class="kpi-tag">↑ 14% vs anterior</div>
    </div>
    <div class="kpi">
      <div class="kpi-lbl">Órdenes Pendientes</div>
      <div class="kpi-val">7</div>
      <div class="kpi-sub">En espera de entrega</div>
      <div class="kpi-tag warn">Revisar</div>
    </div>
    <div class="kpi">
      <div class="kpi-lbl">Proveedores Activos</div>
      <div class="kpi-val">5</div>
      <div class="kpi-sub">De 8 registrados</div>
      <div class="kpi-tag">↑ 1 nuevo</div>
    </div>
    <div class="kpi">
      <div class="kpi-lbl">Promedio por Orden</div>
      <div class="kpi-val"><sup>$</sup>576.56</div>
      <div class="kpi-sub">Basado en este mes</div>
      <div class="kpi-tag">Estable</div>
    </div>
  </div>

  <!-- CHART + PROVEEDORES -->
  <div class="two-col">
    <div class="card">
      <div class="card-header">
        <div class="card-title">Compras Mensuales</div>
      </div>
      <div class="chart-wrap">
        <canvas id="comprasChart" height="130"></canvas>
      </div>
    </div>
    <div class="card">
      <div class="card-header">
        <div class="card-title">Top Proveedores</div>
        <button class="card-action">Ver todos</button>
      </div>
      <div class="prov-list">
        <div class="prov-item">
          <div class="prov-left">
            <div class="prov-av">VA</div>
            <div><div class="prov-name">Viñas Andinas</div><div class="prov-cat">Vinos · Champagnes</div></div>
          </div>
          <div class="prov-right"><div class="prov-total">$6,200</div><div class="prov-orders">12 órdenes</div></div>
        </div>
        <div class="prov-item">
          <div class="prov-left">
            <div class="prov-av">DS</div>
            <div><div class="prov-name">Destilados Sur</div><div class="prov-cat">Whisky · Ron · Gin</div></div>
          </div>
          <div class="prov-right"><div class="prov-total">$4,850</div><div class="prov-orders">9 órdenes</div></div>
        </div>
        <div class="prov-item">
          <div class="prov-left">
            <div class="prov-av">PI</div>
            <div><div class="prov-name">Premiums Import</div><div class="prov-cat">Vodka · Tequila</div></div>
          </div>
          <div class="prov-right"><div class="prov-total">$3,900</div><div class="prov-orders">7 órdenes</div></div>
        </div>
        <div class="prov-item">
          <div class="prov-left">
            <div class="prov-av">CB</div>
            <div><div class="prov-name">Cerveza Brava</div><div class="prov-cat">Cervezas · Sidras</div></div>
          </div>
          <div class="prov-right"><div class="prov-total">$2,100</div><div class="prov-orders">4 órdenes</div></div>
        </div>
      </div>
    </div>
  </div>

  <!-- ORDERS TABLE -->
  <div class="card" style="padding:0">
    <div class="toolbar">
      <div class="toolbar-left">
        <input class="search-input" type="text" placeholder="Buscar orden…" id="searchInput" />
        <select class="filter-select" id="statusFilter">
          <option value="">Todos los estados</option>
          <option value="Recibido">Recibido</option>
          <option value="Pendiente">Pendiente</option>
          <option value="Cancelado">Cancelado</option>
        </select>
        <select class="filter-select" id="provFilter">
          <option value="">Todos los proveedores</option>
          <option value="Viñas Andinas">Viñas Andinas</option>
          <option value="Destilados Sur">Destilados Sur</option>
          <option value="Premiums Import">Premiums Import</option>
          <option value="Cerveza Brava">Cerveza Brava</option>
        </select>
      </div>
      <button class="export-btn">↓ Exportar CSV</button>
    </div>
    <div class="tbl-wrap">
      <table>
        <thead>
          <tr>
            <th>Orden #</th>
            <th>Fecha</th>
            <th>Proveedor</th>
            <th>Producto</th>
            <th style="text-align:center">Cantidad</th>
            <th>Precio Unit.</th>
            <th>Total</th>
            <th>Estado</th>
          </tr>
        </thead>
        <tbody id="tableBody"></tbody>
      </table>
    </div>
    <div class="pagination">
      <div class="page-info" id="pageInfo">Página 1 de 2</div>
      <div class="page-btns" id="pageBtns"></div>
    </div>
  </div>

</div>

<script>
const ORDERS = [
  { orden:'ORD-1001', fecha:'2024-12-24', proveedor:'Viñas Andinas',    producto:'Vino Tinto Reserva',  qty:50, precio:'$110.00', total:'$5,500.00', estado:'Recibido'  },
  { orden:'ORD-1002', fecha:'2024-12-22', proveedor:'Destilados Sur',   producto:'Whisky Single Malt',  qty:24, precio:'$80.00',  total:'$1,920.00', estado:'Recibido'  },
  { orden:'ORD-1003', fecha:'2024-12-20', proveedor:'Viñas Andinas',    producto:'Champagne Brut',      qty:72, precio:'$40.00',  total:'$2,880.00', estado:'Recibido'  },
  { orden:'ORD-1004', fecha:'2024-12-19', proveedor:'Premiums Import',  producto:'Vodka Premium',       qty:20, precio:'$35.00',  total:'$700.00',   estado:'Pendiente' },
  { orden:'ORD-1005', fecha:'2024-12-18', proveedor:'Destilados Sur',   producto:'Ron Añejo 12 Años',   qty:18, precio:'$55.00',  total:'$990.00',   estado:'Recibido'  },
  { orden:'ORD-1006', fecha:'2024-12-17', proveedor:'Premiums Import',  producto:'Tequila Reposado',    qty:12, precio:'$62.00',  total:'$744.00',   estado:'Pendiente' },
  { orden:'ORD-1007', fecha:'2024-12-16', proveedor:'Cerveza Brava',    producto:'Cerveza Artesanal',   qty:96, precio:'$8.00',   total:'$768.00',   estado:'Recibido'  },
  { orden:'ORD-1008', fecha:'2024-12-15', proveedor:'Destilados Sur',   producto:'Gin Botánico',        qty:15, precio:'$58.00',  total:'$870.00',   estado:'Pendiente' },
  { orden:'ORD-1009', fecha:'2024-12-13', proveedor:'Viñas Andinas',    producto:'Vino Blanco Reserva', qty:30, precio:'$95.00',  total:'$2,850.00', estado:'Cancelado' },
  { orden:'ORD-1010', fecha:'2024-12-10', proveedor:'Premiums Import',  producto:'Whisky Japonés',      qty:8,  precio:'$120.00', total:'$960.00',   estado:'Pendiente' },
  { orden:'ORD-1011', fecha:'2024-12-08', proveedor:'Cerveza Brava',    producto:'Sidra Premium',       qty:48, precio:'$12.00',  total:'$576.00',   estado:'Recibido'  },
  { orden:'ORD-1012', fecha:'2024-12-05', proveedor:'Destilados Sur',   producto:'Mezcal Artesanal',    qty:10, precio:'$85.00',  total:'$850.00',   estado:'Cancelado' },
];

const ROWS = 7;
let page = 1, search = '', statusF = '', provF = '';

const BADGE = { Recibido:'badge-ok', Pendiente:'badge-pending', Cancelado:'badge-cancel' };

function filtered() {
  return ORDERS.filter(r => {
    const q = search.toLowerCase();
    const match = !q || Object.values(r).some(v => String(v).toLowerCase().includes(q));
    return match && (!statusF || r.estado === statusF) && (!provF || r.proveedor === provF);
  });
}

function render() {
  const rows = filtered();
  const pages = Math.max(1, Math.ceil(rows.length / ROWS));
  if (page > pages) page = 1;
  const slice = rows.slice((page-1)*ROWS, page*ROWS);

  document.getElementById('tableBody').innerHTML = slice.map(r => `
    <tr>
      <td class="td-id">${r.orden}</td>
      <td class="td-date">${r.fecha}</td>
      <td>${r.proveedor}</td>
      <td style="color:var(--cream);font-weight:500">${r.producto}</td>
      <td style="text-align:center">${r.qty}</td>
      <td>${r.precio}</td>
      <td class="td-price">${r.total}</td>
      <td><span class="badge ${BADGE[r.estado]}">${r.estado}</span></td>
    </tr>`).join('');

  document.getElementById('pageInfo').textContent = `Página ${page} de ${pages}`;

  const pb = document.getElementById('pageBtns');
  pb.innerHTML = '';
  const prev = Object.assign(document.createElement('button'),{className:'page-btn',textContent:'‹',disabled:page===1});
  prev.onclick = ()=>{ page--; render(); };
  pb.appendChild(prev);
  for (let i=1;i<=pages;i++) {
    const b = Object.assign(document.createElement('button'),{className:'page-btn'+(i===page?' active':''),textContent:i});
    b.onclick=(()=>{const p=i;return()=>{page=p;render();}})();
    pb.appendChild(b);
  }
  const next = Object.assign(document.createElement('button'),{className:'page-btn',textContent:'›',disabled:page===pages});
  next.onclick=()=>{page++;render();};
  pb.appendChild(next);
}

document.getElementById('searchInput').addEventListener('input', e=>{search=e.target.value;page=1;render();});
document.getElementById('statusFilter').addEventListener('change', e=>{statusF=e.target.value;page=1;render();});
document.getElementById('provFilter').addEventListener('change', e=>{provF=e.target.value;page=1;render();});
render();

// Chart
Chart.defaults.color='#4A4A4A'; Chart.defaults.font.family='Montserrat'; Chart.defaults.font.size=10;
new Chart(document.getElementById('comprasChart'),{
  type:'bar',
  data:{
    labels:['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'],
    datasets:[{
      label:'Compras',
      data:[9200,8400,11000,10200,13500,12100,14800,13200,15600,16100,17200,18450],
      backgroundColor:'rgba(197,160,89,.22)',borderColor:'#C5A059',borderWidth:1,borderRadius:4
    }]
  },
  options:{
    responsive:true,
    plugins:{legend:{display:false}},
    scales:{
      x:{grid:{display:false},ticks:{color:'#4A4A4A'}},
      y:{grid:{color:'rgba(255,255,255,.03)'},ticks:{color:'#4A4A4A',callback:v=>'$'+v.toLocaleString()}}
    }
  }
});
</script>
</body>
</html>