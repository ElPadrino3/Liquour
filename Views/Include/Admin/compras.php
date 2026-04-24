<?php
require_once '../../../Config/Liquour_bdd.php';

$db = new BDD();
$conexion = $db->conectar();

$stmtKpi = $conexion->query("
    SELECT 
        IFNULL(SUM(total), 0) as total_mes, 
        COUNT(id_compra) as ordenes_mes,
        IFNULL(AVG(total), 0) as promedio_mes
    FROM compras 
    WHERE MONTH(fecha) = MONTH(CURRENT_DATE()) AND YEAR(fecha) = YEAR(CURRENT_DATE())
");
$kpi = $stmtKpi->fetch();

$stmtProv = $conexion->query("
    SELECT 
        (SELECT COUNT(*) FROM proveedores WHERE estado = 1) as activos,
        (SELECT COUNT(*) FROM proveedores) as totales
");
$provKpi = $stmtProv->fetch();

$stmtTopProv = $conexion->query("
    SELECT prov.nombre, COUNT(DISTINCT c.id_compra) as ordenes, SUM(dc.subtotal) as total_comprado 
    FROM detalle_compras dc 
    JOIN compras c ON dc.id_compra = c.id_compra 
    JOIN proveedores prov ON dc.id_proveedor = prov.id_proveedor 
    GROUP BY prov.id_proveedor 
    ORDER BY total_comprado DESC LIMIT 4
");
$topProveedores = $stmtTopProv->fetchAll();

$chartData = array_fill(0, 12, 0);
$stmtChart = $conexion->query("
    SELECT MONTH(fecha) as mes, SUM(total) as total_mes 
    FROM compras 
    WHERE YEAR(fecha) = YEAR(CURRENT_DATE()) 
    GROUP BY MONTH(fecha)
");
while($row = $stmtChart->fetch()) {
    $chartData[$row['mes'] - 1] = (float)$row['total_mes'];
}

$stmtOrders = $conexion->query("
    SELECT 
        IFNULL(CONCAT('ORD-', c.id_compra), 'N/A') as orden,
        IFNULL(DATE(c.fecha), 'Sin fecha') as fecha,
        IFNULL(prov.nombre, 'Sin proveedor') as proveedor,
        p.nombre as producto,
        IFNULL(dc.cantidad, 0) as qty,
        CONCAT('$', FORMAT(IFNULL(dc.precio_compra, 0), 2)) as precio,
        CONCAT('$', FORMAT(IFNULL(dc.subtotal, 0), 2)) as total,
        'Recibido' as estado 
    FROM productos p
    LEFT JOIN detalle_compras dc ON p.id_producto = dc.id_producto
    LEFT JOIN compras c ON dc.id_compra = c.id_compra
    LEFT JOIN proveedores prov ON dc.id_proveedor = prov.id_proveedor
    ORDER BY c.fecha DESC, p.nombre ASC
");
$ordersData = $stmtOrders->fetchAll();

$proveedoresFiltro = array_unique(array_column($ordersData, 'proveedor'));
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <script>
(function() {
    const coloresGuardados = localStorage.getItem('liquour_colors');
    if (coloresGuardados) {
        try {
            const colores = JSON.parse(coloresGuardados);
            const dorado = colores['--color-dorado'] || '#C5A059';
            const fondo = colores['--bg-carbon'] || '#1A1A1A';
            const texto = colores['--text-blanco-crema'] || '#F5F5DC';
            const borde = colores['--border-fuerte'] || '#4A4A4A';
            
            function lightenColor(hex, percent) {
                let r = parseInt(hex.slice(1, 3), 16);
                let g = parseInt(hex.slice(3, 5), 16);
                let b = parseInt(hex.slice(5, 7), 16);
                r = Math.min(255, r + (r * percent / 100));
                g = Math.min(255, g + (g * percent / 100));
                b = Math.min(255, b + (b * percent / 100));
                return '#' + Math.round(r).toString(16).padStart(2, '0') + 
                           Math.round(g).toString(16).padStart(2, '0') + 
                           Math.round(b).toString(16).padStart(2, '0');
            }
            
            function darkenColor(hex, percent) {
                let r = parseInt(hex.slice(1, 3), 16);
                let g = parseInt(hex.slice(3, 5), 16);
                let b = parseInt(hex.slice(5, 7), 16);
                r = Math.max(0, r - (r * percent / 100));
                g = Math.max(0, g - (g * percent / 100));
                b = Math.max(0, b - (b * percent / 100));
                return '#' + Math.round(r).toString(16).padStart(2, '0') + 
                           Math.round(g).toString(16).padStart(2, '0') + 
                           Math.round(b).toString(16).padStart(2, '0');
            }
            
            document.documentElement.style.setProperty('--gold', dorado);
            document.documentElement.style.setProperty('--gold-lt', lightenColor(dorado, 15));
            document.documentElement.style.setProperty('--gold-dk', darkenColor(dorado, 20));
            document.documentElement.style.setProperty('--carbon', fondo);
            document.documentElement.style.setProperty('--cream', texto);
            document.documentElement.style.setProperty('--oxford', borde);
            document.documentElement.style.setProperty('--border', dorado + '26');
            document.documentElement.style.setProperty('--border-md', dorado + '4D');
            
        } catch(e) {}
    }
})();

window.addEventListener('storage', function(e) {
    if (e.key === 'liquour_colors') location.reload();
});
</script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Compras | Liquour</title>
    <link rel="stylesheet" href="../../../Assets/CSS/-Catalogo_Admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../../../Assets/CSS/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body>

<?php @include '../../Layout/nav_admin.php'; ?> 

<div class="page">
  <div class="page-heading">Gestión de Compras</div>

  <div class="kpi-row">
    <div class="kpi">
      <div class="kpi-lbl">Total Comprado (Mes)</div>
      <div class="kpi-val"><sup>$</sup><?php echo number_format($kpi['total_mes'], 2); ?></div>
      <div class="kpi-sub"><?php echo $kpi['ordenes_mes']; ?> órdenes este mes</div>
      <div class="kpi-tag">Actualizado</div>
    </div>
    <div class="kpi">
      <div class="kpi-lbl">Órdenes Realizadas</div>
      <div class="kpi-val"><?php echo $kpi['ordenes_mes']; ?></div>
      <div class="kpi-sub">Total del mes actual</div>
      <div class="kpi-tag warn">Info</div>
    </div>
    <div class="kpi">
      <div class="kpi-lbl">Proveedores Activos</div>
      <div class="kpi-val"><?php echo $provKpi['activos']; ?></div>
      <div class="kpi-sub">De <?php echo $provKpi['totales']; ?> registrados</div>
      <div class="kpi-tag">Sistema</div>
    </div>
    <div class="kpi">
      <div class="kpi-lbl">Promedio por Orden</div>
      <div class="kpi-val"><sup>$</sup><?php echo number_format($kpi['promedio_mes'], 2); ?></div>
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
      </div>
      <div class="prov-list">
        <?php foreach($topProveedores as $prov): 
            $iniciales = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $prov['nombre']), 0, 2));
        ?>
        <div class="prov-item">
          <div class="prov-left">
            <div class="prov-av"><?php echo htmlspecialchars($iniciales); ?></div>
            <div><div class="prov-name"><?php echo htmlspecialchars($prov['nombre']); ?></div><div class="prov-cat">Proveedor</div></div>
          </div>
          <div class="prov-right"><div class="prov-total">$<?php echo number_format($prov['total_comprado'], 2); ?></div><div class="prov-orders"><?php echo $prov['ordenes']; ?> órdenes</div></div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <div class="card" style="padding:0">
    <div class="toolbar">
      <div class="toolbar-left">
        <input class="search-input" type="text" placeholder="Buscar orden o producto…" id="searchInput" />
        <select class="filter-select" id="statusFilter">
          <option value="">Todos los estados</option>
          <option value="Recibido">Recibido</option>
          <option value="Pendiente">Pendiente</option>
          <option value="Cancelado">Cancelado</option>
        </select>
        <select class="filter-select" id="provFilter">
          <option value="">Todos los proveedores</option>
          <?php foreach($proveedoresFiltro as $pf): ?>
            <option value="<?php echo htmlspecialchars($pf); ?>"><?php echo htmlspecialchars($pf); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <button type="button" class="export-btn" onclick="imprimirReporteCompras()">↓ Exportar PDF</button>
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
      <div class="page-info" id="pageInfo">Página 1 de 1</div>
      <div class="page-btns" id="pageBtns"></div>
    </div>
  </div>
</div>

<script>
const ORDERS = <?php echo json_encode($ordersData); ?>;
const CHART_DATA = <?php echo json_encode($chartData); ?>;
const ROWS = 7;
let page = 1, search = '', statusF = '', provF = '';
const BADGE = { Recibido:'badge-ok', Pendiente:'badge-pending', Cancelado:'badge-cancel' };

function imprimirReporteCompras() {
    const busqueda = document.getElementById('searchInput').value;
    const proveedor = document.getElementById('provFilter').value;
    const url = `../../../../Controller/Public/ReporteController.php?reporte=compras&busqueda=${encodeURIComponent(busqueda)}&vendedor=${encodeURIComponent(proveedor)}`;
    window.open(url, '_blank');
}

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
  const tbody = document.getElementById('tableBody');
  if(slice.length === 0) {
      tbody.innerHTML = '<tr><td colspan="8" style="text-align:center; padding:20px;">No hay registros encontrados.</td></tr>';
  } else {
      tbody.innerHTML = slice.map(r => `
        <tr>
          <td class="td-id">${r.orden}</td>
          <td class="td-date">${r.fecha}</td>
          <td>${r.proveedor}</td>
          <td style="color:var(--cream);font-weight:500">${r.producto}</td>
          <td style="text-align:center">${r.qty}</td>
          <td>${r.precio}</td>
          <td class="td-price">${r.total}</td>
          <td><span class="badge ${BADGE[r.estado] || 'badge-ok'}">${r.estado}</span></td>
        </tr>`).join('');
  }
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
      data: CHART_DATA,
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
<script id="ordersData" type="application/json"><?php echo json_encode($ordersData); ?></script>
<script id="chartData" type="application/json"><?php echo json_encode($chartData); ?></script>
<script src="../../../Assets/JS/Compras.js"></script>
</body>
</html>