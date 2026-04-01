<?php
require_once '../../../Config/Liquour_bdd.php';

$bdd = new BDD();
$conn = $bdd->conectar();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_employee') {
    $nombre = $_POST['nombre'] ?? '';
    $usuario = $_POST['usuario'] ?? '';
    $pass = $_POST['password'] ?? '';
    $rol = $_POST['rol'] ?? 'empleado';

    if (!empty($nombre) && !empty($usuario) && !empty($pass)) {
        $stmtInsert = $conn->prepare("INSERT INTO usuarios (nombre, usuario, password, rol, estado) VALUES (?, ?, ?, ?, 1)");
        $stmtInsert->execute([$nombre, $usuario, $pass, $rol]);
        header("Location: empleados.php");
        exit;
    }
}

$stmtEmpleados = $conn->query("
    SELECT 
        u.id_usuario,
        u.nombre, 
        u.rol, 
        u.estado,
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
$empleados = $stmtEmpleados->fetchAll();

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
$bdd->desconectar();
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
</head>
<body>

<?php @include '../../Layout/nav_admin.php'; ?> 
<?php @include '../../../Layout/header_admin.php'; ?>

<div id="modalNewEmp" class="modal-overlay" style="display: none;">
    <div class="modal-container modal-animate-in">
        <div class="modal-header-perfil">
            <h3>Nuevo Empleado</h3>
            <button id="closeNewEmp" class="close-modal">&times;</button>
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
            
            <div class="admin-input-group" style="margin-bottom:25px;">
                <label>Rol</label>
                <select name="rol" class="filter-select" style="width:100%; border-radius:4px; padding:10px;" required>
                    <option value="empleado">Empleado</option>
                    <option value="admin">Administrador</option>
                </select>
            </div>
            
            <button type="submit" class="btn-confirmar-admin">Guardar Empleado</button>
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
      <button class="add-btn btn-open-modal">+ Nuevo Empleado</button>
    </div>
    <div class="emp-grid">
      <?php foreach (array_slice($empleados, 0, 6) as $emp): 
            $iniciales = strtoupper(substr($emp['nombre'], 0, 2));
            $claseEstado = $emp['estado'] ? 'active' : 'inactive';
            $textoEstado = $emp['estado'] ? 'Activo' : 'Inactivo';
      ?>
      <div class="emp-card">
        <div class="emp-avatar"><?= $iniciales ?></div>
        <div class="emp-name"><?= htmlspecialchars($emp['nombre']) ?></div>
        <div class="emp-role" style="text-transform: capitalize;"><?= htmlspecialchars($emp['rol']) ?></div>
        <div class="status-pill <?= $claseEstado ?>"><?= $textoEstado ?></div>
        <div class="emp-stats">
          <div><div class="emp-stat-lbl">Ventas</div><div class="emp-stat-val">$<?= number_format($emp['total_ventas'], 2) ?></div></div>
          <div><div class="emp-stat-lbl">Órdenes</div><div class="emp-stat-val"><?= $emp['total_ordenes'] ?></div></div>
          <div><div class="emp-stat-lbl">Ingreso</div><div class="emp-stat-val" style="font-size:10px;"><?= $emp['fecha_ingreso'] ?></div></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
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
      <div class="rank-list">
        <?php 
        $rank = 1;
        $maxVentasRnk = ($mejorVendedorVentas > 0) ? $mejorVendedorVentas : 1;
        foreach (array_slice($empleados, 0, 5) as $emp): 
            $pct = ($emp['total_ventas'] / $maxVentasRnk) * 100;
        ?>
        <div class="rank-item">
          <div class="rank-num"><?= $rank++ ?></div>
          <div class="rank-av"><?= strtoupper(substr($emp['nombre'], 0, 2)) ?></div>
          <div class="rank-info">
            <div class="rank-name"><?= htmlspecialchars($emp['nombre']) ?></div>
            <div class="rank-sub"><?= $emp['total_ordenes'] ?> órdenes · <span style="text-transform: capitalize;"><?= htmlspecialchars($emp['rol']) ?></span></div>
            <div class="rank-bar-bg"><div class="rank-bar-fg" style="width:<?= $pct ?>%"></div></div>
          </div>
          <div class="rank-val">$<?= number_format($emp['total_ventas'], 2) ?></div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <div class="card" style="padding:0">
    <div class="toolbar">
      <div class="toolbar-left">
        <input class="search-input" type="text" placeholder="Buscar empleado…" id="searchInput" />
        <select class="filter-select" id="roleFilter">
          <option value="">Todos los roles</option>
          <option value="admin">Admin</option>
          <option value="empleado">Empleado</option>
        </select>
        <select class="filter-select" id="statusFilter">
          <option value="">Todos los estados</option>
          <option value="1">Activo</option>
          <option value="0">Inactivo</option>
        </select>
      </div>
      <button class="add-btn btn-open-modal">+ Nuevo Empleado</button>
    </div>
    <div class="tbl-wrap">
      <table>
        <thead>
          <tr>
            <th>Empleado</th>
            <th>Rol</th>
            <th>Ingreso</th>
            <th>Ventas Mes</th>
            <th>Órdenes</th>
            <th>Desempeño</th>
            <th>Estado</th>
          </tr>
        </thead>
        <tbody id="tableBody"></tbody>
      </table>
    </div>
  </div>

</div>

<script>
const dbEmpleados = <?= $empleadosJSON ?>;
const maxVentasGbl = <?= ($mejorVendedorVentas > 0) ? $mejorVendedorVentas : 1 ?>;

const SBADGE = { '1':'active', '0':'inactive' };
const SLBL   = { '1':'Activo', '0':'Inactivo' };

function initials(n){ return n.split(' ').map(w=>w[0]).join('').slice(0,2).toUpperCase(); }

let search='', roleF='', statusF='';

function filtered(){
  return dbEmpleados.filter(r=>{
    const q = search.toLowerCase();
    const m = !q || r.nombre.toLowerCase().includes(q) || r.rol.toLowerCase().includes(q);
    return m && (!roleF||r.rol===roleF) && (!statusF||String(r.estado)===statusF);
  });
}

function render(){
  document.getElementById('tableBody').innerHTML = filtered().map(r=>{
      let pct = (r.total_ventas / maxVentasGbl) * 100;
      return `
    <tr>
      <td>
        <div style="display:flex;align-items:center;gap:9px">
          <div style="width:28px;height:28px;border-radius:50%;background:rgba(197,160,89,.1);border:1px solid rgba(197,160,89,.3);display:flex;align-items:center;justify-content:center;font-size:9px;color:var(--gold);font-weight:600;flex-shrink:0">${initials(r.nombre)}</div>
          <span style="color:var(--cream);font-weight:500">${r.nombre}</span>
        </div>
      </td>
      <td style="text-transform: capitalize;">${r.rol}</td>
      <td style="color:var(--oxford);font-size:10px;letter-spacing:1px">${r.fecha_ingreso}</td>
      <td class="td-gold">$${Number(r.total_ventas).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
      <td style="text-align:center">${r.total_ordenes}</td>
      <td>
        <div class="prog-wrap">${Math.round(pct)}%
          <div class="prog-bg"><div class="prog-fg" style="width:${pct}%"></div></div>
        </div>
      </td>
      <td><span class="badge status-pill ${SBADGE[r.estado]}">${SLBL[r.estado]}</span></td>
    </tr>`}).join('');
}

document.getElementById('searchInput').addEventListener('input', e=>{search=e.target.value;render();});
document.getElementById('roleFilter').addEventListener('change', e=>{roleF=e.target.value;render();});
document.getElementById('statusFilter').addEventListener('change', e=>{statusF=e.target.value;render();});
render();

Chart.defaults.color='#4A4A4A'; Chart.defaults.font.family='Montserrat'; Chart.defaults.font.size=10;

const topNombres = dbEmpleados.slice(0, 7).map(e => e.nombre.split(' ')[0]);
const topVentasData = dbEmpleados.slice(0, 7).map(e => e.total_ventas);

new Chart(document.getElementById('empChart'),{
  type:'bar',
  data:{
    labels: topNombres.length > 0 ? topNombres : ['Sin datos'],
    datasets:[{
      data: topVentasData.length > 0 ? topVentasData : [0],
      backgroundColor:'rgba(197,160,89,.2)',
      borderColor:'#C5A059',borderWidth:1,borderRadius:5
    }]
  },
  options:{
    indexAxis:'y',
    responsive:true,
    plugins:{legend:{display:false}},
    scales:{
      x:{grid:{color:'rgba(255,255,255,.03)'},ticks:{color:'#4A4A4A',callback:v=>'$'+v.toLocaleString()}},
      y:{grid:{display:false},ticks:{color:'#9A7A3F'}}
    }
  }
});

const modalNewEmp = document.getElementById('modalNewEmp');
const btnsOpenModal = document.querySelectorAll('.btn-open-modal');
const btnCloseModal = document.getElementById('closeNewEmp');

btnsOpenModal.forEach(btn => {
    btn.addEventListener('click', () => {
        modalNewEmp.classList.add('active');
    });
});

btnCloseModal.addEventListener('click', () => {
    modalNewEmp.classList.remove('active');
});

modalNewEmp.addEventListener('click', (e) => {
    if (e.target === modalNewEmp) {
        modalNewEmp.classList.remove('active');
    }
});
</script>

<script src="../../../Assets/JS/Catalogo_Admin.js"></script>

</body>
</html>