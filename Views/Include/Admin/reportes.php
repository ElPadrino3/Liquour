<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Liquour — Reportes</title>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet" />
<style>
  *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

  :root {
    --carbon:   #1A1A1A;
    --gold:     #C5A059;
    --gold-lt:  #D4B577;
    --gold-dk:  #9A7A3F;
    --oxford:   #4A4A4A;
    --cream:    #F5F5DC;
    --surface:  #242424;
    --surface2: #2E2E2E;
    --border:   rgba(197,160,89,.15);
    --border-md:rgba(197,160,89,.30);
  }

  html, body {
    min-height: 100vh;
    background: var(--carbon);
    color: var(--cream);
    font-family: 'Montserrat', sans-serif;
    font-size: 13px;
  }

  /* ─── TOPBAR ─── */
  .topbar {
    background: #0f0f0f;
    border-bottom: 1px solid var(--border);
    display: flex; align-items: center; justify-content: space-between;
    padding: 0 32px;
    height: 62px;
  }

  .logo { display: flex; align-items: center; gap: 12px; text-decoration: none; }
  .logo-ring {
    width: 44px; height: 44px; border-radius: 50%;
    border: 1.5px solid var(--gold);
    display: flex; align-items: center; justify-content: center;
    font-family: 'Cormorant Garamond', serif; font-size: 20px; color: var(--gold);
  }
  .logo-text { font-family: 'Cormorant Garamond', serif; font-size: 18px; letter-spacing: 3px; color: var(--cream); }
  .logo-sub  { font-size: 8px; letter-spacing: 3px; color: var(--gold-dk); text-transform: uppercase; }

  .nav-links { display: flex; gap: 6px; }
  .nav-link {
    padding: 8px 22px;
    border: 1px solid var(--border-md);
    border-radius: 6px;
    font-size: 10px; letter-spacing: 2px; text-transform: uppercase;
    color: var(--cream); cursor: pointer; background: none;
    font-family: 'Montserrat', sans-serif;
    transition: background .2s, color .2s, border-color .2s;
  }
  .nav-link:hover { background: rgba(197,160,89,.12); border-color: var(--gold); color: var(--gold); }
  .nav-link.active { background: rgba(197,160,89,.15); border-color: var(--gold); color: var(--gold); }

  /* ─── PAGE WRAPPER ─── */
  .page { padding: 30px 32px; max-width: 1100px; margin: 0 auto; }

  /* ─── SECTION TITLE ─── */
  .section-heading {
    text-align: center;
    font-family: 'Cormorant Garamond', serif;
    font-size: 26px; letter-spacing: 6px; font-weight: 400;
    color: var(--gold);
    margin-bottom: 22px;
    text-transform: uppercase;
    position: relative;
  }
  .section-heading::after {
    content: '';
    display: block; width: 80px; height: 1px;
    background: var(--gold); opacity: .4;
    margin: 8px auto 0;
  }

  /* ─── TAB BAR ─── */
  .tab-bar {
    display: flex; gap: 8px;
    margin-bottom: 20px;
    flex-wrap: wrap;
  }
  .tab-btn {
    padding: 10px 20px;
    border: 1px solid var(--border-md);
    border-radius: 6px;
    font-size: 10px; letter-spacing: 1.5px; text-transform: uppercase;
    color: rgba(245,245,220,.5); cursor: pointer; background: none;
    font-family: 'Montserrat', sans-serif;
    transition: all .2s;
    line-height: 1.4;
    text-align: center;
    position: relative;
  }
  .tab-btn:hover { color: var(--cream); border-color: var(--gold); }
  .tab-btn.active {
    background: rgba(197,160,89,.14);
    border-color: var(--gold);
    color: var(--gold);
  }
  .tab-btn.active::after {
    content: '';
    position: absolute; bottom: -10px; left: 50%; transform: translateX(-50%);
    width: 6px; height: 6px; border-radius: 50%;
    background: var(--gold);
  }

  /* ─── TABLE CARD ─── */
  .table-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 10px;
    overflow: hidden;
  }

  .table-toolbar {
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 20px;
    border-bottom: 1px solid var(--border);
    gap: 12px; flex-wrap: wrap;
  }
  .toolbar-left { display: flex; align-items: center; gap: 10px; }
  .toolbar-right { display: flex; align-items: center; gap: 8px; }

  .search-input {
    background: var(--surface2); border: 1px solid var(--border);
    border-radius: 18px; padding: 6px 14px; width: 200px;
    font-family: 'Montserrat', sans-serif; font-size: 11px;
    color: var(--cream); outline: none;
  }
  .search-input::placeholder { color: var(--oxford); }
  .search-input:focus { border-color: var(--border-md); }

  .filter-select {
    background: var(--surface2); border: 1px solid var(--border);
    border-radius: 18px; padding: 6px 12px;
    font-family: 'Montserrat', sans-serif; font-size: 10px;
    color: var(--cream); outline: none; cursor: pointer;
    letter-spacing: 1px;
  }
  .filter-select option { background: var(--surface2); }

  .export-btn {
    background: rgba(197,160,89,.12); border: 1px solid var(--border-md);
    border-radius: 18px; padding: 6px 14px;
    font-family: 'Montserrat', sans-serif; font-size: 9px; letter-spacing: 1.5px;
    color: var(--gold); text-transform: uppercase; cursor: pointer;
    transition: background .2s;
  }
  .export-btn:hover { background: rgba(197,160,89,.22); }

  .record-count { font-size: 9px; color: var(--oxford); letter-spacing: 1px; }

  /* Table */
  .tbl-wrap { overflow-x: auto; max-height: 420px; overflow-y: auto; }
  .tbl-wrap::-webkit-scrollbar { width: 4px; height: 4px; }
  .tbl-wrap::-webkit-scrollbar-thumb { background: rgba(197,160,89,.2); border-radius: 4px; }

  table { width: 100%; border-collapse: collapse; min-width: 680px; }

  thead { position: sticky; top: 0; z-index: 2; }
  thead tr { background: var(--surface2); }
  th {
    font-size: 8px; letter-spacing: 2px; color: var(--oxford);
    text-transform: uppercase; padding: 11px 16px;
    text-align: left; font-weight: 500; white-space: nowrap;
    border-bottom: 1px solid var(--border);
    cursor: pointer; user-select: none;
  }
  th:hover { color: var(--gold-lt); }
  th .sort-icon { margin-left: 4px; opacity: .4; font-size: 9px; }
  th.sorted { color: var(--gold); }
  th.sorted .sort-icon { opacity: 1; }

  td {
    font-size: 11px; color: rgba(245,245,220,.72);
    padding: 10px 16px;
    border-bottom: 1px solid rgba(255,255,255,.035);
    white-space: nowrap;
  }
  tbody tr:last-child td { border-bottom: none; }
  tbody tr:hover td { color: var(--cream); background: rgba(197,160,89,.04); }

  .td-date  { color: var(--oxford); font-size: 10px; letter-spacing: 1px; }
  .td-id    { color: var(--gold-lt); font-weight: 500; }
  .td-prod  { color: var(--cream); font-weight: 500; letter-spacing: .5px; }
  .td-qty   { text-align: center; }
  .td-price { color: rgba(245,245,220,.65); }
  .td-sub   { color: var(--gold); font-weight: 500; }
  .td-vendor { display: flex; align-items: center; gap: 8px; }
  .vendor-av {
    width: 24px; height: 24px; border-radius: 50%;
    background: rgba(197,160,89,.12); border: 1px solid var(--border-md);
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 8px; color: var(--gold); font-weight: 600; flex-shrink: 0;
  }

  /* Pagination */
  .pagination {
    display: flex; align-items: center; justify-content: space-between;
    padding: 12px 20px;
    border-top: 1px solid var(--border);
    flex-wrap: wrap; gap: 8px;
  }
  .page-info { font-size: 9px; color: var(--oxford); letter-spacing: 1px; }
  .page-btns { display: flex; gap: 4px; }
  .page-btn {
    width: 30px; height: 30px; border-radius: 6px;
    background: none; border: 1px solid var(--border);
    color: rgba(245,245,220,.5); font-size: 10px; cursor: pointer;
    font-family: 'Montserrat', sans-serif;
    display: flex; align-items: center; justify-content: center;
    transition: all .2s;
  }
  .page-btn:hover { border-color: var(--gold); color: var(--gold); }
  .page-btn.active { background: rgba(197,160,89,.15); border-color: var(--gold); color: var(--gold); }
  .page-btn:disabled { opacity: .25; cursor: default; }

  /* Summary row */
  .summary-row {
    display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px;
    margin-bottom: 20px;
  }
  .sum-card {
    background: var(--surface); border: 1px solid var(--border);
    border-radius: 8px; padding: 14px 16px;
    position: relative; overflow: hidden;
  }
  .sum-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px; background: linear-gradient(90deg,transparent,var(--gold),transparent); opacity: .35; }
  .sum-lbl { font-size: 8px; letter-spacing: 2px; color: var(--oxford); text-transform: uppercase; margin-bottom: 6px; }
  .sum-val { font-family: 'Cormorant Garamond', serif; font-size: 22px; color: var(--cream); }
  .sum-val span { color: var(--gold); }
