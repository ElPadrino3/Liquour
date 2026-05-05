<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../../../Config/Liquour_bdd.php';

$db = new BDD();
$conexion = $db->conectar();

$rol_usuario = $_SESSION['rol'] ?? ''; 
$nombre_usuario_sesion = $_SESSION['nombre'] ?? 'Cajero VIP';

$texto_atendido_por = $nombre_usuario_sesion;
if (strtolower($rol_usuario) === 'admin' || strtolower($rol_usuario) === 'administrador') {
    $texto_atendido_por .= " (Administrador)";
} else {
    $id_usuario_actual = $_SESSION['id_usuario'] ?? 0;
    $stmtCaja = $conexion->prepare("SELECT num_caja FROM usuarios WHERE id_usuario = ?");
    $stmtCaja->execute([$id_usuario_actual]);
    $rowCaja = $stmtCaja->fetch();
    $num_caja = $rowCaja['num_caja'] ?? '1';
    $texto_atendido_por .= " (Caja " . $num_caja . ")";
}

$sql = "
    SELECT 
        p.id_producto,
        p.nombre,
        p.codigo_barras,
        p.precio_compra,
        p.precio_venta,
        p.stock,
        p.reservado,
        p.imagen,
        p.estado,
        p.id_categoria,
        c.nombre AS categoria
    FROM productos p
    LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
    WHERE p.estado = 1
    ORDER BY p.nombre ASC
";

$stmt = $conexion->prepare($sql);
$stmt->execute();
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sql_resumen = "SELECT COUNT(id_venta) as total_ventas, IFNULL(SUM(total), 0) as suma_total, MAX(fecha) as ultima_fecha FROM ventas WHERE fecha >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
$stmt_resumen = $conexion->query($sql_resumen);
$resumen_ventas = $stmt_resumen->fetch(PDO::FETCH_ASSOC);

$cant_ventas = $resumen_ventas['total_ventas'] ?? 0;
$suma_total = $resumen_ventas['suma_total'] ?? 0;
$ultima_fecha = $resumen_ventas['ultima_fecha'] ? date('d/m/Y', strtotime($resumen_ventas['ultima_fecha'])) : '-';

$sql_ultimas = "SELECT id_venta, fecha, total, id_usuario FROM ventas WHERE fecha >= DATE_SUB(NOW(), INTERVAL 7 DAY) ORDER BY id_venta DESC LIMIT 4";
$stmt_ultimas = $conexion->query($sql_ultimas);
$ultimas_ventas_raw = $stmt_ultimas->fetchAll(PDO::FETCH_ASSOC);

$ultimas_ventas = [];
$datos_ventas_js = []; 

foreach ($ultimas_ventas_raw as $v) {
    $id_v = $v['id_venta'];
    
    $sql_det = "SELECT d.cantidad as qty, p.nombre as name, d.precio as price FROM detalle_ventas d JOIN productos p ON d.id_producto = p.id_producto WHERE d.id_venta = $id_v";
    $stmt_det = $conexion->query($sql_det);
    $detalles = $stmt_det->fetchAll(PDO::FETCH_ASSOC);
    
    $carrito = [];
    $subtotal = 0;
    foreach($detalles as $d) {
        $carrito[] = [
            'qty' => (int)$d['qty'],
            'name' => $d['name'],
            'price' => (float)$d['price']
        ];
        $subtotal += ((int)$d['qty'] * (float)$d['price']);
    }
    
    $id_usuario_venta = $v['id_usuario'];
    $nombre_vendedor_historico = "Usuario Desconocido";
    if ($id_usuario_venta) {
        $stmtVend = $conexion->prepare("SELECT nombre, rol, num_caja FROM usuarios WHERE id_usuario = ?");
        $stmtVend->execute([$id_usuario_venta]);
        $rowVend = $stmtVend->fetch();
        if ($rowVend) {
            $nombre_vendedor_historico = $rowVend['nombre'];
            if (strtolower($rowVend['rol']) === 'admin' || strtolower($rowVend['rol']) === 'administrador') {
                $nombre_vendedor_historico .= " (Administrador)";
            } else {
                $caja_hist = $rowVend['num_caja'] ?? '1';
                $nombre_vendedor_historico .= " (Caja " . $caja_hist . ")";
            }
        }
    }

    $v['subtotal'] = $subtotal;
    $v['descuento'] = $subtotal - $v['total'];
    if ($v['descuento'] < 0) {
        $v['descuento'] = 0;
    }
    $v['vendedor'] = $nombre_vendedor_historico;
    
    $ultimas_ventas[] = $v;

    $datos_ventas_js[$id_v] = [
        'carrito' => $carrito,
        'subtotal' => $subtotal,
        'descuento' => $v['descuento'],
        'total' => (float)$v['total'],
        'vendedor' => $nombre_vendedor_historico,
        'fecha' => date('d/m/Y H:i', strtotime($v['fecha']))
    ];
}

