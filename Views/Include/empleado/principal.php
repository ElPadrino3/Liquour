<?php
require_once '../../../Config/Liquour_bdd.php';

$bdd = new BDD();
$conn = $bdd->conectar();

$stmt = $conn->query("
    SELECT 
        p.nombre as name, 
        p.precio_venta as sale_price, 
        p.imagen as img, 
        COALESCE(c.nombre, 'Exclusivo') as cat 
    FROM productos p 
    LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
    WHERE p.stock > 0
    ORDER BY RAND() 
    LIMIT 3
");
$destacados = $stmt->fetchAll();

$bdd->desconectar();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liquour - Premium Spirits</title>
    <link rel="stylesheet" href="../../../Assets/CSS/pricipal.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #0a0a0a;
            background-image: linear-gradient(rgba(10, 10, 10, 0.75), rgba(10, 10, 10, 0.95)), url('https://i.pinimg.com/1200x/10/56/66/105666419226b5e7214114f64bac47a3.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: #ffffff;
            margin: 0;
            font-family: 'Montserrat', sans-serif;
        }

        .hero-section {
            position: relative;
            height: 65vh;
            min-height: 450px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            padding-bottom: 80px; 
            max-width: 800px;
        }

        .hero-content h1 {
            font-size: 3.5rem;
            color: #ffffff;
            margin-bottom: 15px;
            letter-spacing: 2px;
            text-shadow: 0 4px 15px rgba(0, 0, 0, 0.8);
        }

        .subtitle-box {
            background: rgba(197, 160, 89, 0.15);
            border: 1px solid rgba(197, 160, 89, 0.3);
            padding: 10px 25px;
            border-radius: 30px;
            display: inline-block;
            backdrop-filter: blur(5px);
        }

        .subtitle-box p {
            color: #e5c158;
            margin: 0;
            font-size: 1.1rem;
            font-weight: 500;
        }

        .features-section {
            position: relative;
            z-index: 3;
            max-width: 1200px;
            margin: -60px auto 60px auto; 
            padding: 0 20px;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        .feature-card {
            background: rgba(21, 21, 21, 0.85);
            backdrop-filter: blur(10px);
            border-top: 4px solid #C5A059;
            border-radius: 8px;
            padding: 40px 30px;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 25px 50px rgba(197, 160, 89, 0.15);
        }

        .feature-card i {
            font-size: 2.5rem;
            color: #C5A059;
            margin-bottom: 20px;
        }

        .feature-card h3 {
            font-size: 1.3rem;
            margin-bottom: 15px;
            color: #ffffff;
            letter-spacing: 1px;
        }

        .feature-card p {
            color: #cccccc;
            font-size: 0.95rem;
            line-height: 1.6;
            margin: 0;
        }

        .destacados-section {
            max-width: 1200px;
            margin: 0 auto 80px auto;
            padding: 0 20px;
        }

        .section-header {
            text-align: center;
            margin-bottom: 50px;
        }

        .gold-line {
            display: block;
            width: 60px;
            height: 3px;
            background-color: #C5A059;
            margin: 0 auto 15px auto;
        }

        .section-header h2 {
            font-size: 2.2rem;
            color: #ffffff;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .productos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 40px;
        }

        .producto-card {
            background: rgba(17, 17, 17, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(229, 193, 88, 0.2);
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s ease;
            position: relative;
        }

        .producto-card:hover {
            border-color: #C5A059;
            box-shadow: 0 10px 30px rgba(197, 160, 89, 0.15);
        }

        .img-container {
            width: 100%;
            height: 300px;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            box-sizing: border-box;
        }

        .img-container img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            transition: transform 0.4s ease;
        }

        .producto-card:hover .img-container img {
            transform: scale(1.08);
        }

        .producto-info {
            padding: 25px;
            text-align: center;
        }

        .categoria {
            display: inline-block;
            color: #C5A059;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .producto-info h4 {
            margin: 0 0 15px 0;
            font-size: 1.2rem;
            color: #ffffff;
            font-weight: 500;
        }

        .precio {
            font-size: 1.4rem;
            color: #e5c158;
            font-weight: bold;
            margin: 0;
        }

        @media (max-width: 768px) {
            .hero-content h1 {
                font-size: 2.2rem;
            }
            .features-section {
                margin-top: -30px;
            }
        }
    </style>
</head>
<body>

    <?php include '../../../Views/Layout/header_principal.php'; ?>

    <section id="inicio" class="hero-section">
        <div class="hero-content">
            <h1>Bienvenido a Liquour</h1>
            <div class="subtitle-box">
                <p>La selección más exclusiva de licores para paladares exigentes.</p>
            </div>
        </div>
    </section>

    <section class="features-section">
        <div class="features-grid">
            <div class="feature-card">
                <i class="fa-solid fa-wine-bottle"></i>
                <h3>Colección Premium</h3>
                <p>Encuentra whiskies de malta, rones añejos y reservas exclusivas de todo el mundo.</p>
            </div>
            
            <div class="feature-card">
                <i class="fa-solid fa-certificate"></i>
                <h3>Calidad Garantizada</h3>
                <p>Botellas 100% auténticas, conservadas bajo los más altos estándares de temperatura.</p>
            </div>
            
            <div class="feature-card">
                <i class="fa-solid fa-martini-glass-citrus"></i>
                <h3>Asesoría Experta</h3>
                <p>Nuestros especialistas te guiarán para encontrar el maridaje perfecto para tu evento.</p>
            </div>
        </div>
    </section>

    <section id="catalogo" class="destacados-section">
        <div class="section-header">
            <span class="gold-line"></span>
            <h2>Productos Destacados</h2>
        </div>
        
        <div class="productos-grid">
            <?php 
            if (count($destacados) > 0):
                foreach ($destacados as $p): 
                    $imagen = !empty($p['img']) ? $p['img'] : '../../../Assets/IMG/image.png';
            ?>
                <div class="producto-card">
                    <div class="img-container">
                        <img src="<?php echo htmlspecialchars($imagen); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>">
                    </div>
                    <div class="producto-info">
                        <span class="categoria"><?php echo htmlspecialchars($p['cat']); ?></span>
                        <h4><?php echo htmlspecialchars($p['name']); ?></h4>
                        <p class="precio">$<?php echo number_format((float)$p['sale_price'], 2); ?></p>
                    </div>
                </div>
            <?php 
                endforeach; 
            else: 
            ?>
                <p style="color: #e5c158; text-align: center; grid-column: 1 / -1; font-weight: bold;">
                    Nuestras reservas exclusivas se están actualizando. Vuelve pronto.
                </p>
            <?php endif; ?>
        </div>
    </section>

    <script src="../Assets/JS/principal.js"></script>
</body>
</html>