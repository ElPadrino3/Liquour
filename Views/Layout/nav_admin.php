<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$rol_usuario = $_SESSION['rol'] ?? ''; 
?>
<link rel="stylesheet" href="/LIQUOUR/Assets/CSS/nav.css">
<link rel="stylesheet" href="../../../Assets/CSS/nav.css">

<header class="navbar-liquour">
    <div class="logo-container">
        <img src="/LIQUOUR/Assets/IMG/Logo.jpeg" alt="Liquour Logo" class="logo-img">
    </div>
    
    <nav class="nav-menu">
        <?php if ($rol_usuario === 'admin'): ?>
            <a href="/LIQUOUR/Views/Include/Admin/dashboard.php" class="nav-item">Dashboard</a>
            <a href="/LIQUOUR/Views/Include/Admin/Tienda_pos.php" class="nav-item">Punto de Venta</a>
            <a href="/LIQUOUR/Views/Include/Admin/Catalogo_Admin.php" class="nav-item">Catálogo</a>
            <a href="/LIQUOUR/Views/Include/Admin/Proveedores.php" class="nav-item">Proveedores</a>
            <a href="/LIQUOUR/Views/Include/Admin/empleados.php" class="nav-item">Empleados</a>
            <a href="/LIQUOUR/Views/Include/Admin/reportes.php" class="nav-item">Reportes</a>
            <a href="/LIQUOUR/Views/Include/Admin/compras.php" class="nav-item">Compras</a>
            <a href="/LIQUOUR/Views/Include/Admin/perfil_Admin.php" class="nav-item">Perfil</a>
        <?php elseif ($rol_usuario === 'empleado'): ?>
            <a href="/LIQUOUR/Views/Include/Admin/Tienda_pos.php" class="nav-item">Punto de Venta</a>
            <a href="/LIQUOUR/Views/Include/empleado/Catalogo_Empleado.php" class="nav-item">Inventario</a>
            <a href="/LIQUOUR/Views/Include/Admin/perfil_Admin.php" class="nav-item">Perfil</a>
        <?php endif; ?>
    </nav>
</header>