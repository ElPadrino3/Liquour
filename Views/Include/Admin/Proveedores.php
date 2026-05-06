<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../../../Config/Liquour_bdd.php';

$db = new BDD();
$conexion = $db->conectar();

$mensaje = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btn_agregar'])) {
    $nombre = trim($_POST['nombre']);
    $telefono = trim($_POST['telefono']);

    if (!empty($nombre)) {
        try {
            $sql = "INSERT INTO proveedores (nombre, telefono) VALUES (:nombre, :telefono)";
            $stmt = $conexion->prepare($sql);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':telefono', $telefono);
            
            if ($stmt->execute()) {
                $mensaje = "¡Éxito! Un nuevo proveedor se ha unido a la red. 🥂";
            } else {
                $error = "Error al intentar guardar el proveedor.";
            }
        } catch (PDOException $e) {
            $error = "Error en la base de datos: " . $e->getMessage();
        }
    } else {
        $error = "El nombre de la empresa es obligatorio.";
    }
}

try {
    $consulta = "SELECT * FROM proveedores ORDER BY id_proveedor DESC";
    $stmt = $conexion->prepare($consulta);
    $stmt->execute();
    $listaProveedores = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Error al cargar los proveedores: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Proveedores | Liquour</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="../../../Assets/CSS/style.css">
    <link rel="stylesheet" href="../../../Assets/CSS/Proveedores.css?v=<?php echo time(); ?>">
    
    <!-- ========================================= -->
    <!-- FORZAR ESTILOS DEL HEADER Y TEMA -->
    <!-- ========================================= -->
    <style>
        :root {
            --tema-color: #C5A059;
            --tema-color-rgb: 197, 160, 89;
            --bg-carbon: #1A1A1A;
            --text-cream: #F5F5DC;
            --border-color: #4A4A4A;
            --negro-carbon: #1A1A1A;
            --dorado-mate: #C5A059;
            --gris-oxford: #4A4A4A;
            --blanco-crema: #F5F5DC;
        }
        
        /* ============================================
           FORZAR ESTILOS DEL HEADER - SOLUCIÓN DEFINITIVA
        ============================================ */
        .navbar-liquour {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            background: var(--bg-carbon) !important;
            padding: 8px 5% !important;
            height: 76px !important;
            border-bottom: 1px solid var(--tema-color) !important;
            position: sticky !important;
            top: 0 !important;
            z-index: 9999 !important;
            margin-bottom: 20px !important;
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
            transition: transform 0.3s ease !important;
            object-fit: contain !important;
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
        }
        
        .nav-item:hover {
            background: var(--tema-color) !important;
            color: var(--bg-carbon) !important;
            border-color: var(--tema-color) !important;
            transform: translateY(-2px) !important;
        }
        
        /* Reset para que Bootstrap no afecte los botones del header */
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
        }
        
        .navbar-liquour a:hover {
            background: var(--tema-color) !important;
            color: var(--bg-carbon) !important;
            border-color: var(--tema-color) !important;
            transform: translateY(-2px) !important;
        }
        
        .liquour-page-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: radial-gradient(circle at 50% 50%, var(--bg-carbon) 0%, #0d0d0d 100%);
            z-index: -2;
        }

        .liquour-bg-animated {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-image: 
                radial-gradient(circle at 20% 80%, rgba(197, 160, 89, 0.03) 0%, transparent 40%),
                radial-gradient(circle at 80% 20%, rgba(197, 160, 89, 0.03) 0%, transparent 40%);
            z-index: -1;
            animation: pulseBg 15s infinite alternate ease-in-out;
        }

        @keyframes pulseBg {
            0% { transform: scale(1); opacity: 0.5; }
            100% { transform: scale(1.1); opacity: 1; }
        }

        .floating-particles {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            pointer-events: none;
            z-index: 0;
            overflow: hidden;
        }

        .particle {
            position: absolute;
            background: rgba(197, 160, 89, 0.2);
            border-radius: 50%;
            animation: floatUp linear infinite;
        }

        @keyframes floatUp {
            0% { transform: translateY(100vh) scale(0); opacity: 0; }
            50% { opacity: 1; }
            100% { transform: translateY(-20vh) scale(1.5); opacity: 0; }
        }
        
        /* Ajustes para el contenedor principal */
        .container.mt-5 {
            margin-top: 30px !important;
        }
    </style>
    
    <script>
    // LiquourThemeManager en nav_admin.php ahora controla el tema
    </script>
</head>
<body>
<?php @include '../../Layout/nav_admin.php'; ?>
<div class="liquour-page-bg"></div>
<div class="liquour-bg-animated"></div>
<div class="floating-particles" id="particles"></div>

