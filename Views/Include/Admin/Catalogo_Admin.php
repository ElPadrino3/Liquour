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
        $stock_maximo = (int)$_POST['stock_maximo'];
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
            $stmt = $conexion->prepare("INSERT INTO productos (nombre, codigo_barras, precio_compra, precio_venta, stock, stock_maximo, id_categoria, imagen, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)");
            $stmt->execute([$nombre, $codigo_barras, $precio_compra, $precio_venta, $stock, $stock_maximo, $id_categoria, $imagen_path]);
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
        $nuevo_stock_total = (int)$_POST['stock'];
        $stock_maximo = (int)$_POST['stock_maximo'];
        $id_categoria = $_POST['id_categoria'];
        $id_proveedor = !empty($_POST['id_proveedor']) ? (int)$_POST['id_proveedor'] : 1;

        $stmtOld = $conexion->prepare("SELECT stock FROM productos WHERE id_producto = ?");
        $stmtOld->execute([$id_producto]);
        $stock_anterior = $stmtOld->fetchColumn();
        $cantidad_agregada = $nuevo_stock_total - $stock_anterior;

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
            $conexion->beginTransaction();

            $stmt = $conexion->prepare("UPDATE productos SET nombre=?, codigo_barras=?, precio_compra=?, precio_venta=?, stock=?, stock_maximo=?, id_categoria=?, imagen=? WHERE id_producto=?");
            $stmt->execute([$nombre, $codigo_barras, $precio_compra, $precio_venta, $nuevo_stock_total, $stock_maximo, $id_categoria, $imagen_path, $id_producto]);

            if ($cantidad_agregada > 0) {
                $subtotal = $cantidad_agregada * $precio_compra;
                $stmtC = $conexion->prepare("INSERT INTO compras (fecha, total, id_usuario) VALUES (NOW(), ?, ?)");
                $stmtC->execute([$subtotal, $_SESSION['id_usuario'] ?? 1]);
                $id_compra_auto = $conexion->lastInsertId();

                $stmtD = $conexion->prepare("INSERT INTO detalle_compras (id_compra, id_producto, cantidad, precio_compra, subtotal, id_proveedor) VALUES (?, ?, ?, ?, ?, ?)");
                $stmtD->execute([$id_compra_auto, $id_producto, $cantidad_agregada, $precio_compra, $subtotal, $id_proveedor]);
            }

            $conexion->commit();
            $mensaje = "Stock actualizado y compra registrada.";
        } catch (Exception $e) {
            $conexion->rollBack();
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
    if ($p['stock'] <= 10) { $alertas_stock++; }
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
    
    <style>
        :root {
            --tema-color: #C5A059;
            --tema-color-rgb: 197, 160, 89;
            --bg-carbon: #1A1A1A;
            --text-cream: #F5F5DC;
            --border-color: #4A4A4A;
        }
    </style>
    
    <script>
    (function sincronizarCatalogo() {
        const coloresGuardados = localStorage.getItem('liquour_colors');
        if (coloresGuardados) {
            try {
                const colores = JSON.parse(coloresGuardados);
                const dorado = colores['--color-dorado'] || '#C5A059';
                const fondo = colores['--bg-carbon'] || '#1A1A1A';
                const texto = colores['--text-blanco-crema'] || '#F5F5DC';
                const borde = colores['--border-fuerte'] || '#4A4A4A';
                
                document.documentElement.style.setProperty('--tema-color', dorado);
                document.documentElement.style.setProperty('--bg-carbon', fondo);
                document.documentElement.style.setProperty('--text-cream', texto);
                document.documentElement.style.setProperty('--border-color', borde);
                
                const r = parseInt(dorado.slice(1,3), 16);
                const g = parseInt(dorado.slice(3,5), 16);
                const b = parseInt(dorado.slice(5,7), 16);
                document.documentElement.style.setProperty('--tema-color-rgb', `${r}, ${g}, ${b}`);
                
                console.log('🎨 Catálogo sincronizado con tema:', dorado);
            } catch(e) {}
        }
        
        const logoGuardado = localStorage.getItem('liquour_theme_logo');
        if (logoGuardado) {
            const logos = document.querySelectorAll('.logo-img, .theme-logo');
            logos.forEach(img => {
                if (img && img.tagName === 'IMG') img.src = logoGuardado;
            });
        }
    })();
    
    window.addEventListener('storage', function(e) {
        if (e.key === 'liquour_colors' || e.key === 'liquour_theme_logo') {
            location.reload();
        }
    });
    </script>
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
                    $stock = (int)$p['stock'];
                    $stock_maximo = (int)$p['stock_maximo'];
                    $porcentaje = $stock_maximo > 0 ? min(($stock / $stock_maximo) * 100, 100) : 0;
                    $colorBarra = $stock <= 10 ? '#e74c3c' : 'var(--tema-color)';
                    $prodJSON = htmlspecialchars(json_encode($p), ENT_QUOTES, 'UTF-8');
                ?>
                <div class="table-row animate__animated animate__fadeIn">
                    <div class="col-img"><img src="<?php echo htmlspecialchars($imagen); ?>"></div>
                    <div class="col-info">
                        <strong><?php echo htmlspecialchars($p['nombre']); ?></strong>
                        <small><?php echo htmlspecialchars($p['codigo_barras']); ?></small>
                    </div>
                    <div class="col-brand"><?php echo htmlspecialchars($p['nombre_categoria']); ?></div>
                    <div class="col-price">$<?php echo number_format($p['precio_compra'], 2); ?></div>
                    <div class="col-stock">
                        <div class="stock-info"><span>Cant: <?php echo $stock; ?> / <?php echo $stock_maximo; ?></span></div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?php echo $porcentaje; ?>%; background-color: <?php echo $colorBarra; ?>;"></div>
                        </div>
                    </div>
                    <div class="col-status"><span class="badge-activo">ACTIVO</span></div>
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
        <div class="modal-header-perfil"><h3>NUEVO PRODUCTO</h3><button class="close-modal" onclick="cerrarModal('modal-add')">&times;</button></div>
        <form method="POST" enctype="multipart/form-data">
            <div class="modal-body">
                <div class="admin-input-row">
                    <div class="admin-input-group"><label>Nombre</label><input type="text" name="nombre" required></div>
                    <div class="admin-input-group"><label>SKU</label><input type="text" name="codigo_barras" required></div>
                </div>
                <div class="admin-input-row">
                    <div class="admin-input-group">
                        <label>Categoría</label>
                        <select name="id_categoria" id="add_categoria" required onchange="detectarProveedor('add')">
                            <option value="">Selecciona...</option>
                            <?php foreach($categorias as $cat): ?>
                                <option value="<?php echo $cat['id_categoria']; ?>"><?php echo htmlspecialchars($cat['nombre']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="admin-input-group">
                        <label>Proveedor</label>
                        <select name="id_proveedor" id="add_proveedor_id" required>
                            <option value="">Selecciona...</option>
                            <?php foreach($proveedores as $prov): ?>
                                <option value="<?php echo $prov['id_proveedor']; ?>"><?php echo htmlspecialchars($prov['nombre']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="admin-input-row">
                    <div class="admin-input-group"><label>Costo</label><input type="number" name="precio_compra" step="0.01" required></div>
                    <div class="admin-input-group"><label>Venta</label><input type="number" name="precio_venta" step="0.01" required></div>
                    <div class="admin-input-group"><label>Stock Inicial</label><input type="number" name="stock" required></div>
                    <div class="admin-input-group"><label>Stock Máximo</label><input type="number" name="stock_maximo" value="100" required></div>
                </div>
                <div class="admin-input-row">
                    <div class="admin-input-group"><label>Imagen</label><input type="file" name="imagen" accept="image/*" onchange="previewImage(event, 'add_preview')"></div>
                </div>
                <div class="preview-container"><img id="add_preview" style="display:none; max-height: 80px;"></div>
            </div>
            <div class="modal-footer"><button type="submit" name="btn_add" class="btn-confirmar-admin">GUARDAR</button></div>
        </form>
    </div>
</div>

<div id="modal-edit" class="modal-overlay">
    <div class="modal-container admin-form-modal animate__animated animate__zoomIn">
        <div class="modal-header-perfil"><h3>EDITAR PRODUCTO</h3><button class="close-modal" onclick="cerrarModal('modal-edit')">&times;</button></div>
        <form method="POST" enctype="multipart/form-data">
            <div class="modal-body">
                <input type="hidden" name="id_producto" id="edit_id">
                <input type="hidden" name="imagen_actual" id="edit_imagen_actual">
                <div class="admin-input-row">
                    <div class="admin-input-group"><label>Nombre</label><input type="text" name="nombre" id="edit_nombre" required></div>
                    <div class="admin-input-group"><label>SKU</label><input type="text" name="codigo_barras" id="edit_codigo" required></div>
                </div>
                <div class="admin-input-row">
                    <div class="admin-input-group">
                        <label>Categoría</label>
                        <select name="id_categoria" id="edit_categoria" required onchange="detectarProveedor('edit')">
                            <?php foreach($categorias as $cat): ?>
                                <option value="<?php echo $cat['id_categoria']; ?>"><?php echo htmlspecialchars($cat['nombre']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="admin-input-group">
                        <label>Proveedor</label>
                        <select name="id_proveedor" id="edit_proveedor_id" required>
                            <option value="">Selecciona...</option>
                            <?php foreach($proveedores as $prov): ?>
                                <option value="<?php echo $prov['id_proveedor']; ?>"><?php echo htmlspecialchars($prov['nombre']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="admin-input-row">
                    <div class="admin-input-group"><label>Costo</label><input type="number" name="precio_compra" id="edit_costo" step="0.01" required></div>
                    <div class="admin-input-group"><label>Venta</label><input type="number" name="precio_venta" id="edit_precio" step="0.01" required></div>
                    <div class="admin-input-group">
                        <label>Rellenar Stock (Máx. <span id="edit_stock_maximo_label">100</span>)</label>
                        <div class="slider-container">
                            <input type="range" id="edit_stock_slider" min="0" max="100" value="0" step="1" style="width:100%; accent-color:var(--tema-color);">
                            <span id="stock_val_display" style="color:var(--tema-color); font-weight:bold; font-size:0.9rem;">0 / 0</span>
                            <small id="costo_recarga_display" style="color:#a39678; display:block; height:15px;"></small>
                            <input type="hidden" name="stock" id="edit_stock">
                        </div>
                    </div>
                    <div class="admin-input-group"><label>Stock Máximo</label><input type="number" name="stock_maximo" id="edit_stock_maximo" required></div>
                </div>
                <div class="admin-input-row">
                    <div class="admin-input-group"><label>Cambiar Imagen</label><input type="file" name="imagen" accept="image/*" onchange="previewImage(event, 'edit_preview')"></div>
                </div>
                <div class="preview-container"><img id="edit_preview" style="max-height: 80px;"></div>
            </div>
            <div class="modal-footer"><button type="submit" name="btn_edit" class="btn-confirmar-admin">ACTUALIZAR</button></div>
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
            <h2 id="view_nombre" style="color: var(--text-cream); margin-bottom: 5px;"></h2>
            <p style="color: var(--tema-color); font-weight: bold; margin-bottom: 20px;" id="view_sku"></p>
            
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
                    <strong id="view_precio" style="color:var(--tema-color); font-size: 1.2rem;"></strong>
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
            <h3 style="color: var(--text-cream);">¿Eliminar Producto?</h3>
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
    let stockActualBase = 0;
    let costoUnitarioBase = 0;
    let stockMaximoBase = 100;

    function sincronizarStock(valorAdicional) {
        const slider = document.getElementById('edit_stock_slider');
        const hiddenInput = document.getElementById('edit_stock');
        const display = document.getElementById('stock_val_display');
        const costoDisplay = document.getElementById('costo_recarga_display');

        if(slider && hiddenInput && display) {
            valorAdicional = parseInt(valorAdicional);
            let nuevoTotal = stockActualBase + valorAdicional;
            let maximo = stockMaximoBase;
            
            if(nuevoTotal > maximo) {
                nuevoTotal = maximo;
                valorAdicional = maximo - stockActualBase;
                slider.value = valorAdicional;
            }

            display.innerText = `${nuevoTotal} / ${maximo} (Agregando: ${valorAdicional})`;
            hiddenInput.value = nuevoTotal;

            if(costoDisplay) {
                let costoExtra = valorAdicional * costoUnitarioBase;
                costoDisplay.innerText = valorAdicional > 0 ? `Costo recarga: $${costoExtra.toFixed(2)}` : '';
            }
        }
    }

    function actualizarSliderMaximo() {
        const maxInput = document.getElementById('edit_stock_maximo');
        if(maxInput) {
            stockMaximoBase = parseInt(maxInput.value) || 100;
            const slider = document.getElementById('edit_stock_slider');
            if(slider) {
                slider.max = stockMaximoBase - stockActualBase;
                if(slider.max < 0) slider.max = 0;
                slider.value = 0;
                sincronizarStock(0);
            }
            const label = document.getElementById('edit_stock_maximo_label');
            if(label) label.innerText = stockMaximoBase;
        }
    }

    document.addEventListener('input', (e) => {
        if (e.target && e.target.id === 'edit_stock_slider') {
            sincronizarStock(e.target.value);
        }
        if (e.target && e.target.id === 'edit_stock_maximo') {
            actualizarSliderMaximo();
        }
    });

    function cerrarModal(idModal) {
        const modal = document.getElementById(idModal);
        if(modal) modal.style.display = 'none';
    }

    function abrirModalAdd() {
        document.getElementById('modal-add').style.display = 'flex';
    }

    function abrirModalView(producto) {
        document.getElementById('view_img').src = producto.imagen ? producto.imagen : 'https://images.pexels.com/photos/11271794/pexels-photo-11271794.jpeg';
        document.getElementById('view_nombre').innerText = producto.nombre;
        document.getElementById('view_sku').innerText = 'SKU: ' + (producto.codigo_barras || 'N/A');
        document.getElementById('view_cat').innerText = producto.nombre_categoria || 'N/A';
        document.getElementById('view_stock').innerText = producto.stock + ' / ' + (producto.stock_maximo || 100);
        document.getElementById('view_costo').innerText = '$' + parseFloat(producto.precio_compra).toFixed(2);
        document.getElementById('view_precio').innerText = '$' + parseFloat(producto.precio_venta).toFixed(2);
        document.getElementById('modal-view').style.display = 'flex';
    }

    function abrirModalEdit(producto) {
        document.getElementById('edit_id').value = producto.id_producto;
        document.getElementById('edit_nombre').value = producto.nombre;
        document.getElementById('edit_codigo').value = producto.codigo_barras;
        document.getElementById('edit_categoria').value = producto.id_categoria;
        document.getElementById('edit_costo').value = producto.precio_compra;
        document.getElementById('edit_precio').value = producto.precio_venta;
        document.getElementById('edit_stock_maximo').value = producto.stock_maximo || 100;
        
        stockActualBase = parseInt(producto.stock);
        costoUnitarioBase = parseFloat(producto.precio_compra);
        stockMaximoBase = parseInt(producto.stock_maximo) || 100;
        
        const slider = document.getElementById('edit_stock_slider');
        if(slider) {
            slider.max = stockMaximoBase - stockActualBase;
            if(slider.max < 0) slider.max = 0;
            slider.value = 0;
            sincronizarStock(0);
        }
        
        const label = document.getElementById('edit_stock_maximo_label');
        if(label) label.innerText = stockMaximoBase;
        
        document.getElementById('edit_imagen_actual').value = producto.imagen;
        const imgPreview = document.getElementById('edit_preview');
        imgPreview.src = producto.imagen ? producto.imagen : '';
        imgPreview.style.display = producto.imagen ? 'inline-block' : 'none';
        
        const provSelect = document.getElementById('edit_proveedor_id');
        if(provSelect) {
            provSelect.value = producto.id_proveedor || "";
        }
        
        detectarProveedor('edit');
        document.getElementById('modal-edit').style.display = 'flex';
    }

    function abrirModalDelete(id, nombre) {
        document.getElementById('delete_id').value = id;
        document.getElementById('delete_nombre').innerText = nombre;
        document.getElementById('modal-delete').style.display = 'flex';
    }

    function previewImage(event, previewId) {
        const input = event.target;
        const preview = document.getElementById(previewId);
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'inline-block';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function detectarProveedor(tipo) {
        const catSelect = document.getElementById(tipo + '_categoria');
        const provSelect = document.getElementById(tipo + '_proveedor_id');
        
        if(catSelect && provSelect) {
            const nombreCat = catSelect.options[catSelect.selectedIndex].text.toLowerCase();
            
            for (let i = 0; i < provSelect.options.length; i++) {
                if (provSelect.options[i].text.toLowerCase().includes(nombreCat)) {
                    provSelect.selectedIndex = i;
                    return;
                }
            }
            
            if(provSelect.selectedIndex === 0 && provSelect.options.length > 1) {
                provSelect.selectedIndex = 1;
            }
        }
    }
</script>
<script src="../../../Assets/JS/Catalogo_Admin.js"></script>
</body>
</html>