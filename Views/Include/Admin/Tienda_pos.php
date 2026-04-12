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

        <div class="cart-items" id="cart-body">
        </div>

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
                    <div style="text-align: center; color: var(--gris-oxford); padding: 20px; font-weight: bold; width: 100%;">No hay ventas en los últimos 7 días.</div>
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
            <h3 id="det-nombre" style="color: var(--dorado-mate); margin-bottom: 10px;">Nombre</h3>
            <p style="color: var(--blanco-crema); margin-bottom: 5px;"><strong>Categoría:</strong> <span id="det-cat"></span></p>
            <p style="color: var(--blanco-crema); margin-bottom: 5px;"><strong>Código:</strong> <span id="det-cod"></span></p>
            <p style="color: var(--blanco-crema); margin-bottom: 5px;"><strong>Stock Disponible:</strong> <span id="det-stock"></span></p>
            <h2 id="det-precio" style="color: var(--dorado-mate); margin-top: 15px; font-size: 1.8rem;">$0.00</h2>
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
            <p style="color: var(--dorado-mate); margin-bottom: 10px; font-weight: bold;">Descuentos Individuales (%):</p>
            <div style="max-height: 200px; overflow-y: auto; background-color: var(--gris-fondo); border: 1px solid var(--gris-oxford); border-radius: 8px; padding: 10px; margin-bottom: 20px;">
                <table style="width: 100%; border-collapse: collapse; color: var(--blanco-crema); font-size: 0.9rem;">
                    <thead>
                        <tr style="border-bottom: 2px solid var(--dorado-mate); text-align: left;">
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

            <div style="display: flex; align-items: center; justify-content: space-between; background-color: var(--gris-fondo); border: 1px solid var(--gris-oxford); padding: 15px; border-radius: 8px;">
                <label style="color: var(--dorado-mate); font-weight: bold;">Descuento a TODO el carrito (%):</label>
                <div style="display: flex; align-items: center; gap: 5px;">
                    <input type="number" id="desc-global-input" placeholder="0" min="0" max="100" style="width: 80px; padding: 8px; border-radius: 4px; border: none; font-size: 1.1rem; text-align: center; background: var(--negro-carbon); color: var(--blanco-crema);">
                    <span style="color: var(--white); font-size: 1.2rem; font-weight: bold;">%</span>
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
<script src="../../../Assets/JS/pos.js?v=<?php echo time(); ?>"></script>