$url_perfil = "/LIQUOUR/Views/Include/Admin/perfil_Admin.php";
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="../../../Assets/CSS/pos.css?v=<?php echo time(); ?>">

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
        --tema-color: #C5A059;
    }
    
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
        font-family: 'Inter', sans-serif;
        color: var(--text-blanco-crema);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 20px;
        overflow-x: hidden;
        background-color: var(--bg-carbon);
    }
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
        opacity: 1;
    }
    .video-overlay {
        position: fixed;
        top: 0; left: 0;
        width: 100vw; height: 100vh;
        z-index: -1;
        background: var(--bg-carbon);
        opacity: 0.5;
        backdrop-filter: blur(3px);
    }
    .top-bar {
        position: fixed; top: 0; left: 0; width: 100%;
        padding: 15px 30px;
        display: flex; justify-content: space-between; align-items: center;
        background: var(--bg-carbon-claro);
        backdrop-filter: blur(10px);
        border-bottom: 1px solid var(--border-fuerte);
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.8);
        z-index: 1000;
        transition: border-color 0.3s ease;
    }
    .top-bar:hover { border-bottom: 1px solid var(--color-dorado); }
    .brand {
        font-weight: 700; letter-spacing: 2px;
        display: flex; align-items: center; font-size: 0.95rem;
        color: var(--color-dorado); text-transform: uppercase;
        text-shadow: 0 0 10px rgba(0,0,0,0.8);
    }
    .user-section { display: flex; align-items: center; gap: 20px; }
    .user-info {
        display: flex; align-items: center; gap: 10px;
        background: var(--bg-carbon-oscuro);
        border: 1px solid var(--border-fuerte);
        padding: 6px 18px; border-radius: 30px;
        transition: all 0.3s ease;
    }
    .user-info:hover {
        border-color: var(--color-dorado);
        background: var(--bg-carbon-claro);
        box-shadow: 0 0 15px rgba(0,0,0,0.5);
    }
    .user-text { display: flex; flex-direction: column; text-align: left; }
    .username { font-size: 0.8rem; font-weight: 700; color: var(--text-blanco-crema); letter-spacing: 0.5px; }
    .role { font-size: 0.65rem; font-weight: 600; color: var(--color-dorado); text-transform: uppercase; }
    .btn-exit {
        color: var(--text-blanco-crema); text-decoration: none; font-size: 0.85rem; font-weight: 600;
        background: var(--bg-carbon-oscuro); padding: 8px 18px;
        border-radius: 8px; transition: all 0.3s ease;
        border: 1px solid var(--border-fuerte);
    }
    .btn-exit:hover {
        background: #e74c3c; color: #fff; border-color: #e74c3c;
        box-shadow: 0 0 15px rgba(231, 76, 60, 0.4); transform: scale(1.05);
    }
    .main-container { margin-top: 130px; width: 100%; max-width: 1000px; text-align: center; }
    .header h1 {
        font-size: 3rem; letter-spacing: 4px; margin-bottom: 10px; font-weight: 800;
        color: var(--text-blanco-crema);
        text-shadow: 0 4px 15px rgba(0,0,0,0.9);
    }
    .header p {
        font-size: 1.1rem; opacity: 0.9; margin-bottom: 50px; font-weight: 400; color: var(--text-gris-claro);
        text-shadow: 0 2px 10px rgba(0,0,0,0.9);
    }
    .grid-container { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }
    .card {
        text-decoration: none; color: var(--text-blanco-crema);
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        background: var(--bg-carbon-claro) !important; 
        padding: 30px 15px; border-radius: 12px;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275) !important;
        cursor: pointer;
        border: 1px solid var(--border-fuerte) !important; 
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
        border-color: var(--color-dorado) !important;
        background: var(--bg-carbon) !important; 
        box-shadow: 0 15px 35px rgba(0,0,0,0.8), 0 0 15px rgba(255,255,255, 0.1) !important;
    }
    .card i {
        font-size: 2.2rem; margin-bottom: 12px; color: var(--text-blanco-crema);
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        text-shadow: 0 2px 10px rgba(0,0,0,0.8);
    }
    .card:hover i {
        transform: scale(1.25) translateY(-5px);
        color: var(--color-dorado) !important;
        text-shadow: 0 5px 15px rgba(0,0,0,0.9);
    }
    .card h3 { 
        font-size: 0.95rem; letter-spacing: 1px; margin-bottom: 8px; font-weight: 700; 
        text-transform: uppercase; transition: color 0.3s ease;
        text-shadow: 0 2px 8px rgba(0,0,0,0.8);
    }
    .card:hover h3 { color: var(--color-dorado); }
    .card p { 
        font-size: 0.8rem; font-weight: 400; color: var(--text-gris-claro); 
        text-shadow: 0 1px 5px rgba(0,0,0,0.8);
    }
    @media (max-width: 900px) { .grid-container { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 480px) { .grid-container { grid-template-columns: 1fr; } .header h1 { font-size: 2rem; } }
    @keyframes modalAparicion { 0% { opacity: 0; transform: translateY(30px) scale(0.95); } 100% { opacity: 1; transform: translateY(0) scale(1); } }
    .swal2-popup.modal-elegante {
        border: 1px solid var(--border-fuerte) !important; border-top: 4px solid var(--color-dorado) !important;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.8) !important; border-radius: 12px !important; font-family: 'Inter', sans-serif !important;
        animation: modalAparicion 0.5s cubic-bezier(0.165, 0.84, 0.44, 1) forwards !important;
        background: var(--bg-carbon) !important;
    }
    .swal2-cancel.btn-cancelar { border: 1px solid var(--border-fuerte) !important; transition: all 0.3s ease; background: var(--bg-carbon-oscuro) !important; color: var(--text-blanco-crema) !important; }
    .swal2-cancel.btn-cancelar:hover { background: rgba(255, 255, 255, 0.1) !important; }
    .swal2-confirm.btn-guardar { background-color: var(--color-dorado) !important; color: #000 !important; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3) !important; transition: all 0.3s ease; }
    .swal2-confirm.btn-guardar:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0, 0.4) !important; }
