<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Liquour — Reservas</title>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet" />
 <link rel="stylesheet" href="./../Assets/CSS/reservas.css">
</head>
<body>

<!-- TOPBAR -->
<header class="topbar">
  <nav class="nav-links">
     <button class="nav-link">DASHBOARD</button>
      <button class="nav-link">CATALOGO</button>
       <button class="nav-link">EMPLEADOS</button>
    <button class="nav-link">REPORTES</button>
    <button class="nav-link active">REPORTES</button>
    <button class="nav-link">COMPRAS</button>
     <button class="nav-link">MI PERFIL</button>
  </nav>
</header>

<!-- PAGE -->
<div class="page">

  <!-- KPIs -->
  <div class="kpi-row">
    <div class="kpi">
      <div class="kpi-lbl">Total Reservas</div>
      <div class="kpi-val">24</div>
      <div class="kpi-sub">Este mes</div>
      <div class="kpi-tag">↑ 6 vs anterior</div>
    </div>
    <div class="kpi">
      <div class="kpi-lbl">Pendientes</div>
      <div class="kpi-val">8</div>
      <div class="kpi-sub">En espera de atención</div>
      <div class="kpi-tag warn">Revisar</div>
    </div>
    <div class="kpi">
      <div class="kpi-lbl">Completadas</div>
      <div class="kpi-val">13</div>
      <div class="kpi-sub">Finalizadas correctamente</div>
      <div class="kpi-tag ok">✓ Al día</div>
    </div>
    <div class="kpi">
      <div class="kpi-lbl">Vencidas</div>
      <div class="kpi-val">3</div>
      <div class="kpi-sub">Sin atender a tiempo</div>
      <div class="kpi-tag warn">Atención</div>
    </div>
  </div>

  <!-- TABLE CARD -->
  <div class="table-card">

    <!-- TOOLBAR -->
    <div class="toolbar">
      <div class="toolbar-left">
        <div class="search-wrap">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
          </svg>
          <input type="text" id="searchInput" placeholder="Buscar…" />
        </div>
        <select class="filter-select" id="statusFilter">
          <option value="">Todos los estados</option>
          <option value="Pendiente">Pendiente</option>
          <option value="Completado">Completado</option>
          <option value="Vencido">Vencido</option>
          <option value="Cancelado">Cancelado</option>
        </select>
        <select class="filter-select" id="monthFilter">
          <option value="">Todos los meses</option>
          <option value="2026-04">Abril 2026</option>
          <option value="2026-03">Marzo 2026</option>
          <option value="2026-02">Febrero 2026</option>
        </select>
      </div>
      <div class="toolbar-right">
        <span class="rec-count" id="recCount">24 registros</span>
        <button class="export-btn">↓ Exportar</button>
        <button class="new-btn" id="openModal">+ Nueva Reserva</button>
      </div>
    </div>

    <!-- TABLE -->
    <div class="tbl-wrap">
      <table>
        <thead>
          <tr>
            <th data-col="id">ID <span class="si">↕</span></th>
            <th data-col="nombre">Nombre <span class="si">↕</span></th>
            <th data-col="telefono">Teléfono <span class="si">↕</span></th>
            <th data-col="fecha">Fecha Límite <span class="si">↕</span></th>
            <th data-col="estado">Estado <span class="si">↕</span></th>
            <th style="text-align:center">Carrito</th>
          </tr>
        </thead>
        <tbody id="tableBody"></tbody>
      </table>
    </div>

    <!-- PAGINATION -->
    <div class="pagination">
      <div class="page-info" id="pageInfo">Página 1 de 3</div>
      <div class="page-btns" id="pageBtns"></div>
    </div>
  </div>
</div>

<!-- MODAL -->
<div class="modal-bg" id="modalBg">
  <div class="modal">
    <button class="modal-close" id="closeModal">✕</button>
    <div class="modal-title">Nueva Reserva</div>
    <div class="form-row-2">
      <div class="form-row">
        <div class="form-label">Nombre</div>
        <input class="form-input" type="text" placeholder="Ana María López" />
      </div>
      <div class="form-row">
        <div class="form-label">Apellido</div>
        <input class="form-input" type="text" placeholder="Rivas" />
      </div>
    </div>
    <div class="form-row-2">
      <div class="form-row">
        <div class="form-label">Teléfono</div>
        <input class="form-input" type="text" placeholder="1234-5678" />
      </div>
      <div class="form-row">
        <div class="form-label">Fecha Límite</div>
        <input class="form-input" type="date" />
      </div>
    </div>
    <div class="form-row">
      <div class="form-label">Producto / Descripción</div>
      <input class="form-input" type="text" placeholder="Ej. Vino Tinto Reserva × 2" />
    </div>
    <div class="form-row">
      <div class="form-label">Estado</div>
      <select class="form-select">
        <option>Pendiente</option>
        <option>Completado</option>
        <option>Vencido</option>
        <option>Cancelado</option>
      </select>
    </div>
    <div class="modal-footer">
      <button class="btn-cancel" id="closeModal2">Cancelar</button>
      <button class="btn-save">Guardar Reserva</button>
    </div>
  </div>
