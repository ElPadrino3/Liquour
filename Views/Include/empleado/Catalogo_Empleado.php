<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liquour Licorería - Home</title>
    <link rel="stylesheet" href="../../../Assets/CSS/style.css">
</head>
<body>

<?php include '../../Layout/header_empleados.php'; ?>

<main class="main-content">
    <aside class="cart-section">
        <div class="cart-card">
            <h2 class="cart-title">Carrito de compras</h2>
            <div id="cart-container" class="cart-items-list"></div>
            <div class="cart-summary">
                <div class="total-line">
                    <span>TOTAL</span>
                    <span id="grand-total">$ 0.00</span>
                </div>
                <button class="btn-next" id="btn-next" onclick="openCheckoutModal()">Siguiente</button>
            </div>
        </div>
    </aside>

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

            foreach ($items as $p): 
            ?>
                <div class="product-card">
                    <div class="img-wrapper">
                        <img src="<?php echo $p['img']; ?>" alt="<?php echo $p['name']; ?>">
                    </div>
                    <div class="info-wrapper">
                        <h3><?php echo $p['name']; ?></h3>
                        <p>$<?php echo number_format($p['price'], 2); ?></p>
                        <button class="btn-add" onclick="addToCart('<?php echo $p['name']; ?>', <?php echo $p['price']; ?>, '<?php echo $p['img']; ?>')">
                            AGREGAR AL CARRITO
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</main>

<div id="modal-perfil" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-header-perfil">
            <h3>Mi Perfil</h3>
            <button id="close-modal" class="close-modal">&times;</button>
        </div>
        <div class="modal-body">
            <div class="perfil-avatar">
                <img src="../../Assets/IMG/WhatsApp Image 2026-03-13 at 9.11.48 AM.jpeg" alt="Avatar" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover;">
            </div>
            <div class="perfil-info">
                <label>Nombre:</label>
                <p>Empleado Liquour</p>
                <label>Rol:</label>
                <p>Empleado</p>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-logout" onclick="alert('Cerrando sesión...')">Cerrar Sesión</button>
        </div>
    </div>
</div>

<div id="modal-checkout" class="modal-overlay">
    <div class="modal-checkout-container">
        <div class="checkout-header">
            <h2>TU CARRITO DE COMPRAS</h2>
            <button id="close-checkout" class="close-modal">&times;</button>
        </div>

        <div class="checkout-content">
            <div class="checkout-table-wrapper">
                <table class="checkout-table">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Producto</th>
                            <th>Precio</th>
                            <th>Cantidad</th> 
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody id="checkout-table-body"></tbody>
                </table>
            </div>

            <div class="checkout-options">
                <div class="purchase-options">
                    <h3 class="options-title">OPCIONES DE COMPRA</h3>
                    <label class="custom-checkbox">
                        <input type="checkbox" checked> Compra Inmediata
                    </label>
                    <label class="custom-checkbox">
                        <input type="checkbox"> Compra de Reserva
                    </label>
                </div>

                <div class="action-buttons">
                    <button class="btn-cancel" id="btn-cancel-checkout">CANCELAR</button>
                    <button class="btn-clear" onclick="clearCart()">LIMPIAR</button>
                    <button class="btn-confirm">CONFIRMAR</button>
                </div>

                <div class="reservation-details">
                    <button class="btn-reserva-big" style="margin-bottom: 10px;">PEDIDO DE RESERVA</button>
                    <div class="res-box">
                        <div class="res-header">DETALLES DE RESERVA</div>
                        <div class="res-body">
                            <label>Nombre Completo</label>
                            <input type="text" placeholder="Juan Perez">
                            <label>Número de Teléfono</label>
                            <input type="text" placeholder="+1 555-123-4567">
                            <label>Fecha Límite</label>
                            <input type="date" value="2024-12-31">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../../../Assets/JS/Catalogo_Empleado.js"></script>

</body>
</html>