</style>

<!-- SCRIPT PARA CARGAR COLORES GUARDADOS DESDE EL MENÚ -->
<script>
    (function() {
        // Cargar colores guardados desde localStorage
        const coloresGuardados = localStorage.getItem('liquour_colors');
        if (coloresGuardados) {
            try {
                const colores = JSON.parse(coloresGuardados);
                const root = document.documentElement;
                for (const [key, value] of Object.entries(colores)) {
                    root.style.setProperty(key, value);
                }
                // Actualizar también la variable tema-color que usa tu CSS
                if (colores['--color-dorado']) {
                    root.style.setProperty('--tema-color', colores['--color-dorado']);
                }
            } catch(e) {
                console.log('Error cargando colores:', e);
            }
        }
        
        // Cargar logo guardado
        const logoGuardado = localStorage.getItem('liquour_logo');
        if (logoGuardado) {
            const logos = document.querySelectorAll('#logo-sistema, .logo-img, img[src*="Logo"]');
            logos.forEach(img => {
                if (img.tagName === 'IMG') img.src = logoGuardado;
            });
        }

        // Cargar video guardado
        const videoGuardado = localStorage.getItem('liquour_video');
        if (videoGuardado) {
            const container = document.querySelector('.video-background');
            if (container) {
                const ytMatch = videoGuardado.match(/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/);
                if (ytMatch) {
                    const videoId = ytMatch[1];
                    container.innerHTML = `<iframe src="https://www.youtube.com/embed/${videoId}?autoplay=1&mute=1&controls=0&showinfo=0&autohide=1&loop=1&playlist=${videoId}&playsinline=1&enablejsapi=1" style="position: absolute; top: 50%; left: 50%; width: 100vw; height: 56.25vw; min-height: 100vh; min-width: 177.77vh; transform: translate(-50%, -50%); pointer-events: none;" frameborder="0" allow="autoplay; encrypted-media"></iframe>`;
                } else {
                    container.innerHTML = `<video autoplay muted loop playsinline id="bg-video"><source src="${videoGuardado}"></video>`;
                }
            }
        }
    })();
