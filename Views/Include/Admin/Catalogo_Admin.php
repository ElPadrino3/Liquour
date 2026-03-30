<?php include '../../Layout/head.php'; ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liquour Licorería - Admin Catálogo</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="../../../Assets/CSS/style.css">
</head>
<body>



<link rel="stylesheet" href="../../../Assets/CSS/nav.css">
<link rel="stylesheet" href="../../../Assets/CSS/Catalogo_Admin.css">

<?php include '../../Layout/nav_admin.php'; ?> 
   
<main class="main-content admin-layout">


    <br>
    
    <section class="products-display">
        <div class="products-grid">
            <?php
            $items = [
                ["id" => 1, "name" => "WHISKY ESCOCÉS 12 AÑOS", "price" => 139.00, "img" => "https://images.pexels.com/photos/11271794/pexels-photo-11271794.jpeg"],
                ["id" => 2, "name" => "VINO TINTO RESERVA", "price" => 159.00, "img" => "https://images.pexels.com/photos/2912108/pexels-photo-2912108.jpeg"],
                ["id" => 3, "name" => "BUDWEISER BLACK LAGER", "price" => 18.00, "img" => "https://i.pinimg.com/1200x/d2/72/e1/d272e13fea9d56f79c44d63a085bdf2d.jpg"],
                ["id" => 4, "name" => "BLUE LABEL - JOHNNY WALKER", "price" => 220.99, "img" => "https://i.pinimg.com/736x/f0/67/97/f0679774f573ddd6dc3c82fd10624a6f.jpg"],
                ["id" => 5, "name" => "TEQUILA REPOSADO PREMIUM", "price" => 85.00, "img" => "https://i.pinimg.com/736x/4e/df/d7/4edfd76539603505ef771b0ec4a1f343.jpg"],
                ["id" => 6, "name" => "VODKA GREY GOOSE", "price" => 45.50, "img" => "https://i.pinimg.com/736x/4f/c9/c4/4fc9c44c6d835350c9aa4e8009f61a83.jpg"],
                ["id" => 7, "name" => "RON AÑEJO 7 AÑOS", "price" => 32.00, "img" => "https://i.pinimg.com/736x/ea/ff/a2/eaffa23a48caa3aab3487219f25de1fe.jpg"],
                ["id" => 8, "name" => "GINEBRA TANQUERAY", "price" => 29.99, "img" => "https://i.pinimg.com/1200x/ad/90/61/ad9061891a96361c0aac6fab61ab63f0.jpg"],
                ["id" => 9, "name" => "CHAMPAGNE MOËT & CHANDON", "price" => 110.00, "img" => "https://i.pinimg.com/736x/3c/e4/88/3ce488c312dc2d9b152d8f23b9d243ea.jpg"],
                ["id" => 10, "name" => "MEZCAL ARTESANAL", "price" => 65.00, "img" => "https://i.pinimg.com/736x/2e/91/4f/2e914fd9c44a0fd9f425a8b7639730a0.jpg"],
                ["id" => 11, "name" => "CERVEZA ARTESANAL IPA", "price" => 4.50, "img" => "https://i.pinimg.com/736x/39/d7/57/39d757e939c56ff67c1270603d7dadb1.jpg"],
                ["id" => 12, "name" => "LICOR DE CAFÉ", "price" => 22.00, "img" => "https://i.pinimg.com/736x/29/28/2f/29282fcd6cd3b4e15433484719854e14.jpg"]
            ];

            $delay = 0.1;
            foreach ($items as $p): 
            ?>
                <div class="product-card animate__animated animate__zoomIn" style="animation-duration: 0.5s; animation-delay: <?php echo $delay; ?>s;">
                    <div class="img-wrapper">
                        <img src="<?php echo $p['img']; ?>" alt="<?php echo $p['name']; ?>">
                    </div>
                    <div class="info-wrapper">
                        <h3><?php echo $p['name']; ?></h3>
                        <p>$<?php echo number_format($p['price'], 2); ?></p>
                        <div class="admin-btn-group">
                            <button class="btn-edit" onclick="editarProducto(<?php echo $p['id']; ?>)">EDITAR</button>
                            <button class="btn-delete" onclick="eliminarProducto(<?php echo $p['id']; ?>)">ELIMINAR</button>
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

<div id="modal-editar-producto" class="modal-overlay">
    <div class="modal-container admin-form-modal animate__animated animate__fadeInDown" style="animation-duration: 0.4s;">
        <div class="modal-header-perfil">
            <h3>EDITAR PRODUCTO</h3>
            <button id="close-modal-editar" class="close-modal" onclick="cerrarModalEditar()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="admin-input-row">
                <div class="admin-input-group">
                    <label>Nombre del Producto</label>
                    <input type="text" id="edit-nombre">
                </div>
            </div>
            <div class="admin-input-row">
                <div class="admin-input-group">
                    <label>Precio ($)</label>
                    <input type="number" id="edit-precio" step="0.01">
                </div>
                <div class="admin-input-group">
                    <label>URL de Imagen</label>
                    <input type="text" id="edit-img">
                </div>
            </div>
        </div>
        <div class="modal-footer" style="margin-top: 20px;">
            <button class="btn-confirmar-admin">GUARDAR CAMBIOS</button>
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
                <img src="../../../Assets/IMG/icon-7797704_640.png" alt="Avatar" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover;">
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

<script src="../../../Assets/JS/Catalogo_Admin.js"></script>
<script>
    function cerrarModalEditar() {
        document.getElementById('modal-editar-producto').style.display = 'none';
    }
    
    function editarProducto(id) {
        document.getElementById('modal-editar-producto').style.display = 'flex';
    }
</script>

</body>
</html>