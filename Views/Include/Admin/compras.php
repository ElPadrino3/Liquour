<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
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
    // LiquourThemeManager en nav_admin.php ahora controla el tema
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
      <button type="button" class="export-btn" onclick="openExportModal()">↓ Exportar PDF</button>
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
            <th style="text-align:center">Acciones</th>
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

<!-- Modal para Previsualizar Compra -->
<div id="previewModal" class="modal-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:1000; align-items:center; justify-content:center;">
  <div class="modal-content" style="background:var(--carbon); border:1px solid var(--gold); border-radius:8px; width:400px; padding:20px; box-shadow:0 10px 30px rgba(0,0,0,0.5); font-family:'Montserrat', sans-serif;">
    <h3 style="color:var(--gold); margin-top:0; border-bottom:1px solid var(--border); padding-bottom:10px;">Detalles de la Compra</h3>
    <div id="modalDetails" style="color:var(--cream); font-size:14px; line-height:1.6;">
      <!-- Contenido dinámico -->
    </div>
    <div style="text-align:right; margin-top:20px;">
      <button onclick="closeModal()" style="background:transparent; border:1px solid var(--gold); color:var(--gold); padding:8px 16px; border-radius:4px; cursor:pointer; transition:0.3s;">Cerrar</button>
      <button id="modalPrintBtn" style="background:var(--gold); border:none; color:var(--carbon); padding:8px 16px; border-radius:4px; cursor:pointer; font-weight:600; margin-left:10px;">Imprimir PDF</button>
    </div>
  </div>
</div>

<!-- Modal de Exportación PDF -->
<div id="exportModal" class="modal-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:1000; align-items:center; justify-content:center;">
  <div class="modal-content" style="background:var(--carbon); border:1px solid var(--gold); border-radius:8px; width:350px; padding:20px; box-shadow:0 10px 30px rgba(0,0,0,0.5); font-family:'Montserrat', sans-serif;">
    <h3 style="color:var(--gold); margin-top:0; border-bottom:1px solid var(--border); padding-bottom:10px;">Opciones de Exportación</h3>
    <div style="margin:20px 0;">
      <label style="color:var(--cream); display:block; margin-bottom:8px;">Selecciona el periodo:</label>
      <select id="exportMonth" style="width:100%; padding:10px; background:#1A1A1A; color:var(--cream); border:1px solid var(--gold); border-radius:4px; outline:none; font-family:'Montserrat', sans-serif;">
        <option value="">Todo el histórico</option>
        <option value="1">Enero</option>
        <option value="2">Febrero</option>
        <option value="3">Marzo</option>
        <option value="4">Abril</option>
        <option value="5">Mayo</option>
        <option value="6">Junio</option>
        <option value="7">Julio</option>
        <option value="8">Agosto</option>
        <option value="9">Septiembre</option>
        <option value="10">Octubre</option>
        <option value="11">Noviembre</option>
        <option value="12">Diciembre</option>
      </select>
    </div>
    <div style="text-align:right;">
      <button onclick="closeExportModal()" style="background:transparent; border:1px solid var(--gold); color:var(--gold); padding:8px 16px; border-radius:4px; cursor:pointer; transition:0.3s;">Cancelar</button>
      <button id="confirmExportBtn" style="background:var(--gold); border:none; color:var(--carbon); padding:8px 16px; border-radius:4px; cursor:pointer; font-weight:600; margin-left:10px;">Exportar</button>
    </div>
  </div>
</div>

<script>
const ORDERS = <?php echo json_encode($ordersData); ?>;
const CHART_DATA = <?php echo json_encode($chartData); ?>;
const ROWS = 7;
let page = 1, search = '', statusF = '', provF = '';
const BADGE = { Recibido:'badge-ok', Pendiente:'badge-pending', Cancelado:'badge-cancel' };

function openExportModal() {
    document.getElementById('exportModal').style.display = 'flex';
    document.getElementById('confirmExportBtn').onclick = function() {
        const mes = document.getElementById('exportMonth').value;
        const busqueda = document.getElementById('searchInput').value;
        const proveedor = document.getElementById('provFilter').value;
        const url = `../../../Controller/Public/generar_pdf.php?reporte=compras&busqueda=${encodeURIComponent(busqueda)}&vendedor=${encodeURIComponent(proveedor)}&mes=${encodeURIComponent(mes)}`;
        window.open(url, '_blank');
        closeExportModal();
    };
}

function closeExportModal() {
    document.getElementById('exportModal').style.display = 'none';
}

function imprimirItem(id) {
    const url = `../../../Controller/Public/generar_pdf.php?reporte=compras&id=${encodeURIComponent(id)}`;
    window.open(url, '_blank');
}

function previewItem(btn) {
    const data = JSON.parse(btn.getAttribute('data-json'));
    const id = data.orden.replace('ORD-', '');
    
    document.getElementById('modalDetails').innerHTML = `
      <p><strong>Orden:</strong> ${data.orden}</p>
      <p><strong>Fecha:</strong> ${data.fecha}</p>
      <p><strong>Proveedor:</strong> ${data.proveedor}</p>
      <p><strong>Producto:</strong> ${data.producto}</p>
      <p><strong>Cantidad:</strong> ${data.qty}</p>
      <p><strong>Precio Unitario:</strong> ${data.precio}</p>
      <p><strong>Total:</strong> <span style="color:var(--gold); font-weight:bold;">${data.total}</span></p>
      <p><strong>Estado:</strong> ${data.estado}</p>
    `;
    
    document.getElementById('modalPrintBtn').onclick = function() { imprimirItem(id); };
    document.getElementById('previewModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('previewModal').style.display = 'none';
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
          <td style="text-align:center;">
             <button class="action-btn" onclick="previewItem(this)" data-json='${JSON.stringify(r).replace(/'/g, "&#39;")}' title="Ver Detalles" style="background:transparent; border:none; cursor:pointer; color:var(--gold); display:inline-flex; align-items:center; justify-content:center; padding:4px; transition:0.2s;" onmouseover="this.style.color='var(--cream)'" onmouseout="this.style.color='var(--gold)'">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
             </button>
             <button class="action-btn" onclick="imprimirItem('${r.orden.replace('ORD-','')}')" title="Imprimir PDF" style="background:transparent; border:none; cursor:pointer; color:var(--gold); display:inline-flex; align-items:center; justify-content:center; padding:4px; margin-left:5px; transition:0.2s;" onmouseover="this.style.color='var(--cream)'" onmouseout="this.style.color='var(--gold)'">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
             </button>
          </td>
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
</body>
</html>