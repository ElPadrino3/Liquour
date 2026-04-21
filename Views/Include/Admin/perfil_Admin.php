<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../../../Config/Liquour_bdd.php';
$db = new BDD();
$conexion = $db->conectar();

$rol = $_SESSION['rol'] ?? 'empleado';
$nombre = $_SESSION['nombre'] ?? 'Usuario';
$id_usuario = $_SESSION['id_usuario'] ?? 0;

$stmtFoto = $conexion->prepare("SELECT foto_perfil FROM usuarios WHERE id_usuario = ?");
$stmtFoto->execute([$id_usuario]);
$rowFoto = $stmtFoto->fetch();
$foto_db = $rowFoto['foto_perfil'] ?? null;

if (!empty($foto_db)) {
    $avatar = $foto_db . '?v=' . time(); 
} else {
    $nombre_url = urlencode($nombre);
    $avatar = "https://ui-avatars.com/api/?name={$nombre_url}&background=C5A059&color=1A1A1A&size=128";
}

$admin_ventas_mes = 0;
$admin_inventario = 0;
$admin_alertas = 0;

$emp_caja_hoy = 0;
$emp_tickets = 0;
$emp_articulos = 0;

if ($rol === 'admin') {
    $stmtVentas = $conexion->query("SELECT IFNULL(SUM(total), 0) as total FROM ventas WHERE MONTH(fecha) = MONTH(CURRENT_DATE()) AND YEAR(fecha) = YEAR(CURRENT_DATE())");
    $admin_ventas_mes = $stmtVentas->fetch()['total'];

    $stmtInv = $conexion->query("SELECT IFNULL(SUM(stock), 0) as total FROM productos WHERE estado = 1");
    $admin_inventario = $stmtInv->fetch()['total'];

    $stmtAlertas = $conexion->query("SELECT COUNT(id_producto) as total FROM productos WHERE stock <= 10 AND estado = 1");
    $admin_alertas = $stmtAlertas->fetch()['total'];
    
    $meta_mensual_admin = 20000; 
    $pct_meta = min(100, round(($admin_ventas_mes / $meta_mensual_admin) * 100));
    
    $meta_stock = 1500;
    $pct_stock = min(100, round(($admin_inventario / $meta_stock) * 100));
    
    $pct_crecimiento = 92; 
    
} else {
    $stmtCaja = $conexion->prepare("SELECT IFNULL(SUM(total), 0) as total, COUNT(id_venta) as tickets FROM ventas WHERE DATE(fecha) = CURRENT_DATE() AND id_usuario = ?");
    $stmtCaja->execute([$id_usuario]);
    $rowCaja = $stmtCaja->fetch();
    $emp_caja_hoy = $rowCaja['total'];
    $emp_tickets = $rowCaja['tickets'];

    $stmtArt = $conexion->prepare("SELECT IFNULL(SUM(dv.cantidad), 0) as total FROM detalle_ventas dv JOIN ventas v ON dv.id_venta = v.id_venta WHERE DATE(v.fecha) = CURRENT_DATE() AND v.id_usuario = ?");
    $stmtArt->execute([$id_usuario]);
    $emp_articulos = $stmtArt->fetch()['total'];
    
    $meta_diaria_emp = 1000;
    $pct_meta_diaria = min(100, round(($emp_caja_hoy / $meta_diaria_emp) * 100));
    
    $pct_efectividad = $emp_tickets > 0 ? min(100, round(($emp_articulos / ($emp_tickets * 2)) * 100)) : 0;
    
    $pct_puntualidad = 100;
}
?>
<link rel="stylesheet" href="../../../Assets/CSS/perfil.css">

<div class="liquour-page-bg"></div>

