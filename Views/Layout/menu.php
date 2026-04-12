<?php
session_start();

$nombreUsuario = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Usuario VIP';
$rolRaw = strtolower($_SESSION['rol'] ?? 'empleado');

$esAdmin = ($rolRaw === 'admin' || $rolRaw === 'administrador');
$rolUsuario = $esAdmin ? 'Administrador' : 'Empleado';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liquour - Panel Premium</title>
    <link rel="stylesheet" href="../../Assets/CSS/menu.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .swal2-popup.border-gold {
            border: 1px solid rgba(229, 193, 88, 0.4) !important;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.8) !important;
            border-radius: 16px !important;
            font-family: 'Inter', sans-serif !important;
        }
        .swal2-cancel.border-gray {
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            transition: all 0.3s ease;
        }
        .swal2-cancel.border-gray:hover {
            background: rgba(255, 255, 255, 0.1) !important;
        }
        .swal2-confirm {
            box-shadow: 0 0 15px rgba(229, 193, 88, 0.3) !important;
            transition: all 0.3s ease;
        }
        .swal2-confirm:hover {
            transform: scale(1.05);
            box-shadow: 0 0 20px rgba(229, 193, 88, 0.6) !important;
        }
    </style>
</head>
<body>

    <nav class="top-bar">
        <div class="brand">
            <img src="/LIQUOUR/Assets/IMG/Logo.jpeg" alt="Liquour Logo" style="height: 50px; width: auto; margin-right: 12px; object-fit: contain;"> LIQUOUR POS
        </div>
        <div class="user-section">
            <div class="user-info">
                <i class="fa-solid fa-circle-user" style="color: #e5c158;"></i>
                <div class="user-text">
                    <span class="username"><?php echo htmlspecialchars($nombreUsuario); ?></span>
                    <span class="role"><?php echo htmlspecialchars($rolUsuario); ?></span>
                </div>
            </div>
            <a href="../login/logout.php" class="btn-exit">
                <i class="fa-solid fa-right-from-bracket"></i> Salir
            </a>
        </div>
    </nav>

    <div class="main-container">
        <header class="header">
            <h1>BIENVENIDO</h1>
            <p>Panel Administrativo Liquour — Selecciona una opción</p>
        </header>

        <div class="grid-container">
            
            <a href="#" class="card">
                <i class="fa-solid fa-cart-shopping"></i>
                <h3>PUNTO DE VENTA</h3>
                <p>Registrar ventas</p>
            </a>

            <a href="#" class="card">
                <i class="fa-solid fa-box-archive"></i>
                <h3>INVENTARIO</h3>
                <p>Productos y stock</p>
            </a>

            <a href="#" class="card">
                <i class="fa-solid fa-globe"></i>
                <h3>PÁGINA WEB</h3>
                <p>Presentación del negocio</p>
            </a>

            <?php if ($esAdmin): ?>
            
            <a href="#" class="card">
                <i class="fa-solid fa-wine-bottle"></i>
                <h3>VENTAS</h3>
                <p>Historial de ventas</p>
            </a>

            <a href="#" class="card">
                <i class="fa-solid fa-chart-pie"></i>
                <h3>ESTADÍSTICAS</h3>
                <p>Reportes y análisis</p>
            </a>

            <a href="#" class="card">
                <i class="fa-solid fa-users"></i>
                <h3>USUARIOS</h3>
                <p>Gestión de accesos</p>
            </a>

            <a href="#" class="card">
                <i class="fa-solid fa-gear"></i>
                <h3>AJUSTES</h3>
                <p>Configuración general</p>
            </a>

            <a href="#" class="card">
                <i class="fa-solid fa-calendar-check"></i>
                <h3>RESERVAS</h3>
                <p>Gestión de eventos</p>
            </a>
            
            <?php endif; ?>

        </div>
    </div>

</body>
<script src="../../Assets/JS/menu.js"></script>
</html>