</div>

<script>
const RESERVAS = [
  { id:1,  nombre:'Ana Maria Lopez Rivas',    telefono:'1234-5678', fecha:'Miercoles 16/Abril/2026', raw:'2026-04', estado:'Pendiente'  },
  { id:2,  nombre:'Ana Maria Lopez Rivas',    telefono:'1234-5678', fecha:'Miercoles 16/Abril/2026', raw:'2026-04', estado:'Completado' },
  { id:3,  nombre:'Ana Maria Lopez Rivas',    telefono:'1234-5678', fecha:'Miercoles 16/Abril/2026', raw:'2026-04', estado:'Vencido'    },
  { id:4,  nombre:'Carlos Rivas Morales',     telefono:'2233-4455', fecha:'Jueves 17/Abril/2026',    raw:'2026-04', estado:'Pendiente'  },
  { id:5,  nombre:'Sara López Díaz',          telefono:'9988-7766', fecha:'Viernes 18/Abril/2026',   raw:'2026-04', estado:'Completado' },
  { id:6,  nombre:'Roberto Mejía',            telefono:'5544-3322', fecha:'Lunes 21/Abril/2026',     raw:'2026-04', estado:'Vencido'    },
  { id:7,  nombre:'Diana Cruz Herrera',       telefono:'6677-8899', fecha:'Martes 22/Abril/2026',    raw:'2026-04', estado:'Pendiente'  },
  { id:8,  nombre:'Luis Torres Fuentes',      telefono:'1122-3344', fecha:'Miercoles 23/Abril/2026', raw:'2026-04', estado:'Completado' },
  { id:9,  nombre:'Marp Perez Gonzalez',      telefono:'4455-6677', fecha:'Jueves 24/Abril/2026',    raw:'2026-04', estado:'Cancelado'  },
  { id:10, nombre:'Juan Perez',               telefono:'7788-9900', fecha:'Viernes 25/Abril/2026',   raw:'2026-04', estado:'Pendiente'  },
  { id:11, nombre:'María Fernanda Ruiz',      telefono:'3344-5566', fecha:'Lunes 28/Abril/2026',     raw:'2026-04', estado:'Completado' },
  { id:12, nombre:'Pedro Alvarado Soto',      telefono:'8877-6655', fecha:'Martes 29/Abril/2026',    raw:'2026-04', estado:'Pendiente'  },
  { id:13, nombre:'Lucía Morales Castro',     telefono:'2211-4433', fecha:'Miercoles 30/Abril/2026', raw:'2026-04', estado:'Completado' },
  { id:14, nombre:'Andrés Vega Lima',         telefono:'5566-7788', fecha:'Jueves 10/Marzo/2026',    raw:'2026-03', estado:'Completado' },
  { id:15, nombre:'Valentina Ríos Prado',     telefono:'9900-1122', fecha:'Viernes 11/Marzo/2026',   raw:'2026-03', estado:'Vencido'    },
  { id:16, nombre:'Felipe Castro Reyes',      telefono:'3322-1100', fecha:'Lunes 14/Marzo/2026',     raw:'2026-03', estado:'Completado' },
  { id:17, nombre:'Gabriela Núñez Torres',    telefono:'6655-4433', fecha:'Martes 15/Marzo/2026',    raw:'2026-03', estado:'Pendiente'  },
  { id:18, nombre:'Marco Jiménez Salinas',    telefono:'0099-8877', fecha:'Miercoles 16/Marzo/2026', raw:'2026-03', estado:'Completado' },
  { id:19, nombre:'Isabel Ortega Flores',     telefono:'1133-5577', fecha:'Jueves 17/Marzo/2026',    raw:'2026-03', estado:'Cancelado'  },
  { id:20, nombre:'Ricardo Blanco Mora',      telefono:'2244-6688', fecha:'Viernes 18/Marzo/2026',   raw:'2026-03', estado:'Completado' },
  { id:21, nombre:'Natalia Sandoval',         telefono:'3355-7799', fecha:'Lunes 03/Feb/2026',       raw:'2026-02', estado:'Completado' },
  { id:22, nombre:'Camilo Herrera',           telefono:'4466-8800', fecha:'Martes 04/Feb/2026',      raw:'2026-02', estado:'Completado' },
  { id:23, nombre:'Daniela Vargas',           telefono:'5577-9911', fecha:'Miercoles 05/Feb/2026',   raw:'2026-02', estado:'Completado' },
  { id:24, nombre:'Tomás Espinoza',           telefono:'6688-0022', fecha:'Jueves 06/Feb/2026',      raw:'2026-02', estado:'Completado' },
];

