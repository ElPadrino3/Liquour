<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Liquour — Empleados Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet" />
   <link rel="stylesheet" href="./../Assets/CSS/empleados.css">
</head>

<body>

    <!-- TOPBAR -->
    <header class="topbar">
        <nav class="nav-links">
            <button class="nav-link">Dashboard</button>
            <button class="nav-link">Catálogo</button>
            <button class="nav-link active">Empleados</button>
            <button class="nav-link">Reportes</button>
            <button class="nav-link">Compras</button>
            <button class="nav-link">Mi Perfil</button>
        </nav>
    </header>

    <!-- PAGE -->
    <div class="page">

        <!-- + NUEVO button (top right) -->
        <div class="top-action">
            <button class="btn-nuevo" id="btnNuevo">+ Nuevo</button>
        </div>

        <!-- TABLE -->
        <div class="table-card">
            <table id="mainTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Usuario</th>
                        <th>Estado</th>
                        <th>Fecha de Registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="tableBody"></tbody>
            </table>
        </div>

    </div>

    <!-- MODAL: NUEVO / EDITAR EMPLEADO -->
    <div class="modal-bg" id="modalBg">
        <div class="modal">
            <button class="modal-close" id="closeModal">✕</button>
            <div class="modal-title" id="modalTitle">Nuevo Empleado</div>

            <div class="form-row-2">
                <div class="form-row">
                    <div class="form-label">Nombre</div>
                    <input class="form-input" type="text" id="fNombre" placeholder="Aldo Isai" />
                </div>
                <div class="form-row">
                    <div class="form-label">Apellidos</div>
                    <input class="form-input" type="text" id="fApellido" placeholder="Galindo Calles" />
                </div>
            </div>
            <div class="form-row">
                <div class="form-label">Usuario</div>
                <input class="form-input" type="text" id="fUsuario" placeholder="EL Padrino 3" />
            </div>
            <div class="form-row">
                <div class="form-label">Contraseña</div>
                <input class="form-input" type="password" id="fPass" placeholder="••••••••" />
            </div>
            <div class="form-row-2">
                <div class="form-row">
                    <div class="form-label">Rol</div>
                    <select class="form-sel" id="fRol">
                        <option value="Vendedor">Vendedor</option>
                        <option value="Cajero">Cajero</option>
                        <option value="Supervisor">Supervisor</option>
                        <option value="Almacenero">Almacenero</option>
                        <option value="Administrador">Administrador</option>
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-label">Estado</div>
                    <select class="form-sel" id="fEstado">
                        <option value="Activo">Activo</option>
                        <option value="Inactivo">Inactivo</option>
                        <option value="Vacaciones">Vacaciones</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-label">Fecha de Registro</div>
                <input class="form-input" type="date" id="fFecha" />
            </div>

            <div class="modal-footer">
                <button class="mbtn mbtn-cancel" id="closeModal2">Cancelar</button>
                <button class="mbtn mbtn-save" id="saveBtn">Guardar</button>
            </div>
        </div>
    </div>

    <!-- MODAL: CONFIRMAR ELIMINAR -->
    <div class="modal-bg" id="confirmBg">
        <div class="modal confirm-modal">
            <button class="modal-close" id="closeConfirm">✕</button>
            <div class="modal-title">Eliminar Empleado</div>
            <div class="confirm-msg" id="confirmMsg">¿Estás seguro de eliminar a este empleado?<br />Esta acción no se puede deshacer.</div>
            <div class="modal-footer" style="justify-content:center">
                <button class="mbtn mbtn-cancel" id="cancelConfirm">Cancelar</button>
                <button class="mbtn mbtn-del" id="confirmDel">Eliminar</button>
            </div>
        </div>
    </div>

    <div class="toast" id="toast"></div>

    <script>
        /* ─── DATA ─── */
        let empleados = [{
                id: 1,
                nombre: 'Aldo Isai Galindo Calles',
                usuario: 'EL Padrino 3',
                estado: 'Activo',
                fecha: '20/03/2026',
                rol: 'Vendedor'
            },
            {
                id: 2,
                nombre: 'Aldo Isai Galindo Calles',
                usuario: 'EL Padrino 3',
                estado: 'Activo',
                fecha: '20/03/2026',
                rol: 'Cajero'
            },
            {
                id: 3,
                nombre: 'Aldo Isai Galindo Calles',
                usuario: 'EL Padrino 3',
                estado: 'Activo',
                fecha: '20/03/2026',
                rol: 'Supervisor'
            },
            {
                id: 4,
                nombre: 'Aldo Isai Galindo Calles',
                usuario: 'EL Padrino 3',
                estado: 'Activo',
                fecha: '20/03/2026',
                rol: 'Vendedor'
            },
            {
                id: 5,
                nombre: 'Aldo Isai Galindo Calles',
                usuario: 'EL Padrino 3',
                estado: 'Activo',
                fecha: '20/03/2026',
                rol: 'Almacenero'
            },
            {
                id: 6,
                nombre: 'Aldo Isai Galindo Calles',
                usuario: 'EL Padrino 3',
                estado: 'Activo',
                fecha: '20/03/2026',
                rol: 'Vendedor'
            },
        ];
        let nextId = 7;
        let editingId = null;
        let deletingId = null;
        const MIN_ROWS = 10; // minimum visible rows (empty filler)

        /* ─── RENDER ─── */
        function statusClass(e) {
            if (e === 'Activo') return 'status-activo';
            if (e === 'Inactivo') return 'status-inactivo';
            return 'status-vac';
        }

        function fmtDate(dateStr) {
            // converts YYYY-MM-DD to DD/MM/YYYY
            if (!dateStr) return '';
            if (dateStr.includes('/')) return dateStr;
            const [y, m, d] = dateStr.split('-');
            return `${d}/${m}/${y}`;
        }

        const EDIT_ICON = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
  <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
  <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