<div class="container mt-5" style="max-width: 1200px; position: relative; z-index: 1;">
    
    <div class="row mb-5 animate__animated animate__fadeInDown">
        <div class="col-12 text-center text-md-start">
            <h2 class="text-primary" style="font-family: 'Cormorant Garamond', serif; font-size: 2.5rem;">Directorio de Proveedores</h2>
            <p class="text-muted mt-2" style="font-family: 'Montserrat', sans-serif;">Administra tus alianzas estratégicas y contactos comerciales.</p>
        </div>
    </div>

    <?php if ($mensaje): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm animate__animated animate__fadeIn" style="background: rgba(197, 160, 89, 0.1); border: 1px solid var(--dorado-mate); color: var(--dorado-mate);" role="alert">
            <?php echo $mensaje; ?>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm animate__animated animate__fadeIn" style="background: rgba(220, 53, 69, 0.1); border: 1px solid #dc3545; color: #ff6b6b;" role="alert">
            <?php echo $error; ?>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-4 animate__animated animate__fadeInLeft" style="animation-delay: 0.2s;">
            <div class="card h-100" style="background: rgba(26, 26, 26, 0.8); backdrop-filter: blur(10px);">
                <div class="card-header bg-transparent border-bottom-0 pt-4 pb-0">
                    <h5 class="mb-0" style="color: var(--dorado-mate); font-family: 'Montserrat', sans-serif; text-transform: uppercase; font-size: 0.9rem; letter-spacing: 2px;">Registrar Nuevo</h5>
                    <hr style="border-color: rgba(197, 160, 89, 0.3); margin-top: 15px;">
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="mb-4">
                            <label for="nombre" class="form-label" style="color: var(--blanco-crema); font-size: 0.85rem;">Nombre de la Empresa</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ej. Distribuidora El Oasis" required style="background: rgba(0,0,0,0.5); border: 1px solid rgba(197, 160, 89, 0.2); color: var(--blanco-crema);">
                        </div>
                        <div class="mb-4">
                            <label for="telefono" class="form-label" style="color: var(--blanco-crema); font-size: 0.85rem;">Teléfono de Contacto</label>
                            <input type="text" class="form-control" id="telefono" name="telefono" placeholder="Ej. 555-1234" style="background: rgba(0,0,0,0.5); border: 1px solid rgba(197, 160, 89, 0.2); color: var(--blanco-crema);">
                        </div>
                        <button type="submit" name="btn_agregar" class="btn btn-primary w-100 mt-2" style="background: var(--dorado-mate); border: none; color: #1A1A1A; font-weight: bold; text-transform: uppercase; letter-spacing: 1px;">Agregar Proveedor</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8 animate__animated animate__fadeInRight" style="animation-delay: 0.4s;">
            <div class="card h-100" style="background: rgba(26, 26, 26, 0.8); backdrop-filter: blur(10px);">
                <div class="card-header bg-transparent border-bottom-0 pt-4 pb-0">
                    <h5 class="mb-0" style="color: var(--dorado-mate); font-family: 'Montserrat', sans-serif; text-transform: uppercase; font-size: 0.9rem; letter-spacing: 2px;">Contactos Activos</h5>
                    <hr style="border-color: rgba(197, 160, 89, 0.3); margin-top: 15px;">
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle" style="color: var(--blanco-crema); background: transparent;">
                            <thead style="background: transparent;">
                                <tr style="border-bottom: 1px solid rgba(197, 160, 89, 0.2); background: transparent;">
                                    <th scope="col" style="background: transparent; width: 15%; padding-left: 25px; color: var(--gris-oxford); font-weight: normal; text-transform: uppercase; font-size: 0.8rem;">Código</th>
                                    <th scope="col" style="background: transparent; width: 45%; color: var(--gris-oxford); font-weight: normal; text-transform: uppercase; font-size: 0.8rem;">Razón Social</th>
                                    <th scope="col" style="background: transparent; width: 25%; color: var(--gris-oxford); font-weight: normal; text-transform: uppercase; font-size: 0.8rem;">Contacto</th>
                                    <th scope="col" style="background: transparent; width: 15%; color: var(--gris-oxford); font-weight: normal; text-transform: uppercase; font-size: 0.8rem;">Estatus</th>
                                </tr>
                            </thead>
                            <tbody style="background: transparent;">
                                <?php if (!empty($listaProveedores)): ?>
                                    <?php foreach ($listaProveedores as $prov): ?>
                                        <tr style="background: transparent; border-bottom: 1px solid rgba(255,255,255,0.05); transition: background 0.3s;" onmouseover="this.style.background='rgba(197, 160, 89, 0.05)'" onmouseout="this.style.background='transparent'">
                                            <td style="background: transparent; padding-left: 25px; padding-top: 15px; padding-bottom: 15px;"><span style="color: var(--gris-oxford);">PRV-</span><strong style="color: var(--dorado-mate);"><?php echo str_pad($prov['id_proveedor'], 3, '0', STR_PAD_LEFT); ?></strong></td>
                                            <td style="background: transparent; font-weight: 500; font-size: 1.05rem;"><?php echo htmlspecialchars($prov['nombre']); ?></td>
                                            <td style="background: transparent; color: #A0A0A0;"><?php echo htmlspecialchars($prov['telefono'] ?? 'N/D'); ?></td>
                                            <td style="background: transparent;">
                                                <?php if ($prov['estado']): ?>
                                                    <span style="background: rgba(197, 160, 89, 0.1); color: var(--dorado-mate); border: 1px solid var(--dorado-mate); padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px;">Operativo</span>
                                                <?php else: ?>
                                                    <span style="background: rgba(220, 53, 69, 0.1); color: #dc3545; border: 1px solid #dc3545; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px;">Suspendido</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr style="background: transparent;">
                                        <td colspan="4" class="text-center py-5" style="background: transparent;">
                                            <div style="color: var(--gris-oxford); font-size: 1.1rem; margin-bottom: 10px;">No hay registros en el directorio</div>
                                            <div style="color: #A0A0A0; font-size: 0.9rem;">Utiliza el panel para registrar tu primer proveedor.</div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('particles');
        for (let i = 0; i < 20; i++) {
            let p = document.createElement('div');
            p.className = 'particle';
            let size = Math.random() * 4 + 2;
            p.style.width = size + 'px';
            p.style.height = size + 'px';
            p.style.left = Math.random() * 100 + 'vw';
            p.style.animationDuration = (Math.random() * 10 + 10) + 's';
            p.style.animationDelay = (Math.random() * 5) + 's';
            container.appendChild(p);
        }
    });
</script>
</body>
</html>