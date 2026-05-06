<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// =============================================
// DETECCIÓN CORRECTA DE ROLES - FORZADA
// =============================================
$rol_usuario = isset($_SESSION['rol']) ? strtolower(trim($_SESSION['rol'])) : '';

// Si no hay rol en sesión O si el rol no es reconocido, intentar obtener desde la base de datos
if (empty($rol_usuario) || ($rol_usuario !== 'admin' && $rol_usuario !== 'administrador' && $rol_usuario !== 'empleado' && $rol_usuario !== 'vendedor')) {
    if (isset($_SESSION['id_usuario'])) {
        require_once __DIR__ . '/../../Config/Liquour_bdd.php';
        $db = new BDD();
        $conexion = $db->conectar();
        
        $stmt = $conexion->prepare("SELECT rol FROM usuarios WHERE id_usuario = ?");
        $stmt->execute([$_SESSION['id_usuario']]);
        $userData = $stmt->fetch();
        if ($userData) {
            $rol_usuario = strtolower(trim($userData['rol']));
            $_SESSION['rol'] = $rol_usuario; // Guardar en sesión
            // Forzar actualización de la variable de sesión
            $_SESSION['rol_actualizado'] = true;
        }
        $db->desconectar();
    }
}

// Debug: Mostrar rol en consola (opcional, borrar después)
$debug_rol = $rol_usuario;

// =============================================
// SISTEMA DE TEMAS - Colores y Logo
// =============================================
$themeColor = $_SESSION['theme_color'] ?? '#C5A059';
$themeLogo = $_SESSION['theme_logo'] ?? '/LIQUOUR/Assets/IMG/Logo.jpeg';

// Validar que el logo existe
if (!empty($themeLogo) && !file_exists($_SERVER['DOCUMENT_ROOT'] . $themeLogo)) {
    $themeLogo = '/LIQUOUR/Assets/IMG/Logo.jpeg';
}

// Pasar color a formato RGB
$r = hexdec(substr($themeColor, 1, 2));
$g = hexdec(substr($themeColor, 3, 2));
$b = hexdec(substr($themeColor, 5, 2));
?>

<!-- ========================================= -->
<!-- CSS y Recursos -->
<!-- ========================================= -->
<link rel="stylesheet" href="../../../Assets/CSS/nav.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">


