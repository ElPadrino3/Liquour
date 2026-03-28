<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Liquour — Reportes</title>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../../../Assets/CSS/style.css">
</head>
<body>

<?php include '../../Layout/header_admin.php'; ?>

<div class="page">

  <div class="section-heading">Seleccionar Reporte</div>

  <div class="tab-bar" id="tabBar">
    <button class="tab-btn active" data-tab="ventas">Historial de Venta</button>
    <button class="tab-btn" data-tab="productos">Productos Más Vendidos</button>
    <button class="tab-btn" data-tab="inventario">Inventario</button>
    <button class="tab-btn" data-tab="compras">Historial de Compras</button>
  </div>

  <div class="summary-row" id="summaryRow">
    <div class="sum-card"><div class="sum-lbl">Total Ventas</div><div class="sum-val"><span>$</span>4,218.00</div></div>
    <div class="sum-card"><div class="sum-lbl">Transacciones</div><div class="sum-val">24</div></div>
    <div class="sum-card"><div class="sum-lbl">Promedio / Venta</div><div class="sum-val"><span>$</span>175.75</div></div>
    <div class="sum-card"><div class="sum-lbl">Vendedores</div><div class="sum-val">3</div></div>
  </div>

  <div class="table-card">
    <div class="table-toolbar">
      <div class="toolbar-left">
        <input class="search-input" type="text" placeholder="Buscar…" id="searchInput" />
        <select class="filter-select" id="vendorFilter">
          <option value="">Todos los vendedores</option>
          <option value="Juan Perez">Juan Perez</option>
          <option value="Marp Perez">Marp Perez</option>
          <option value="Vendedor">Vendedor</option>
        </select>
      </div>
      <div class="toolbar-right">
        <span class="record-count" id="recordCount">Mostrando 7 registros</span>
        <button class="export-btn">↓ Exportar CSV</button>
      </div>
    </div>

    <div class="tbl-wrap">
      <table id="mainTable">
        <thead>
          <tr>
            <th data-col="fecha">Fecha de Venta <span class="sort-icon">↕</span></th>
            <th data-col="id">ID del Cliente <span class="sort-icon">↕</span></th>
            <th data-col="producto">Producto <span class="sort-icon">↕</span></th>
            <th data-col="cantidad" style="text-align:center">Cantidad <span class="sort-icon">↕</span></th>
            <th data-col="precio">Precio Unitario <span class="sort-icon">↕</span></th>
            <th data-col="subtotal">Subtotal <span class="sort-icon">↕</span></th>
            <th data-col="vendedor">Vendedor <span class="sort-icon">↕</span></th>
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
const DATA = {
  ventas: [
    { fecha:'2024-12-24', id:'20001', producto:'VINO TINTO RESERVA', cantidad:1, precio:'$139.00', subtotal:'$348.00', vendedor:'Juan Perez' },
    { fecha:'2024-12-24', id:'20002', producto:'VINO TINTO RESERVA', cantidad:1, precio:'$159.00', subtotal:'$190.00', vendedor:'Juan Perez' },
    { fecha:'2024-12-24', id:'20003', producto:'VINO TINTO RESERVA', cantidad:1, precio:'$190.00', subtotal:'$190.00', vendedor:'Juan Perez' },
    { fecha:'2024-12-24', id:'20004', producto:'VINO TINTO RESERVA', cantidad:1, precio:'$228.00', subtotal:'$238.00', vendedor:'Marp Perez' },
    { fecha:'2024-12-24', id:'20005', producto:'VINO TINTO RESERVA', cantidad:1, precio:'$570.00', subtotal:'$700.00', vendedor:'Vendedor' },
    { fecha:'2024-12-24', id:'20007', producto:'VINO TINTO RESERVA', cantidad:1, precio:'$270.00', subtotal:'$700.00', vendedor:'Juan Perez' },
    { fecha:'2024-12-24', id:'20008', producto:'VINO TINTO RESERVA', cantidad:1, precio:'$118.00', subtotal:'$118.00', vendedor:'Juan Perez' },
    { fecha:'2024-12-23', id:'19990', producto:'WHISKY SINGLE MALT', cantidad:2, precio:'$95.00',  subtotal:'$190.00', vendedor:'Juan Perez' },
    { fecha:'2024-12-23', id:'19991', producto:'RON AÑEJO 12 AÑOS',  cantidad:1, precio:'$65.00',  subtotal:'$65.00',  vendedor:'Marp Perez' },
    { fecha:'2024-12-22', id:'19980', producto:'CHAMPAGNE BRUT',     cantidad:3, precio:'$55.00',  subtotal:'$165.00', vendedor:'Vendedor' },
    { fecha:'2024-12-22', id:'19981', producto:'VODKA PREMIUM',      cantidad:2, precio:'$48.00',  subtotal:'$96.00',  vendedor:'Juan Perez' },
    { fecha:'2024-12-21', id:'19970', producto:'GIN BOTANICO',       cantidad:1, precio:'$72.00',  subtotal:'$72.00',  vendedor:'Marp Perez' },
  ],
  productos: [
    { fecha:'2024-12-24', id:'P001', producto:'VINO TINTO RESERVA',  cantidad:7, precio:'$190.00', subtotal:'$1,330.00', vendedor:'Juan Perez' },
    { fecha:'2024-12-23', id:'P002', producto:'WHISKY SINGLE MALT',  cantidad:5, precio:'$95.00',  subtotal:'$475.00',  vendedor:'Juan Perez' },
    { fecha:'2024-12-22', id:'P003', producto:'RON AÑEJO 12 AÑOS',   cantidad:4, precio:'$65.00',  subtotal:'$260.00',  vendedor:'Marp Perez' },
    { fecha:'2024-12-21', id:'P004', producto:'CHAMPAGNE BRUT',      cantidad:3, precio:'$55.00',  subtotal:'$165.00',  vendedor:'Vendedor' },
    { fecha:'2024-12-20', id:'P005', producto:'VODKA PREMIUM',       cantidad:2, precio:'$48.00',  subtotal:'$96.00',   vendedor:'Juan Perez' },
  ],
  inventario: [
    { fecha:'2024-12-24', id:'INV01', producto:'VINO TINTO RESERVA',  cantidad:42, precio:'$139.00', subtotal:'$5,838.00', vendedor:'Bodega A' },
    { fecha:'2024-12-24', id:'INV02', producto:'WHISKY SINGLE MALT',  cantidad:18, precio:'$95.00',  subtotal:'$1,710.00', vendedor:'Bodega B' },
    { fecha:'2024-12-24', id:'INV03', producto:'RON AÑEJO 12 AÑOS',   cantidad:8,  precio:'$65.00',  subtotal:'$520.00',   vendedor:'Bodega A' },
    { fecha:'2024-12-24', id:'INV04', producto:'CHAMPAGNE BRUT',      cantidad:66, precio:'$55.00',  subtotal:'$3,630.00', vendedor:'Bodega C' },
    { fecha:'2024-12-24', id:'INV05', producto:'VODKA PREMIUM',       cantidad:6,  precio:'$48.00',  subtotal:'$288.00',   vendedor:'Bodega B' },
    { fecha:'2024-12-24', id:'INV06', producto:'GIN BOTANICO',        cantidad:11, precio:'$72.00',  subtotal:'$792.00',   vendedor:'Bodega A' },
  ],
  compras: [
    { fecha:'2024-12-20', id:'C001', producto:'VINO TINTO RESERVA',  cantidad:50, precio:'$110.00', subtotal:'$5,500.00', vendedor:'Proveedor A' },
    { fecha:'2024-12-18', id:'C002', producto:'WHISKY SINGLE MALT',  cantidad:24, precio:'$80.00',  subtotal:'$1,920.00', vendedor:'Proveedor B' },
    { fecha:'2024-12-15', id:'C003', producto:'CHAMPAGNE BRUT',      cantidad:72, precio:'$40.00',  subtotal:'$2,880.00', vendedor:'Proveedor C' },
    { fecha:'2024-12-10', id:'C004', producto:'VODKA PREMIUM',       cantidad:20, precio:'$35.00',  subtotal:'$700.00',   vendedor:'Proveedor A' },
  ]
};

