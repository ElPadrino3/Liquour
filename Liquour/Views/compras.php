<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Liquour — Compras Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="./../Assets/CSS/compras.css">
</head>

<body>

    <!-- TOPBAR -->
    <header class="topbar">
        <nav class="nav-links">
            <button class="nav-link">Dashboard</button>
            <button class="nav-link">Catálogo</button>
            <button class="nav-link">Empleados</button>
            <button class="nav-link">Reportes</button>
            <button class="nav-link active">Compras</button>
            <button class="nav-link">Mi Perfil</button>
        </nav>
    </header>

    <!-- PAGE -->
    <div class="page">

        <div class="section-title">Compras</div>

        <!-- FILTERS -->
        <div class="filters-row">
            <div class="filter-group">
                <div class="filter-label">Proveedor</div>
                <div class="select-wrap">
                    <select id="provFilter">
                        <option value="">Seleccionar Proveedor</option>
                        <option value="Viñas Andinas">Viñas Andinas</option>
                        <option value="Destilados Sur">Destilados Sur</option>
                        <option value="Premiums Import">Premiums Import</option>
                        <option value="Cerveza Brava">Cerveza Brava</option>
                    </select>
                    <span class="select-arrow">▼</span>
                </div>
            </div>
            <div class="filter-group">
                <div class="filter-label">Buscar Producto</div>
                <div class="search-wrap">
                    <input type="text" id="searchInput" placeholder="Buscar producto" />
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <circle cx="11" cy="11" r="8" />
                        <line x1="21" y1="21" x2="16.65" y2="16.65" />
                    </svg>
                </div>
            </div>
            <div class="filter-group">
                <div class="filter-label">Fecha</div>
                <input class="date-input" type="date" id="dateInput" value="2026-03-21" />
            </div>
        </div>

        <!-- TABLE CARD -->
        <div class="table-card">

            <!-- HEAD -->
            <div class="tbl-head">
                <div class="th"></div>
                <div class="th left">Producto</div>
                <div class="th">Stock</div>
                <div class="th">Precio</div>
                <div class="th">Precio Unidad</div>
                <div class="th">Proveedor</div>
                <div class="th">Cantidad</div>
                <div class="th">Subtotal</div>
            </div>

            <!-- BODY -->
            <div class="tbl-body" id="tableBody"></div>

            <!-- TOTAL -->
            <div class="total-row">
                <div class="total-label">Total</div>
                <div class="total-value" id="totalValue">$0.00</div>
            </div>

            <!-- ACTIONS -->
            <div class="actions-row">
                <button class="btn btn-add" id="openModal">+ Agregar Producto</button>
                <button class="btn btn-cancel" id="btnCancel">Cancelar</button>
                <button class="btn btn-clear" id="btnClear">Limpiar</button>
                <button class="btn btn-confirm" id="btnConfirm">Confirmar</button>
            </div>
        </div>

    </div>

    <!-- MODAL: ADD PRODUCT -->
    <div class="modal-bg" id="modalBg">
        <div class="modal">
            <button class="modal-close" id="closeModal">✕</button>
            <div class="modal-title">Agregar Producto</div>
            <div class="form-row">
                <div class="form-label">Nombre del Producto</div>
                <input class="form-input" type="text" id="mNombre" placeholder="Ej. Licor de Café" />
            </div>
            <div class="form-row-2">
                <div class="form-row">
                    <div class="form-label">Stock Actual</div>
                    <input class="form-input" type="number" id="mStock" placeholder="20" min="0" />
                </div>
                <div class="form-row">
                    <div class="form-label">Precio ($)</div>
                    <input class="form-input" type="number" id="mPrecio" placeholder="102.00" min="0" step="0.01" />
                </div>
            </div>
            <div class="form-row-2">
                <div class="form-row">
                    <div class="form-label">Precio por Unidad ($)</div>
                    <input class="form-input" type="number" id="mPrecioU" placeholder="17.00" min="0" step="0.01" />
                </div>
                <div class="form-row">
                    <div class="form-label">Unidades por Caja</div>
                    <input class="form-input" type="number" id="mUxC" placeholder="6" min="1" />
                </div>
            </div>
            <div class="form-row">
                <div class="form-label">Proveedor</div>
                <select class="form-select-m" id="mProv">
                    <option value="">Seleccionar Proveedor</option>
                    <option>Viñas Andinas</option>
                    <option>Destilados Sur</option>
                    <option>Premiums Import</option>
                    <option>Cerveza Brava</option>
                </select>
            </div>
            <div class="modal-footer">
                <button class="mbtn-cancel" id="closeModal2">Cancelar</button>
                <button class="mbtn-add" id="addProductBtn">Agregar</button>
            </div>
        </div>
    </div>

    <div class="toast" id="toast"></div>

    <script>
        /* ── BOTTLE SVG placeholder ── */
        const BOTTLE_SVG = `<svg viewBox="0 0 40 60" fill="none" xmlns="http://www.w3.org/2000/svg">
  <rect x="14" y="2" width="12" height="8" rx="2" fill="rgba(197,160,89,.3)"/>
  <path d="M10 18 Q8 22 8 30 L8 52 Q8 56 12 56 L28 56 Q32 56 32 52 L32 30 Q32 22 30 18 L26 10 L14 10 Z" fill="rgba(197,160,89,.15)" stroke="rgba(197,160,89,.4)" stroke-width="1"/>
  <rect x="12" y="28" width="16" height="2" rx="1" fill="rgba(197,160,89,.3)"/>
  <rect x="12" y="33" width="16" height="8" rx="1" fill="rgba(197,160,89,.2)"/>
</svg>`;

        const PROVEEDORES = ['Viñas Andinas', 'Destilados Sur', 'Premiums Import', 'Cerveza Brava'];

        /* Initial data */
        let rows = [{
                id: 1,
                nombre: 'LICOR DE CAFÉ',
                stock: 20,
                precio: 102,
                precioU: 17,
                uxc: 6,
                proveedor: '',
                qty: 5
            },
            {
                id: 2,
                nombre: 'LICOR DE CAFÉ',
                stock: 20,
                precio: 102,
                precioU: 17,
                uxc: 6,
                proveedor: '',
                qty: 5
            },
            {
                id: 3,
                nombre: 'LICOR DE CAFÉ',
                stock: 20,
                precio: 102,
                precioU: 17,
                uxc: 6,
                proveedor: '',
                qty: 5
            },
            {
                id: 4,
                nombre: 'LICOR DE CAFÉ',
                stock: 20,
                precio: 102,
                precioU: 17,
                uxc: 6,
                proveedor: '',
                qty: 5
            },
            {
                id: 5,
                nombre: 'LICOR DE CAFÉ',
                stock: 20,
                precio: 102,
                precioU: 17,
                uxc: 6,
                proveedor: '',
                qty: 5
            },
            {
                id: 6,
                nombre: 'LICOR DE CAFÉ',
                stock: 20,
                precio: 102,
                precioU: 17,
                uxc: 6,
                proveedor: '',
                qty: 5
            },
            {
                id: 7,
                nombre: 'LICOR DE CAFÉ',
                stock: 20,
                precio: 102,
                precioU: 17,
                uxc: 6,
                proveedor: '',
                qty: 5
            },
            {
                id: 8,
                nombre: 'LICOR DE CAFÉ',
                stock: 20,
                precio: 102,
                precioU: 17,
                uxc: 6,
                proveedor: '',
                qty: 5
            },
        ];
        let nextId = 9;
        let searchTerm = '',
            provFilter = '';

        function subtotal(r) {
            return r.precio * r.qty;
        }

        function getFiltered() {
            return rows.filter(r => {
                const q = searchTerm.toLowerCase();
                const ms = !q || r.nombre.toLowerCase().includes(q);
                const mp = !provFilter || r.proveedor === provFilter;
                return ms && mp;
            });
        }

        function calcTotal() {
            return rows.reduce((acc, r) => acc + subtotal(r), 0);
        }

        function fmt(n) {
            return '$' + n.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        }

        function render() {
            const filtered = getFiltered();
            const tbody = document.getElementById('tableBody');

            tbody.innerHTML = filtered.map(r => `
    <div class="tbl-row" data-id="${r.id}">
      <div class="tc">
        <div class="prod-img">${BOTTLE_SVG}</div>
      </div>
      <div class="tc left">
        <div class="prod-name">${r.nombre}</div>
      </div>
      <div class="tc">
        <span class="stock-val">${r.stock}</span>
      </div>
      <div class="tc">
        <span class="price-main">${fmt(r.precio)}</span>
      </div>
      <div class="tc">
        <div>
          <div class="price-main">${fmt(r.precioU)}</div>
          <div class="price-unit">por caja (${r.uxc} unid.)</div>
        </div>
      </div>
      <div class="tc">
        <select class="row-select" onchange="changeProveedor(${r.id}, this.value)">
          <option value="">Selec. Proveedor</option>
          ${PROVEEDORES.map(p => `<option value="${p}"${r.proveedor===p?' selected':''}>${p}</option>`).join('')}
        </select>
      </div>
      <div class="tc">
        <div class="qty-wrap">
          <button class="qty-btn" onclick="changeQty(${r.id}, -1)">−</button>
          <input class="qty-input" type="number" value="${r.qty}" min="1"
            onchange="setQty(${r.id}, this.value)" oninput="setQty(${r.id}, this.value)" />
          <button class="qty-btn" onclick="changeQty(${r.id}, 1)">+</button>
          <button class="del-btn" onclick="deleteRow(${r.id})">✕</button>
        </div>
      </div>
      <div class="tc">
        <span class="subtotal-val">${fmt(subtotal(r))}</span>
      </div>
    </div>
  `).join('');

            document.getElementById('totalValue').textContent = fmt(calcTotal());
        }

        function changeProveedor(id, val) {
            const r = rows.find(r => r.id === id);
            if (r) {
                r.proveedor = val;
                updateTotal();
            }
        }

        function changeQty(id, delta) {
            const r = rows.find(r => r.id === id);
            if (r) {
                r.qty = Math.max(1, r.qty + delta);
                render();
            }
        }

        function setQty(id, val) {
            const r = rows.find(r => r.id === id);
            const n = parseInt(val);
            if (r && !isNaN(n) && n >= 1) {
                r.qty = n;
                updateTotal();
            }
        }

        function updateTotal() {
            document.getElementById('totalValue').textContent = fmt(calcTotal());
        }

        function deleteRow(id) {
            rows = rows.filter(r => r.id !== id);
            render();
        }

        /* MODAL */
        const modalBg = document.getElementById('modalBg');
        document.getElementById('openModal').onclick = () => modalBg.classList.add('open');
        document.getElementById('closeModal').onclick = () => modalBg.classList.remove('open');
        document.getElementById('closeModal2').onclick = () => modalBg.classList.remove('open');
        modalBg.addEventListener('click', e => {
            if (e.target === modalBg) modalBg.classList.remove('open');
        });

        document.getElementById('addProductBtn').onclick = () => {
            const nombre = document.getElementById('mNombre').value.trim();
            const stock = parseInt(document.getElementById('mStock').value) || 0;
            const precio = parseFloat(document.getElementById('mPrecio').value) || 0;
            const precioU = parseFloat(document.getElementById('mPrecioU').value) || 0;
            const uxc = parseInt(document.getElementById('mUxC').value) || 6;
            const prov = document.getElementById('mProv').value;

            if (!nombre) {
                showToast('Ingresa el nombre del producto');
                return;
            }

            rows.push({
                id: nextId++,
                nombre: nombre.toUpperCase(),
                stock,
                precio,
                precioU,
                uxc,
                proveedor: prov,
                qty: 1
            });
            modalBg.classList.remove('open');

            // reset
            ['mNombre', 'mStock', 'mPrecio', 'mPrecioU', 'mUxC'].forEach(id => document.getElementById(id).value = '');
            document.getElementById('mProv').value = '';

            render();
            showToast('Producto agregado correctamente');
        };

        /* FILTERS */
        document.getElementById('searchInput').addEventListener('input', e => {
            searchTerm = e.target.value;
            render();
        });
        document.getElementById('provFilter').addEventListener('change', e => {
            provFilter = e.target.value;
            render();
        });

        /* ACTION BUTTONS */
        document.getElementById('btnClear').onclick = () => {
            rows = [];
            render();
            showToast('Lista limpiada');
        };
        document.getElementById('btnCancel').onclick = () => {
            if (confirm('¿Cancelar la compra actual?')) {
                rows = [];
                render();
                showToast('Compra cancelada');
            }
        };
        document.getElementById('btnConfirm').onclick = () => {
            if (rows.length === 0) {
                showToast('No hay productos en la compra');
                return;
            }
            showToast(`Compra confirmada — Total: ${fmt(calcTotal())}`);
        };

        /* TOAST */
        function showToast(msg) {
            const t = document.getElementById('toast');
            t.textContent = msg;
            t.classList.add('show');
            setTimeout(() => t.classList.remove('show'), 2800);
        }

        render();
    </script>
</body>

</html>