<?php
require_once '../../../Config/Liquour_bdd.php';

$bdd = new BDD();
$conn = $bdd->conectar();

$stmt = $conn->query("
    SELECT 
        p.id_producto as id, 
        p.codigo_barras as barcode, 
        p.nombre as name, 
        p.precio_venta as sale_price, 
        p.precio_compra as purchase_price, 
        p.stock, 
        p.imagen as img, 
        COALESCE(c.nombre, 'Sin categoría') as cat 
    FROM productos p 
    LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
    ORDER BY p.nombre ASC
");
$items = $stmt->fetchAll();

$bdd->desconectar();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo - Empleado</title>
    <link rel="stylesheet" href="../../../Assets/CSS/nav.css">
    <link rel="stylesheet" href="../../../Assets/CSS/Catalogo_Empleado.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
</head>
<body>

<?php @include '../../Layout/nav_admin.php'; ?> 
<link rel="stylesheet" href="/LIQUOUR/Assets/CSS/nav.css">
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
            $delay = 0.05;
            foreach ($items as $p):
                $imagen = !empty($p['img']) ? $p['img'] : 'https://via.placeholder.com/150?text=Sin+Imagen';
                $stock_actual = isset($p['stock']) && $p['stock'] !== '' ? $p['stock'] : 0;
                $codigo_actual = !empty($p['barcode']) ? $p['barcode'] : 'N/A';
            ?>
                <article class="product-card animate__animated animate__fadeInUp item-producto"
                    data-categoria="<?php echo htmlspecialchars($p['cat']); ?>"
                    style="animation-duration: 0.5s; animation-delay: <?php echo $delay; ?>s;">
                    
                    <div class="product-image-box">
                        <img src="<?php echo htmlspecialchars($imagen); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" class="product-image">
                    </div>

                    <div class="product-body">
                        <div class="product-brand"><?php echo strtoupper(htmlspecialchars($p['cat'])); ?></div>
                        <h3 class="nombre-producto"><?php echo htmlspecialchars($p['name']); ?></h3>

                        <div class="product-meta">
                            <p><strong>Categoría:</strong> <span class="product-category"><?php echo htmlspecialchars($p['cat']); ?></span></p>
                            <p class="codigo-producto"><strong>Código:</strong> <?php echo htmlspecialchars($codigo_actual); ?></p>
                            <p><strong>Stock:</strong> <?php echo htmlspecialchars($stock_actual); ?> uds.</p>
                        </div>

                        <div class="product-prices">
                            <div class="buy-price">Compra: $<?php echo number_format((float)$p['purchase_price'], 2); ?></div>
                            <div class="sale-price">Venta: $<?php echo number_format((float)$p['sale_price'], 2); ?></div>
                        </div>
                    </div>
                </article>
            <?php
                $delay += 0.03;
            endforeach;
            
            if (count($items) === 0):
            ?>
                <p style="color: #F5F5DC; text-align: center; grid-column: 1 / -1; font-weight: bold; margin-top: 20px;">No hay productos registrados en la base de datos.</p>
            <?php endif; ?>
        </div>
    </section>
</main>

<script src="../../../Assets/JS/Catalogo_Empleado.js?v=<?php echo time(); ?>"></script>
</body>
</html>