const ROWS_PER_PAGE = 7;
let currentTab    = 'ventas';
let currentPage   = 1;
let sortCol       = null;
let sortAsc       = true;
let searchTerm    = '';
let vendorFilter  = '';

function initials(name) {
  return name.split(' ').map(w => w[0]).join('').slice(0,2).toUpperCase();
}

function getFiltered() {
  let rows = [...DATA[currentTab]];
  if (searchTerm) {
    const q = searchTerm.toLowerCase();
    rows = rows.filter(r =>
      Object.values(r).some(v => String(v).toLowerCase().includes(q))
    );
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
  const rows  = getFiltered();
  const total = rows.length;
  const pages = Math.max(1, Math.ceil(total / ROWS_PER_PAGE));
  if (currentPage > pages) currentPage = 1;
  const slice = rows.slice((currentPage-1)*ROWS_PER_PAGE, currentPage*ROWS_PER_PAGE);

  const tbody = document.getElementById('tableBody');
  tbody.innerHTML = slice.map(r => `
    <tr>
      <td class="td-date">${r.fecha}</td>
      <td class="td-id">${r.id}</td>
      <td class="td-prod">${r.producto}</td>
      <td class="td-qty" style="text-align:center">${r.cantidad}</td>
      <td class="td-price">${r.precio}</td>
      <td class="td-sub">${r.subtotal}</td>
      <td><div class="td-vendor"><div class="vendor-av">${initials(r.vendedor)}</div>${r.vendedor}</div></td>
    </tr>
  `).join('');

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
    btn.onclick = (()=>{ const p=i; return ()=>{ currentPage=p; render(); }; })();
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
  currentPage = 1; sortCol = null;
  render();
});

document.getElementById('mainTable').querySelector('thead').addEventListener('click', e => {
  const th = e.target.closest('th');
  if (!th || !th.dataset.col) return;
  if (sortCol === th.dataset.col) sortAsc = !sortAsc; else { sortCol = th.dataset.col; sortAsc = true; }
  document.querySelectorAll('th').forEach(t => t.classList.remove('sorted'));
  th.classList.add('sorted');
  const icon = th.querySelector('.sort-icon');
  if (icon) icon.textContent = sortAsc ? '↑' : '↓';
  render();
});

document.getElementById('searchInput').addEventListener('input', e => {
  searchTerm = e.target.value; currentPage = 1; render();
});

document.getElementById('vendorFilter').addEventListener('change', e => {
  vendorFilter = e.target.value; currentPage = 1; render();
});

render();
</script>
</body>
</html>