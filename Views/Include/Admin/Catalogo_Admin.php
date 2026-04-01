<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../../../Config/Liquour_bdd.php';

$db = new BDD();
$conexion = $db->conectar();

$mensaje = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_comprar'])) {
    $id_producto = $_POST['id_producto'];
    $id_proveedor = $_POST['id_proveedor'];
    $cantidad = (int)$_POST['cantidad'];
    $precio_compra = (float)$_POST['precio_compra'];
    $id_usuario = $_SESSION['id_usuario'] ?? 1; 

    if ($cantidad > 0 && $precio_compra >= 0 && !empty($id_proveedor)) {
        try {
            $conexion->beginTransaction();
            
            $total = $cantidad * $precio_compra;
            
            $stmt = $conexion->prepare("INSERT INTO compras (total, id_usuario) VALUES (?, ?)");
            $stmt->execute([$total, $id_usuario]);
            $id_compra = $conexion->lastInsertId();
            
            $stmt2 = $conexion->prepare("INSERT INTO detalle_compras (id_compra, id_producto, id_proveedor, cantidad, precio_compra, subtotal) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt2->execute([$id_compra, $id_producto, $id_proveedor, $cantidad, $precio_compra, $total]);
            
            $stmt3 = $conexion->prepare("UPDATE productos SET stock = stock + ? WHERE id_producto = ?");
            $stmt3->execute([$cantidad, $id_producto]);
            
            $conexion->commit();
            $mensaje = "¡Compra registrada! El stock se ha actualizado correctamente.";
        } catch (Exception $e) {
            $conexion->rollBack();
            $error = "Error al procesar la compra: " . $e->getMessage();
        }
    } else {
        $error = "Por favor, completa todos los campos correctamente.";
    }
}

$stmtProductos = $conexion->query("SELECT * FROM productos WHERE estado = 1");
$productos = $stmtProductos->fetchAll();

$stmtProveedores = $conexion->query("SELECT * FROM proveedores WHERE estado = 1");
$proveedores = $stmtProveedores->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liquour Licorería - Abastecimiento</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="../../../Assets/CSS/style.css">
    <link rel="stylesheet" href="../../../Assets/CSS/Catalogo_Admin.css">
</head>
<body>

<?php @include '../../Layout/nav_admin.php'; ?> 
   
<main class="main-content admin-layout">

    <?php if ($mensaje): ?>
        <div style="background: #C5A059; color: #1A1A1A; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: bold; text-align: center;">
            <?php echo $mensaje; ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div style="background: #4a0000; color: #fff; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: bold; text-align: center;">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
    
    <section class="products-display">
        <div class="products-grid">
            <?php
            $delay = 0.1;
            foreach ($productos as $p): 
                $imagen = !empty($p['imagen']) ? $p['imagen'] : 'https://images.pexels.com/photos/11271794/pexels-photo-11271794.jpeg';
            ?>
                <div class="product-card animate__animated animate__zoomIn" style="animation-duration: 0.5s; animation-delay: <?php echo $delay; ?>s;">
                    <div class="img-wrapper">
                        <img src="<?php echo htmlspecialchars($imagen); ?>" alt="<?php echo htmlspecialchars($p['nombre']); ?>">
                    </div>
                    <div class="info-wrapper">
                        <h3><?php echo htmlspecialchars($p['nombre']); ?></h3>
                        <p style="color: #A0A0A0; font-size: 0.9rem;">Stock Actual: <?php echo $p['stock']; ?></p>
                        <p>$<?php echo number_format($p['precio_compra'], 2); ?> <span style="font-size: 0.7rem; color: #A0A0A0;">(Costo)</span></p>
                        <div class="admin-btn-group">
                            <button class="btn-edit" onclick="abrirModalCompra(<?php echo $p['id_producto']; ?>, '<?php echo addslashes($p['nombre']); ?>', <?php echo $p['precio_compra']; ?>)">ABASTECER</button>
                        </div>
                    </div>
                </div>
            <?php 
                $delay += 0.05;
            endforeach; 
            ?>
        </div>
    </section>
</main>

<div id="modal-comprar-producto" class="modal-overlay">
    <div class="modal-container admin-form-modal animate__animated animate__fadeInDown" style="animation-duration: 0.4s;">
        <div class="modal-header-perfil">
            <h3>REGISTRAR COMPRA</h3>
            <button class="close-modal" onclick="cerrarModalCompra()">&times;</button>
        </div>
        <form method="POST" action="">
            <div class="modal-body">
                <input type="hidden" id="compra-id-producto" name="id_producto">
                
                <div class="admin-input-row">
                    <div class="admin-input-group">
                        <label>Producto a Abastecer</label>
                        <input type="text" id="compra-nombre" readonly style="background: #1A1A1A; color: #A0A0A0;">
                    </div>
                </div>

                <div class="admin-input-row">
                    <div class="admin-input-group">
                        <label>Proveedor</label>
                        <select name="id_proveedor" required style="width: 100%; background: #000; border: 1px solid #3d3428; color: #fff; padding: 10px;">
                            <option value="">Selecciona un proveedor...</option>
                            <?php foreach($proveedores as $prov): ?>
                                <option value="<?php echo $prov['id_proveedor']; ?>"><?php echo htmlspecialchars($prov['nombre']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="admin-input-row">
                    <div class="admin-input-group">
                        <label>Cantidad a Comprar</label>
                        <input type="number" name="cantidad" id="compra-cantidad" min="1" required oninput="calcularTotal()">
                    </div>
                    <div class="admin-input-group">
                        <label>Costo Unitario ($)</label>
                        <input type="number" name="precio_compra" id="compra-precio" step="0.01" required oninput="calcularTotal()">
                    </div>
                </div>
                
                <div class="admin-input-row">
                    <div class="admin-input-group">
                        <label>Total de la Compra ($)</label>
                        <input type="text" id="compra-total" readonly style="background: #1A1A1A; color: #C5A059; font-weight: bold; font-size: 1.2rem;">
                    </div>
                </div>

            </div>
            <div class="modal-footer" style="margin-top: 20px;">
                <button type="submit" name="btn_comprar" class="btn-confirmar-admin">CONFIRMAR COMPRA 🛒</button>
            </div>
        </form>
    </div>
</div>

<script src="../../../Assets/JS/Catalogo_Admin.js?v=<?php echo time(); ?>"></script>

</body>
</html>