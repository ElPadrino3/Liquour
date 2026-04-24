<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../../../Config/Liquour_bdd.php';
$db = new BDD();
$conn = $db->conectar();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (isset($_POST['ajax_action']) && $_POST['ajax_action'] === 'toggle_status') {
        $id = $_POST['id_usuario'];
        $estado = $_POST['estado'];
        $stmtUpdate = $conn->prepare("UPDATE usuarios SET estado = ? WHERE id_usuario = ?");
        $stmtUpdate->execute([$estado, $id]);
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
    }

    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add_employee') {
            $nombre = $_POST['nombre'] ?? '';
            $usuario = $_POST['usuario'] ?? '';
            $pass = $_POST['password'] ?? '';
            $rol = $_POST['rol'] ?? 'cajero';
            $num_caja = ($rol === 'cajero') ? ($_POST['num_caja'] ?? 1) : null;

            if (!empty($nombre) && !empty($usuario) && !empty($pass)) {
                $stmtInsert = $conn->prepare("INSERT INTO usuarios (nombre, usuario, password, rol, num_caja, estado) VALUES (?, ?, ?, ?, ?, 1)");
                $stmtInsert->execute([$nombre, $usuario, $pass, $rol, $num_caja]);
            }
            header("Location: empleados.php");
            exit;
        }

        if ($_POST['action'] === 'edit_employee') {
            $id_usuario = $_POST['id_usuario'];
            $nombre = $_POST['nombre'];
            $usuario = $_POST['usuario'];
            $pass = $_POST['password'];
            $rol = $_POST['rol'];
            
            $num_caja = ($rol === 'cajero') ? ($_POST['num_caja'] ?? 1) : null;

            $query = "UPDATE usuarios SET nombre = ?, usuario = ?, rol = ?, num_caja = ? WHERE id_usuario = ?";
            $params = [$nombre, $usuario, $rol, $num_caja, $id_usuario];

            if (!empty($pass)) {
                $query = "UPDATE usuarios SET nombre = ?, usuario = ?, password = ?, rol = ?, num_caja = ? WHERE id_usuario = ?";
                $params = [$nombre, $usuario, $pass, $rol, $num_caja, $id_usuario];
            }

            $stmtEdit = $conn->prepare($query);
            $stmtEdit->execute($params);

            if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
                $nombreArchivo = time() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "", basename($_FILES['foto_perfil']['name']));
                
                $directorioFisico = __DIR__ . '/../../../Assets/IMG/perfiles/';
                $rutaWeb = '../../../Assets/IMG/perfiles/' . $nombreArchivo;
                
                if (!file_exists($directorioFisico)) {
                    mkdir($directorioFisico, 0777, true);
                }

                if (move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $directorioFisico . $nombreArchivo)) {
                    $stmtFoto = $conn->prepare("UPDATE usuarios SET foto_perfil = ? WHERE id_usuario = ?");
                    $stmtFoto->execute([$rutaWeb, $id_usuario]);
                }
            }

            header("Location: empleados.php");
            exit;
        }
    }
}

$rol_sesion = $_SESSION['rol'] ?? 'empleado';
$nombre_sesion = $_SESSION['nombre'] ?? 'Usuario';
$id_usuario_sesion = $_SESSION['id_usuario'] ?? 0;

$nombre_url = urlencode($nombre_sesion);
$avatar = "https://ui-avatars.com/api/?name={$nombre_url}&background=C5A059&color=1A1A1A&size=128";