</script>

<div class="video-background">
    <video autoplay muted loop playsinline id="bg-video">
        <source src="../../Assets/IMG/licores.mp4" type="video/mp4">
    </video>
</div>
<div class="video-overlay"></div>

<div class="register">
  <div class="left">
    <div class="resumen-card">
        <h3 class="resumen-title">Resumen de venta</h3>
        <div class="table-headers">
            <span style="width: 15%;">CANT.</span>
            <span style="flex: 1;">PRODUCTO</span>
            <span style="width: 20%;">P. UNIT</span>
            <span style="width: 25%; text-align: right;">SUBTOTAL</span>
        </div>
        <div class="cart-items" id="cart-body"></div>
        <div class="totales-pequenos">
            <p>TOTAL PRODUCTOS: <span id="tot-prod">0</span></p>
            <p>CON DESCUENTO: <span id="tot-desc">$0.00</span></p>
        </div>
        <div class="totales-grandes">
            <div class="box-total">
                <span class="label-tot">TOTAL</span>
                <h2 id="display-total">$0.00</h2>
            </div>
            <div class="box-desc">
                <span class="label-tot">DESCUENTO</span>
                <h2 id="display-discount" style="color: #e74c3c;">$0.00</h2>
            </div>
        </div>
        <div class="action-buttons">
            <button id="btn-pagar" class="btn-pagar">
                <i class="fas fa-shopping-cart"></i> Pagar
            </button>
            <button id="btn-descuento" class="btn-descuento">
                <i class="fas fa-tags"></i> % Descuento
            </button>
        </div>
    </div>
  </div>

  <div class="right">
    <div class="top-search-bar">
        <div class="search-box">
            <label>BUSCAR PRODUCTO</label>
            <input type="text" placeholder="Nombre o código..." id="search-input">
        </div>
        <div class="category-box">
            <label>CATEGORÍAS</label>
            <select id="category-select">
                <option value="Todos">Todos los productos</option>
                <option value="Whisky">Whisky</option>
                <option value="Vino">Vinos</option>
                <option value="Tequila">Tequila</option>
                <option value="Cerveza">Cervezas</option>
                <option value="Ron">Ron</option>
                <option value="Vodka">Vodka</option>
            </select>
        </div>
        <div class="nav-icons">
            <i class="fas fa-home" onclick="location.href='../../Layout/menu.php'" title="Menú Principal"></i>
            <i class="fas fa-user" onclick="location.href='<?php echo $url_perfil; ?>'" title="Perfil"></i>
            <div class="bell-wrapper" id="btn-abrir-ventas" title="Últimas Ventas">
                <i class="fas fa-bell"></i>
                <span class="notif-dot" id="dot-ventas" style="display: none;"></span>
            </div>
        </div>
    </div>

    <div class="menu-items">
      <ul id="product-grid">
        <?php if (!empty($items)): ?>
          <?php foreach ($items as $p): ?>
            <li class="product-card" data-categoria="<?php echo htmlspecialchars($p['categoria'] ?? 'Sin categoría'); ?>" data-codigo="<?php echo htmlspecialchars($p['codigo_barras'] ?? 'N/A'); ?>">
              <div class="product-top-info">
                  <span class="stock-pill">STOCK <?php echo (int)$p['stock']; ?></span>
                  <i class="fas fa-toggle-off toggle-icon"></i>
              </div>
              <div class="product-image-container">
                <img src="<?php echo !empty($p['imagen']) ? htmlspecialchars($p['imagen'], ENT_QUOTES) : '../../../Assets/IMG/image.png'; ?>" alt="<?php echo htmlspecialchars($p['nombre'], ENT_QUOTES); ?>" class="product-img">
              </div>
              <div class="product-info">
                <span class="item"><?php echo htmlspecialchars($p['nombre'], ENT_QUOTES); ?></span>
                <span class="category"><?php echo htmlspecialchars($p['categoria'] ?? 'Sin categoría', ENT_QUOTES); ?></span>
              </div>
              <div class="product-price-pill">
                  $<?php echo number_format((float)$p['precio_venta'], 2); ?>
              </div>
              <div class="product-controls">
                <button class="btn-qty btn-minus" data-id="<?php echo (int)$p['id_producto']; ?>"><i class="fas fa-minus"></i></button>
                <span class="qty-counter">0</span>
                <button class="btn-qty btn-plus" data-id="<?php echo (int)$p['id_producto']; ?>" data-stock="<?php echo (int)$p['stock']; ?>"><i class="fas fa-plus"></i></button>
                <i class="fas fa-eye eye-icon"></i>
              </div>
            </li>
          <?php endforeach; ?>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</div>