</style>
</head>
<body>

<!-- TOPBAR -->
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
    <button class="nav-link">Reserva</button>
    <button class="nav-link active">Mi Perfil</button>
  </nav>
</header>

<!-- PAGE -->
<div class="page">

  <div class="section-heading">Seleccionar Reporte</div>

  <!-- TABS -->
  <div class="tab-bar" id="tabBar">
    <button class="tab-btn active" data-tab="ventas">Historial de Venta</button>
    <button class="tab-btn" data-tab="productos">Productos Más Vendidos</button>
    <button class="tab-btn" data-tab="inventario">Inventario</button>
    <button class="tab-btn" data-tab="compras">Historial de Compras</button>
  </div>

  <!-- SUMMARY CARDS -->
  <div class="summary-row" id="summaryRow">
    <div class="sum-card"><div class="sum-lbl">Total Ventas</div><div class="sum-val"><span>$</span>4,218.00</div></div>
    <div class="sum-card"><div class="sum-lbl">Transacciones</div><div class="sum-val">24</div></div>
    <div class="sum-card"><div class="sum-lbl">Promedio / Venta</div><div class="sum-val"><span>$</span>175.75</div></div>
    <div class="sum-card"><div class="sum-lbl">Vendedores</div><div class="sum-val">3</div></div>
  </div>

  <!-- TABLE CARD -->
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

  // tbody
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

  // count
  document.getElementById('recordCount').textContent = `Mostrando ${total} registro${total!==1?'s':''}`;
  document.getElementById('pageInfo').textContent = `Página ${currentPage} de ${pages}`;

  // pagination
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

// Tabs
document.getElementById('tabBar').addEventListener('click', e => {
  const btn = e.target.closest('.tab-btn');
  if (!btn) return;
  document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  currentTab = btn.dataset.tab;
  currentPage = 1; sortCol = null;
  render();
});

// Sort
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

// Search
document.getElementById('searchInput').addEventListener('input', e => {
  searchTerm = e.target.value; currentPage = 1; render();
});

// Vendor filter
document.getElementById('vendorFilter').addEventListener('change', e => {
  vendorFilter = e.target.value; currentPage = 1; render();
});

render();
</script>
</body>
</html>