const BADGE_CLASS = { Pendiente:'b-pending', Completado:'b-done', Vencido:'b-expired', Cancelado:'b-canceled' };
const BADGE_LABEL = { Pendiente:'Pendiente', Completado:'Completado', Vencido:'Vencido', Cancelado:'Cancelado' };
const ROWS = 8;

let page = 1, search = '', statusF = '', monthF = '', sortCol = null, sortAsc = true;

function filtered() {
  return RESERVAS.filter(r => {
    const q = search.toLowerCase();
    const m = !q || [r.nombre, r.telefono, r.fecha, r.estado, String(r.id)].some(v => v.toLowerCase().includes(q));
    return m && (!statusF || r.estado === statusF) && (!monthF || r.raw === monthF);
  }).sort((a, b) => {
    if (!sortCol) return 0;
    let av = a[sortCol], bv = b[sortCol];
    if (sortCol === 'id') { av = Number(av); bv = Number(bv); }
    return sortAsc ? (av > bv ? 1 : -1) : (av < bv ? 1 : -1);
  });
}

function render() {
  const rows  = filtered();
  const pages = Math.max(1, Math.ceil(rows.length / ROWS));
  if (page > pages) page = 1;
  const slice = rows.slice((page-1)*ROWS, page*ROWS);

  document.getElementById('recCount').textContent = `${rows.length} registro${rows.length !== 1 ? 's' : ''}`;
  document.getElementById('pageInfo').textContent = `Página ${page} de ${pages}`;

  document.getElementById('tableBody').innerHTML = slice.map(r => `
    <tr>
      <td class="td-id">${r.id}</td>
      <td class="td-name">${r.nombre}</td>
      <td class="td-phone">${r.telefono}</td>
      <td class="td-date">${r.fecha}</td>
      <td><span class="badge ${BADGE_CLASS[r.estado]}"><span class="badge-dot"></span>${BADGE_LABEL[r.estado]}</span></td>
      <td style="text-align:center">
        <button class="cart-btn" title="Ver carrito">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
          </svg>
        </button>
      </td>
    </tr>`).join('');

  // pagination
  const pb = document.getElementById('pageBtns');
  pb.innerHTML = '';

  const prev = document.createElement('button');
  prev.className = 'page-btn'; prev.textContent = '‹'; prev.disabled = page === 1;
  prev.onclick = () => { page--; render(); };
  pb.appendChild(prev);

  for (let i = 1; i <= pages; i++) {
    const b = document.createElement('button');
    b.className = 'page-btn' + (i === page ? ' active' : '');
    b.textContent = i;
    b.onclick = (p => () => { page = p; render(); })(i);
    pb.appendChild(b);
  }

  const next = document.createElement('button');
  next.className = 'page-btn'; next.textContent = '›'; next.disabled = page === pages;
  next.onclick = () => { page++; render(); };
  pb.appendChild(next);
}

// SORT
document.querySelector('thead').addEventListener('click', e => {
  const th = e.target.closest('th[data-col]');
  if (!th) return;
  const col = th.dataset.col;
  if (sortCol === col) sortAsc = !sortAsc; else { sortCol = col; sortAsc = true; }
  document.querySelectorAll('th').forEach(t => t.classList.remove('sorted'));
  th.classList.add('sorted');
  const si = th.querySelector('.si');
  if (si) si.textContent = sortAsc ? '↑' : '↓';
  render();
});

// FILTERS
document.getElementById('searchInput').addEventListener('input', e => { search = e.target.value; page = 1; render(); });
document.getElementById('statusFilter').addEventListener('change', e => { statusF = e.target.value; page = 1; render(); });
document.getElementById('monthFilter').addEventListener('change', e => { monthF = e.target.value; page = 1; render(); });

// MODAL
const modalBg = document.getElementById('modalBg');
document.getElementById('openModal').onclick   = () => modalBg.classList.add('open');
document.getElementById('closeModal').onclick  = () => modalBg.classList.remove('open');
document.getElementById('closeModal2').onclick = () => modalBg.classList.remove('open');
modalBg.addEventListener('click', e => { if (e.target === modalBg) modalBg.classList.remove('open'); });

render();
</script>
</body>
</html>