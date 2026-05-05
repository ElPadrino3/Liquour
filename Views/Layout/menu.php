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
        /* ============================================
           VARIABLES DE COLOR - LIQUOUR ORIGINAL
           ============================================ */
        :root {
            --color-dorado: #C5A059;
            --color-dorado-oscuro: #A8883A;
            --color-dorado-claro: #D4B87A;
            --bg-carbon: #1A1A1A;
            --bg-carbon-claro: #2A2A2A;
            --bg-carbon-oscuro: #0D0D0D;
            --bg-gris-oxford: #4A4A4A;
            --text-blanco-crema: #F5F5DC;
            --text-gris-claro: #D0D0D0;
            --border-fuerte: #4A4A4A;
            --border-suave: rgba(74, 74, 74, 0.3);
            --transition: all 0.3s ease;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-carbon);
            color: var(--text-blanco-crema);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            overflow-x: hidden;
        }

        /* Fondo de video */
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
            opacity: 0.5;
        }

        .video-overlay {
            position: fixed;
            top: 0; left: 0;
            width: 100vw; height: 100vh;
            z-index: -1;
            background: var(--bg-carbon);
            opacity: 0.7;
            backdrop-filter: blur(3px);
        }

        /* Top Bar */
        .top-bar {
            position: fixed; top: 0; left: 0; width: 100%;
            padding: 15px 30px;
            display: flex; justify-content: space-between; align-items: center;
            background: var(--bg-carbon-claro);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border-fuerte);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.5);
            z-index: 1000;
            transition: var(--transition);
        }
        
        .top-bar:hover { border-bottom-color: var(--color-dorado); }

        .brand {
            font-weight: 700; letter-spacing: 2px;
            display: flex; align-items: center; font-size: 0.95rem;
            color: var(--color-dorado); text-transform: uppercase;
        }

        .user-section { display: flex; align-items: center; gap: 20px; }

        .user-info {
            display: flex; align-items: center; gap: 10px;
            background: var(--bg-carbon-oscuro);
            border: 1px solid var(--border-fuerte);
            padding: 6px 18px; border-radius: 30px;
            transition: var(--transition);
        }
        
        .user-info:hover { border-color: var(--color-dorado); }

        .username { font-size: 0.8rem; font-weight: 700; color: var(--text-blanco-crema); }
        .role { font-size: 0.65rem; font-weight: 600; color: var(--color-dorado); text-transform: uppercase; }

        .btn-exit {
            color: var(--text-blanco-crema); text-decoration: none; font-size: 0.85rem;
            background: var(--bg-carbon-oscuro); padding: 8px 18px;
            border-radius: 8px; border: 1px solid var(--border-fuerte);
            transition: var(--transition);
        }

        .btn-exit:hover {
            background: #e74c3c; color: #fff; border-color: #e74c3c;
            transform: scale(1.05);
        }

        /* Contenedor principal */
        .main-container { margin-top: 130px; width: 100%; max-width: 1000px; text-align: center; }

        .header h1 {
            font-size: 3rem; letter-spacing: 4px; margin-bottom: 10px;
            color: var(--color-dorado);
        }
        .header p {
            font-size: 1.1rem; opacity: 0.8; margin-bottom: 50px;
            color: var(--text-gris-claro);
        }

        /* Grid de tarjetas */
        .grid-container { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }

        .card {
            text-decoration: none;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            background: var(--bg-carbon-claro);
            padding: 30px 15px; border-radius: 12px;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            cursor: pointer;
            border: 1px solid var(--border-fuerte);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        }

        .card:hover {
            transform: translateY(-8px) scale(1.02);
            border-color: var(--color-dorado);
            box-shadow: 0 15px 35px rgba(0,0,0,0.5);
        }

        .card i {
            font-size: 2.2rem; margin-bottom: 12px;
            color: var(--color-dorado);
            transition: transform 0.3s ease;
        }

        .card:hover i { transform: scale(1.25) translateY(-5px); }

        .card h3 { 
            font-size: 0.95rem; letter-spacing: 1px; margin-bottom: 8px; 
            text-transform: uppercase; color: var(--text-blanco-crema);
        }
        
        .card:hover h3 { color: var(--color-dorado); }
        
        .card p { 
            font-size: 0.75rem; opacity: 0.7; color: var(--text-gris-claro);
        }

        /* Responsive */
        @media (max-width: 900px) { .grid-container { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 480px) { .grid-container { grid-template-columns: 1fr; } .header h1 { font-size: 2rem; } }

        /* Estilos del modal */
        .swal2-popup.modal-elegante {
            border-radius: 20px !important;
            border-top: 4px solid var(--color-dorado) !important;
            background: var(--bg-carbon) !important;
        }
        .swal2-confirm.btn-guardar {
            background: var(--color-dorado) !important;
            color: #1A1A1A !important;
            padding: 12px 28px !important;
            border-radius: 10px !important;
            font-weight: 700 !important;
            letter-spacing: 1px !important;
            transition: all 0.3s ease !important;
        }
        .swal2-confirm.btn-guardar:hover {
            transform: translateY(-2px) !important;
            filter: brightness(1.1) !important;
        }
        .swal2-cancel.btn-cancelar {
            background: var(--bg-carbon-oscuro) !important;
            color: var(--text-gris-claro) !important;
            border: 1px solid var(--border-fuerte) !important;
            padding: 12px 28px !important;
            border-radius: 10px !important;
            transition: all 0.3s ease !important;
        }
        .swal2-cancel.btn-cancelar:hover {
            background: #e74c3c !important;
            color: #fff !important;
            border-color: #e74c3c !important;
        }
        .palette-option {
            transition: all 0.2s ease;
        }
        .palette-option:hover {
            transform: translateY(-3px);
        }
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
                <i class="fa-solid fa-circle-user" style="color: var(--color-dorado);"></i>
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
            <a href="#" class="card" data-module="venta">
                <i class="fa-solid fa-cart-shopping"></i>
                <h3>PUNTO DE VENTA</h3>
                <p>Registrar ventas</p>
            </a>
            <a href="#" class="card" data-module="inventario">
                <i class="fa-solid fa-box-archive"></i>
                <h3>INVENTARIO</h3>
                <p>Productos y stock</p>
            </a>
            <a href="#" class="card" data-module="web">
                <i class="fa-solid fa-globe"></i>
                <h3>PÁGINA WEB</h3>
                <p>Presentación del negocio</p>
            </a>

            <?php if ($esAdmin): ?>
            <a href="#" class="card" data-module="ventas">
                <i class="fa-solid fa-wine-bottle"></i>
                <h3>VENTAS</h3>
                <p>Historial de ventas</p>
            </a>
            <a href="#" class="card" data-module="estadisticas">
                <i class="fa-solid fa-chart-pie"></i>
                <h3>ESTADÍSTICAS</h3>
                <p>Reportes y análisis</p>
            </a>
            <a href="#" class="card" data-module="usuarios">
                <i class="fa-solid fa-users"></i>
                <h3>USUARIOS</h3>
                <p>Gestión de accesos</p>
            </a>
            <a href="#" class="card" id="btn-ajustes" data-module="configuracion">
                <i class="fa-solid fa-gear"></i>
                <h3>AJUSTES</h3>
                <p>Configuración general</p>
            </a>
            <a href="#" class="card" data-module="compras">
                <i class="fa-solid fa-calendar-check"></i>
                <h3>COMPRAS</h3>
                <p>Gestión de compras</p>
            </a>
            <?php endif; ?>
        </div>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        
        // ============================================
        // SISTEMA DE TEMAS - COLORES ORIGINALES
        // ============================================
        
        const DEFAULT_COLORS = {
            '--color-dorado': '#C5A059',
            '--color-dorado-oscuro': '#A8883A',
            '--color-dorado-claro': '#D4B87A',
            '--bg-carbon': '#1A1A1A',
            '--bg-carbon-claro': '#2A2A2A',
            '--bg-carbon-oscuro': '#0D0D0D',
            '--bg-gris-oxford': '#4A4A4A',
            '--text-blanco-crema': '#F5F5DC',
            '--text-gris-claro': '#D0D0D0',
            '--border-fuerte': '#4A4A4A'
        };
        
        const PALETTES = {
            original: { nombre: 'Original', desc: 'Clásico Liquour', colores: DEFAULT_COLORS },
            noche: { nombre: 'Noche Profunda', desc: 'Misterioso y premium', colores: {
                '--color-dorado': '#D4AF37',
                '--color-dorado-oscuro': '#B8960C',
                '--color-dorado-claro': '#FFD700',
                '--bg-carbon': '#0A0A0A',
                '--bg-carbon-claro': '#1A1A1A',
                '--bg-carbon-oscuro': '#050505',
                '--bg-gris-oxford': '#333333',
                '--text-blanco-crema': '#FFFFFF',
                '--text-gris-claro': '#CCCCCC',
                '--border-fuerte': '#333333'
            }},
            vino: { nombre: 'Vino Tinto', desc: 'Pasión y elegancia', colores: {
                '--color-dorado': '#8B1A1A',
                '--color-dorado-oscuro': '#6B1010',
                '--color-dorado-claro': '#B84040',
                '--bg-carbon': '#1A0A0A',
                '--bg-carbon-claro': '#2A1515',
                '--bg-carbon-oscuro': '#0A0505',
                '--bg-gris-oxford': '#5A3030',
                '--text-blanco-crema': '#F5E6E6',
                '--text-gris-claro': '#C4A0A0',
                '--border-fuerte': '#5A3030'
            }},
            esmeralda: { nombre: 'Esmeralda', desc: 'Naturaleza y frescura', colores: {
                '--color-dorado': '#8B9A46',
                '--color-dorado-oscuro': '#6B7A36',
                '--color-dorado-claro': '#B0C060',
                '--bg-carbon': '#1A1A0A',
                '--bg-carbon-claro': '#2A2A15',
                '--bg-carbon-oscuro': '#0A0A05',
                '--bg-gris-oxford': '#4A4A30',
                '--text-blanco-crema': '#F0F0E0',
                '--text-gris-claro': '#B0B0A0',
                '--border-fuerte': '#4A4A30'
            }}
        };
        
        function applyColors(colors) {
            const root = document.documentElement;
            for (const [key, value] of Object.entries(colors)) {
                root.style.setProperty(key, value);
            }
            localStorage.setItem('liquour_colors', JSON.stringify(colors));
        }
        
        function loadColors() {
            const saved = localStorage.getItem('liquour_colors');
            if (saved) {
                try {
                    const colors = JSON.parse(saved);
                    applyColors(colors);
                } catch(e) {
                    applyColors(DEFAULT_COLORS);
                }
            } else {
                applyColors(DEFAULT_COLORS);
            }
        }
        
        function getCurrentColors() {
            const root = getComputedStyle(document.documentElement);
            return {
                '--color-dorado': root.getPropertyValue('--color-dorado').trim() || '#C5A059',
                '--bg-carbon': root.getPropertyValue('--bg-carbon').trim() || '#1A1A1A',
                '--bg-carbon-claro': root.getPropertyValue('--bg-carbon-claro').trim() || '#2A2A2A',
                '--bg-gris-oxford': root.getPropertyValue('--bg-gris-oxford').trim() || '#4A4A4A',
                '--text-blanco-crema': root.getPropertyValue('--text-blanco-crema').trim() || '#F5F5DC',
                '--border-fuerte': root.getPropertyValue('--border-fuerte').trim() || '#4A4A4A'
            };
        }
        
        function adjustBrightness(hex, percent) {
            let r = parseInt(hex.slice(1, 3), 16);
            let g = parseInt(hex.slice(3, 5), 16);
            let b = parseInt(hex.slice(5, 7), 16);
            r = Math.min(255, Math.max(0, r + (r * percent / 100)));
            g = Math.min(255, Math.max(0, g + (g * percent / 100)));
            b = Math.min(255, Math.max(0, b + (b * percent / 100)));
            return `#${Math.round(r).toString(16).padStart(2, '0')}${Math.round(g).toString(16).padStart(2, '0')}${Math.round(b).toString(16).padStart(2, '0')}`;
        }
        
        loadColors();
        
        const savedLogo = localStorage.getItem('liquour_logo');
        if (savedLogo) {
            const logoImg = document.getElementById('logo-sistema');
            if (logoImg) logoImg.src = savedLogo;
        }

        const savedVideo = localStorage.getItem('liquour_video');
        if (savedVideo) {
            const container = document.querySelector('.video-background');
            if (container) {
                const ytMatch = savedVideo.match(/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/);
                if (ytMatch) {
                    const videoId = ytMatch[1];
                    container.innerHTML = `<iframe src="https://www.youtube.com/embed/${videoId}?autoplay=1&mute=1&controls=0&showinfo=0&autohide=1&loop=1&playlist=${videoId}&playsinline=1&enablejsapi=1" style="position: absolute; top: 50%; left: 50%; width: 100vw; height: 56.25vw; min-height: 100vh; min-width: 177.77vh; transform: translate(-50%, -50%); pointer-events: none;" frameborder="0" allow="autoplay; encrypted-media"></iframe>`;
                } else {
                    container.innerHTML = `<video autoplay muted loop playsinline id="bg-video"><source src="${savedVideo}"></video>`;
                }
            }
        }
        
        const roleElement = document.querySelector('.role');
        let rol = roleElement ? roleElement.innerText.trim().toLowerCase() : 'empleado';
        
        // ============================================
        // MODAL DE CONFIGURACIÓN (CORREGIDO)
        // ============================================
        function openColorConfigModal() {
            const currentColors = getCurrentColors();
            const currentLogo = localStorage.getItem('liquour_logo') || '/LIQUOUR/Assets/IMG/Logo.jpeg';
            
            Swal.fire({
                title: '<span style="font-size: 1.3rem; letter-spacing: 2px;">🎨 PERSONALIZAR LIQUOUR</span>',
                html: `
                    <div style="text-align: left; max-height: 65vh; overflow-y: auto; padding: 0 5px;">
                        
                        <!-- PALETAS RÁPIDAS -->
                        <div style="margin-bottom: 25px;">
                            <h3 style="color: var(--color-dorado); margin-bottom: 12px; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 2px;">
                                <i class="fa-solid fa-palette"></i> Paletas Rápidas
                            </h3>
                            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px;">
                                <button type="button" class="palette-option" data-palette="original" style="background: linear-gradient(135deg, #1A1A1A, #C5A059); border: none; padding: 12px; border-radius: 12px; color: white; cursor: pointer; font-weight: 600;">⚜️ ORIGINAL</button>
                                <button type="button" class="palette-option" data-palette="noche" style="background: linear-gradient(135deg, #0A0A0A, #D4AF37); border: none; padding: 12px; border-radius: 12px; color: white; cursor: pointer; font-weight: 600;">🌙 NOCHE</button>
                                <button type="button" class="palette-option" data-palette="vino" style="background: linear-gradient(135deg, #1A0A0A, #8B1A1A); border: none; padding: 12px; border-radius: 12px; color: white; cursor: pointer; font-weight: 600;">🍷 VINO</button>
                                <button type="button" class="palette-option" data-palette="esmeralda" style="background: linear-gradient(135deg, #1A1A0A, #8B9A46); border: none; padding: 12px; border-radius: 12px; color: white; cursor: pointer; font-weight: 600;">🫒 ESMERALDA</button>
                            </div>
                        </div>
                        
                        <div style="border-top: 1px solid var(--border-fuerte); margin: 15px 0;"></div>
                        
                        <!-- COLOR DORADO -->
                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 8px; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; font-weight: 600;">
                                ✨ DORADO MATE (Acentos / Botones)
                            </label>
                            <div style="display: flex; gap: 12px; align-items: center;">
                                <input type="color" id="color-dorado" value="${currentColors['--color-dorado']}" style="width: 55px; height: 45px; border-radius: 10px; border: 2px solid var(--color-dorado); cursor: pointer;">
                                <input type="text" id="color-dorado-hex" value="${currentColors['--color-dorado']}" style="flex: 1; padding: 10px; border-radius: 10px; background: #111; border: 1px solid var(--border-fuerte); color: #fff; font-family: monospace; font-size: 14px;">
                            </div>
                            <small style="color: #888;">Botones, enlaces activos, bordes decorativos</small>
                        </div>
                        
                        <!-- FONDO NEGRO CARBÓN -->
                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 8px; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; font-weight: 600;">
                                🖤 NEGRO CARBÓN (Fondo Principal)
                            </label>
                            <div style="display: flex; gap: 12px; align-items: center;">
                                <input type="color" id="bg-carbon" value="${currentColors['--bg-carbon']}" style="width: 55px; height: 45px; border-radius: 10px; border: 2px solid var(--color-dorado); cursor: pointer;">
                                <input type="text" id="bg-carbon-hex" value="${currentColors['--bg-carbon']}" style="flex: 1; padding: 10px; border-radius: 10px; background: #111; border: 1px solid var(--border-fuerte); color: #fff; font-family: monospace; font-size: 14px;">
                            </div>
                            <small style="color: #888;">Fondo general del sistema</small>
                        </div>
                        
                        <!-- GRIS OXFORD (Tarjetas) -->
                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 8px; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; font-weight: 600;">
                                📦 GRIS OXFORD (Fondo Tarjetas)
                            </label>
                            <div style="display: flex; gap: 12px; align-items: center;">
                                <input type="color" id="bg-gris" value="${currentColors['--bg-gris-oxford']}" style="width: 55px; height: 45px; border-radius: 10px; border: 2px solid var(--color-dorado); cursor: pointer;">
                                <input type="text" id="bg-gris-hex" value="${currentColors['--bg-gris-oxford']}" style="flex: 1; padding: 10px; border-radius: 10px; background: #111; border: 1px solid var(--border-fuerte); color: #fff; font-family: monospace; font-size: 14px;">
                            </div>
                            <small style="color: #888;">Tarjetas, paneles, modales</small>
                        </div>
                        
                        <!-- BLANCO CREMA (Texto) -->
                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 8px; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; font-weight: 600;">
                                📝 BLANCO CREMA (Texto Principal)
                            </label>
                            <div style="display: flex; gap: 12px; align-items: center;">
                                <input type="color" id="text-color" value="${currentColors['--text-blanco-crema']}" style="width: 55px; height: 45px; border-radius: 10px; border: 2px solid var(--color-dorado); cursor: pointer;">
                                <input type="text" id="text-color-hex" value="${currentColors['--text-blanco-crema']}" style="flex: 1; padding: 10px; border-radius: 10px; background: #111; border: 1px solid var(--border-fuerte); color: #fff; font-family: monospace; font-size: 14px;">
                            </div>
                            <small style="color: #888;">Textos principales, títulos</small>
                        </div>
                        
                        <!-- COLOR DE BORDES -->
                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 8px; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; font-weight: 600;">
                                🔲 COLOR DE BORDES
                            </label>
                            <div style="display: flex; gap: 12px; align-items: center;">
                                <input type="color" id="border-color" value="${currentColors['--border-fuerte']}" style="width: 55px; height: 45px; border-radius: 10px; border: 2px solid var(--color-dorado); cursor: pointer;">
                                <input type="text" id="border-color-hex" value="${currentColors['--border-fuerte']}" style="flex: 1; padding: 10px; border-radius: 10px; background: #111; border: 1px solid var(--border-fuerte); color: #fff; font-family: monospace; font-size: 14px;">
                            </div>
                            <small style="color: #888;">Bordes de tarjetas, inputs, tablas</small>
                        </div>
                        
                        <div style="border-top: 1px solid var(--border-fuerte); margin: 15px 0;"></div>
                        
                        <!-- LOGOTIPO -->
                        <div style="margin-bottom: 15px;">
                            <label style="display: block; margin-bottom: 8px; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; font-weight: 600;">
                                🏷️ LOGOTIPO DE LA EMPRESA
                            </label>
                            <input type="text" id="input-logo" value="${currentLogo}" style="width: 100%; padding: 12px; border-radius: 10px; background: #111; border: 1px solid var(--border-fuerte); color: #fff; font-size: 14px;">
                            <small style="color: #888;">Pega la URL de tu logo o cámbiala aquí</small>
                        </div>
                        
                        <!-- VIDEO DE FONDO -->
                        <div style="margin-bottom: 15px;">
                            <label style="display: block; margin-bottom: 8px; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; font-weight: 600;">
                                🎥 VIDEO DE FONDO
                            </label>
                            <input type="text" id="input-video" value="${localStorage.getItem('liquour_video') || '../../Assets/IMG/licores.mp4'}" style="width: 100%; padding: 12px; border-radius: 10px; background: #111; border: 1px solid var(--border-fuerte); color: #fff; font-size: 14px;">
                            <small style="color: #888;">Pega la URL de tu video (MP4) o déjalo por defecto</small>
                        </div>
                        
                        <div style="background: rgba(197,160,89,0.1); padding: 12px; border-radius: 10px; margin-top: 15px; border-left: 3px solid var(--color-dorado);">
                            <small style="color: #aaa;">✨ Los cambios se guardan automáticamente y se aplican en todo el sistema</small>
                        </div>
                    </div>
                `,
                background: '#1A1A1A',
                showCancelButton: true,
                confirmButtonText: '<i class="fa-solid fa-check"></i> Guardar Cambios',
                cancelButtonText: '<i class="fa-solid fa-times"></i> Cancelar',
                buttonsStyling: false,
                customClass: {
                    popup: 'modal-elegante',
                    confirmButton: 'swal2-confirm btn-guardar',
                    cancelButton: 'swal2-cancel btn-cancelar'
                },
                didOpen: () => {
                    // Paletas rápidas
                    document.querySelectorAll('.palette-option').forEach(btn => {
                        btn.addEventListener('click', () => {
                            const paletteName = btn.getAttribute('data-palette');
                            const palette = PALETTES[paletteName];
                            if (palette) {
                                applyColors(palette.colores);
                                Swal.fire({
                                    title: `🎨 ${palette.nombre}`,
                                    text: palette.desc,
                                    icon: 'success',
                                    timer: 1200,
                                    showConfirmButton: false,
                                    background: '#1A1A1A',
                                    color: '#F5F5DC'
                                });
                                const modal = Swal.getPopup();
                                if (modal) Swal.close();
                            }
                        });
                    });
                    
                    // Sincronizar colores
                    const syncColor = (colorId, hexId, variable) => {
                        const colorInput = document.getElementById(colorId);
                        const hexInput = document.getElementById(hexId);
                        if (colorInput && hexInput) {
                            colorInput.addEventListener('input', () => {
                                hexInput.value = colorInput.value;
                                document.documentElement.style.setProperty(variable, colorInput.value);
                            });
                            hexInput.addEventListener('input', () => {
                                if (hexInput.value.match(/^#[0-9A-Fa-f]{6}$/)) {
                                    colorInput.value = hexInput.value;
                                    document.documentElement.style.setProperty(variable, hexInput.value);
                                }
                            });
                        }
                    };
                    syncColor('color-dorado', 'color-dorado-hex', '--color-dorado');
                    syncColor('bg-carbon', 'bg-carbon-hex', '--bg-carbon');
                    syncColor('bg-gris', 'bg-gris-hex', '--bg-gris-oxford');
                    syncColor('text-color', 'text-color-hex', '--text-blanco-crema');
                    syncColor('border-color', 'border-color-hex', '--border-fuerte');
                },
                preConfirm: () => {
                    return {
                        '--color-dorado': document.getElementById('color-dorado').value,
                        '--color-dorado-oscuro': adjustBrightness(document.getElementById('color-dorado').value, -15),
                        '--color-dorado-claro': adjustBrightness(document.getElementById('color-dorado').value, 15),
                        '--bg-carbon': document.getElementById('bg-carbon').value,
                        '--bg-carbon-claro': adjustBrightness(document.getElementById('bg-carbon').value, 20),
                        '--bg-carbon-oscuro': adjustBrightness(document.getElementById('bg-carbon').value, -15),
                        '--bg-gris-oxford': document.getElementById('bg-gris').value,
                        '--text-blanco-crema': document.getElementById('text-color').value,
                        '--text-gris-claro': adjustBrightness(document.getElementById('text-color').value, -20),
                        '--border-fuerte': document.getElementById('border-color').value,
                        'logo': document.getElementById('input-logo').value,
                        'video': document.getElementById('input-video').value
                    };
                }
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    const { logo, video, ...colors } = result.value;
                    applyColors(colors);
                    localStorage.setItem('liquour_logo', logo);
                    localStorage.setItem('liquour_theme_logo', logo);
                    localStorage.setItem('liquour_video', video);
                    const logoImg = document.getElementById('logo-sistema');
                    if (logoImg) logoImg.src = logo;
                    
                    const container = document.querySelector('.video-background');
                    if (container && video) {
                        const ytMatch = video.match(/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/);
                        if (ytMatch) {
                            const videoId = ytMatch[1];
                            container.innerHTML = `<iframe src="https://www.youtube.com/embed/${videoId}?autoplay=1&mute=1&controls=0&showinfo=0&autohide=1&loop=1&playlist=${videoId}&playsinline=1&enablejsapi=1" style="position: absolute; top: 50%; left: 50%; width: 100vw; height: 56.25vw; min-height: 100vh; min-width: 177.77vh; transform: translate(-50%, -50%); pointer-events: none;" frameborder="0" allow="autoplay; encrypted-media"></iframe>`;
                        } else {
                            container.innerHTML = `<video autoplay muted loop playsinline id="bg-video"><source src="${video}"></video>`;
                        }
                    }
                    
                    Swal.fire({
                        title: '✅ ¡Configuración Guardada!',
                        text: 'Los cambios se han aplicado en todo el sistema.',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false,
                        background: '#1A1A1A',
                        color: '#F5F5DC'
                    });
                }
            });
        }
        
        // ============================================
        // MANEJADOR DE CLICS EN TARJETAS
        // ============================================
        const cards = document.querySelectorAll('.card');
        
        cards.forEach(card => {
            card.addEventListener('click', function(e) {
                e.preventDefault();
                const titulo = this.querySelector('h3').innerText.trim();
                
                const rutas = {
                    "PUNTO DE VENTA": "../Include/Admin/Tienda_pos.php",
                    "INVENTARIO": (rol === "administrador" || rol === "admin") ? "../Include/Admin/Catalogo_Admin.php" : "../Include/empleado/Catalogo_Empleado.php",
                    "PÁGINA WEB": "../Include/empleado/principal.php",
                    "USUARIOS": "../Include/Admin/empleados.php",
                    "ESTADÍSTICAS": "../Include/Admin/dashboard.php",
                    "VENTAS": "../Include/Admin/reportes.php",
                    "COMPRAS": "../Include/Admin/compras.php"
                };
                
                if (rutas[titulo]) {
                    window.location.href = rutas[titulo];
                }
                else if (titulo === "AJUSTES") {
                    openColorConfigModal();
                }
                else {
                    Swal.fire({
                        title: '<span style="color:var(--color-dorado); letter-spacing: 2px;">EN CONSTRUCCIÓN</span>',
                        html: '<span style="color:#cccccc;">Estamos preparándote una experiencia VIP. ¡Pronto estará lista! 🚧</span>',
                        icon: 'info',
                        iconColor: 'var(--color-dorado)',
                        background: '#1a1a1a',
                        confirmButtonText: 'Aceptar',
                        buttonsStyling: false,
                        customClass: {
                            popup: 'modal-elegante',
                            confirmButton: 'swal2-confirm btn-guardar'
                        }
                    });
                }
            });
        });
        
        // Botón de salir
        const btnExit = document.querySelector('.btn-exit');
        if (btnExit) {
            btnExit.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.getAttribute('href');
                
                Swal.fire({
                    title: '<span style="color:var(--color-dorado); letter-spacing: 2px;">¿CERRAR SESIÓN?</span>',
                    html: '<span style="color:#cccccc;">Saldrás de tu panel de Liquour.</span>',
                    icon: 'warning',
                    iconColor: 'var(--color-dorado)',
                    showCancelButton: true,
                    background: '#1a1a1a',
                    confirmButtonText: 'Sí, salir',
                    cancelButtonText: 'Cancelar',
                    buttonsStyling: false,
                    customClass: {
                        popup: 'modal-elegante',
                        confirmButton: 'swal2-confirm btn-guardar',
                        cancelButton: 'swal2-cancel btn-cancelar'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            });
        }
        
        // ============================================
        // CONTROL DE SONIDO PARA VIDEOS
        // ============================================
        let isMuted = true;
        const btnSound = document.getElementById('btn-toggle-sound');
        if (btnSound) {
            btnSound.addEventListener('click', () => {
                isMuted = !isMuted;
                btnSound.innerHTML = isMuted ? '<i class="fas fa-volume-mute"></i>' : '<i class="fas fa-volume-up"></i>';
                btnSound.style.boxShadow = isMuted ? '0 4px 15px rgba(0,0,0,0.5)' : '0 4px 15px var(--color-dorado)';
                
                // HTML5 Video
                const videoElement = document.getElementById('bg-video');
                if (videoElement && videoElement.tagName === 'VIDEO') {
                    videoElement.muted = isMuted;
                }
                
                // YouTube Iframe
                const iframeElement = document.querySelector('.video-background iframe');
                if (iframeElement) {
                    iframeElement.contentWindow.postMessage(JSON.stringify({
                        event: 'command',
                        func: isMuted ? 'mute' : 'unMute',
                        args: []
                    }), '*');
                    
                    if (!isMuted) {
                        iframeElement.contentWindow.postMessage(JSON.stringify({
                            event: 'command',
                            func: 'setVolume',
                            args: [100]
                        }), '*');
                    }
                }
            });
        }
    });
    </script>
    
    <!-- Botón flotante para sonido -->
    <button id="btn-toggle-sound" style="position: fixed; bottom: 25px; right: 25px; z-index: 9999; background: var(--bg-carbon-oscuro, #0D0D0D); color: var(--color-dorado, #C5A059); border: 2px solid var(--color-dorado, #C5A059); border-radius: 50%; width: 50px; height: 50px; cursor: pointer; display: flex; justify-content: center; align-items: center; box-shadow: 0 4px 15px rgba(0,0,0,0.5); transition: all 0.3s ease; font-size: 1.2rem;">
        <i class="fas fa-volume-mute"></i>
    </button>
</body>
</html>