$stmtEmpleados = $conn->query("
    SELECT 
        u.id_usuario,
        u.nombre, 
        u.usuario,
        u.rol, 
        u.estado,
        u.num_caja,
        u.foto_perfil,
        DATE_FORMAT(u.fecha_registro, '%Y-%m-%d') as fecha_ingreso,
        COALESCE(SUM(v.total), 0) as total_ventas,
        COUNT(v.id_venta) as total_ordenes
    FROM usuarios u
    LEFT JOIN ventas v ON u.id_usuario = v.id_usuario 
        AND MONTH(v.fecha) = MONTH(CURDATE()) 
        AND YEAR(v.fecha) = YEAR(CURDATE())
    GROUP BY u.id_usuario
    ORDER BY total_ventas DESC
");
$empleados = $stmtEmpleados->fetchAll(PDO::FETCH_ASSOC);

$totalEmpleados = count($empleados);
$activos = 0;
$inactivos = 0;
$ventasEquipo = 0;

$mejorVendedorNombre = "N/A";
$mejorVendedorVentas = 0;

if ($totalEmpleados > 0) {
    $mejorVendedorNombre = $empleados[0]['nombre'];
    $mejorVendedorVentas = $empleados[0]['total_ventas'];
}

foreach ($empleados as $emp) {
    $ventasEquipo += $emp['total_ventas'];
    if ($emp['estado'] == 1) {
        $activos++;
    } else {
        $inactivos++;
    }
}

$empleadosJSON = json_encode($empleados);
$db->desconectar();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Gestión de Empleados - Liquour</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <link rel="stylesheet" href="../../../Assets/CSS/nav.css">
    <link rel="stylesheet" href="../../../Assets/CSS/style.css">
    <link rel="stylesheet" href="../../../Assets/CSS/-Catalogo_Admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- ========================================= -->
    <!-- SISTEMA DE TEMAS - SINCRONIZAR COLORES -->
    <!-- ========================================= -->
    <style>
        :root {
            --tema-color: #C5A059;
            --tema-color-rgb: 197, 160, 89;
            --bg-carbon: #1A1A1A;
            --text-cream: #F5F5DC;
            --border-color: #4A4A4A;
        }
        
        .estado-switch { display: inline-flex; align-items: center; justify-content: center; }
        .switch { position: relative; display: inline-block; width: 46px; height: 24px; margin-right: 10px; }
        .switch input { display: none; }
        .slider { position: absolute; inset: 0; cursor: pointer; border-radius: 999px; background: #8b0000; transition: all 0.25s ease; }
        .slider::before { content: ""; position: absolute; width: 18px; height: 18px; left: 3px; top: 3px; border-radius: 50%; background: #fff; transition: all 0.25s ease; box-shadow: 0 1px 4px rgba(0,0,0,0.35); }
        .switch input:checked + .slider { background: var(--tema-color); }
        .switch input:checked + .slider::before { transform: translateX(22px); }
        .status-text { font-size: 11px; font-weight: bold; text-transform: uppercase; width: 60px; display: inline-block; }
        .status-text.activo { color: var(--tema-color); }
        .status-text.inactivo { color: #8b0000; }
        .btn-edit { background: none; border: none; color: var(--tema-color); cursor: pointer; font-size: 16px; transition: 0.2s; margin-left: 10px; }
        .btn-edit:hover { color: #fff; transform: scale(1.1); }
        .caja-input-container { display: none; }
        
        /* Forzar estilos del header */
        .navbar-liquour {
            background: var(--bg-carbon) !important;
            border-bottom: 1px solid var(--tema-color) !important;
        }
        
        .nav-item:hover {
            color: var(--tema-color) !important;
        }
        
        .nav-item::after {
            background: var(--tema-color) !important;
        }
        
        .add-btn {
            background: var(--tema-color) !important;
            color: var(--bg-carbon) !important;
        }
        
        .add-btn:hover {
            background: var(--tema-color) !important;
            filter: brightness(1.1) !important;
        }
        
        .kpi-tag, .card-title, .page-heading {
            color: var(--tema-color) !important;
        }
        
        .page-heading::after {
            background: var(--tema-color) !important;
        }
        
        .prog-fg, .rank-bar-fg {
            background: var(--tema-color) !important;
        }
        
        .td-gold, .rank-val, .emp-stat-val {
            color: var(--tema-color) !important;
        }
        
        .emp-avatar {
            background: rgba(197, 160, 89, 0.12) !important;
            border: 2px solid rgba(197, 160, 89, 0.3) !important;
            color: var(--tema-color) !important;
        }
    </style>
    
    <script>
    (function sincronizarEmpleados() {
        // Cargar colores desde localStorage
        const coloresGuardados = localStorage.getItem('liquour_colors');
        if (coloresGuardados) {
            try {
                const colores = JSON.parse(coloresGuardados);
                const dorado = colores['--color-dorado'] || '#C5A059';
                const fondo = colores['--bg-carbon'] || '#1A1A1A';
                const texto = colores['--text-blanco-crema'] || '#F5F5DC';
                const borde = colores['--border-fuerte'] || '#4A4A4A';
                
                document.documentElement.style.setProperty('--tema-color', dorado);
                document.documentElement.style.setProperty('--bg-carbon', fondo);
                document.documentElement.style.setProperty('--text-cream', texto);
                document.documentElement.style.setProperty('--border-color', borde);
                
                console.log('🎨 Empleados sincronizado con tema:', dorado);
            } catch(e) {
                console.log('Error cargando colores:', e);
            }
        }
        
        // Cargar logo
        const logoGuardado = localStorage.getItem('liquour_theme_logo');
        if (logoGuardado) {
            setTimeout(function() {
                const logos = document.querySelectorAll('.logo-img, .theme-logo, #main-logo');
                logos.forEach(function(img) {
                    if (img && img.tagName === 'IMG') img.src = logoGuardado;
                });
            }, 100);
        }
    })();
    
    window.addEventListener('storage', function(e) {
        if (e.key === 'liquour_colors' || e.key === 'liquour_theme_logo') {
            location.reload();
        }
    });
    </script>
</head>
<script>
(function sincronizarEmpleados() {
    // Obtener colores guardados desde el menú (AJUSTES)
    const coloresGuardados = localStorage.getItem('liquour_colors');
    if (coloresGuardados) {
        try {
            const colores = JSON.parse(coloresGuardados);
            // Mapeo de variables del menú a las del CSS global
            const dorado   = colores['--color-dorado'] || '#C5A059';
            const fondo    = colores['--bg-carbon'] || '#1A1A1A';
            const texto    = colores['--text-blanco-crema'] || '#F5F5DC';
            const borde    = colores['--border-fuerte'] || '#4A4A4A';
            
            // Colores derivados (para mantener armonía)
            const doradoClaro = lightenColor(dorado, 15);
            const doradoOscuro = darkenColor(dorado, 20);
            const superficie = darkenColor(fondo, 5);
            const superficieClara = lightenColor(fondo, 8);
            
            // Variables específicas del CSS global
            document.documentElement.style.setProperty('--gold', dorado);
            document.documentElement.style.setProperty('--gold-lt', doradoClaro);
            document.documentElement.style.setProperty('--gold-dk', doradoOscuro);
            document.documentElement.style.setProperty('--carbon', fondo);
            document.documentElement.style.setProperty('--oxford', borde);
            document.documentElement.style.setProperty('--cream', texto);
            document.documentElement.style.setProperty('--surface', superficie);
            document.documentElement.style.setProperty('--surface2', superficieClara);
            document.documentElement.style.setProperty('--border', dorado + '26');
            document.documentElement.style.setProperty('--border-md', dorado + '4D');
            
            // Variables adicionales que usa empleados.php (por si acaso)
            document.documentElement.style.setProperty('--tema-color', dorado);
            document.documentElement.style.setProperty('--bg-carbon', fondo);
            document.documentElement.style.setProperty('--text-cream', texto);
            document.documentElement.style.setProperty('--border-color', borde);
            
            console.log('🎨 Empleados sincronizado con tema:', { dorado, fondo, texto });
        } catch(e) {
            console.log('Error cargando colores:', e);
        }
    }
    
    // Función para aclarar un color
    function lightenColor(hex, percent) {
        let r = parseInt(hex.slice(1,3), 16);
        let g = parseInt(hex.slice(3,5), 16);
        let b = parseInt(hex.slice(5,7), 16);
        r = Math.min(255, r + (r * percent / 100));
        g = Math.min(255, g + (g * percent / 100));
        b = Math.min(255, b + (b * percent / 100));
        return `#${Math.round(r).toString(16).padStart(2,'0')}${Math.round(g).toString(16).padStart(2,'0')}${Math.round(b).toString(16).padStart(2,'0')}`;
    }
    
    // Función para oscurecer un color
    function darkenColor(hex, percent) {
        let r = parseInt(hex.slice(1,3), 16);
        let g = parseInt(hex.slice(3,5), 16);
        let b = parseInt(hex.slice(5,7), 16);
        r = Math.max(0, r - (r * percent / 100));
        g = Math.max(0, g - (g * percent / 100));
        b = Math.max(0, b - (b * percent / 100));
        return `#${Math.round(r).toString(16).padStart(2,'0')}${Math.round(g).toString(16).padStart(2,'0')}${Math.round(b).toString(16).padStart(2,'0')}`;
    }
    
    // Cargar logo guardado
    const logoGuardado = localStorage.getItem('liquour_theme_logo');
    if (logoGuardado) {
        setTimeout(() => {
            const logos = document.querySelectorAll('.logo-img, .theme-logo, #main-logo');
            logos.forEach(img => { if (img && img.tagName === 'IMG') img.src = logoGuardado; });
        }, 100);
    }
})();

// Escuchar cambios en localStorage (cuando se cambia el tema desde otra pestaña)
window.addEventListener('storage', function(e) {
    if (e.key === 'liquour_colors' || e.key === 'liquour_theme_logo') {
        location.reload();
    }
});

// Escuchar el evento personalizado que dispara el Theme Manager
window.addEventListener('liquourThemeChanged', function() {
    setTimeout(() => {
        location.reload();
    }, 100);
});
</script>
<body>

<?php @include '../../Layout/nav_admin.php'; ?> 
<?php @include '../../../Layout/header_admin.php'; ?>

<div id="modalNewEmp" class="modal-overlay" style="display: none;">
    <div class="modal-container modal-animate-in">
        <div class="modal-header-perfil">
            <h3>Nuevo Empleado</h3>
            <button class="close-modal" onclick="cerrarModal('modalNewEmp')">&times;</button>
        </div>
        <form method="POST" action="">
            <input type="hidden" name="action" value="add_employee">
            <div class="admin-input-group" style="margin-bottom:15px;">
                <label>Nombre Completo</label>
                <input type="text" name="nombre" placeholder="Ej. Carlos Martínez" required>
            </div>
            <div class="admin-input-group" style="margin-bottom:15px;">
                <label>Usuario</label>
                <input type="text" name="usuario" placeholder="Ej. cmartinez" required>
            </div>
            <div class="admin-input-group" style="margin-bottom:15px;">
                <label>Contraseña</label>
                <input type="password" name="password" placeholder="***" required>
            </div>
            <div class="admin-input-group" style="margin-bottom:15px;">
                <label>Rol</label>
                <select name="rol" class="filter-select select-rol-modal" style="width:100%; border-radius:4px; padding:10px;" required>
                    <option value="cajero">Cajero</option>
                    <option value="admin">Administrador</option>
                </select>
            </div>
            <div class="admin-input-group caja-input-container" style="margin-bottom:25px; display: block;">
                <label>Número de Caja</label>
                <input type="number" name="num_caja" placeholder="Ej. 1" value="1" min="1">
            </div>
            <button type="submit" class="btn-confirmar-admin">Guardar Empleado</button>
        </form>
    </div>
</div>

<div id="modalEditEmp" class="modal-overlay" style="display: none;">
    <div class="modal-container modal-animate-in">
        <div class="modal-header-perfil">
            <h3>Editar Empleado</h3>
            <button class="close-modal" onclick="cerrarModal('modalEditEmp')">&times;</button>
        </div>
        <form method="POST" action="" enctype="multipart/form-data">
            <input type="hidden" name="action" value="edit_employee">
            <input type="hidden" name="id_usuario" id="edit_id_usuario">
            <div class="admin-input-group" style="margin-bottom:15px; text-align:center;">
                <img id="edit_preview_foto" src="" style="width:80px; height:80px; border-radius:50%; object-fit:cover; margin-bottom:10px; border:2px solid var(--tema-color); display:none;">
                <label style="display:block; margin-bottom:5px;">Foto de Perfil</label>
                <input type="file" name="foto_perfil" accept="image/*" style="color:#fff;">
            </div>
            <div class="admin-input-group" style="margin-bottom:15px;">
                <label>Nombre Completo</label>
                <input type="text" name="nombre" id="edit_nombre" required>
            </div>
            <div class="admin-input-group" style="margin-bottom:15px;">
                <label>Usuario</label>
                <input type="text" name="usuario" id="edit_usuario" required>
            </div>
            <div class="admin-input-group" style="margin-bottom:15px;">
                <label>Nueva Contraseña <small style="color:#888;">(Dejar en blanco para no cambiar)</small></label>
                <input type="password" name="password" placeholder="***">
            </div>
            <div class="admin-input-group" style="margin-bottom:15px;">
                <label>Rol</label>
                <select name="rol" id="edit_rol" class="filter-select select-rol-modal" style="width:100%; border-radius:4px; padding:10px;" required>
                    <option value="cajero">Cajero</option>
                    <option value="admin">Administrador</option>
                </select>
            </div>
            <div class="admin-input-group caja-input-container" id="edit_caja_container" style="margin-bottom:25px;">
                <label>Número de Caja</label>
                <input type="number" name="num_caja" id="edit_num_caja" min="1">
            </div>
            <button type="submit" class="btn-confirmar-admin">Actualizar Empleado</button>
        </form>
    </div>
</div>

<div class="page">
  <div class="page-heading">Gestión de Empleados</div>

  <div class="kpi-row">
    <div class="kpi">
      <div class="kpi-lbl">Total Empleados</div>
      <div class="kpi-val"><?= $totalEmpleados ?></div>
      <div class="kpi-sub"><?= $activos ?> activos · <?= $inactivos ?> inactivos</div>
      <div class="kpi-tag">Planilla</div>
    </div>
    <div class="kpi">
      <div class="kpi-lbl">Ventas del Equipo (Mes)</div>
      <div class="kpi-val"><sup>$</sup><?= number_format($ventasEquipo, 2) ?></div>
      <div class="kpi-sub">Suma total del equipo</div>
      <div class="kpi-tag">Métricas actuales</div>
    </div>
    <div class="kpi">
      <div class="kpi-lbl">Mejor Vendedor</div>
      <div class="kpi-val"><?= htmlspecialchars(explode(' ', $mejorVendedorNombre)[0]) ?></div>
      <div class="kpi-sub">$<?= number_format($mejorVendedorVentas, 2) ?> este mes</div>
      <div class="kpi-tag">★ Top 1</div>
    </div>
    <div class="kpi">
      <div class="kpi-lbl">Promedio Ventas / Emp.</div>
      <div class="kpi-val"><sup>$</sup><?= ($activos > 0) ? number_format($ventasEquipo / $activos, 2) : '0.00' ?></div>
      <div class="kpi-sub">Por empleado activo</div>
      <div class="kpi-tag">Estable</div>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <div class="card-title">Equipo</div>
      <button class="add-btn btn-open-modal" onclick="abrirModal('modalNewEmp')">+ Nuevo Empleado</button>
    </div>
    <div class="emp-grid" id="empGridCards"></div>
  </div>

  <div class="two-col">
    <div class="card">
      <div class="card-header">
        <div class="card-title">Ventas por Empleado (Este Mes)</div>
      </div>
      <canvas id="empChart" height="170"></canvas>
    </div>
    <div class="card">
      <div class="card-header">
        <div class="card-title">Ranking de Ventas</div>
      </div>
      <div class="rank-list" id="rankListBody"></div>
    </div>
  </div>

  <div class="card" style="padding:0">
    <div class="toolbar">
      <div class="toolbar-left">
        <input class="search-input" type="text" placeholder="Buscar empleado…" id="searchInput" />
        <select class="filter-select" id="roleFilter">
          <option value="">Todos los roles</option>
          <option value="admin">Admin</option>
          <option value="cajero">Cajero</option>
        </select>
        <select class="filter-select" id="statusFilter">
          <option value="">Todos los estados</option>
          <option value="1">Activo</option>
          <option value="0">Inactivo</option>
        </select>
      </div>
    </div>
    <div class="tbl-wrap">
      <table>
        <thead>
          <tr>
            <th>Empleado</th>
            <th>Rol / Caja</th>
            <th>Ingreso</th>
            <th>Ventas Mes</th>
            <th>Órdenes</th>
            <th>Desempeño</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody id="tableBody"></tbody>
      </table>
    </div>
  </div>
</div>

<script>
let dbEmpleados = <?= $empleadosJSON ?>;
const maxVentasGbl = <?= ($mejorVendedorVentas > 0) ? $mejorVendedorVentas : 1 ?>;
let search = '', roleF = '', statusF = '';

function initials(n){ return n.split(' ').map(w=>w[0]).join('').slice(0,2).toUpperCase(); }

function estadoSwitchHTML(estado, id) {
  const activo = estado == 1;
  const textoClass = activo ? 'activo' : 'inactivo';
  const textoVal = activo ? 'Activo' : 'Inactivo';

  return `
    <div style="display:flex; align-items:center; justify-content:center;">
        <span class="estado-switch">
          <label class="switch">
            <input type="checkbox" class="chk-emp-${id}" onchange="toggleStatus(${id}, this.checked, this)" ${activo ? 'checked' : ''}>
            <span class="slider"></span>
          </label>
        </span>
        <span class="status-text status-text-${id} ${textoClass}" style="margin-left: 8px;">${textoVal}</span>
    </div>
  `;
}

function toggleStatus(id, isChecked, checkboxElem) {
    const estado = isChecked ? 1 : 0;
    
    const textSpans = document.querySelectorAll(`.status-text-${id}`);
    textSpans.forEach(span => {
        span.textContent = isChecked ? 'Activo' : 'Inactivo';
        span.className = `status-text status-text-${id} ${isChecked ? 'activo' : 'inactivo'}`;
    });

    const checkboxes = document.querySelectorAll(`.chk-emp-${id}`);
    checkboxes.forEach(chk => {
        if(chk !== checkboxElem) chk.checked = isChecked;
    });

    const formData = new FormData();
    formData.append('ajax_action', 'toggle_status');
    formData.append('id_usuario', id);
    formData.append('estado', estado);

    fetch('', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            const empIndex = dbEmpleados.findIndex(e => e.id_usuario == id);
            if(empIndex > -1) dbEmpleados[empIndex].estado = estado;
        }
    })
    .catch(error => console.error('Error:', error));
}

function filtered() {
  return dbEmpleados.filter(r => {
    const q = search.toLowerCase();
    const m = !q || r.nombre.toLowerCase().includes(q) || r.rol.toLowerCase().includes(q);
    
    let normRol = (r.rol === 'admin' || r.rol === 'administrador') ? 'admin' : 'cajero';
    let matchRole = !roleF || normRol === roleF;
    
    return m && matchRole && (!statusF || String(r.estado) === statusF);
  });
}

function abrirModal(id) { document.getElementById(id).style.display = 'flex'; }
function cerrarModal(id) { document.getElementById(id).style.display = 'none'; }

function editarEmpleado(id) {
    const emp = dbEmpleados.find(e => e.id_usuario == id);
    if(emp) {
        document.getElementById('edit_id_usuario').value = emp.id_usuario;
        document.getElementById('edit_nombre').value = emp.nombre;
        document.getElementById('edit_usuario').value = emp.usuario;
        
        let normRol = (emp.rol === 'admin' || emp.rol === 'administrador') ? 'admin' : 'cajero';
        document.getElementById('edit_rol').value = normRol;
        
        document.getElementById('edit_num_caja').value = emp.num_caja || '';
        
        const preview = document.getElementById('edit_preview_foto');
        if(emp.foto_perfil) {
            preview.src = emp.foto_perfil + '?v=' + new Date().getTime();
            preview.style.display = 'inline-block';
        } else {
            preview.style.display = 'none';
        }

        const cajaContainer = document.getElementById('edit_caja_container');
        cajaContainer.style.display = (normRol === 'cajero') ? 'block' : 'none';

        abrirModal('modalEditEmp');
    }
}

document.querySelectorAll('.select-rol-modal').forEach(select => {
    select.addEventListener('change', function() {
        const container = this.closest('form').querySelector('.caja-input-container');
        container.style.display = (this.value === 'cajero') ? 'block' : 'none';
    });
});

function render() {
  const fData = filtered();
  
  document.getElementById('tableBody').innerHTML = fData.map(r => {
      let pct = (r.total_ventas / maxVentasGbl) * 100;
      let avatarHTML = r.foto_perfil 
        ? `<img src="${r.foto_perfil}?v=${new Date().getTime()}" style="width:28px;height:28px;border-radius:50%;object-fit:cover;flex-shrink:0;border:1px solid var(--tema-color);">` 
        : `<div style="width:28px;height:28px;border-radius:50%;background:rgba(197,160,89,.1);border:1px solid rgba(197,160,89,.3);display:flex;align-items:center;justify-content:center;font-size:9px;color:var(--tema-color);font-weight:600;flex-shrink:0">${initials(r.nombre)}</div>`;
      
      let normRol = (r.rol === 'admin' || r.rol === 'administrador') ? 'admin' : 'cajero';
      let rolCaja = normRol === 'cajero' ? `Cajero (Caja ${r.num_caja || 'N/A'})` : `Administrador`;

      return `
    <tr>
      <td>
        <div style="display:flex;align-items:center;gap:9px">
          ${avatarHTML}
          <span style="color:var(--text-cream);font-weight:500">${r.nombre}</span>
        </div>
      </td>
      <td style="text-transform: capitalize;">${rolCaja}</td>
      <td style="color:var(--border-color);font-size:10px;letter-spacing:1px">${r.fecha_ingreso}</td>
      <td class="td-gold">$${Number(r.total_ventas).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
      <td style="text-align:center">${r.total_ordenes}</td>
      <td>
        <div class="prog-wrap">${Math.round(pct)}%
          <div class="prog-bg"><div class="prog-fg" style="width:${pct}%"></div></div>
        </div>
      </td>
      <td>${estadoSwitchHTML(r.estado, r.id_usuario)}</td>
      <td>
        <button class="btn-edit" onclick="editarEmpleado(${r.id_usuario})"><i class="fas fa-edit"></i></button>
      </td>
    </tr>`;
  }).join('');

  document.getElementById('empGridCards').innerHTML = fData.slice(0, 6).map(r => {
      let avatarHTML = r.foto_perfil 
        ? `<img src="${r.foto_perfil}?v=${new Date().getTime()}" style="width:40px;height:40px;border-radius:50%;object-fit:cover;margin:0 auto;display:block;border:2px solid var(--tema-color);">` 
        : `<div class="emp-avatar">${initials(r.nombre)}</div>`;
      
      let normRol = (r.rol === 'admin' || r.rol === 'administrador') ? 'admin' : 'cajero';
      let rolCaja = normRol === 'cajero' ? `Cajero (Caja ${r.num_caja || 'N/A'})` : `Administrador`;

      return `
      <div class="emp-card">
        ${avatarHTML}
        <div class="emp-name" style="margin-top:10px;">${r.nombre}</div>
        <div class="emp-role" style="text-transform: capitalize;">${rolCaja}</div>
        <div style="margin-bottom: 10px; display:flex; justify-content:center;">
          ${estadoSwitchHTML(r.estado, r.id_usuario)}
        </div>
        <div class="emp-stats">
          <div><div class="emp-stat-lbl">Ventas</div><div class="emp-stat-val">$${Number(r.total_ventas).toLocaleString('en-US', {minimumFractionDigits: 2})}</div></div>
          <div><div class="emp-stat-lbl">Órdenes</div><div class="emp-stat-val">${r.total_ordenes}</div></div>
          <div><div class="emp-stat-lbl">Ingreso</div><div class="emp-stat-val" style="font-size:10px;">${r.fecha_ingreso}</div></div>
        </div>
      </div>
      `;
  }).join('');

  let rankListHTML = '';
  let rank = 1;
  fData.slice(0, 5).forEach(r => {
      let pct = (r.total_ventas / maxVentasGbl) * 100;
      let avatarHTML = r.foto_perfil 
        ? `<img src="${r.foto_perfil}?v=${new Date().getTime()}" style="width:30px;height:30px;border-radius:50%;object-fit:cover;border:1px solid var(--tema-color);">` 
        : `<div class="rank-av">${initials(r.nombre)}</div>`;
      
      let normRol = (r.rol === 'admin' || r.rol === 'administrador') ? 'admin' : 'cajero';
      let rolCaja = normRol === 'cajero' ? `Cajero (Caja ${r.num_caja || 'N/A'})` : `Administrador`;

      rankListHTML += `
        <div class="rank-item">
          <div class="rank-num">${rank++}</div>
          ${avatarHTML}
          <div class="rank-info">
            <div class="rank-name">${r.nombre}</div>
            <div class="rank-sub">${r.total_ordenes} órdenes · <span style="text-transform: capitalize;">${rolCaja}</span></div>
            <div class="rank-bar-bg"><div class="rank-bar-fg" style="width:${pct}%"></div></div>
          </div>
          <div class="rank-val">$${Number(r.total_ventas).toLocaleString('en-US', {minimumFractionDigits: 2})}</div>
        </div>
      `;
  });
  document.getElementById('rankListBody').innerHTML = rankListHTML;
}

document.getElementById('searchInput').addEventListener('input', e => { search = e.target.value; render(); });
document.getElementById('roleFilter').addEventListener('change', e => { roleF = e.target.value; render(); });
document.getElementById('statusFilter').addEventListener('change', e => { statusF = e.target.value; render(); });

render();

// Forzar actualización de colores del gráfico con el tema
function actualizarGrafico() {
    const temaColor = getComputedStyle(document.documentElement).getPropertyValue('--tema-color').trim() || '#C5A059';
    
    const topNombres = dbEmpleados.slice(0, 7).map(e => e.nombre.split(' ')[0]);
    const topVentasData = dbEmpleados.slice(0, 7).map(e => e.total_ventas);
    
    const canvas = document.getElementById('empChart');
    if (canvas.chart) {
        canvas.chart.destroy();
    }
    
    canvas.chart = new Chart(canvas, {
        type: 'bar',
        data: {
            labels: topNombres.length > 0 ? topNombres : ['Sin datos'],
            datasets: [{
                data: topVentasData.length > 0 ? topVentasData : [0],
                backgroundColor: temaColor + '33',
                borderColor: temaColor,
                borderWidth: 1,
                borderRadius: 5
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { color: 'rgba(255,255,255,.03)' }, ticks: { color: '#4A4A4A', callback: v => '$' + v.toLocaleString() } },
                y: { grid: { display: false }, ticks: { color: '#9A7A3F' } }
            }
        }
    });
}

// Inicializar gráfico después de cargar
setTimeout(() => {
    actualizarGrafico();
}, 100);

// Escuchar cambios de tema
window.addEventListener('liquourThemeChanged', function() {
    setTimeout(() => {
        actualizarGrafico();
        render();
    }, 100);
});
</script>
<script src="../../../Assets/JS/Catalogo_Admin.js"></script>
</body>
</html>