<div id="modal-pago-efectivo" class="modal-pago-emergente" style="display: none;">
    <div class="modal-pago-caja">
        <div class="modal-pago-cabecera">
            <h2><i class="fas fa-wallet"></i> Cobro en Efectivo</h2>
            <span class="btn-cerrar-modal" id="cerrar-modal-pago">&times;</span>
        </div>
        <div class="modal-pago-cuerpo">
            <div class="pago-detalle-fila">
                <span class="pago-etiqueta">Total a Cobrar:</span>
                <span class="pago-valor total-oro" id="pago-total-mostrar">$0.00</span>
            </div>
            <div class="pago-detalle-fila caja-ingreso">
                <label for="efectivo-recibido" class="pago-etiqueta">Efectivo Recibido:</label>
                <div class="input-con-icono">
                    <span class="icono-dolar">$</span>
                    <input type="number" id="efectivo-recibido" step="0.01" placeholder="0.00">
                </div>
            </div>
            <div class="pago-detalle-fila linea-separadora">
                <span class="pago-etiqueta">Cambio a Entregar:</span>
                <span class="pago-valor cambio-blanco" id="pago-cambio">$0.00</span>
            </div>
        </div>
        <div class="modal-pago-pie">
            <button class="btn-pago-cancelar" id="btn-cancelar-pago">CANCELAR</button>
            <button class="btn-pago-confirmar" id="btn-finalizar-venta">FINALIZAR VENTA</button>
        </div>
    </div>
</div>

