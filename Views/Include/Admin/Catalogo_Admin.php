<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../../../Config/Liquour_bdd.php';

$db = new BDD();
$conexion = $db->conectar();

$mensaje = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['btn_add'])) {
        $nombre = $_POST['nombre'];
        $codigo_barras = $_POST['codigo_barras'];
        $precio_compra = (float)$_POST['precio_compra'];
        $precio_venta = (float)$_POST['precio_venta'];
        $stock = (int)$_POST['stock'];
        $id_categoria = $_POST['id_categoria'];
        
        $imagen_path = "";
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
            $nombre_archivo = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "_", basename($_FILES['imagen']['name']));
            $ruta_destino = "../../../Assets/IMG/" . $nombre_archivo;
            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_destino)) {
                $imagen_path = "/LIQUOUR/Assets/IMG/" . $nombre_archivo;
            }
        }

        try {
            $stmt = $conexion->prepare("INSERT INTO productos (nombre, codigo_barras, precio_compra, precio_venta, stock, id_categoria, imagen, estado) VALUES (?, ?, ?, ?, ?, ?, ?, 1)");
            $stmt->execute([$nombre, $codigo_barras, $precio_compra, $precio_venta, $stock, $id_categoria, $imagen_path]);
            $mensaje = "Producto agregado correctamente.";
        } catch (Exception $e) {
            $error = "Error al agregar: " . $e->getMessage();
        }
    }

    if (isset($_POST['btn_edit'])) {
        $id_producto = $_POST['id_producto'];
        $nombre = $_POST['nombre'];
        $codigo_barras = $_POST['codigo_barras'];
        $precio_compra = (float)$_POST['precio_compra'];
        $precio_venta = (float)$_POST['precio_venta'];
        $stock = (int)$_POST['stock'];
        $id_categoria = $_POST['id_categoria'];
        
        $imagen_actual = $_POST['imagen_actual'];
        $imagen_path = $imagen_actual;

        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
            $nombre_archivo = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "_", basename($_FILES['imagen']['name']));
            $ruta_destino = "../../../Assets/IMG/" . $nombre_archivo;
            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_destino)) {
                $imagen_path = "/LIQUOUR/Assets/IMG/" . $nombre_archivo;
            }
        }

        try {
            $stmt = $conexion->prepare("UPDATE productos SET nombre=?, codigo_barras=?, precio_compra=?, precio_venta=?, stock=?, id_categoria=?, imagen=? WHERE id_producto=?");
            $stmt->execute([$nombre, $codigo_barras, $precio_compra, $precio_venta, $stock, $id_categoria, $imagen_path, $id_producto]);
            $mensaje = "Producto actualizado correctamente.";
        } catch (Exception $e) {
            $error = "Error al actualizar: " . $e->getMessage();
        }
    }

    if (isset($_POST['btn_delete'])) {
        $id_producto = $_POST['id_producto'];
        try {
            $stmt = $conexion->prepare("UPDATE productos SET estado = 0 WHERE id_producto = ?");
            $stmt->execute([$id_producto]);
            $mensaje = "Producto eliminado correctamente.";
        } catch (Exception $e) {
            $error = "Error al eliminar: " . $e->getMessage();
        }
    }
}

$sqlProd = "SELECT p.*, c.nombre as nombre_categoria FROM productos p LEFT JOIN categorias c ON p.id_categoria = c.id_categoria WHERE p.estado = 1 ORDER BY p.id_producto DESC";
$stmtProductos = $conexion->query($sqlProd);
$productos = $stmtProductos->fetchAll();

$stmtCat = $conexion->query("SELECT * FROM categorias");
$categorias = $stmtCat->fetchAll();

$stmtProv = $conexion->query("SELECT * FROM proveedores WHERE estado = 1");
$proveedores = $stmtProv->fetchAll();

$total_modelos = count($productos);
$stock_global = 0;
$alertas_stock = 0;