<!-- ========================================= -->
<!-- ESTILOS DINÁMICOS -->
<!-- ========================================= -->
<style>
    :root {
        --tema-color: <?php echo $themeColor; ?>;
        --tema-color-rgb: <?php echo "$r, $g, $b"; ?>;
        --bg-carbon: #1A1A1A;
        --text-cream: #F5F5DC;
        --border-color: #4A4A4A;
    }
    
    * {
        transition: background-color 0.3s ease, border-color 0.3s ease, color 0.2s ease;
    }
    
    .navbar-liquour {
        background: rgba(5, 5, 5, 0.92);
        backdrop-filter: blur(12px);
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        transition: border-color 0.3s ease;
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        padding: 8px 5% !important;
        height: 76px !important;
        position: sticky !important;
        top: 0 !important;
        z-index: 9999 !important;
    }
    
    .navbar-liquour:hover {
        border-bottom-color: var(--tema-color);
    }
    
    .logo-container {
        display: flex !important;
        align-items: center !important;
        height: 100% !important;
    }
    
    .logo-img {
        height: 60px !important;
        max-height: 60px !important;
        width: auto !important;
        transition: filter 0.3s ease, transform 0.3s ease;
        object-fit: contain !important;
    }
    
    .logo-img:hover {
        filter: drop-shadow(0 0 8px var(--tema-color));
        transform: scale(1.02);
    }
    
    .nav-menu {
        display: flex !important;
        gap: 10px !important;
        margin: 0 !important;
        padding: 0 !important;
        list-style: none !important;
    }
    
    .nav-item {
        background: var(--bg-carbon) !important;
        border: 1px solid var(--border-color) !important;
        color: var(--text-cream) !important;
        padding: 8px 16px !important;
        font-family: 'Montserrat', sans-serif !important;
        font-weight: 600 !important;
        font-size: 12px !important;
        text-transform: uppercase !important;
        cursor: pointer !important;
        text-decoration: none !important;
        border-radius: 6px !important;
        transition: all 0.3s ease !important;
        display: inline-block !important;
        position: relative;
    }
    
    .nav-item:hover {
        background: var(--tema-color) !important;
        color: var(--bg-carbon) !important;
        border-color: var(--tema-color) !important;
        transform: translateY(-2px) !important;
    }
    
    .nav-item::after {
        content: '';
        position: absolute;
        bottom: -5px;
        left: 50%;
        transform: translateX(-50%);
        width: 0;
        height: 2px;
        background: var(--tema-color);
        transition: width 0.3s ease;
    }
    
    .nav-item:hover::after {
        width: 80%;
    }
    
    .navbar-liquour a {
        all: unset;
        background: var(--bg-carbon) !important;
        border: 1px solid var(--border-color) !important;
        color: var(--text-cream) !important;
        padding: 8px 16px !important;
        font-family: 'Montserrat', sans-serif !important;
        font-weight: 600 !important;
        font-size: 12px !important;
        text-transform: uppercase !important;
        border-radius: 6px !important;
        cursor: pointer !important;
        text-decoration: none !important;
        display: inline-block !important;
        margin: 0 !important;
        position: relative;
    }
    
    .navbar-liquour a:hover {
        background: var(--tema-color) !important;
        color: var(--bg-carbon) !important;
        border-color: var(--tema-color) !important;
        transform: translateY(-2px) !important;
    }
</style>

<!-- ========================================= -->
<!-- THEME MANAGER -->
<!-- ========================================= -->
<script>
class LiquourThemeManager {
    constructor() {
        this.color = null;
        
        // Primero sincronizar con la configuración de menu.php
        const coloresMenu = localStorage.getItem('liquour_colors');
        if (coloresMenu) {
            try {
                const coloresObj = JSON.parse(coloresMenu);
                if (coloresObj['--color-dorado']) {
                    this.color = coloresObj['--color-dorado'];
                    localStorage.setItem('liquour_theme_color', this.color);
                }
            } catch(e) {}
        }
        
        if (!this.color) {
            this.color = localStorage.getItem('liquour_theme_color') || '<?php echo $themeColor; ?>';
        }
        
        this.logo = localStorage.getItem('liquour_theme_logo') || '<?php echo $themeLogo; ?>';
        this.init();
    }

    init() {
        this.applyTheme();
        this.setupLogoObserver();
        this.setupColorObserver();
        this.syncWithServer();
        // Forzar actualización del logo
        this.forceLogoUpdate();
    }

    forceLogoUpdate() {
        const logoImg = document.querySelector('.logo-img, .theme-logo, #main-logo');
        if (logoImg && this.logo) {
            logoImg.src = this.logo;
            console.log('🖼️ Logo forzado:', this.logo);
        }
    }

    applyTheme() {
        // Set all variations of the gold color used across the system
        document.documentElement.style.setProperty('--tema-color', this.color);
        document.documentElement.style.setProperty('--color-dorado', this.color);
        document.documentElement.style.setProperty('--dorado-mate', this.color);
        document.documentElement.style.setProperty('--gold', this.color);
        
        const rgb = this.hexToRgb(this.color);
        if (rgb) {
            document.documentElement.style.setProperty('--tema-color-rgb', `${rgb.r}, ${rgb.g}, ${rgb.b}`);
        }
        
        const logoElements = document.querySelectorAll('.logo-img, .theme-logo, [data-theme-logo]');
        logoElements.forEach(img => {
            if (img.tagName === 'IMG') {
                img.src = this.logo;
                img.onerror = () => {
                    img.src = '/LIQUOUR/Assets/IMG/Logo.jpeg';
                };
            }
        });
        
        console.log('✅ Tema aplicado:', { color: this.color, logo: this.logo });
    }

