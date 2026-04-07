<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../../../Config/Liquour_bdd.php';

$db = new BDD();
$conexion = $db->conectar();

$rol_usuario = $_SESSION['rol'] ?? ''; 

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

if ($rol_usuario === 'empleado') {
    $url_regreso = "/LIQUOUR/Views/Include/empleado/Catalogo_Empleado.php";
    $url_perfil = "/LIQUOUR/Views/Include/Admin/perfil_Admin.php"; 
} else {
    $url_regreso = "/LIQUOUR/Views/Include/Admin/dashboard.php";
    $url_perfil = "/LIQUOUR/Views/Include/Admin/perfil_Admin.php";
}
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="../../../Assets/CSS/pos.css?v=<?php echo time(); ?>">

<div class="register">
  <div class="left">
    <div class="order-window">
      <table>
        <tbody id="cart-body">
          <tr>
            <td>Cant.</td>
            <td>Producto</td>
            <td>P. Unit</td>
            <td>Subtotal</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="order-total">
      <span id="display-total">$0.00</span>
    </div>

    <div class="buttons" id="keyboard-area">
      <button class="btn-special op-plus"><i class="fas fa-plus"></i></button>
      <button class="btn-special op-minus"><i class="fas fa-minus"></i></button>
      <button class="btn-special op-reset"><i class="fas fa-times"></i> Reiniciar</button>
      <button class="btn-special op-void"><i class="fas fa-ban"></i> Anular</button>

      <button class="btn-num n1">1</button>
      <button class="btn-num n2">2</button>
      <button class="btn-num n3">3</button>
      <button class="btn-num n4">4</button>
      <button class="btn-num n5">5</button>
      <button class="btn-num n6">6</button>
      <button class="btn-num n7">7</button>
      <button class="btn-num n8">8</button>
      <button class="btn-num n9">9</button>
      <button class="btn-num n0">0</button>
      <button class="btn-num ndot">.00</button>

      <button class="btn-special op-equal"><i class="fas fa-equals"></i></button>
    </div>
  </div>

  <div class="right">
    <div class="categories">
      <ul>
        <li><a href="#" data-filter="Todos" style="color: #C5A059;">TODOS</a></li>
        <li><a href="#" data-filter="Whisky">WHISKY</a></li>
        <li><a href="#" data-filter="Vino">VINOS</a></li>
        <li><a href="#" data-filter="Tequila">TEQUILA</a></li>
        <li><a href="#" data-filter="Cerveza">CERVEZAS</a></li>
        <li><a href="#" data-filter="Ron">RON</a></li>
      </ul>
    </div>

    <div class="menu-items">
      <ul>
        <?php if (!empty($items)): ?>
          <?php foreach ($items as $p): ?>
            <li class="product-card" data-categoria="<?php echo htmlspecialchars($p['categoria'] ?? 'Sin categoría'); ?>">
              <div class="product-stock">
                <span class="stock-label">STOCK</span>
                <span class="stock-value"><?php echo (int)$p['stock']; ?></span>
              </div>
              <div class="product-image-container">
                <img src="<?php echo !empty($p['imagen']) ? htmlspecialchars($p['imagen']) : '../../../Assets/IMG/image.png'; ?>" alt="<?php echo htmlspecialchars($p['nombre']); ?>" class="product-img">
              </div>
              <div class="product-info">
                <span class="item"><?php echo htmlspecialchars($p['nombre']); ?></span>
                <span class="category"><?php echo htmlspecialchars($p['categoria'] ?? 'Sin categoría'); ?></span>
              </div>
              <div class="product-price">$<?php echo number_format((float)$p['precio_venta'], 2); ?></div>
              <div class="product-controls">
                <button class="btn-qty btn-minus" data-id="<?php echo (int)$p['id_producto']; ?>"><i class="fas fa-minus"></i></button>
                <span class="qty-counter">0</span>
                <button class="btn-qty btn-plus" data-id="<?php echo (int)$p['id_producto']; ?>" data-stock="<?php echo (int)$p['stock']; ?>"><i class="fas fa-plus"></i></button>
              </div>
            </li>
          <?php endforeach; ?>
        <?php endif; ?>
      </ul>
    </div>

    <div class="payment-keys">
      <ul>
        <li id="toggle-keyboard">
            <i class="fas fa-keyboard fa-2x fa-fw"></i>
            <span>TECLADO</span>
        </li>
        <li onclick="abrirModalEfectivo()">
            <i class="fas fa-money-bill-alt fa-2x fa-fw"></i>
            <span>EFECTIVO</span>
        </li>
        <li onclick="location.href='ventas_tarjeta.php'">
            <i class="fas fa-credit-card fa-2x fa-fw"></i>
            <span>TARJETA</span>
        </li>
        <li onclick="location.href='promociones.php'">
            <i class="fas fa-tags fa-2x fa-fw"></i>
            <span>DESCUENTO</span>
        </li>
        <li onclick="location.href='<?php echo $url_perfil; ?>'">
            <i class="fas fa-user fa-2x fa-fw"></i>
            <span>PERFIL</span>
        </li>
        <li onclick="location.href='<?php echo $url_regreso; ?>'">
            <i class="fas fa-sign-out-alt fa-2x fa-fw"></i>
            <span>SALIR</span>
        </li>
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
                <span class="pago-etiqueta">Total Venta:</span>
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

<script src="../../../Assets/JS/pos.js?v=<?php echo time(); ?>"></script>