foreach ($productos as $p) {
    $stock_global += $p['stock'];
    if ($p['stock'] <= 10) {
        $alertas_stock++;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liquour - Gestión de Inventario</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../../Assets/CSS/style.css">
    <link rel="stylesheet" href="../../../Assets/CSS/Catalogo_Admin.css?v=<?php echo time(); ?>">
</head>
<body>

<?php @include '../../Layout/nav_admin.php'; ?> 
   
<main class="main-content admin-layout">

    <?php if ($mensaje): ?>
        <div class="alert-success animate__animated animate__fadeInDown"><?php echo $mensaje; ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert-error animate__animated animate__fadeInDown"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="top-header-section">
        <h2>Gestión de Inventario</h2>
        <div class="header-controls">
            <select id="filter-category" class="filter-select">
                <option value="Todos">Todas las Categorías</option>
                <?php foreach($categorias as $cat): ?>
                    <option value="<?php echo htmlspecialchars($cat['nombre']); ?>"><?php echo htmlspecialchars($cat['nombre']); ?></option>
                <?php endforeach; ?>
            </select>
            <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" id="search-inventory" placeholder="Buscar producto, SKU...">
            </div>
            <div class="date-display">
                <i class="far fa-calendar-alt"></i>
                <span><?php echo date('d/m/Y'); ?></span>
            </div>
        </div>
    </div>

    <div class="dashboard-stats">
        <div class="stat-card">
            <span>PRODUCTOS TOTALES</span>
            <h2><?php echo $total_modelos; ?></h2>
        </div>
        <div class="stat-card">
            <span>STOCK GLOBAL</span>
            <h2><?php echo $stock_global; ?></h2>
        </div>
        <div class="stat-card alert-card">
            <span>ALERTAS STOCK</span>
            <h2><?php echo $alertas_stock; ?></h2>
        </div>
    </div>

    <div class="action-bar-top">
        <button class="btn-new-product" onclick="abrirModalAdd()"><i class="fas fa-plus"></i> Nuevo Producto</button>
    </div>

    <section class="inventory-section">
        <div class="inventory-table">
            <div class="table-header">
                <div>Imagen</div>
                <div>Producto / SKU</div>
                <div>Categoría</div>
                <div>Costo ($)</div>
                <div>Stock Actual</div>
                <div>Estado</div>
                <div style="text-align: center;">Acciones</div>
            </div>

            <div class="table-body" id="inventory-body">
                <?php foreach ($productos as $p): 
                    $imagen = !empty($p['imagen']) ? $p['imagen'] : 'https://images.pexels.com/photos/11271794/pexels-photo-11271794.jpeg';
                    $codigo = !empty($p['codigo_barras']) ? $p['codigo_barras'] : 'Sin Código';
                    $categoria = !empty($p['nombre_categoria']) ? $p['nombre_categoria'] : 'Sin Categoría';
                    $stock = (int)$p['stock'];
                    $porcentaje = min(($stock / 100) * 100, 100);
                    $colorBarra = $stock <= 10 ? '#e74c3c' : '#c5a87a';
                    $prodJSON = htmlspecialchars(json_encode($p), ENT_QUOTES, 'UTF-8');
                ?>
                <div class="table-row animate__animated animate__fadeIn">
                    <div class="col-img">
                        <img src="<?php echo htmlspecialchars($imagen); ?>" alt="Img">
                    </div>
                    <div class="col-info">
                        <strong><?php echo htmlspecialchars($p['nombre']); ?></strong>
                        <small><?php echo htmlspecialchars($codigo); ?></small>
                    </div>
                    <div class="col-brand">
                        <?php echo htmlspecialchars($categoria); ?>
                    </div>
                    <div class="col-price">
                        $<?php echo number_format($p['precio_compra'], 2); ?>
                    </div>
                    <div class="col-stock">
                        <div class="stock-info">
                            <span>Cant: <?php echo $stock; ?></span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?php echo $porcentaje; ?>%; background-color: <?php echo $colorBarra; ?>;"></div>
                        </div>
                    </div>
                    <div class="col-status">
                        <span class="badge-activo">ACTIVO</span>
                    </div>
                    <div class="col-actions">
                        <button class="action-btn btn-view" onclick="abrirModalView(<?php echo $prodJSON; ?>)"><i class="fas fa-eye"></i></button>
                        <button class="action-btn btn-edit" onclick="abrirModalEdit(<?php echo $prodJSON; ?>)"><i class="fas fa-edit"></i></button>
                        <button class="action-btn btn-delete" onclick="abrirModalDelete(<?php echo $p['id_producto']; ?>, '<?php echo addslashes($p['nombre']); ?>')"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</main>

<div id="modal-add" class="modal-overlay">
    <div class="modal-container admin-form-modal animate__animated animate__zoomIn">
        <div class="modal-header-perfil">
            <h3>NUEVO PRODUCTO</h3>
            <button class="close-modal" onclick="cerrarModal('modal-add')">&times;</button>
        </div>
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="modal-body">
                <div class="admin-input-row">
                    <div class="admin-input-group">
                        <label>Nombre del Producto</label>
                        <input type="text" name="nombre" required>
                    </div>
                    <div class="admin-input-group">
                        <label>Código de Barras (SKU)</label>
                        <input type="text" name="codigo_barras" required>
                    </div>
                </div>
                <div class="admin-input-row">
                    <div class="admin-input-group">
                        <label>Categoría</label>
                        <select name="id_categoria" id="add_categoria" required onchange="detectarProveedor('add')">
                            <option value="">Selecciona...</option>
                            <?php foreach($categorias as $cat): ?>
                                <option value="<?php echo $cat['id_categoria']; ?>" data-nombre="<?php echo htmlspecialchars($cat['nombre']); ?>"><?php echo htmlspecialchars($cat['nombre']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="admin-input-group">
                        <label>Proveedor Sugerido</label>
                        <input type="text" id="add_proveedor_nombre" readonly style="background: #1a1a1a; color: #a39678; cursor: not-allowed;" placeholder="Automático...">
                        <input type="hidden" name="id_proveedor" id="add_proveedor_id">
                    </div>
                </div>
                <div class="admin-input-row">
                    <div class="admin-input-group">
                        <label>Costo ($)</label>
                        <input type="number" name="precio_compra" step="0.01" required>
                    </div>
                    <div class="admin-input-group">
                        <label>Precio Venta ($)</label>
                        <input type="number" name="precio_venta" step="0.01" required>
                    </div>
                    <div class="admin-input-group">
                        <label>Stock Inicial</label>
                        <input type="number" name="stock" required>
                    </div>
                </div>
                <div class="admin-input-row">
                    <div class="admin-input-group">
                        <label>Imagen del Producto</label>
                        <input type="file" name="imagen" accept="image/*" id="add_imagen_input" onchange="previewImage(event, 'add_preview')">
                    </div>
                </div>
                <div class="preview-container">
                    <img id="add_preview" style="display:none; max-height: 100px; border-radius: 8px;">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" name="btn_add" class="btn-confirmar-admin">GUARDAR PRODUCTO</button>
            </div>
        </form>
    </div>
</div>

<div id="modal-edit" class="modal-overlay">
    <div class="modal-container admin-form-modal animate__animated animate__zoomIn">
        <div class="modal-header-perfil">
            <h3>EDITAR PRODUCTO</h3>
            <button class="close-modal" onclick="cerrarModal('modal-edit')">&times;</button>
        </div>
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="modal-body">
                <input type="hidden" name="id_producto" id="edit_id">
                <input type="hidden" name="imagen_actual" id="edit_imagen_actual">
                
                <div class="admin-input-row">
                    <div class="admin-input-group">
                        <label>Nombre del Producto</label>
                        <input type="text" name="nombre" id="edit_nombre" required>
                    </div>
                    <div class="admin-input-group">
                        <label>Código de Barras (SKU)</label>
                        <input type="text" name="codigo_barras" id="edit_codigo" required>
                    </div>
                </div>
                <div class="admin-input-row">
                    <div class="admin-input-group">
                        <label>Categoría</label>
                        <select name="id_categoria" id="edit_categoria" required onchange="detectarProveedor('edit')">
                            <?php foreach($categorias as $cat): ?>
                                <option value="<?php echo $cat['id_categoria']; ?>" data-nombre="<?php echo htmlspecialchars($cat['nombre']); ?>"><?php echo htmlspecialchars($cat['nombre']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="admin-input-group">
                        <label>Proveedor Sugerido</label>
                        <input type="text" id="edit_proveedor_nombre" readonly style="background: #1a1a1a; color: #a39678; cursor: not-allowed;" placeholder="Automático...">
                        <input type="hidden" name="id_proveedor" id="edit_proveedor_id">
                    </div>
                </div>
                <div class="admin-input-row">
                    <div class="admin-input-group">
                        <label>Costo ($)</label>
                        <input type="number" name="precio_compra" id="edit_costo" step="0.01" required>
                    </div>
                    <div class="admin-input-group">
                        <label>Precio Venta ($)</label>
                        <input type="number" name="precio_venta" id="edit_precio" step="0.01" required>
                    </div>
                    <div class="admin-input-group">
                        <label>Stock</label>
                        <input type="number" name="stock" id="edit_stock" required>
                    </div>
                </div>
                <div class="admin-input-row">
                    <div class="admin-input-group">
                        <label>Cambiar Imagen</label>
                        <input type="file" name="imagen" accept="image/*" onchange="previewImage(event, 'edit_preview')">
                    </div>
                </div>
                <div class="preview-container">
                    <img id="edit_preview" style="max-height: 100px; border-radius: 8px;">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" name="btn_edit" class="btn-confirmar-admin">ACTUALIZAR PRODUCTO</button>
            </div>
        </form>
    </div>
</div>

<div id="modal-view" class="modal-overlay">
    <div class="modal-container admin-form-modal animate__animated animate__zoomIn" style="width: 400px; text-align: center;">
        <div class="modal-header-perfil">
            <h3>INFO DEL PRODUCTO</h3>
            <button class="close-modal" onclick="cerrarModal('modal-view')">&times;</button>
        </div>
        <div class="modal-body" style="padding-top: 10px;">
            <img id="view_img" style="max-width: 150px; border-radius: 8px; margin-bottom: 15px;">
            <h2 id="view_nombre" style="color: #f1e4bc; margin-bottom: 5px;"></h2>
            <p style="color: #c5a87a; font-weight: bold; margin-bottom: 20px;" id="view_sku"></p>
            
            <div style="display: flex; justify-content: space-between; border-top: 1px solid #3d3428; padding-top: 15px;">
                <div style="text-align: left;">
                    <small style="color:#a39678;">Categoría</small><br>
                    <strong id="view_cat"></strong>
                </div>
                <div style="text-align: right;">
                    <small style="color:#a39678;">Stock</small><br>
                    <strong id="view_stock"></strong>
                </div>
            </div>
            <div style="display: flex; justify-content: space-between; margin-top: 15px;">
                <div style="text-align: left;">
                    <small style="color:#a39678;">Costo</small><br>
                    <strong id="view_costo"></strong>
                </div>
                <div style="text-align: right;">
                    <small style="color:#a39678;">Precio Venta</small><br>
                    <strong id="view_precio" style="color:#c5a87a; font-size: 1.2rem;"></strong>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modal-delete" class="modal-overlay">
    <div class="modal-container admin-form-modal animate__animated animate__zoomIn" style="width: 400px; text-align: center;">
        <form method="POST" action="">
            <input type="hidden" name="id_producto" id="delete_id">
            <i class="fas fa-exclamation-triangle" style="font-size: 50px; color: #e74c3c; margin-bottom: 15px;"></i>
            <h3 style="color: #f1e4bc;">¿Eliminar Producto?</h3>
            <p style="color: #a39678; margin-bottom: 25px;" id="delete_nombre"></p>
            <div style="display: flex; gap: 15px;">
                <button type="button" class="btn-confirmar-admin" style="background: #3d3428; border-color: #3d3428; color: #fff;" onclick="cerrarModal('modal-delete')">CANCELAR</button>
                <button type="submit" name="btn_delete" class="btn-confirmar-admin" style="background: #e74c3c; border-color: #c0392b; color: #fff;">SÍ, ELIMINAR</button>
            </div>
        </form>
    </div>
</div>

<script>
    const proveedoresDB = <?php echo json_encode($proveedores); ?>;
</script>
<script src="../../../Assets/JS/Catalogo_Admin.js?v=<?php echo time(); ?>"></script>

</body>
</html>