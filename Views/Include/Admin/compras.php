<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Liquour — Compras</title>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="../../../Assets/CSS/style.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body>

<?php include '../../Layout/header_admin.php'; ?>

<div class="page">

  <div class="page-heading">Gestión de Compras</div>

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