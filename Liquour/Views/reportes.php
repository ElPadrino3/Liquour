<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Liquour — Reportes Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="./../Assets/CSS/resportes.css">
</head>

<body>

    <!-- TOPBAR -->
    <header class="topbar">
        <nav class="nav-links">
            <button class="nav-link">Dashboard</button>
            <button class="nav-link">Catálogo</button>
            <button class="nav-link">Empleados</button>
            <button class="nav-link active">Reportes</button>
            <button class="nav-link">Compras</button>
            <button class="nav-link">Mi Perfil</button>
        </nav>
    </header>

    <!-- PAGE -->
    <div class="page">

        <div class="section-title">Seleccionar Reporte</div>

        <!-- TABS -->
        <div class="tabs" id="tabBar">
            <button class="tab-btn active" data-tab="ventas">Historial de Venta</button>
            <button class="tab-btn" data-tab="productos">Productos más Vendidos</button>
            <button class="tab-btn" data-tab="inventario">Inventario</button>
            <button class="tab-btn" data-tab="compras">Historial de Compra</button>
        </div>

        <!-- CONTENT CARD -->
        <div class="content-card">
            <!-- toolbar -->
            <div class="card-toolbar">
                <div class="toolbar-left">
                    <div class="search-wrap">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <circle cx="11" cy="11" r="8" />
                            <line x1="21" y1="21" x2="16.65" y2="16.65" />
                        </svg>
                        <input type="text" id="searchInput" placeholder="Buscar…" />
                    </div>
                </div>
                <div class="toolbar-right">
                    <span class="rec-count" id="recCount"></span>
                    <button class="export-btn">↓ Exportar CSV</button>
                </div>
            </div>

            <!-- TABLE -->
            <div class="tbl-wrap">
                <table>
                    <thead>
                        <tr id="tableHead"></tr>
                    </thead>
                    <tbody id="tableBody"></tbody>
                </table>
            </div>

            <!-- PAGINATION -->
            <div class="pagination">
                <div class="page-info" id="pageInfo">Página 1 de 1</div>
                <div class="page-btns" id="pageBtns"></div>
            </div>
        </div>

    </div>

    <!-- DETAIL MODAL -->
    <div class="modal-bg" id="modalBg">
        <div class="modal">
            <button class="modal-close" id="closeModal">✕</button>
            <div class="modal-title" id="modalTitle">Detalles</div>
            <div class="detail-grid" id="detailGrid"></div>
        </div>
    </div>

    <script>
        /* ═══ DATA ═══ */
        const DATA = {
            ventas: {
                cols: [{
                        key: 'id',
                        label: 'ID'
                    },
                    {
                        key: 'fecha',
                        label: 'Fecha'
                    },
                    {
                        key: 'cajero',
                        label: 'Cajero'
                    },
                    {
                        key: 'cantidad',
                        label: 'Cantidad'
                    },
                    {
                        key: 'total',
                        label: 'Total',
                        cls: 'td-price'
                    },
                    {
                        key: '_detail',
                        label: 'Detalles'
                    },
                ],
                rows: [{
                        id: 'V-001',
                        fecha: '2026-03-20',
                        cajero: 'Juan Perez',
                        cantidad: 3,
                        total: '$348.00',
                        producto: 'Vino Tinto Reserva',
                        cliente: 'Ana López',
                        metodo: 'Efectivo'
                    },
                    {
                        id: 'V-002',
                        fecha: '2026-03-20',
                        cajero: 'Marp Perez',
                        cantidad: 1,
                        total: '$190.00',
                        producto: 'Champagne Brut',
                        cliente: 'Carlos Rivas',
                        metodo: 'Tarjeta'
                    },
                    {
                        id: 'V-003',
                        fecha: '2026-03-21',
                        cajero: 'Juan Perez',
                        cantidad: 2,
                        total: '$190.00',
                        producto: 'Licor de Café',
                        cliente: 'Sara Díaz',
                        metodo: 'Efectivo'
                    },
                    {
                        id: 'V-004',
                        fecha: '2026-03-21',
                        cajero: 'Carlos Rivas',
                        cantidad: 1,
                        total: '$238.00',
                        producto: 'Whisky Single Malt',
                        cliente: 'Pedro Alvarado',
                        metodo: 'Tarjeta'
                    },
                    {
                        id: 'V-005',
                        fecha: '2026-03-22',
                        cajero: 'Marp Perez',
                        cantidad: 4,
                        total: '$700.00',
                        producto: 'Ron Añejo 12 Años',
                        cliente: 'Lucía Morales',
                        metodo: 'Transferencia'
                    },
                    {
                        id: 'V-006',
                        fecha: '2026-03-22',
                        cajero: 'Juan Perez',
                        cantidad: 2,
                        total: '$700.00',
                        producto: 'Vodka Premium',
                        cliente: 'Andrés Vega',
                        metodo: 'Efectivo'
                    },
                    {
                        id: 'V-007',
                        fecha: '2026-03-23',
                        cajero: 'Carlos Rivas',
                        cantidad: 1,
                        total: '$118.00',
                        producto: 'Gin Botánico',
                        cliente: 'Diana Cruz',
                        metodo: 'Tarjeta'
                    },
                    {
                        id: 'V-008',
                        fecha: '2026-03-23',
                        cajero: 'Juan Perez',
                        cantidad: 3,
                        total: '$420.00',
                        producto: 'Tequila Reposado',
                        cliente: 'Felipe Castro',
                        metodo: 'Efectivo'
                    },
                    {
                        id: 'V-009',
                        fecha: '2026-03-24',
                        cajero: 'Marp Perez',
                        cantidad: 1,
                        total: '$55.00',
                        producto: 'Cerveza Artesanal',
                        cliente: 'Gabriela Núñez',
                        metodo: 'Efectivo'
                    },
                    {
                        id: 'V-010',
                        fecha: '2026-03-24',
                        cajero: 'Juan Perez',
                        cantidad: 2,
                        total: '$210.00',
                        producto: 'Mezcal Artesanal',
                        cliente: 'Marco Jiménez',
                        metodo: 'Tarjeta'
                    },
                ]
            },
            productos: {
                cols: [{
                        key: 'id',
                        label: 'ID'
                    },
                    {
                        key: 'nombre',
                        label: 'Nombre',
                        cls: 'td-name'
                    },
                    {
                        key: 'precio',
                        label: 'Precio',
                        cls: 'td-price'
                    },
                    {
                        key: 'vendidas',
                        label: 'Unidades Vendidas'
                    },
                ],
                rows: [{
                        id: 'P-001',
                        nombre: 'Vino Tinto Reserva',
                        precio: '$139.00',
                        vendidas: 120
                    },
                    {
                        id: 'P-002',
                        nombre: 'Whisky Single Malt',
                        precio: '$95.00',
                        vendidas: 95
                    },
                    {
                        id: 'P-003',
                        nombre: 'Champagne Brut',
                        precio: '$55.00',
                        vendidas: 87
                    },
                    {
                        id: 'P-004',
                        nombre: 'Ron Añejo 12 Años',
                        precio: '$65.00',
                        vendidas: 74
                    },
                    {
                        id: 'P-005',
                        nombre: 'Vodka Premium',
                        precio: '$48.00',
                        vendidas: 68
                    },
                    {
                        id: 'P-006',
                        nombre: 'Licor de Café',
                        precio: '$102.00',
                        vendidas: 60
                    },
                    {
                        id: 'P-007',
                        nombre: 'Gin Botánico',
                        precio: '$72.00',
                        vendidas: 52
                    },
                    {
                        id: 'P-008',
                        nombre: 'Tequila Reposado',
                        precio: '$62.00',
                        vendidas: 44
                    },
                    {
                        id: 'P-009',
                        nombre: 'Mezcal Artesanal',
                        precio: '$85.00',
                        vendidas: 30
                    },
                    {
                        id: 'P-010',
                        nombre: 'Cerveza Artesanal',
                        precio: '$8.00',
                        vendidas: 210
                    },
                ]
            },
            inventario: {
                cols: [{
                        key: 'id',
                        label: 'ID'
                    },
                    {
                        key: 'nombre',
                        label: 'Nombre',
                        cls: 'td-name'
                    },
                    {
                        key: 'stock',
                        label: 'Stock'
                    },
                    {
                        key: 'encargados',
                        label: 'Encargados'
                    },
                    {
                        key: 'disponibles',
                        label: 'Disponibles'
                    },
                    {
                        key: 'precio',
                        label: 'Precio',
                        cls: 'td-price'
                    },
                    {
                        key: 'precioU',
                        label: 'Precio Unitario',
                        cls: 'td-price'
                    },
                    {
                        key: 'barcode',
                        label: 'Código de Barras'
                    },
                ],
                rows: [{
                        id: 'I-001',
                        nombre: 'Vino Tinto Reserva',
                        stock: 42,
                        encargados: 5,
                        disponibles: 37,
                        precio: '$139.00',
                        precioU: '$23.17',
                        barcode: '7501030471058'
                    },
                    {
                        id: 'I-002',
                        nombre: 'Whisky Single Malt',
                        stock: 18,
                        encargados: 2,
                        disponibles: 16,
                        precio: '$95.00',
                        precioU: '$15.83',
                        barcode: '5000267023656'
                    },
                    {
                        id: 'I-003',
                        nombre: 'Ron Añejo 12 Años',
                        stock: 8,
                        encargados: 1,
                        disponibles: 7,
                        precio: '$65.00',
                        precioU: '$10.83',
                        barcode: '8410261015527'
                    },
                    {
                        id: 'I-004',
                        nombre: 'Champagne Brut',
                        stock: 66,
                        encargados: 8,
                        disponibles: 58,
                        precio: '$55.00',
                        precioU: '$9.17',
                        barcode: '3185370030093'
                    },
                    {
                        id: 'I-005',
                        nombre: 'Vodka Premium',
                        stock: 6,
                        encargados: 1,
                        disponibles: 5,
                        precio: '$48.00',
                        precioU: '$8.00',
                        barcode: '7312040017072'
                    },
                    {
                        id: 'I-006',
                        nombre: 'Gin Botánico',
                        stock: 11,
                        encargados: 2,
                        disponibles: 9,
                        precio: '$72.00',
                        precioU: '$12.00',
                        barcode: '5010327905754'
                    },
                    {
                        id: 'I-007',
                        nombre: 'Licor de Café',
                        stock: 24,
                        encargados: 3,
                        disponibles: 21,
                        precio: '$102.00',
                        precioU: '$17.00',
                        barcode: '7503014970369'
                    },
                    {
                        id: 'I-008',
                        nombre: 'Tequila Reposado',
                        stock: 15,
                        encargados: 2,
                        disponibles: 13,
                        precio: '$62.00',
                        precioU: '$10.33',
                        barcode: '7503005600071'
                    },
                    {
                        id: 'I-009',
                        nombre: 'Mezcal Artesanal',
                        stock: 10,
                        encargados: 1,
                        disponibles: 9,
                        precio: '$85.00',
                        precioU: '$14.17',
                        barcode: '7503020001843'
                    },
                    {
                        id: 'I-010',
                        nombre: 'Cerveza Artesanal',
                        stock: 96,
                        encargados: 12,
                        disponibles: 84,
                        precio: '$8.00',
                        precioU: '$1.33',
                        barcode: '7501512800048'
                    },
                ]
            },
            compras: {
                cols: [{
                        key: 'id',
                        label: 'ID'
                    },
                    {
                        key: 'fecha',
                        label: 'Fecha'
                    },
                    {
                        key: 'cantidad',
                        label: 'Cantidad'
                    },
                    {
                        key: 'total',
                        label: 'Total',
                        cls: 'td-price'
                    },
                    {
                        key: 'usuario',
                        label: 'Usuario'
                    },
                    {
                        key: '_detail',
                        label: 'Detalles'
                    },
                ],
                rows: [{
                        id: 'C-001',
                        fecha: '2026-03-20',
                        cantidad: 50,
                        total: '$5,500.00',
                        usuario: 'Admin',
                        proveedor: 'Viñas Andinas',
                        producto: 'Vino Tinto Reserva',
                        estado: 'Recibido'
                    },
                    {
                        id: 'C-002',
                        fecha: '2026-03-18',
                        cantidad: 24,
                        total: '$1,920.00',
                        usuario: 'Admin',
                        proveedor: 'Destilados Sur',
                        producto: 'Whisky Single Malt',
                        estado: 'Recibido'
                    },
                    {
                        id: 'C-003',
                        fecha: '2026-03-17',
                        cantidad: 72,
                        total: '$2,880.00',
                        usuario: 'Supervisor',
                        proveedor: 'Viñas Andinas',
                        producto: 'Champagne Brut',
                        estado: 'Recibido'
                    },
                    {
                        id: 'C-004',
                        fecha: '2026-03-15',
                        cantidad: 20,
                        total: '$700.00',
                        usuario: 'Admin',
                        proveedor: 'Premiums Import',
                        producto: 'Vodka Premium',
                        estado: 'Pendiente'
                    },
                    {
                        id: 'C-005',
                        fecha: '2026-03-14',
                        cantidad: 18,
                        total: '$990.00',
                        usuario: 'Supervisor',
                        proveedor: 'Destilados Sur',
                        producto: 'Ron Añejo 12 Años',
                        estado: 'Recibido'
                    },
                    {
                        id: 'C-006',
                        fecha: '2026-03-12',
                        cantidad: 12,
                        total: '$744.00',
                        usuario: 'Admin',
                        proveedor: 'Premiums Import',
                        producto: 'Tequila Reposado',
                        estado: 'Pendiente'
                    },
                    {
                        id: 'C-007',
                        fecha: '2026-03-10',
                        cantidad: 96,
                        total: '$768.00',
                        usuario: 'Admin',
                        proveedor: 'Cerveza Brava',
                        producto: 'Cerveza Artesanal',
                        estado: 'Recibido'
                    },
                    {
                        id: 'C-008',
                        fecha: '2026-03-08',
                        cantidad: 15,
                        total: '$870.00',
                        usuario: 'Supervisor',
                        proveedor: 'Destilados Sur',
                        producto: 'Gin Botánico',
                        estado: 'Pendiente'
                    },
                    {
                        id: 'C-009',
                        fecha: '2026-03-05',
                        cantidad: 30,
                        total: '$2,850.00',
                        usuario: 'Admin',
                        proveedor: 'Viñas Andinas',
                        producto: 'Vino Blanco Reserva',
                        estado: 'Cancelado'
                    },
                    {
                        id: 'C-010',
                        fecha: '2026-03-03',
                        cantidad: 8,
                        total: '$960.00',
                        usuario: 'Admin',
                        proveedor: 'Premiums Import',
                        producto: 'Whisky Japonés',
                        estado: 'Pendiente'
                    },
                ]
            }
        };

        const ROWS_PER_PAGE = 8;
        const MIN_ROWS = 8;
        let currentTab = 'ventas';
        let page = 1;
        let search = '';

        /* ═══ RENDER ═══ */
        function getFiltered() {
            const {
                rows
            } = DATA[currentTab];
            if (!search) return rows;
            const q = search.toLowerCase();
            return rows.filter(r => Object.values(r).some(v => String(v).toLowerCase().includes(q)));
        }

        function renderHead() {
            const {
                cols
            } = DATA[currentTab];
            document.getElementById('tableHead').innerHTML = cols.map(c =>
                `<th>${c.label}</th>`
            ).join('');
        }

        function cellHtml(col, row) {
            if (col.key === '_detail') {
                return `<td><button class="detail-btn" onclick="openDetail('${row.id}')">Ver detalles</button></td>`;
            }
            const val = row[col.key] ?? '—';
            const cls = col.cls || '';
            if (col.key === 'barcode') return `<td class="barcode">${val}</td>`;
            if (col.key === 'id') return `<td class="td-id">${val}</td>`;
            if (col.key === 'fecha') return `<td class="td-date">${val}</td>`;
            return `<td class="${cls}">${val}</td>`;
        }

        function render() {
            renderHead();
            const filtered = getFiltered();
            const total = filtered.length;
            const pages = Math.max(1, Math.ceil(total / ROWS_PER_PAGE));
            if (page > pages) page = 1;
            const slice = filtered.slice((page - 1) * ROWS_PER_PAGE, page * ROWS_PER_PAGE);
            const {
                cols
            } = DATA[currentTab];

            let html = slice.map(row =>
                `<tr>${cols.map(c => cellHtml(c, row)).join('')}</tr>`
            ).join('');

            // filler rows
            const fillers = Math.max(0, MIN_ROWS - slice.length);
            for (let i = 0; i < fillers; i++) {
                html += `<tr class="empty-row">${cols.map(() => '<td></td>').join('')}</tr>`;
            }

            document.getElementById('tableBody').innerHTML = html;
            document.getElementById('recCount').textContent = `${total} registro${total !== 1 ? 's' : ''}`;
            document.getElementById('pageInfo').textContent = `Página ${page} de ${pages}`;
            renderPagination(pages);
        }

        function renderPagination(pages) {
            const pb = document.getElementById('pageBtns');
            pb.innerHTML = '';

            const prev = document.createElement('button');
            prev.className = 'page-btn';
            prev.textContent = '‹';
            prev.disabled = page === 1;
            prev.onclick = () => {
                page--;
                render();
            };
            pb.appendChild(prev);

            for (let i = 1; i <= pages; i++) {
                const b = document.createElement('button');
                b.className = 'page-btn' + (i === page ? ' active' : '');
                b.textContent = i;
                b.onclick = (p => () => {
                    page = p;
                    render();
                })(i);
                pb.appendChild(b);
            }

            const next = document.createElement('button');
            next.className = 'page-btn';
            next.textContent = '›';
            next.disabled = page === pages;
            next.onclick = () => {
                page++;
                render();
            };
            pb.appendChild(next);
        }

        /* ═══ TABS ═══ */
        document.getElementById('tabBar').addEventListener('click', e => {
            const btn = e.target.closest('.tab-btn');
            if (!btn) return;
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            currentTab = btn.dataset.tab;
            page = 1;
            search = '';
            document.getElementById('searchInput').value = '';
            render();
        });

        /* ═══ SEARCH ═══ */
        document.getElementById('searchInput').addEventListener('input', e => {
            search = e.target.value;
            page = 1;
            render();
        });

        /* ═══ DETAIL MODAL ═══ */
        const modalBg = document.getElementById('modalBg');

        const DETAIL_LABELS = {
            ventas: {
                id: 'ID Venta',
                fecha: 'Fecha',
                cajero: 'Cajero',
                cantidad: 'Cantidad',
                total: 'Total',
                producto: 'Producto',
                cliente: 'Cliente',
                metodo: 'Método de Pago'
            },
            compras: {
                id: 'ID Compra',
                fecha: 'Fecha',
                cantidad: 'Cantidad',
                total: 'Total',
                usuario: 'Usuario',
                proveedor: 'Proveedor',
                producto: 'Producto',
                estado: 'Estado'
            },
        };

        function openDetail(id) {
            const {
                rows
            } = DATA[currentTab];
            const row = rows.find(r => r.id === id);
            if (!row) return;

            const labels = DETAIL_LABELS[currentTab] || {};
            const title = currentTab === 'ventas' ? 'Detalle de Venta' : 'Detalle de Compra';
            document.getElementById('modalTitle').textContent = title;

            const goldKeys = ['total'];
            document.getElementById('detailGrid').innerHTML = Object.entries(row)
                .filter(([k]) => !k.startsWith('_'))
                .map(([k, v]) => `
      <div class="detail-row">
        <div class="d-label">${labels[k] || k}</div>
        <div class="d-value${goldKeys.includes(k) ? ' gold' : ''}">${v}</div>
      </div>`).join('');

            modalBg.classList.add('open');
        }

        document.getElementById('closeModal').onclick = () => modalBg.classList.remove('open');
        modalBg.addEventListener('click', e => {
            if (e.target === modalBg) modalBg.classList.remove('open');
        });

        render();
    </script>
</body>

</html>