</svg>`;

        const DEL_ICON = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
  <polyline points="3 6 5 6 21 6"/>
  <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
  <path d="M10 11v6M14 11v6"/>
  <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
</svg>`;

        function render() {
            const tbody = document.getElementById('tableBody');
            let html = empleados.map(e => `
    <tr>
      <td class="td-id">${e.id}</td>
      <td class="td-name">${e.nombre}</td>
      <td class="td-user">${e.usuario}</td>
      <td><span class="${statusClass(e.estado)}">${e.estado}</span></td>
      <td class="td-date">${e.fecha}</td>
      <td>
        <div class="actions-cell">
          <button class="icon-btn icon-edit" title="Editar" onclick="openEdit(${e.id})">${EDIT_ICON}</button>
          <button class="icon-btn icon-del"  title="Eliminar" onclick="openDelete(${e.id})">${DEL_ICON}</button>
        </div>
      </td>
    </tr>`).join('');

            // filler empty rows
            const fillers = Math.max(0, MIN_ROWS - empleados.length);
            for (let i = 0; i < fillers; i++) {
                html += `<tr class="empty-row"><td></td><td></td><td></td><td></td><td></td><td></td></tr>`;
            }

            tbody.innerHTML = html;
        }

        /* ─── MODAL: NEW ─── */
        const modalBg = document.getElementById('modalBg');

        function openNew() {
            editingId = null;
            document.getElementById('modalTitle').textContent = 'Nuevo Empleado';
            document.getElementById('fNombre').value = '';
            document.getElementById('fApellido').value = '';
            document.getElementById('fUsuario').value = '';
            document.getElementById('fPass').value = '';
            document.getElementById('fRol').value = 'Vendedor';
            document.getElementById('fEstado').value = 'Activo';
            // default date = today
            const today = new Date();
            document.getElementById('fFecha').value = today.toISOString().split('T')[0];
            modalBg.classList.add('open');
        }

        function openEdit(id) {
            const e = empleados.find(x => x.id === id);
            if (!e) return;
            editingId = id;
            document.getElementById('modalTitle').textContent = 'Editar Empleado';

            // Split nombre into partes
            const parts = e.nombre.split(' ');
            document.getElementById('fNombre').value = parts.slice(0, 2).join(' ');
            document.getElementById('fApellido').value = parts.slice(2).join(' ');
            document.getElementById('fUsuario').value = e.usuario;
            document.getElementById('fPass').value = '';
            document.getElementById('fRol').value = e.rol;
            document.getElementById('fEstado').value = e.estado;
            // Convert DD/MM/YYYY → YYYY-MM-DD for input
            if (e.fecha && e.fecha.includes('/')) {
                const [d, m, y] = e.fecha.split('/');
                document.getElementById('fFecha').value = `${y}-${m}-${d}`;
            } else {
                document.getElementById('fFecha').value = e.fecha || '';
            }
            modalBg.classList.add('open');
        }

        function closeModal() {
            modalBg.classList.remove('open');
        }

        document.getElementById('btnNuevo').onclick = openNew;
        document.getElementById('closeModal').onclick = closeModal;
        document.getElementById('closeModal2').onclick = closeModal;
        modalBg.addEventListener('click', e => {
            if (e.target === modalBg) closeModal();
        });

        document.getElementById('saveBtn').onclick = () => {
            const nombre = document.getElementById('fNombre').value.trim();
            const apellido = document.getElementById('fApellido').value.trim();
            const usuario = document.getElementById('fUsuario').value.trim();
            const rol = document.getElementById('fRol').value;
            const estado = document.getElementById('fEstado').value;
            const fechaRaw = document.getElementById('fFecha').value;

            if (!nombre || !usuario) {
                showToast('Completa los campos requeridos');
                return;
            }

            const fullName = apellido ? `${nombre} ${apellido}` : nombre;
            const fecha = fmtDate(fechaRaw);

            if (editingId !== null) {
                const idx = empleados.findIndex(x => x.id === editingId);
                if (idx !== -1) {
                    empleados[idx] = {
                        ...empleados[idx],
                        nombre: fullName,
                        usuario,
                        rol,
                        estado,
                        fecha
                    };
                    showToast('Empleado actualizado correctamente');
                }
            } else {
                empleados.push({
                    id: nextId++,
                    nombre: fullName,
                    usuario,
                    rol,
                    estado,
                    fecha
                });
                showToast('Empleado creado correctamente');
            }

            closeModal();
            render();
        };

        /* ─── MODAL: DELETE ─── */
        const confirmBg = document.getElementById('confirmBg');

        function openDelete(id) {
            deletingId = id;
            const e = empleados.find(x => x.id === id);
            if (e) document.getElementById('confirmMsg').innerHTML =
                `¿Estás seguro de eliminar a <strong style="color:var(--cream)">${e.nombre}</strong>?<br/>Esta acción no se puede deshacer.`;
            confirmBg.classList.add('open');
        }

        function closeConfirm() {
            confirmBg.classList.remove('open');
            deletingId = null;
        }

        document.getElementById('closeConfirm').onclick = closeConfirm;
        document.getElementById('cancelConfirm').onclick = closeConfirm;
        confirmBg.addEventListener('click', e => {
            if (e.target === confirmBg) closeConfirm();
        });

        document.getElementById('confirmDel').onclick = () => {
            if (deletingId === null) return;
            empleados = empleados.filter(e => e.id !== deletingId);
            closeConfirm();
            render();
            showToast('Empleado eliminado');
        };

        /* ─── TOAST ─── */
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