    hexToRgb(hex) {
        const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
        return result ? {
            r: parseInt(result[1], 16),
            g: parseInt(result[2], 16),
            b: parseInt(result[3], 16)
        } : null;
    }

    setupLogoObserver() {
        const observer = new MutationObserver(() => {
            const logoElements = document.querySelectorAll('.logo-img, .theme-logo');
            logoElements.forEach(img => {
                if (img.tagName === 'IMG') {
                    img.src = this.logo;
                }
            });
        });
        observer.observe(document.body, { childList: true, subtree: true });
    }

    setupColorObserver() {
        const observer = new MutationObserver(() => {
            const newColor = getComputedStyle(document.documentElement).getPropertyValue('--tema-color').trim();
            if (newColor && newColor !== this.color) {
                this.color = newColor;
                localStorage.setItem('liquour_theme_color', this.color);
            }
        });
        observer.observe(document.documentElement, { attributes: true, attributeFilter: ['style'] });
        
        // Sincronizar cambios hechos desde otras pestañas (como menu.php)
        window.addEventListener('storage', (e) => {
            if (e.key === 'liquour_colors' || e.key === 'liquour_theme_logo' || e.key === 'liquour_logo') {
                location.reload();
            }
        });
    }

    async syncWithServer() {
        try {
            const response = await fetch('/LIQUOUR/Include/Config/sync_theme.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    color: this.color,
                    logo: this.logo
                })
            });
            const data = await response.json();
            if (!data.success) {
                console.warn('No se pudo sincronizar con el servidor');
            }
        } catch (error) {
            console.log('Modo offline - tema guardado localmente');
        }
    }

    updateTheme(color, logo) {
        if (color && color !== this.color) {
            this.color = color;
            localStorage.setItem('liquour_theme_color', this.color);
            
            // Sincronizar también con liquour_colors para compatibilidad global
            try {
                const coloresGuardados = localStorage.getItem('liquour_colors');
                let coloresObj = coloresGuardados ? JSON.parse(coloresGuardados) : {};
                coloresObj['--color-dorado'] = this.color;
                localStorage.setItem('liquour_colors', JSON.stringify(coloresObj));
            } catch(e) {}
        }
        if (logo && logo !== this.logo) {
            this.logo = logo;
            localStorage.setItem('liquour_theme_logo', this.logo);
            localStorage.setItem('liquour_logo', this.logo);
        }
        
        this.applyTheme();
        this.syncWithServer();
        
        window.dispatchEvent(new CustomEvent('liquourThemeChanged', {
            detail: { color: this.color, logo: this.logo }
        }));
        
        console.log('🎨 Tema actualizado:', { color: this.color, logo: this.logo });
    }

    getCurrentColor() {
        return this.color;
    }

    getCurrentLogo() {
        return this.logo;
    }
}

