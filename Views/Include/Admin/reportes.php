<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../../../Config/Liquour_bdd.php';

$db = new BDD();
$conexion = $db->conectar();

$stmtSum = $conexion->query("SELECT IFNULL(SUM(total), 0) as total_ventas, COUNT(id_venta) as transacciones, COUNT(DISTINCT id_usuario) as vendedores FROM ventas");
$summary = $stmtSum->fetch();
$totalVentas = $summary['total_ventas'];
$transacciones = $summary['transacciones'];
$vendedores_count = $summary['vendedores'];
$promedio = $transacciones > 0 ? $totalVentas / $transacciones : 0;

$stmtUsers = $conexion->query("SELECT DISTINCT u.nombre FROM ventas v JOIN usuarios u ON v.id_usuario = u.id_usuario");
$listaVendedores = $stmtUsers->fetchAll();

$stmtVentas = $conexion->query("
    SELECT DATE(v.fecha) as fecha, v.id_venta as id, p.nombre as producto, dv.cantidad, 
           CONCAT('$', FORMAT(dv.precio, 2)) as precio, CONCAT('$', FORMAT(dv.subtotal, 2)) as subtotal, u.nombre as vendedor
    FROM detalle_ventas dv
    JOIN ventas v ON dv.id_venta = v.id_venta
    JOIN productos p ON dv.id_producto = p.id_producto
    JOIN usuarios u ON v.id_usuario = u.id_usuario
    ORDER BY v.fecha DESC
");
$ventas = $stmtVentas->fetchAll();

$stmtProductos = $conexion->query("
    SELECT MAX(DATE(v.fecha)) as fecha, p.codigo_barras as id, p.nombre as producto, SUM(dv.cantidad) as cantidad, 
           CONCAT('$', FORMAT(p.precio_venta, 2)) as precio, CONCAT('$', FORMAT(SUM(dv.subtotal), 2)) as subtotal, '-' as vendedor
    FROM detalle_ventas dv
    JOIN productos p ON dv.id_producto = p.id_producto
    JOIN ventas v ON dv.id_venta = v.id_venta
    GROUP BY p.id_producto
    ORDER BY cantidad DESC
");
$productos = $stmtProductos->fetchAll();

$stmtInventario = $conexion->query("
    SELECT DATE(CURRENT_DATE) as fecha, p.codigo_barras as id, p.nombre as producto, p.stock as cantidad, 
           CONCAT('$', FORMAT(p.precio_venta, 2)) as precio, CONCAT('$', FORMAT(p.stock * p.precio_venta, 2)) as subtotal, 'Almacén' as vendedor
    FROM productos p
    ORDER BY p.stock DESC
");
$inventario = $stmtInventario->fetchAll();

$stmtCompras = $conexion->query("
    SELECT DATE(c.fecha) as fecha, c.id_compra as id, p.nombre as producto, dc.cantidad, 
           CONCAT('$', FORMAT(dc.precio_compra, 2)) as precio, CONCAT('$', FORMAT(dc.subtotal, 2)) as subtotal, prov.nombre as vendedor
    FROM detalle_compras dc
    JOIN compras c ON dc.id_compra = c.id_compra
    JOIN productos p ON dc.id_producto = p.id_producto
    JOIN proveedores prov ON dc.id_proveedor = prov.id_proveedor
    ORDER BY c.fecha DESC
");
$compras = $stmtCompras->fetchAll();

$DATA_DB = [
    'ventas' => $ventas ?: [],
    'productos' => $productos ?: [],
    'inventario' => $inventario ?: [],
    'compras' => $compras ?: []
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <script>
    // LiquourThemeManager en nav_admin.php ahora controla el tema
  </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes | Liquour</title>
    <link rel="stylesheet" href="../../../Assets/CSS/nav.css">
    <link rel="stylesheet" href="../../../Assets/CSS/-Catalogo_Admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../../../Assets/CSS/style.css">
</head>
<body>

<?php @include '../../Layout/nav_admin.php'; ?> 

<div class="page">
  <div class="section-heading">Seleccionar Reporte</div>

  <div class="tab-bar" id="tabBar">
    <button class="tab-btn active" data-tab="ventas">Historial de Venta</button>
    <button class="tab-btn" data-tab="productos">Productos Más Vendidos</button>
    <button class="tab-btn" data-tab="inventario">Inventario</button>
    <button class="tab-btn" data-tab="compras">Historial de Compras</button>
  </div>

  <div class="summary-row" id="summaryRow">
    <div class="sum-card"><div class="sum-lbl">Total Ventas</div><div class="sum-val"><span>$</span><?php echo number_format($totalVentas, 2); ?></div></div>
    <div class="sum-card"><div class="sum-lbl">Transacciones</div><div class="sum-val"><?php echo $transacciones; ?></div></div>
    <div class="sum-card"><div class="sum-lbl">Promedio / Venta</div><div class="sum-val"><span>$</span><?php echo number_format($promedio, 2); ?></div></div>
    <div class="sum-card"><div class="sum-lbl">Vendedores</div><div class="sum-val"><?php echo $vendedores_count; ?></div></div>
  </div>

  <div class="table-card">
    <div class="table-toolbar">
      <div class="toolbar-left">
        <input class="search-input" type="text" placeholder="Buscar…" id="searchInput" />
        <select class="filter-select" id="vendorFilter">
          <option value="">Todos los vendedores</option>
          <?php foreach($listaVendedores as $v): ?>
            <option value="<?php echo htmlspecialchars($v['nombre']); ?>"><?php echo htmlspecialchars($v['nombre']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="toolbar-right">
        <span class="record-count" id="recordCount">Mostrando 0 registros</span>
        <button class="export-btn" id="btnExportarPDF">↓ Exportar PDF</button>
      </div>
    </div>

    <div class="tbl-wrap">
      <table id="mainTable">
        <thead>
          <tr>
            <th data-col="fecha">Fecha <span class="sort-icon">↕</span></th>
            <th data-col="id">ID / Código <span class="sort-icon">↕</span></th>
            <th data-col="producto">Producto <span class="sort-icon">↕</span></th>
            <th data-col="cantidad" style="text-align:center">Cantidad <span class="sort-icon">↕</span></th>
            <th data-col="precio">Precio <span class="sort-icon">↕</span></th>
            <th data-col="subtotal">Subtotal <span class="sort-icon">↕</span></th>
            <th data-col="vendedor">Usuario / Relación <span class="sort-icon">↕</span></th>
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

<!-- Modal para Previsualizar Detalle -->
<div id="previewModal" class="modal-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:1000; align-items:center; justify-content:center;">
  <div class="modal-content" style="background:var(--carbon); border:1px solid var(--gold); border-radius:8px; width:400px; padding:20px; box-shadow:0 10px 30px rgba(0,0,0,0.5); font-family:'Montserrat', sans-serif;">
    <h3 style="color:var(--gold); margin-top:0; border-bottom:1px solid var(--border); padding-bottom:10px;">Detalles de la Transacción</h3>
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
const DATA = <?php echo json_encode($DATA_DB); ?>;
const ROWS_PER_PAGE = 7;
let currentTab = 'ventas';
let currentPage = 1;
let sortCol = null;
let sortAsc = true;
let searchTerm = '';
let vendorFilter = '';

function initials(name) {
    if (!name || name === '-' || name === 'Almacén') return 'LG';
    return name.split(' ').map(w => w[0]).join('').slice(0,2).toUpperCase();
}

function imprimirItem(id, tipo) {
    const url = `../../../Controller/Public/generar_pdf.php?reporte=${tipo}&id=${encodeURIComponent(id)}`;
    window.open(url, '_blank');
}

function previewItem(btn, tipo) {
    const data = JSON.parse(btn.getAttribute('data-json'));
    
    let tipoTexto = tipo.charAt(0).toUpperCase() + tipo.slice(1);
    let relacionLabel = (tipo === 'compras') ? 'Proveedor' : ((tipo === 'ventas') ? 'Vendedor' : 'Usuario/Relación');

    document.getElementById('modalDetails').innerHTML = `
      <p><strong>Tipo:</strong> ${tipoTexto}</p>
      <p><strong>ID/Código:</strong> ${data.id}</p>
      <p><strong>Fecha:</strong> ${data.fecha}</p>
      <p><strong>${relacionLabel}:</strong> ${data.vendedor}</p>
      <p><strong>Producto:</strong> ${data.producto}</p>
      <p><strong>Cantidad:</strong> ${data.cantidad}</p>
      <p><strong>Precio Unitario:</strong> ${data.precio}</p>
      <p><strong>Subtotal:</strong> <span style="color:var(--gold); font-weight:bold;">${data.subtotal}</span></p>
    `;
    
    document.getElementById('modalPrintBtn').onclick = function() { imprimirItem(data.id, tipo); };
    document.getElementById('previewModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('previewModal').style.display = 'none';
}

function getFiltered() {
    let rows = [...DATA[currentTab]];
    if (searchTerm) {
        const q = searchTerm.toLowerCase();
        rows = rows.filter(r => Object.values(r).some(v => String(v).toLowerCase().includes(q)));
    }
    if (vendorFilter) rows = rows.filter(r => r.vendedor === vendorFilter);
    if (sortCol) {
        rows.sort((a, b) => {
            let av = a[sortCol], bv = b[sortCol];
            if (typeof av === 'string' && av.includes('$')) av = parseFloat(av.replace(/[$,]/g,''));
            if (typeof bv === 'string' && bv.includes('$')) bv = parseFloat(bv.replace(/[$,]/g,''));
            return sortAsc ? (av > bv ? 1 : -1) : (av < bv ? 1 : -1);
        });
    }
    return rows;
}

function render() {
    const rows = getFiltered();
    const total = rows.length;
    const pages = Math.max(1, Math.ceil(total / ROWS_PER_PAGE));
    if (currentPage > pages) currentPage = 1;
    const slice = rows.slice((currentPage-1)*ROWS_PER_PAGE, currentPage*ROWS_PER_PAGE);

    const tbody = document.getElementById('tableBody');
    if (slice.length === 0) {
        tbody.innerHTML = `<tr><td colspan="8" style="text-align:center; padding: 20px;">No hay datos registrados.</td></tr>`;
    } else {
        tbody.innerHTML = slice.map(r => `
            <tr>
              <td class="td-date">${r.fecha || '-'}</td>
              <td class="td-id">${r.id || '-'}</td>
              <td class="td-prod">${r.producto || '-'}</td>
              <td class="td-qty" style="text-align:center">${r.cantidad || '0'}</td>
              <td class="td-price">${r.precio || '$0.00'}</td>
              <td class="td-sub">${r.subtotal || '$0.00'}</td>
              <td><div class="td-vendor"><div class="vendor-av">${initials(r.vendedor)}</div>${r.vendedor || '-'}</div></td>
              <td style="text-align:center;">
                 <button class="action-btn" onclick="previewItem(this, '${currentTab}')" data-json='${JSON.stringify(r).replace(/'/g, "&#39;")}' title="Ver Detalles" style="background:transparent; border:none; cursor:pointer; color:var(--gold); display:inline-flex; align-items:center; justify-content:center; padding:4px; transition:0.2s;" onmouseover="this.style.color='var(--cream)'" onmouseout="this.style.color='var(--gold)'">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                 </button>
                 <button class="action-btn" onclick="imprimirItem('${r.id}', '${currentTab}')" title="Imprimir PDF" style="background:transparent; border:none; cursor:pointer; color:var(--gold); display:inline-flex; align-items:center; justify-content:center; padding:4px; margin-left:5px; transition:0.2s;" onmouseover="this.style.color='var(--cream)'" onmouseout="this.style.color='var(--gold)'">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                 </button>
              </td>
            </tr>
        `).join('');
    }

    document.getElementById('recordCount').textContent = `Mostrando ${total} registro${total!==1?'s':''}`;
    document.getElementById('pageInfo').textContent = `Página ${currentPage} de ${pages}`;

    const pb = document.getElementById('pageBtns');
    pb.innerHTML = '';
    const prev = document.createElement('button');
    prev.className = 'page-btn'; prev.textContent = '‹'; prev.disabled = currentPage===1;
    prev.onclick = () => { currentPage--; render(); };
    pb.appendChild(prev);

    for (let i = 1; i <= pages; i++) {
        const btn = document.createElement('button');
        btn.className = 'page-btn' + (i===currentPage ? ' active' : '');
        btn.textContent = i;
        btn.onclick = () => { currentPage = i; render(); };
        pb.appendChild(btn);
    }

    const next = document.createElement('button');
    next.className = 'page-btn'; next.textContent = '›'; next.disabled = currentPage===pages;
    next.onclick = () => { currentPage++; render(); };
    pb.appendChild(next);
}

document.getElementById('tabBar').addEventListener('click', e => {
    const btn = e.target.closest('.tab-btn');
    if (!btn) return;
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    currentTab = btn.dataset.tab;
    currentPage = 1; 
    render();
});

document.getElementById('mainTable').querySelector('thead').addEventListener('click', e => {
    const th = e.target.closest('th');
    if (!th || !th.dataset.col) return;
    if (sortCol === th.dataset.col) sortAsc = !sortAsc; 
    else { sortCol = th.dataset.col; sortAsc = true; }
    document.querySelectorAll('th').forEach(t => t.classList.remove('sorted'));
    th.classList.add('sorted');
    render();
});

document.getElementById('searchInput').addEventListener('input', e => {
    searchTerm = e.target.value; currentPage = 1; render();
});

document.getElementById('vendorFilter').addEventListener('change', e => {
    vendorFilter = e.target.value; currentPage = 1; render();
});

document.getElementById('btnExportarPDF').addEventListener('click', function() {
    document.getElementById('exportModal').style.display = 'flex';
    document.getElementById('confirmExportBtn').onclick = function() {
        const mes = document.getElementById('exportMonth').value;
        const params = new URLSearchParams({
            reporte: currentTab,
            busqueda: searchTerm,
            vendedor: vendorFilter,
            mes: mes
        });

        const url = `../../../Controller/Public/generar_pdf.php?${params.toString()}`;
        
        window.open(url, '_blank');
        closeExportModal();
    };
});

function closeExportModal() {
    document.getElementById('exportModal').style.display = 'none';
}

render();
</script>
</body>
</html>