<div class="liquour-profile">
    <div class="liquour-bg-animated"></div>
    <div class="liquour-avatar">
        <img src="<?php echo $avatar; ?>" alt="Avatar" style="object-fit: cover; width: 100%; height: 100%; border-radius: 50%;">
    </div>
    
    <div class="liquour-info">
        <h2><?php echo htmlspecialchars($nombre); ?></h2>
        <?php if ($rol === 'admin') { ?>
            <p class="rango">Gerente de la Licorería</p>
        <?php } else { ?>
            <p class="rango">Cajero Premium</p>
        <?php } ?>
    </div>
    
    <div class="liquour-stats">
        <?php if ($rol === 'admin') { ?>
            <div class="stat-box">
                <h3>$<?php echo number_format($admin_ventas_mes, 2); ?></h3>
                <span>Ventas Mes</span>
            </div>
            <div class="stat-box">
                <h3><?php echo number_format($admin_inventario); ?></h3>
                <span>Stock Inventario</span>
            </div>
            <div class="stat-box">
                <h3><?php echo $admin_alertas; ?></h3>
                <span>Alertas Stock Bajo</span>
            </div>
        <?php } else { ?>
            <div class="stat-box">
                <h3>$<?php echo number_format($emp_caja_hoy, 2); ?></h3>
                <span>Caja Hoy</span>
            </div>
            <div class="stat-box">
                <h3><?php echo $emp_tickets; ?></h3>
                <span>Tickets de Hoy</span>
            </div>
            <div class="stat-box">
                <h3><?php echo $emp_articulos; ?></h3>
                <span>Artículos Vendidos</span>
            </div>
        <?php } ?>
    </div>
    
    <div class="liquour-charts">
        <?php if ($rol === 'admin') { ?>
            <div class="chart-container">
                <div class="chart-label">
                    <span>Meta Mensual</span>
                    <span><?php echo $pct_meta; ?>%</span>
                </div>
                <div class="chart-bar-bg">
                    <div class="chart-bar-fill"></div>
                </div>
            </div>
            <div class="chart-container">
                <div class="chart-label">
                    <span>Stock Licores</span>
                    <span><?php echo $pct_stock; ?>%</span>
                </div>
                <div class="chart-bar-bg">
                    <div class="chart-bar-fill"></div>
                </div>
            </div>
            <div class="chart-container">
                <div class="chart-label">
                    <span>Crecimiento</span>
                    <span><?php echo $pct_crecimiento; ?>%</span>
                </div>
                <div class="chart-bar-bg">
                    <div class="chart-bar-fill"></div>
                </div>
            </div>
        <?php } else { ?>
            <div class="chart-container">
                <div class="chart-label">
                    <span>Meta Diaria</span>
                    <span><?php echo $pct_meta_diaria; ?>%</span>
                </div>
                <div class="chart-bar-bg">
                    <div class="chart-bar-fill"></div>
                </div>
            </div>
            <div class="chart-container">
                <div class="chart-label">
                    <span>Efectividad</span>
                    <span><?php echo $pct_efectividad; ?>%</span>
                </div>
                <div class="chart-bar-bg">
                    <div class="chart-bar-fill"></div>
                </div>
            </div>
            <div class="chart-container">
                <div class="chart-label">
                    <span>Puntualidad</span>
                    <span><?php echo $pct_puntualidad; ?>%</span>
                </div>
                <div class="chart-bar-bg">
                    <div class="chart-bar-fill"></div>
                </div>
            </div>
        <?php } ?>
    </div>
    
    <div class="liquour-action">
        <button id="btnLiquourAccion" onclick="ejecutarAccion()">Cerrar Turno</button>
    </div>
</div>

<div id="logout-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(13,13,13,0.95); z-index:10000; flex-direction:column; align-items:center; justify-content:center; color:#C5A059; font-family:'Montserrat',sans-serif;">
    <script src="https://unpkg.com/@lottiefiles/dotlottie-wc@0.9.8/dist/dotlottie-wc.js" type="module"></script>
    <dotlottie-wc src="https://lottie.host/740a259b-34b2-405b-bc0e-19202def1508/hkqFNkkmGo.lottie" style="width: 300px; height: 300px" autoplay loop></dotlottie-wc>
    <h3 style="margin-top:-20px; letter-spacing:2px; text-transform:uppercase;">Cerrando Turno...</h3>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const barras = document.querySelectorAll('.chart-bar-fill');
    setTimeout(() => {
        barras.forEach(barra => {
            const contenedor = barra.closest('.chart-container');
            if (contenedor) {
                const porcentajeTexto = contenedor.querySelector('.chart-label span:last-child').textContent;
                barra.style.width = porcentajeTexto;
            }
        });
    }, 500);
});

window.ejecutarAccion = () => {
    if (confirm("¿Estás seguro de que deseas cerrar el turno y salir del sistema?")) {
        const btn = document.getElementById("btnLiquourAccion");
        const overlay = document.getElementById("logout-overlay");
        
        btn.classList.add("action-success");
        btn.textContent = "CERRANDO...";
        overlay.style.display = "flex";
        
        setTimeout(() => {
            window.location.href = "../../../Controller/Public/Logout.php";
        }, 2500);
    }
}
</script>