<div id="modal-ultimas-ventas" class="modal-pago-emergente" style="display: none;">
    <div class="modal-ventas-caja">
        <div class="modal-ventas-cabecera">
            <h2><i class="fas fa-bell"></i> ÚLTIMAS VENTAS</h2>
            <span class="btn-cerrar-modal" id="cerrar-modal-ventas">&times;</span>
        </div>
        <div class="modal-ventas-cuerpo">
            <div class="ventas-resumen-top">
                <div class="vr-item">
                    <h4><?php echo $cant_ventas; ?></h4>
                    <p>VENTAS</p>
                </div>
                <div class="vr-item">
                    <h4 class="oro-text">$<?php echo number_format((float)$suma_total, 2); ?></h4>
                    <p>TOTAL ACUMULADO</p>
                </div>
                <div class="vr-item">
                    <h4><?php echo $ultima_fecha; ?></h4>
                    <p>ÚLTIMA VENTA</p>
                </div>
            </div>
            <div class="ventas-lista">
                <?php if (count($ultimas_ventas) > 0): ?>
                    <?php foreach ($ultimas_ventas as $v): ?>
                        <div class="venta-item">
                            <div class="v-badge">#<?php echo $v['id_venta']; ?></div>
                            <div class="v-info">
                                <strong>VENTA #<?php echo $v['id_venta']; ?></strong>
                                <span><i class="far fa-calendar-alt"></i> <?php echo date('d/m/Y H:i', strtotime($v['fecha'])); ?></span>
                            </div>
                            <div class="v-monto">$<?php echo number_format((float)$v['total'], 2); ?></div>
                            <div class="v-acciones">
                                <button class="btn-icon" onclick="manejarImpresion(<?php echo $v['id_venta']; ?>)"><i class="fas fa-print"></i></button>
                                <button class="btn-icon text-red" onclick="manejarDescarga(<?php echo $v['id_venta']; ?>)"><i class="fas fa-download"></i></button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="text-align: center; color: var(--text-gris-claro); padding: 20px; font-weight: bold; width: 100%;">No hay ventas en los últimos 7 días.</div>
                <?php endif; ?>
            </div>
        </div>
        <div class="modal-ventas-pie">
            <span class="mostrando-txt">• Mostrando ventas recientes</span>
            <button class="btn-cerrar-secundario" id="btn-cerrar-ventas-sec">Cerrar</button>
        </div>
    </div>
</div>

<div id="modal-detalles-producto" class="modal-pago-emergente" style="display: none;">
    <div class="modal-pago-caja" style="max-width: 400px; text-align: center;">
        <div class="modal-pago-cabecera">
            <h2><i class="fas fa-info-circle"></i> Detalles del Producto</h2>
            <span class="btn-cerrar-modal" id="cerrar-modal-detalles">&times;</span>
        </div>
        <div class="modal-pago-cuerpo" style="padding: 20px;">
            <img id="det-img" src="" alt="Producto" style="max-width: 150px; border-radius: 8px; margin-bottom: 15px;">
            <h3 id="det-nombre" style="color: var(--color-dorado); margin-bottom: 10px;">Nombre</h3>
            <p style="color: var(--text-blanco-crema); margin-bottom: 5px;"><strong>Categoría:</strong> <span id="det-cat"></span></p>
            <p style="color: var(--text-blanco-crema); margin-bottom: 5px;"><strong>Código:</strong> <span id="det-cod"></span></p>
            <p style="color: var(--text-blanco-crema); margin-bottom: 5px;"><strong>Stock Disponible:</strong> <span id="det-stock"></span></p>
            <h2 id="det-precio" style="color: var(--color-dorado); margin-top: 15px; font-size: 1.8rem;">$0.00</h2>
        </div>
    </div>
</div>

