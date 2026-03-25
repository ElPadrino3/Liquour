<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liquour Licorería - Inventario Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="../../../Assets/CSS/style.css">
</head>
<body>

   <?php include '../../Layout/header_admin.php'; ?>

<main class="main-content admin-layout">
    <section class="admin-top-bar">
        <h1 class="animate__animated animate__fadeInDown">Panel de Inventario</h1>
        <button type="button" class="btn-add-new" onclick="abrirModalCrear()">+ AGREGAR PRODUCTO</button>
    </section>

    <section class="products-display">
        <div class="products-grid">
            <?php
            $items = [
                ["id" => 1, "name" => "WHISKY ESCOCÉS 12 AÑOS", "price_venta" => 139.00, "price_compra" => 95.00, "barcode" => "750100000001", "stock" => 25, "img" => "https://images.pexels.com/photos/11271794/pexels-photo-11271794.jpeg"],
                ["id" => 2, "name" => "VINO TINTO RESERVA", "price_venta" => 159.00, "price_compra" => 110.00, "barcode" => "750100000002", "stock" => 40, "img" => "https://images.pexels.com/photos/2912108/pexels-photo-2912108.jpeg"],
                ["id" => 3, "name" => "BUDWEISER BLACK LAGER", "price_venta" => 18.00, "price_compra" => 10.00, "barcode" => "750100000003", "stock" => 120, "img" => "https://i.pinimg.com/1200x/d2/72/e1/d272e13fea9d56f79c44d63a085bdf2d.jpg"],
                ["id" => 4, "name" => "BLUE LABEL - JOHNNY WALKER", "price_venta" => 220.99, "price_compra" => 160.00, "barcode" => "750100000004", "stock" => 10, "img" => "https://i.pinimg.com/736x/f0/67/97/f0679774f573ddd6dc3c82fd10624a6f.jpg"],
                ["id" => 5, "name" => "TEQUILA REPOSADO PREMIUM", "price_venta" => 85.00, "price_compra" => 55.00, "barcode" => "750100000005", "stock" => 60, "img" => "https://i.pinimg.com/736x/4e/df/d7/4edfd76539603505ef771b0ec4a1f343.jpg"],
                ["id" => 6, "name" => "VODKA GREY GOOSE", "price_venta" => 45.50, "price_compra" => 28.00, "barcode" => "750100000006", "stock" => 80, "img" => "https://i.pinimg.com/736x/4f/c9/c4/4fc9c44c6d835350c9aa4e8009f61a83.jpg"],
                ["id" => 7, "name" => "RON AÑEJO 7 AÑOS", "price_venta" => 32.00, "price_compra" => 18.00, "barcode" => "750100000007", "stock" => 90, "img" => "https://i.pinimg.com/736x/ea/ff/a2/eaffa23a48caa3aab3487219f25de1fe.jpg"],
                ["id" => 8, "name" => "GINEBRA TANQUERAY", "price_venta" => 29.99, "price_compra" => 15.00, "barcode" => "750100000008", "stock" => 45, "img" => "https://i.pinimg.com/1200x/ad/90/61/ad9061891a96361c0aac6fab61ab63f0.jpg"],
                ["id" => 9, "name" => "CHAMPAGNE MOËT & CHANDON", "price_venta" => 110.00, "price_compra" => 75.00, "barcode" => "750100000009", "stock" => 15, "img" => "https://i.pinimg.com/736x/3c/e4/88/3ce488c312dc2d9b152d8f23b9d243ea.jpg"],
                ["id" => 10, "name" => "MEZCAL ARTESANAL", "price_venta" => 65.00, "price_compra" => 40.00, "barcode" => "750100000010", "stock" => 30, "img" => "https://i.pinimg.com/736x/2e/91/4f/2e914fd9c44a0fd9f425a8b7639730a0.jpg"],
                ["id" => 11, "name" => "CERVEZA ARTESANAL IPA", "price_venta" => 4.50, "price_compra" => 2.00, "barcode" => "750100000011", "stock" => 300, "img" => "https://i.pinimg.com/736x/39/d7/57/39d757e939c56ff67c1270603d7dadb1.jpg"],
                ["id" => 12, "name" => "LICOR DE CAFÉ", "price_venta" => 22.00, "price_compra" => 12.00, "barcode" => "750100000012", "stock" => 50, "img" => "https://i.pinimg.com/736x/29/28/2f/29282fcd6cd3b4e15433484719854e14.jpg"]
            ];

            foreach ($items as $p): 
            ?>
                <div class="product-card animate__animated animate__fadeInUp" style="animation-duration: 0.8s;">
                    <div class="img-wrapper">
                        <img src="<?php echo $p['img']; ?>" alt="<?php echo $p['name']; ?>">
                    </div>
                    <div class="info-wrapper">
                        <h3><?php echo $p['name']; ?></h3>
                        <p>$<?php echo number_format($p['price_venta'], 2); ?></p>
                        <div class="admin-btn-group">
                            <button type="button" class="btn-edit" 
                                data-id="<?php echo $p['id']; ?>"
                                data-name="<?php echo htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8'); ?>"
                                data-compra="<?php echo $p['price_compra']; ?>"
                                data-venta="<?php echo $p['price_venta']; ?>"
                                data-barcode="<?php echo $p['barcode']; ?>"
                                data-stock="<?php echo $p['stock']; ?>"
                                data-img="<?php echo $p['img']; ?>"
                                onclick="abrirModalEditar(this)">EDITAR</button>
                            <button type="button" class="btn-delete" onclick="eliminarProducto(<?php echo $p['id']; ?>)">ELIMINAR</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</main>

