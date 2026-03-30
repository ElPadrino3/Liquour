<?php include '../../Layout/head.php'; ?>

<link rel="stylesheet" href="../../../Assets/CSS/nav.css">
<link rel="stylesheet" href="../../../Assets/CSS/Catalogo_Empleado.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

<?php include '../../Layout/header_empleados.php'; ?>

<main class="catalog-page">
    <aside class="catalog-sidebar animate__animated animate__fadeInLeft" style="animation-duration: 0.5s;">
        <div class="sidebar-card">
            <h2 class="sidebar-title">FILTROS</h2>

            <div class="filter-group">
                <label for="search-input" class="filter-label">Búsqueda</label>
                <input type="text" id="search-input" class="search-box" placeholder="Buscar producto, código...">
            </div>

            <div class="filter-group">
                <label class="filter-label">Categorías</label>
                <div class="category-options">
                    <label class="category-option">
                        <input type="radio" name="categoria" value="Todos" checked>
                        <span>Todos</span>
                    </label>
                    <label class="category-option">
                        <input type="radio" name="categoria" value="Whisky">
                        <span>Whisky</span>
                    </label>
                    <label class="category-option">
                        <input type="radio" name="categoria" value="Vino">
                        <span>Vinos</span>
                    </label>
                    <label class="category-option">
                        <input type="radio" name="categoria" value="Cerveza">
                        <span>Cervezas</span>
                    </label>
                    <label class="category-option">
                        <input type="radio" name="categoria" value="Tequila">
                        <span>Tequila</span>
                    </label>
                    <label class="category-option">
                        <input type="radio" name="categoria" value="Vodka">
                        <span>Vodka</span>
                    </label>
                    <label class="category-option">
                        <input type="radio" name="categoria" value="Ron">
                        <span>Ron</span>
                    </label>
                    <label class="category-option">
                        <input type="radio" name="categoria" value="Ginebra">
                        <span>Ginebra</span>
                    </label>
                    <label class="category-option">
                        <input type="radio" name="categoria" value="Champagne">
                        <span>Champagne</span>
                    </label>
                    <label class="category-option">
                        <input type="radio" name="categoria" value="Mezcal">
                        <span>Mezcal</span>
                    </label>
                    <label class="category-option">
                        <input type="radio" name="categoria" value="Licor">
                        <span>Licores</span>
                    </label>
                </div>
            </div>
        </div>
    </aside>

    <section class="catalog-results">
        <div class="results-topbar">
            <div id="resultado-filtro" class="results-count">Mostrando todos los productos</div>
        </div>

        <div class="products-grid" id="contenedor-productos">
            <?php
            $items = [
                ["id" => 1, "barcode" => "750100001", "name" => "WHISKY ESCOCÉS 12 AÑOS", "sale_price" => 139.00, "purchase_price" => 95.00, "stock" => 24, "cat" => "Whisky", "img" => "https://images.pexels.com/photos/11271794/pexels-photo-11271794.jpeg"],
                ["id" => 2, "barcode" => "750100002", "name" => "VINO TINTO RESERVA", "sale_price" => 159.00, "purchase_price" => 110.50, "stock" => 15, "cat" => "Vino", "img" => "https://images.pexels.com/photos/2912108/pexels-photo-2912108.jpeg"],
                ["id" => 3, "barcode" => "750100003", "name" => "BUDWEISER BLACK LAGER", "sale_price" => 18.00, "purchase_price" => 12.00, "stock" => 120, "cat" => "Cerveza", "img" => "https://i.pinimg.com/1200x/d2/72/e1/d272e13fea9d56f79c44d63a085bdf2d.jpg"],
                ["id" => 4, "barcode" => "750100004", "name" => "BLUE LABEL - JOHNNY WALKER", "sale_price" => 220.99, "purchase_price" => 165.00, "stock" => 8, "cat" => "Whisky", "img" => "https://i.pinimg.com/736x/f0/67/97/f0679774f573ddd6dc3c82fd10624a6f.jpg"],
                ["id" => 5, "barcode" => "750100005", "name" => "TEQUILA REPOSADO PREMIUM", "sale_price" => 85.00, "purchase_price" => 55.00, "stock" => 32, "cat" => "Tequila", "img" => "https://i.pinimg.com/736x/4e/df/d7/4edfd76539603505ef771b0ec4a1f343.jpg"],
                ["id" => 6, "barcode" => "750100006", "name" => "VODKA GREY GOOSE", "sale_price" => 45.50, "purchase_price" => 30.00, "stock" => 45, "cat" => "Vodka", "img" => "https://i.pinimg.com/736x/4f/c9/c4/4fc9c44c6d835350c9aa4e8009f61a83.jpg"],
                ["id" => 7, "barcode" => "750100007", "name" => "RON AÑEJO 7 AÑOS", "sale_price" => 32.00, "purchase_price" => 20.50, "stock" => 60, "cat" => "Ron", "img" => "https://i.pinimg.com/736x/ea/ff/a2/eaffa23a48caa3aab3487219f25de1fe.jpg"],
                ["id" => 8, "barcode" => "750100008", "name" => "GINEBRA TANQUERAY", "sale_price" => 29.99, "purchase_price" => 18.00, "stock" => 28, "cat" => "Ginebra", "img" => "https://i.pinimg.com/1200x/ad/90/61/ad9061891a96361c0aac6fab61ab63f0.jpg"],
                ["id" => 9, "barcode" => "750100009", "name" => "CHAMPAGNE MOËT & CHANDON", "sale_price" => 110.00, "purchase_price" => 80.00, "stock" => 12, "cat" => "Champagne", "img" => "https://i.pinimg.com/736x/3c/e4/88/3ce488c312dc2d9b152d8f23b9d243ea.jpg"],
                ["id" => 10, "barcode" => "750100010", "name" => "MEZCAL ARTESANAL", "sale_price" => 65.00, "purchase_price" => 40.00, "stock" => 18, "cat" => "Mezcal", "img" => "https://i.pinimg.com/736x/2e/91/4f/2e914fd9c44a0fd9f425a8b7639730a0.jpg"],
                ["id" => 11, "barcode" => "750100011", "name" => "CERVEZA ARTESANAL IPA", "sale_price" => 4.50, "purchase_price" => 2.50, "stock" => 150, "cat" => "Cerveza", "img" => "https://i.pinimg.com/736x/39/d7/57/39d757e939c56ff67c1270603d7dadb1.jpg"],
                ["id" => 12, "barcode" => "750100012", "name" => "LICOR DE CAFÉ", "sale_price" => 22.00, "purchase_price" => 14.00, "stock" => 40, "cat" => "Licor", "img" => "https://i.pinimg.com/736x/29/28/2f/29282fcd6cd3b4e15433484719854e14.jpg"]
            ];

            $delay = 0.05;
            foreach ($items as $p):
            ?>
                <article class="product-card animate__animated animate__fadeInUp item-producto"
                    data-categoria="<?php echo $p['cat']; ?>"
                    style="animation-duration: 0.5s; animation-delay: <?php echo $delay; ?>s;">
                    
                    <div class="product-image-box">
                        <img src="<?php echo $p['img']; ?>" alt="<?php echo $p['name']; ?>" class="product-image">
                    </div>

                    <div class="product-body">
                        <div class="product-brand"><?php echo strtoupper($p['cat']); ?></div>
                        <h3 class="nombre-producto"><?php echo $p['name']; ?></h3>

                        <div class="product-meta">
                            <p><strong>Categoría:</strong> <span class="product-category"><?php echo $p['cat']; ?></span></p>
                            <p class="codigo-producto"><strong>Código:</strong> <?php echo $p['barcode']; ?></p>
                            <p><strong>Stock:</strong> <?php echo $p['stock']; ?> uds.</p>
                        </div>

                        <div class="product-prices">
                            <div class="buy-price">Compra: $<?php echo number_format($p['purchase_price'], 2); ?></div>
                            <div class="sale-price">Venta: $<?php echo number_format($p['sale_price'], 2); ?></div>
                        </div>
                    </div>
                </article>
            <?php
                $delay += 0.03;
            endforeach;
            ?>
        </div>
    </section>
</main>

<script src="../../../Assets/JS/Catalogo_Empleado.js"></script>
</body>
</html>