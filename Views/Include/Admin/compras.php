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
        CONCAT('ORD-', c.id_compra) as orden,
        DATE(c.fecha) as fecha,
        prov.nombre as proveedor,
        p.nombre as producto,
        dc.cantidad as qty,
        CONCAT('$', FORMAT(dc.precio_compra, 2)) as precio,
        CONCAT('$', FORMAT(dc.subtotal, 2)) as total,
        'Recibido' as estado 
    FROM detalle_compras dc
    JOIN compras c ON dc.id_compra = c.id_compra
    JOIN productos p ON dc.id_producto = p.id_producto
    JOIN proveedores prov ON dc.id_proveedor = prov.id_proveedor
    ORDER BY c.fecha DESC
");
$ordersData = $stmtOrders->fetchAll();

$proveedoresFiltro = array_unique(array_column($ordersData, 'proveedor'));
?>
<!DOCTYPE html>
<html lang="es">
<head>
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
      <div class="kpi-lbl">Órdenes Pendientes</div>
      <div class="kpi-val">0</div>
      <div class="kpi-sub">En espera de entrega</div>
      <div class="kpi-tag warn">Revisar</div>
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
        <button class="card-action">Ver todos</button>
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
        <input class="search-input" type="text" placeholder="Buscar orden…" id="searchInput" />
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
      tbody.innerHTML = '<tr><td colspan="8" style="text-align:center; padding:20px;">No hay compras registradas.</td></tr>';
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

<script src="../../../Assets/JS/Catalogo_Admin.js"></script>

</body>
</html>