function openLiquourThemeConfig() {
    const currentColor = window.liquourTheme?.getCurrentColor() || '#C5A059';
    const currentLogo = window.liquourTheme?.getCurrentLogo() || '/LIQUOUR/Assets/IMG/Logo.jpeg';
    
    Swal.fire({
        title: '<span style="font-family: Inter; letter-spacing: 2px;">🎨 CONFIGURACIÓN VISUAL</span>',
        html: `
            <div style="text-align: left; padding: 10px;">
                <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 30px;">
                    <i class="fa-solid fa-palette" style="font-size: 2rem; color: ${currentColor};"></i>
                    <h2 style="margin: 0; color: #fff; font-size: 1.3rem; letter-spacing: 1px;">Personaliza Liquour</h2>
                </div>
                
                <div style="background: rgba(255,255,255,0.05); padding: 20px; border-radius: 12px; margin-bottom: 20px;">
                    <label style="display: block; color: #aaa; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 12px;">🎨 Color Principal</label>
                    <div style="display: flex; align-items: center; gap: 20px; flex-wrap: wrap;">
                        <input type="color" id="theme-color-picker" value="${currentColor}" style="width: 65px; height: 65px; border: 2px solid ${currentColor}; border-radius: 12px; cursor: pointer; background: transparent; padding: 0;">
                        <div style="flex: 1;">
                            <code id="color-value" style="background: #000; padding: 8px 15px; border-radius: 8px; color: ${currentColor};">${currentColor}</code>
                            <p style="color: #888; font-size: 0.75rem; margin-top: 8px;">Define el color que identificará tu marca en todo el sistema</p>
                        </div>
                    </div>
                </div>
                
                <div style="background: rgba(255,255,255,0.05); padding: 20px; border-radius: 12px; margin-bottom: 20px;">
                    <label style="display: block; color: #aaa; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 12px;">🏷️ Logotipo</label>
                    <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                        <div style="flex: 2;">
                            <input type="text" id="logo-url-input" placeholder="URL del logo..." value="${currentLogo}" style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #333; background: #111; color: #fff; font-size: 0.85rem;">
                            <small style="color: #666; display: block; margin-top: 8px;">Pega la URL de tu logo o selecciona un archivo</small>
                        </div>
                        <div style="text-align: center;">
                            <div id="logo-preview-container" style="width: 70px; height: 70px; border: 1px solid ${currentColor}; border-radius: 12px; overflow: hidden; background: #111; display: flex; align-items: center; justify-content: center;">
                                <img id="logo-preview-img" src="${currentLogo}" style="max-width: 100%; max-height: 100%; object-fit: contain;" onerror="this.src='/LIQUOUR/Assets/IMG/Logo.jpeg'">
                            </div>
                            <small style="color: #666;">Vista previa</small>
                        </div>
                    </div>
                    <div style="margin-top: 15px;">
                        <label style="display: block; color: #aaa; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">📁 O subir desde tu PC</label>
                        <input type="file" id="logo-file-input" accept="image/*" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #333; background: #111; color: #fff; font-size: 0.8rem;">
                    </div>
                </div>
                
                <div style="background: rgba(197,160,89,0.1); padding: 12px; border-radius: 8px; border-left: 3px solid ${currentColor};">
                    <small style="color: #aaa;">✨ Los cambios se aplicarán automáticamente en todas las páginas del sistema</small>
                </div>
            </div>
        `,
        background: '#121212',
        showCancelButton: true,
        showDenyButton: true,
        confirmButtonText: '<i class="fa-solid fa-check"></i> Aplicar Cambios',
        denyButtonText: '<i class="fa-solid fa-rotate-left"></i> Por defecto',
        cancelButtonText: 'Cancelar',
        buttonsStyling: false,
        customClass: {
            popup: 'theme-config-modal',
            confirmButton: 'theme-btn-confirm',
            denyButton: 'theme-btn-cancel swal2-deny btn-restablecer',
            cancelButton: 'theme-btn-cancel'
        },
        didOpen: () => {
            const colorPicker = document.getElementById('theme-color-picker');
            const colorValue = document.getElementById('color-value');
            const urlInput = document.getElementById('logo-url-input');
            const previewImg = document.getElementById('logo-preview-img');
            const fileInput = document.getElementById('logo-file-input');
            
            colorPicker.addEventListener('input', (e) => {
                const newColor = e.target.value;
                colorValue.textContent = newColor;
                colorValue.style.color = newColor;
                document.getElementById('logo-preview-container').style.borderColor = newColor;
                colorPicker.style.borderColor = newColor;
            });
            
            urlInput.addEventListener('input', () => {
                previewImg.src = urlInput.value;
                previewImg.onerror = () => { previewImg.src = '/LIQUOUR/Assets/IMG/Logo.jpeg'; };
            });
            
            fileInput.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (event) => {
                        const base64 = event.target.result;
                        urlInput.value = base64;
                        previewImg.src = base64;
                    };
                    reader.readAsDataURL(file);
                }
            });
        },
        preConfirm: () => {
            return {
                color: document.getElementById('theme-color-picker').value,
                logo: document.getElementById('logo-url-input').value
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            if (window.liquourTheme) {
                window.liquourTheme.updateTheme(result.value.color, result.value.logo);
            }
            
            Swal.fire({
                title: '¡Configuración Aplicada!',
                html: 'El tema se ha actualizado en todo el sistema. La página se recargará.',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false,
                background: '#121212',
                color: '#fff'
            }).then(() => {
                location.reload();
            });
        } else if (result.isDenied) {
            // Restablecer por defecto
            if (window.liquourTheme) {
                window.liquourTheme.updateTheme('#C5A059', '/LIQUOUR/Assets/IMG/Logo.jpeg');
            }
            Swal.fire({
                title: '¡Restaurado!',
                html: 'Se han restaurado el color dorado y el logo original.',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false,
                background: '#121212',
                color: '#fff'
            }).then(() => {
                location.reload();
            });
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    window.liquourTheme = new LiquourThemeManager();
    window.openLiquourConfig = openLiquourThemeConfig;
    
    const configBtn = document.querySelector('[data-theme-config], #btn-configuracion, .btn-config');
    if (configBtn) {
        configBtn.addEventListener('click', (e) => {
            e.preventDefault();
            openLiquourThemeConfig();
        });
    }
    
    console.log('✅ Liquour Theme Manager iniciado');
    console.log('👤 Rol detectado en PHP:', '<?php echo $debug_rol; ?>');
});
</script>