<div id="modal-admin-prod" class="modal-overlay">
    <div class="modal-container admin-form-modal animate__animated animate__zoomIn" style="animation-duration: 0.4s;">
        <div class="modal-header-perfil">
            <h3 id="admin-modal-title">Detalles del Producto</h3>
            <button id="close-modal-admin" class="close-modal">&times;</button>
        </div>
        <div class="modal-body">
            <form id="form-admin-producto">
                <input type="hidden" id="admin-p-id">
                
                <div class="admin-input-row">
                    <div class="admin-input-group">
                        <label>Nombre del Producto</label>
                        <input type="text" id="admin-p-nombre" required>
                    </div>
                </div>
                <div class="admin-input-row">
                    <div class="admin-input-group">
                        <label>Precio Compra</label>
                        <input type="number" id="admin-p-compra" step="0.01" required>
                    </div>
                    <div class="admin-input-group">
                        <label>Precio Venta</label>
                        <input type="number" id="admin-p-venta" step="0.01" required>
                    </div>
                </div>
                <div class="admin-input-row">
                    <div class="admin-input-group">
                        <label>Código de Barras</label>
                        <input type="text" id="admin-p-barcode" placeholder="750123456789">
                    </div>
                    <div class="admin-input-group">
                        <label>Stock Inicial</label>
                        <input type="number" id="admin-p-stock" value="0">
                    </div>
                </div>
                <div class="admin-input-row">
                    <div class="admin-input-group">
                        <label>URL Imagen</label>
                        <input type="text" id="admin-p-img">
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="submit" form="form-admin-producto" class="btn-confirmar-admin">GUARDAR DATOS</button>
        </div>
    </div>
</div>

<div id="modal-perfil" class="modal-overlay">
    <div class="modal-container animate__animated animate__fadeInRight" style="animation-duration: 0.5s;">
        <div class="modal-header-perfil">
            <h3>Mi Perfil</h3>
            <button id="close-modal" class="close-modal">&times;</button>
        </div>
        <div class="modal-body">
            <div class="perfil-avatar">
                <img src="../../../Assets/IMG/WhatsApp Image 2026-03-13 at 9.11.48 AM.jpeg" alt="Avatar" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover;">
            </div>
            <div class="perfil-info">
                <label>Nombre:</label>
                <p>Admin Liquour</p>
                <label>Rol:</label>
                <p>Administrador</p>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-logout" onclick="alert('Cerrando sesión...')">Cerrar Sesión</button>
        </div>
    </div>
</div>

<script src="../../../Assets/JS/Catalogo_Admin.js?v=2.0"></script>

</body>
</html>