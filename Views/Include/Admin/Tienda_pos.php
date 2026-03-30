<?php
require_once '../../../Model/conexion.php';

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
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="../../../Assets/CSS/pos.css">

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

    <div class="buttons">
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
        <li><a href="#" data-filter="Todos" style="color: #C5A059;">Todos</a></li>
        <li><a href="#" data-filter="Whisky">Whisky</a></li>
        <li><a href="#" data-filter="Vino">Vinos</a></li>
        <li><a href="#" data-filter="Tequila">Tequila</a></li>
        <li><a href="#" data-filter="Cerveza">Cervezas</a></li>
        <li><a href="#" data-filter="Ron">Ron</a></li>
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
                <img 
                  src="<?php echo !empty($p['imagen']) ? htmlspecialchars($p['imagen']) : '../../../Assets/IMG/image.png'; ?>" 
                  alt="<?php echo htmlspecialchars($p['nombre']); ?>" 
                  class="product-img"
                >
              </div>

              <div class="product-info">
                <span class="item"><?php echo htmlspecialchars($p['nombre']); ?></span>
                <span class="category"><?php echo htmlspecialchars($p['categoria'] ?? 'Sin categoría'); ?></span>
              </div>

              <div class="product-price">
                $<?php echo number_format((float)$p['precio_venta'], 2); ?>
              </div>

              <div class="product-controls">
                <button 
                  class="btn-qty btn-minus"
                  data-id="<?php echo (int)$p['id_producto']; ?>"
                  data-name="<?php echo htmlspecialchars($p['nombre']); ?>"
                  data-price="<?php echo (float)$p['precio_venta']; ?>"
                  data-stock="<?php echo (int)$p['stock']; ?>"
                >
                  <i class="fas fa-minus"></i>
                </button>

                <span class="qty-counter">0</span>

                <button 
                  class="btn-qty btn-plus"
                  data-id="<?php echo (int)$p['id_producto']; ?>"
                  data-name="<?php echo htmlspecialchars($p['nombre']); ?>"
                  data-price="<?php echo (float)$p['precio_venta']; ?>"
                  data-stock="<?php echo (int)$p['stock']; ?>"
                >
                  <i class="fas fa-plus"></i>
                </button>
              </div>
            </li>
          <?php endforeach; ?>
        <?php else: ?>
          <li style="list-style: none; color: #C5A059; padding: 20px; text-align: center;">
            No hay productos registrados en la base de datos.
          </li>
        <?php endif; ?>
      </ul>
    </div>

    <div class="payment-keys">
      <ul>
        <li id="toggle-keyboard" style="cursor: pointer;">
          <i class="fas fa-keyboard fa-2x fa-fw" data-fa-transform="up-2"></i> Teclado
        </li>

        <li>
          <a href="ventas_efectivo.php" style="text-decoration: none; color: inherit; display: flex; flex-direction: column; align-items: center;">
            <i class="fas fa-money-bill-alt fa-2x fa-fw" data-fa-transform="up-2"></i> Efectivo
          </a>
        </li>

        <li>
          <a href="ventas_tarjeta.php" style="text-decoration: none; color: inherit; display: flex; flex-direction: column; align-items: center;">
            <i class="fas fa-credit-card fa-2x fa-fw" data-fa-transform="up-2"></i> Tarjeta
          </a>
        </li>

        <li>
          <a href="promociones.php" style="text-decoration: none; color: inherit; display: flex; flex-direction: column; align-items: center;">
            <i class="fas fa-tags fa-2x fa-fw" data-fa-transform="up-2"></i> Descuento
          </a>
        </li>

        <li>
          <a href="perfil_empleado.php" style="text-decoration: none; color: inherit; display: flex; flex-direction: column; align-items: center;">
            <i class="fas fa-user fa-2x fa-fw" data-fa-transform="up-2"></i> Empleado
          </a>
        </li>

        <li>
          <a href="../../Include/Admin/dashboard.php" style="text-decoration: none; color: inherit; display: flex; flex-direction: column; align-items: center;">
            <i class="fas fa-sign-out-alt fa-2x fa-fw" data-fa-transform="up-2"></i> Salir
          </a>
        </li>
      </ul>
    </div>
  </div>
</div>

<script src="../../../Assets/JS/pos.js"></script>