<div id="modal-descuentos-avanzado" class="modal-pago-emergente" style="display: none;">
    <div class="modal-pago-caja" style="max-width: 550px;">
        <div class="modal-pago-cabecera">
            <h2><i class="fas fa-tags"></i> Aplicar Descuentos</h2>
            <span class="btn-cerrar-modal" id="cerrar-modal-desc-avanzado">&times;</span>
        </div>
        <div class="modal-pago-cuerpo" style="padding: 20px;">
            <p style="color: var(--color-dorado); margin-bottom: 10px; font-weight: bold;">Descuentos Individuales (%):</p>
            <div style="max-height: 200px; overflow-y: auto; background-color: var(--bg-carbon-oscuro); border: 1px solid var(--border-fuerte); border-radius: 8px; padding: 10px; margin-bottom: 20px;">
                <table style="width: 100%; border-collapse: collapse; color: var(--text-blanco-crema); font-size: 0.9rem;">
                    <thead>
                        <tr style="border-bottom: 2px solid var(--color-dorado); text-align: left;">
                            <th style="padding-bottom: 5px;">Cant.</th>
                            <th style="padding-bottom: 5px;">Producto</th>
                            <th style="padding-bottom: 5px;">Subtotal</th>
                            <th style="padding-bottom: 5px;">Descuento (%)</th>
                        </tr>
                    </thead>
                    <tbody id="desc-table-body">
                    </tbody>
                </table>
            </div>
            <div style="display: flex; align-items: center; justify-content: space-between; background-color: var(--bg-carbon-oscuro); border: 1px solid var(--border-fuerte); padding: 15px; border-radius: 8px;">
                <label style="color: var(--color-dorado); font-weight: bold;">Descuento a TODO el carrito (%):</label>
                <div style="display: flex; align-items: center; gap: 5px;">
                    <input type="number" id="desc-global-input" placeholder="0" min="0" max="100" style="width: 80px; padding: 8px; border-radius: 4px; border: none; font-size: 1.1rem; text-align: center; background: var(--bg-carbon); color: var(--text-blanco-crema);">
                    <span style="color: var(--text-blanco-crema); font-size: 1.2rem; font-weight: bold;">%</span>
                </div>
            </div>
        </div>
        <div class="modal-pago-pie" style="padding: 15px 20px;">
            <button class="btn-pago-confirmar" id="btn-aplicar-desc-avanzado" style="width: 100%;">APLICAR AL CARRITO</button>
        </div>
    </div>
</div>

<script>
    window.vendedorActual = "<?php echo htmlspecialchars($texto_atendido_por, ENT_QUOTES); ?>";
    window.datosVentasHistoricas = <?php echo json_encode($datos_ventas_js, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
</script>
<script>
// ============================================
// SINCRONIZAR COLORES DEL TEMA CON CSS DE POS
// ============================================
(function sincronizarColores() {
    const coloresGuardados = localStorage.getItem('liquour_colors');
    
    if (coloresGuardados) {
        try {
            const colores = JSON.parse(coloresGuardados);
            
            // Mapear nombres de variables del menú a los nombres del POS
            const dorado = colores['--color-dorado'] || '#C5A059';
            const fondo = colores['--bg-carbon'] || '#1A1A1A';
            const texto = colores['--text-blanco-crema'] || '#F5F5DC';
            const borde = colores['--border-fuerte'] || '#4A4A4A';
            
            // Aplicar a las variables que usa el POS
            document.documentElement.style.setProperty('--dorado-mate', dorado);
            document.documentElement.style.setProperty('--negro-carbon', fondo);
            document.documentElement.style.setProperty('--blanco-crema', texto);
            document.documentElement.style.setProperty('--gris-oxford', borde);
            
            console.log('🎨 Colores sincronizados con POS:', { dorado, fondo, texto });
            
        } catch(e) {
            console.log('Error cargando colores:', e);
        }
    }
})();

// Escuchar cambios en tiempo real
window.addEventListener('storage', function(e) {
    if (e.key === 'liquour_colors') {
        location.reload();
    }
});
</script>
<script src="../../../Assets/JS/pos.js?v=<?php echo time(); ?>"></script>