<!-- ========================================= -->
<!-- HEADER HTML -->
<!-- ========================================= -->
<header class="navbar-liquour">
    <div class="logo-container">
        <img src="<?php echo $themeLogo; ?>" alt="Liquour Logo" class="logo-img theme-logo" id="main-logo">
    </div>
    
    <nav class="nav-menu">
        <?php if ($rol_usuario === 'admin' || $rol_usuario === 'administrador'): ?>
            <a href="/LIQUOUR/Views/Include/Admin/dashboard.php" class="nav-item">Dashboard</a>
            <a href="/LIQUOUR/Views/Include/Admin/Tienda_pos.php" class="nav-item">Punto de Venta</a>
            <a href="/LIQUOUR/Views/Include/Admin/Catalogo_Admin.php" class="nav-item">Inventario</a>
            <a href="/LIQUOUR/Views/Include/Admin/Proveedores.php" class="nav-item">Proveedores</a>
            <a href="/LIQUOUR/Views/Include/Admin/empleados.php" class="nav-item">Empleados</a>
            <a href="/LIQUOUR/Views/Include/Admin/reportes.php" class="nav-item">Reportes</a>
            <a href="/LIQUOUR/Views/Include/Admin/compras.php" class="nav-item">Compras</a>
            <a href="/LIQUOUR/Views/Layout/menu.php" class="nav-item">Home</a>
        <?php elseif ($rol_usuario === 'empleado' || $rol_usuario === 'vendedor' || $rol_usuario === 'cajero'): ?>
            <a href="/LIQUOUR/Views/Include/Admin/Tienda_pos.php" class="nav-item">Punto de Venta</a>
            <a href="/LIQUOUR/Views/Include/empleado/Catalogo_Empleado.php" class="nav-item">Inventario</a>
            <a href="/LIQUOUR/Views/Layout/menu.php" class="nav-item">Home</a>
        <?php else: ?>
            <a href="/LIQUOUR/Views/Layout/menu.php" class="nav-item">Home</a>
        <?php endif; ?>
    </nav>
</header>


<script>
// Forzar actualización del logo desde localStorage
(function() {
    const logoGuardado = localStorage.getItem('liquour_theme_logo') || localStorage.getItem('liquour_logo');
    if (logoGuardado) {
        const logoImg = document.getElementById('main-logo');
        if (logoImg) {
            logoImg.src = logoGuardado;
            console.log('Logo actualizado a:', logoGuardado);
        }
    }
})();
</script>