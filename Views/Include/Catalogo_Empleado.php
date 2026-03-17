<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liquour Licorería - Home</title>
    <link rel="stylesheet" href="../../Assets/CSS/style.css">
</head>
<body>

<header>
    <div class="logo-container">
        <img src="../../Assets/IMG/WhatsApp Image 2026-03-13 at 9.11.48 AM.jpeg" alt="Liquour Logo" class="logo-img">
    </div>
    <nav class="nav-menu">
        <button class="nav-item active">HOME</button>
        <button class="nav-item">RESERVA</button>
        <button class="nav-item">MI PERFIL</button>
    </nav>
</header>

<main class="main-content">
    <aside class="cart-section">
        <div class="cart-card">
            <h2 class="cart-title">Carrito de compras</h2>
            <div id="cart-container" class="cart-items-list">
                </div>
            <div class="cart-summary">
                <div class="total-line">
                    <span>TOTAL</span>
                    <span id="grand-total">$ 0.00</span>
                </div>
                <button class="btn-next" id="btn-next">Siguiente</button>
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
                ["id" => 12, "name" => "LICOR DE CAFÉ", "price" => 22.00, "img" => "https://i.pinimg.com/736x/29/28/2f/29282fcd6cd3b4e15433484719854e14.jpg"],
                ["id" => 13, "name" => "Korta Katarina Wine", "price" => 45.00, "img" => "https://i.pinimg.com/736x/25/1b/09/251b09da0e29093ebb8fed7243810e4f.jpg"],
                ["id" => 14, "name" => "Glenfiddich 18 Year Old", "price" => 120.00, "img" => "https://i.pinimg.com/736x/93/30/f7/9330f701490ff2304b71d051cefa214c.jpg"],
                ["id" => 15, "name" => "Veuve Clicquot Brut", "price" => 95.00, "img" => "https://i.pinimg.com/736x/c3/da/fb/c3dafb486bc0b0814176b2a5fc5d1555.jpg"]
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

<script src="../../Assets/JS/Catalogo_Empleado.js"></script>
</body>
</html>