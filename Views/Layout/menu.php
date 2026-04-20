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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --tema-color: #e5c158; 
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            color: #ffffff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            overflow-x: hidden;
            background-color: #050505;
        }

        /* --- FONDO DE VIDEO (MÁS VISIBLE) --- */
        .video-background {
            position: fixed;
            top: 0; left: 0;
            width: 100vw; height: 100vh;
            z-index: -2;
            overflow: hidden;
            background-color: #000;
        }
        
        .video-background video {
            position: absolute;
            top: 50%; left: 50%;
            min-width: 100%; min-height: 100%;
            width: auto; height: auto;
            transform: translate(-50%, -50%);
            object-fit: cover;
            opacity: 1; /* Video al 100% de opacidad */
        }

        .video-overlay {
            position: fixed;
            top: 0; left: 0;
            width: 100vw; height: 100vh;
            z-index: -1;
            /* Filtro mucho más suave para dejar pasar el video */
            background: rgba(0, 0, 0, 0.4); 
            backdrop-filter: blur(3px); /* Desenfoque más ligero */
        }

        /* --- TOP BAR --- */
        .top-bar {
            position: fixed; top: 0; left: 0; width: 100%;
            padding: 15px 30px;
            display: flex; justify-content: space-between; align-items: center;
            background: rgba(10, 10, 10, 0.75);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.8);
            z-index: 1000;
            transition: border-color 0.3s ease;
        }
        
        .top-bar:hover { border-bottom: 1px solid var(--tema-color); }

        .brand {
            font-weight: 700; letter-spacing: 2px;
            display: flex; align-items: center; font-size: 0.95rem;
            color: var(--tema-color); text-transform: uppercase;
            text-shadow: 0 0 10px rgba(0,0,0,0.8);
        }

        .user-section { display: flex; align-items: center; gap: 20px; }

        .user-info {
            display: flex; align-items: center; gap: 10px;
            background: rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 6px 18px; border-radius: 30px;
            transition: all 0.3s ease;
        }
        
        .user-info:hover {
            border-color: var(--tema-color);
            background: rgba(20, 20, 20, 0.8);
            box-shadow: 0 0 15px rgba(0,0,0,0.5);
        }

        .user-text { display: flex; flex-direction: column; text-align: left; }
        .username { font-size: 0.8rem; font-weight: 700; color: #ffffff; letter-spacing: 0.5px; }
        .role { font-size: 0.65rem; font-weight: 600; color: var(--tema-color); text-transform: uppercase; }

        .btn-exit {
            color: #fff; text-decoration: none; font-size: 0.85rem; font-weight: 600;
            background: rgba(255,255,255,0.1); padding: 8px 18px;
            border-radius: 8px; transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .btn-exit:hover {
            background: #e74c3c; color: #fff; border-color: #e74c3c;
            box-shadow: 0 0 15px rgba(231, 76, 60, 0.4); transform: scale(1.05);
        }

        /* --- CONTENEDOR PRINCIPAL --- */
        .main-container { margin-top: 130px; width: 100%; max-width: 1000px; text-align: center; }

        .header h1 {
            font-size: 3rem; letter-spacing: 4px; margin-bottom: 10px; font-weight: 800;
            color: #fff;
            text-shadow: 0 4px 15px rgba(0,0,0,0.9);
        }
        .header p {
            font-size: 1.1rem; opacity: 0.9; margin-bottom: 50px; font-weight: 400; color: #ddd;
            text-shadow: 0 2px 10px rgba(0,0,0,0.9);
        }

        .grid-container { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }

        /* --- ESTILOS DE TARJETAS (MÁS VISIBLES) --- */
        .card {
            text-decoration: none; color: #fff;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            background: rgba(15, 15, 15, 0.65) !important; /* Más transparente para ver el video */
            padding: 30px 15px; border-radius: 12px;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275) !important;
            cursor: pointer;
            border: 1px solid rgba(255, 255, 255, 0.15) !important; /* Borde un poco más blanco */
            backdrop-filter: blur(8px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.6);
            position: relative; overflow: hidden;
            z-index: 1;
        }

        .card::before {
            content: ''; position: absolute; top: 0; left: -100%;
            width: 50%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
            transform: skewX(-25deg); transition: left 0.6s ease; z-index: -1;
        }

        .card:hover::before { left: 150%; }

        .card:hover {
            transform: translateY(-8px) scale(1.02);
            border-color: var(--tema-color) !important;
            background: rgba(20, 20, 20, 0.85) !important; /* Se oscurece al pasar el mouse para leer mejor */
            box-shadow: 0 15px 35px rgba(0,0,0,0.8), 0 0 15px rgba(255,255,255, 0.1) !important;
        }

        .card i {
            font-size: 2.2rem; margin-bottom: 12px; color: #fff;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            text-shadow: 0 2px 10px rgba(0,0,0,0.8);
        }

        .card:hover i {
            transform: scale(1.25) translateY(-5px);
            color: var(--tema-color) !important;
            text-shadow: 0 5px 15px rgba(0,0,0,0.9);
        }

        .card h3 { 
            font-size: 0.95rem; letter-spacing: 1px; margin-bottom: 8px; font-weight: 700; 
            text-transform: uppercase; transition: color 0.3s ease;
            text-shadow: 0 2px 8px rgba(0,0,0,0.8);
        }
        
        .card:hover h3 { color: var(--tema-color); }
        
        .card p { 
            font-size: 0.8rem; font-weight: 400; color: #ccc; 
            text-shadow: 0 1px 5px rgba(0,0,0,0.8);
        }

        @media (max-width: 900px) { .grid-container { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 480px) { .grid-container { grid-template-columns: 1fr; } .header h1 { font-size: 2rem; } }

        /* --- ESTILOS MODAL SWAL2 --- */
        @keyframes modalAparicion { 0% { opacity: 0; transform: translateY(30px) scale(0.95); } 100% { opacity: 1; transform: translateY(0) scale(1); } }
        .swal2-popup.modal-elegante {
            border: 1px solid rgba(255, 255, 255, 0.1) !important; border-top: 4px solid var(--tema-color) !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.8) !important; border-radius: 12px !important; font-family: 'Inter', sans-serif !important;
            animation: modalAparicion 0.5s cubic-bezier(0.165, 0.84, 0.44, 1) forwards !important;
        }
        .swal2-cancel.btn-cancelar { border: 1px solid rgba(255, 255, 255, 0.2) !important; transition: all 0.3s ease; }
        .swal2-cancel.btn-cancelar:hover { background: rgba(255, 255, 255, 0.1) !important; }
        .swal2-confirm.btn-guardar { background-color: var(--tema-color) !important; color: #000 !important; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3) !important; transition: all 0.3s ease; }
        .swal2-confirm.btn-guardar:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0, 0.4) !important; }
    </style>
</head>
<body>

    <div class="video-background">
        <video autoplay muted loop playsinline id="bg-video">
            <source src="../../Assets/IMG/licores.mp4" type="video/mp4">
        </video>
    </div>
    <div class="video-overlay"></div>

    <nav class="top-bar">
        <div class="brand">
            <img id="logo-sistema" src="/LIQUOUR/Assets/IMG/Logo.jpeg" alt="Liquour Logo" style="height: 50px; width: auto; margin-right: 12px; object-fit: contain;"> LIQUOUR POS
        </div>
        <div class="user-section">
            <div class="user-info">
                <i class="fa-solid fa-circle-user" style="color: var(--tema-color);"></i>
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
                <h3>COMPRAS</h3>
                <p>Gestión de compras</p>
            </a>
            <?php endif; ?>
        </div>
    </div>

</body>
<script src="../../Assets/JS/menu.js"></script>
</html>