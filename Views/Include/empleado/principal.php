<?php
// conexion.php
$host = "localhost";
$dbname = "tienda_licoreria";
$user = "root";
$pass = "";

try {
    $conexion = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Obtener productos destacados (6 productos aleatorios)
$query_destacados = "SELECT p.id_producto, p.nombre, p.precio_venta, p.imagen, c.nombre as categoria 
                      FROM productos p 
                      LEFT JOIN categorias c ON p.id_categoria = c.id_categoria 
                      WHERE p.estado = 1 
                      ORDER BY RAND() 
                      LIMIT 6";
$stmt_destacados = $conexion->prepare($query_destacados);
$stmt_destacados->execute();
$destacados = $stmt_destacados->fetchAll(PDO::FETCH_ASSOC);

// Obtener todas las categorías
$query_cats = "SELECT id_categoria, nombre FROM categorias ORDER BY nombre";
$stmt_cats = $conexion->prepare($query_cats);
$stmt_cats->execute();
$categorias = $stmt_cats->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Liquour - Premium Spirits</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=Raleway:wght@300;400;500;600&display=swap" rel="stylesheet">

  <style>
    /* ─── RESET & VARIABLES ─── */
    *,
    *::before,
    *::after {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    :root {
      --gold: #C5A059;
      --gold-l: #e5c158;
      --gold-dim: rgba(197, 160, 89, 0.22);
      --gold-faint: rgba(197, 160, 89, 0.08);
      --bg: #090909;
      --bg2: #0d0d0b;
      --bg3: #111009;
      --border: rgba(197, 160, 89, 0.13);
      --border-md: rgba(197, 160, 89, 0.28);
      --w60: rgba(255, 255, 255, 0.6);
      --w40: rgba(255, 255, 255, 0.4);
      --w25: rgba(255, 255, 255, 0.22);
    }

    html {
      scroll-behavior: smooth;
    }

    body {
      background: var(--bg);
      color: #fff;
      font-family: 'Raleway', sans-serif;
      overflow-x: hidden;
    }

    /* ─── NAV ─── */
    .lq-nav {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 22px 64px;
      position: sticky;
      top: 0;
      z-index: 100;
      background: rgba(9, 9, 9, 0.94);
      backdrop-filter: blur(14px);
      border-bottom: 1px solid var(--border);
    }

    .lq-logo {
      font-family: 'Cormorant Garamond', serif;
      font-size: 25px;
      font-weight: 300;
      letter-spacing: 8px;
      color: var(--gold);
      text-transform: uppercase;
      text-decoration: none;
    }

    .lq-logo span {
      color: #fff;
    }

    .lq-nav-links {
      display: flex;
      gap: 36px;
      list-style: none;
    }

    .lq-nav-links a {
      color: var(--w60);
      text-decoration: none;
      font-size: 10px;
      letter-spacing: 3px;
      text-transform: uppercase;
      font-weight: 500;
      transition: color .3s;
    }

    .lq-nav-links a:hover {
      color: var(--gold);
    }

    /* ─── HERO ─── */
    .lq-hero {
      position: relative;
      height: 90vh;
      display: flex;
      align-items: center;
      overflow: hidden;
    }

    .lq-hero-bg {
      position: absolute;
      inset: 0;
      background:
        radial-gradient(ellipse 55% 75% at 72% 50%, rgba(197, 160, 89, .07) 0%, transparent 65%),
        linear-gradient(135deg, #090909 0%, #0f0d06 50%, #090909 100%);
    }

    .lq-hero-lines {
      position: absolute;
      inset: 0;
      background-image: repeating-linear-gradient(0deg,
          rgba(197, 160, 89, .025) 0px, transparent 1px,
          transparent 90px, rgba(197, 160, 89, .025) 91px);
    }

    .lq-hero-content {
      position: relative;
      z-index: 2;
      padding: 0 80px;
      max-width: 600px;
    }

    .lq-eyebrow {
      display: flex;
      align-items: center;
      gap: 14px;
      margin-bottom: 28px;
    }

    .lq-eyebrow-line {
      width: 38px;
      height: 1px;
      background: var(--gold);
    }

    .lq-eyebrow span {
      font-size: 10px;
      letter-spacing: 4px;
      text-transform: uppercase;
      color: var(--gold);
      font-weight: 500;
    }

    .lq-hero h1 {
      font-family: 'Cormorant Garamond', serif;
      font-size: 76px;
      font-weight: 300;
      line-height: 1.05;
      margin-bottom: 26px;
    }

    .lq-hero h1 em {
      font-style: italic;
      color: var(--gold);
    }

    .lq-hero-desc {
      color: var(--w40);
      font-size: 14px;
      line-height: 1.85;
      max-width: 400px;
      margin-bottom: 44px;
    }

    .lq-btn-group {
      display: flex;
      align-items: center;
      gap: 30px;
    }

    .lq-btn-primary {
      background: var(--gold);
      color: #090909;
      border: none;
      padding: 15px 40px;
      font-family: 'Raleway', sans-serif;
      font-size: 9px;
      letter-spacing: 3px;
      text-transform: uppercase;
      font-weight: 600;
      cursor: pointer;
      transition: all .3s;
      text-decoration: none;
      display: inline-block;
    }

    .lq-btn-primary:hover {
      background: var(--gold-l);
      transform: translateY(-2px);
    }

    .lq-btn-ghost {
      color: var(--w40);
      font-size: 10px;
      letter-spacing: 2px;
      text-transform: uppercase;
      display: flex;
      align-items: center;
      gap: 10px;
      background: none;
      border: none;
      font-family: 'Raleway', sans-serif;
      transition: color .3s;
      cursor: pointer;
      text-decoration: none;
    }

    .lq-btn-ghost::before {
      content: '';
      width: 28px;
      height: 1px;
      background: currentColor;
      transition: width .3s;
    }

    .lq-btn-ghost:hover {
      color: #fff;
    }

    .lq-btn-ghost:hover::before {
      width: 42px;
    }

    /* Hero visual */
    .lq-hero-visual {
      position: absolute;
      right: 0;
      top: 0;
      width: 50%;
      height: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .lq-bottle-frame {
      position: relative;
      width: 280px;
      height: 480px;
    }

    .lq-bottle-glow {
      position: absolute;
      inset: -80px;
      background: radial-gradient(ellipse, rgba(197, 160, 89, .1) 0%, transparent 60%);
      border-radius: 50%;
      animation: lq-pulse 4s ease-in-out infinite alternate;
    }

    @keyframes lq-pulse {
      from {
        opacity: .6;
        transform: scale(.95);
      }
      to {
        opacity: 1;
        transform: scale(1.05);
      }
    }

    .lq-bottle-svg {
      position: relative;
      z-index: 2;
      width: 100%;
      height: 100%;
      animation: lq-float 6s ease-in-out infinite;
    }

    @keyframes lq-float {
      0%, 100% {
        transform: translateY(0);
      }
      50% {
        transform: translateY(-14px);
      }
    }

    .lq-hero-badge {
      position: absolute;
      bottom: 72px;
      right: 68px;
      width: 104px;
      height: 104px;
      border: 1px solid var(--gold-dim);
      border-radius: 50%;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 4px;
      background: rgba(9, 9, 9, .88);
      backdrop-filter: blur(10px);
    }

    .lq-badge-num {
      font-family: 'Cormorant Garamond', serif;
      font-size: 32px;
      font-weight: 300;
      color: var(--gold);
      line-height: 1;
    }

    .lq-badge-txt {
      font-size: 8px;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      color: var(--w40);
      text-align: center;
      line-height: 1.5;
    }

    /* ─── STATS ─── */
    .lq-stats {
      display: flex;
      border-top: 1px solid var(--border);
      border-bottom: 1px solid var(--border);
    }

    .lq-stat {
      flex: 1;
      padding: 32px 40px;
      border-right: 1px solid var(--border);
      display: flex;
      align-items: center;
      gap: 18px;
      transition: background .3s;
    }

    .lq-stat:last-child {
      border-right: none;
    }

    .lq-stat:hover {
      background: var(--gold-faint);
    }

    .lq-stat-icon {
      width: 44px;
      height: 44px;
      border: 1px solid var(--gold-dim);
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      font-size: 17px;
      color: var(--gold);
    }

    .lq-stat-num {
      font-family: 'Cormorant Garamond', serif;
      font-size: 26px;
      color: #fff;
      line-height: 1;
      margin-bottom: 3px;
    }

    .lq-stat-lbl {
      font-size: 9px;
      letter-spacing: 2px;
      text-transform: uppercase;
      color: var(--w25);
    }

    /* ─── BRAND TICKER ─── */
    .lq-brands {
      border-bottom: 1px solid var(--border);
      padding: 28px 0;
      overflow: hidden;
      position: relative;
    }

    .lq-brands::before,
    .lq-brands::after {
      content: '';
      position: absolute;
      top: 0;
      width: 120px;
      height: 100%;
      z-index: 2;
    }

    .lq-brands::before {
      left: 0;
      background: linear-gradient(to right, var(--bg), transparent);
    }

    .lq-brands::after {
      right: 0;
      background: linear-gradient(to left, var(--bg), transparent);
    }

    .lq-brands-track {
      display: flex;
      gap: 64px;
      align-items: center;
      animation: lq-scroll 28s linear infinite;
      width: max-content;
    }

    @keyframes lq-scroll {
      from {
        transform: translateX(0);
      }
      to {
        transform: translateX(-50%);
      }
    }

    .lq-brand-item {
      font-family: 'Cormorant Garamond', serif;
      font-size: 18px;
      font-weight: 300;
      letter-spacing: 5px;
      text-transform: uppercase;
      color: rgba(255, 255, 255, .18);
      white-space: nowrap;
      transition: color .3s;
    }

    .lq-brand-item:hover {
      color: var(--gold);
    }

    .lq-brand-dot {
      width: 4px;
      height: 4px;
      border-radius: 50%;
      background: var(--gold-dim);
      flex-shrink: 0;
    }

    /* ─── SECTION COMMONS ─── */
    .lq-sec {
      padding: 90px 80px;
    }

    .lq-sec-hd {
      display: flex;
      align-items: flex-end;
      justify-content: space-between;
      margin-bottom: 56px;
    }

    .lq-sec-lbl {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 12px;
    }

    .lq-sec-lbl-dash {
      width: 26px;
      height: 1px;
      background: var(--gold);
    }

    .lq-sec-lbl span {
      font-size: 9px;
      letter-spacing: 4px;
      text-transform: uppercase;
      color: var(--gold);
      font-weight: 500;
    }

    .lq-sec-title {
      font-family: 'Cormorant Garamond', serif;
      font-size: 46px;
      font-weight: 300;
      color: #fff;
      line-height: 1.1;
    }

    .lq-sec-link {
      color: var(--w40);
      font-size: 10px;
      letter-spacing: 2.5px;
      text-transform: uppercase;
      display: flex;
      align-items: center;
      gap: 8px;
      background: none;
      border: none;
      border-bottom: 1px solid transparent;
      font-family: 'Raleway', sans-serif;
      cursor: pointer;
      transition: color .3s;
      padding-bottom: 2px;
      text-decoration: none;
    }

    .lq-sec-link:hover {
      color: var(--gold);
      border-bottom-color: var(--gold-dim);
    }

    /* ─── CATEGORIES ─── */
    .lq-cats-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 2px;
      background: var(--gold-faint);
    }

    .lq-cat-card {
      background: var(--bg2);
      padding: 44px 32px;
      cursor: pointer;
      border-bottom: 3px solid transparent;
      transition: all .3s;
      text-align: center;
    }

    .lq-cat-card:hover {
      background: var(--bg3);
      border-bottom-color: var(--gold);
    }

    .lq-cat-icon {
      font-size: 28px;
      margin-bottom: 16px;
      filter: grayscale(1) brightness(1.4);
      transition: filter .3s;
    }

    .lq-cat-card:hover .lq-cat-icon {
      filter: none;
    }

    .lq-cat-name {
      font-family: 'Cormorant Garamond', serif;
      font-size: 22px;
      font-weight: 300;
      color: #fff;
      margin-bottom: 8px;
    }

    /* ─── PRODUCTS ─── */
    .lq-products {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 2px;
      background: var(--gold-faint);
    }

    .lq-product-card {
      background: var(--bg2);
      overflow: hidden;
      cursor: pointer;
      transition: background .3s;
    }

    .lq-product-card:hover {
      background: var(--bg3);
    }

    .lq-product-img {
      background: transparent;
      height: 280px;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
      position: relative;
    }

    /* Efecto de brillo en las imágenes */
    .lq-product-img::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(197, 160, 89, 0.15), transparent);
      transition: left 0.6s ease;
      pointer-events: none;
    }

    .lq-product-card:hover .lq-product-img::before {
      left: 100%;
    }

    .lq-product-img img {
      max-width: 85%;
      max-height: 85%;
      object-fit: contain;
      transition: transform 0.5s ease, filter 0.3s ease;
      filter: drop-shadow(0 0 10px rgba(197, 160, 89, 0.2));
    }

    .lq-product-card:hover .lq-product-img img {
      transform: scale(1.08);
      filter: drop-shadow(0 0 20px rgba(197, 160, 89, 0.4));
    }

    .lq-product-body {
      padding: 26px 28px 30px;
      border-top: 2px solid var(--gold);
    }

    .lq-product-cat {
      font-size: 9px;
      letter-spacing: 3px;
      text-transform: uppercase;
      color: var(--gold);
      margin-bottom: 7px;
    }

    .lq-product-name {
      font-family: 'Cormorant Garamond', serif;
      font-size: 21px;
      font-weight: 400;
      color: #fff;
      margin-bottom: 16px;
      line-height: 1.2;
    }

    .lq-product-footer {
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .lq-product-price {
      font-family: 'Cormorant Garamond', serif;
      font-size: 24px;
      font-weight: 300;
      color: var(--gold);
    }

    .lq-product-add {
      width: 36px;
      height: 36px;
      border: 1px solid var(--gold-dim);
      background: transparent;
      color: var(--gold);
      font-size: 22px;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all .3s;
    }

    .lq-product-add:hover {
      background: var(--gold);
      color: #090909;
      border-color: var(--gold);
    }

    .lq-empty {
      grid-column: 1 / -1;
      padding: 80px 40px;
      text-align: center;
      color: var(--gold);
      font-family: 'Cormorant Garamond', serif;
      font-size: 22px;
      font-weight: 300;
      font-style: italic;
    }

    /* ─── EXPERIENCE STRIP ─── */
    .lq-exp {
      position: relative;
      min-height: 420px;
      display: flex;
      align-items: center;
      overflow: hidden;
      background: linear-gradient(135deg, #0a0800 0%, #1a1408 50%, #0a0800 100%);
    }

    .lq-exp-lines {
      position: absolute;
      inset: 0;
      background-image: repeating-linear-gradient(45deg, rgba(197, 160, 89, .025) 0px, transparent 1px, transparent 60px, rgba(197, 160, 89, .025) 61px);
    }

    .lq-exp-content {
      position: relative;
      z-index: 2;
      padding: 60px 80px;
      max-width: 520px;
    }

    .lq-exp-tag {
      display: inline-block;
      border: 1px solid var(--gold-dim);
      padding: 6px 18px;
      font-size: 9px;
      letter-spacing: 3px;
      text-transform: uppercase;
      color: var(--gold);
      margin-bottom: 28px;
    }

    .lq-exp-content h2 {
      font-family: 'Cormorant Garamond', serif;
      font-size: 52px;
      font-weight: 300;
      line-height: 1.08;
      margin-bottom: 22px;
    }

    .lq-exp-content h2 em {
      font-style: italic;
      color: var(--gold);
    }

    .lq-exp-content p {
      color: var(--w40);
      font-size: 13.5px;
      line-height: 1.85;
      margin-bottom: 36px;
    }

    .lq-exp-nums {
      display: flex;
      gap: 48px;
    }

    .lq-exp-n {
      font-family: 'Cormorant Garamond', serif;
      font-size: 38px;
      font-weight: 300;
      color: var(--gold);
      line-height: 1;
    }

    .lq-exp-nl {
      font-size: 9px;
      letter-spacing: 2px;
      text-transform: uppercase;
      color: var(--w25);
      margin-top: 4px;
    }

    .lq-exp-visual {
      position: absolute;
      right: 80px;
      top: 50%;
      transform: translateY(-50%);
    }

    .lq-rings {
      position: relative;
      width: 320px;
      height: 320px;
    }

    .lq-ring {
      position: absolute;
      border-radius: 50%;
      border: 1px solid;
    }

    .lq-ring-1 {
      inset: 0;
      border-color: rgba(197, 160, 89, .11);
    }

    .lq-ring-2 {
      inset: 28px;
      border-color: rgba(197, 160, 89, .17);
    }

    .lq-ring-3 {
      inset: 56px;
      border-color: rgba(197, 160, 89, .25);
    }

    .lq-ring-4 {
      inset: 84px;
      border-color: rgba(197, 160, 89, .37);
      animation: lq-spin 24s linear infinite;
    }

    .lq-ring-5 {
      inset: 112px;
      border-color: rgba(197, 160, 89, .54);
    }

    @keyframes lq-spin {
      from {
        transform: rotate(0deg);
      }
      to {
        transform: rotate(360deg);
      }
    }

    .lq-ring-center {
      position: absolute;
      inset: 140px;
      border-radius: 50%;
      background: rgba(197, 160, 89, .09);
      border: 1px solid var(--gold);
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .lq-ring-center-txt {
      font-family: 'Cormorant Garamond', serif;
      font-size: 13px;
      font-style: italic;
      color: var(--gold);
      text-align: center;
      line-height: 1.4;
    }

    /* ─── PROCESS ─── */
    .lq-process {
      background: var(--bg2);
      padding: 90px 80px;
    }

    .lq-process-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 1px;
      background: var(--gold-faint);
      margin-top: 56px;
    }

    .lq-proc-step {
      background: var(--bg2);
      padding: 48px 36px;
      transition: background .3s;
    }

    .lq-proc-step:hover {
      background: var(--bg3);
    }

    .lq-proc-n {
      font-family: 'Cormorant Garamond', serif;
      font-size: 56px;
      font-weight: 300;
      color: rgba(197, 160, 89, .13);
      line-height: 1;
      margin-bottom: 20px;
    }

    .lq-proc-line {
      width: 28px;
      height: 1px;
      background: var(--gold);
      margin-bottom: 20px;
    }

    .lq-proc-title {
      font-family: 'Cormorant Garamond', serif;
      font-size: 20px;
      font-weight: 400;
      color: #fff;
      margin-bottom: 12px;
    }

    .lq-proc-desc {
      font-size: 12.5px;
      color: var(--w25);
      line-height: 1.8;
    }

    /* ─── TESTIMONIALS ─── */
    .lq-testi-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 2px;
      background: var(--gold-faint);
    }

    .lq-testi-card {
      background: var(--bg2);
      padding: 44px 36px;
      transition: background .3s;
    }

    .lq-testi-card:hover {
      background: var(--bg3);
    }

    .lq-stars {
      display: flex;
      gap: 5px;
      margin-bottom: 24px;
    }

    .lq-star {
      width: 10px;
      height: 10px;
      background: var(--gold);
      clip-path: polygon(50% 0%, 61% 35%, 98% 35%, 68% 57%, 79% 91%, 50% 70%, 21% 91%, 32% 57%, 2% 35%, 39% 35%);
    }

    .lq-testi-text {
      font-family: 'Cormorant Garamond', serif;
      font-size: 17px;
      font-weight: 300;
      font-style: italic;
      color: rgba(255, 255, 255, .75);
      line-height: 1.7;
      margin-bottom: 28px;
    }

    .lq-testi-divider {
      width: 24px;
      height: 1px;
      background: var(--gold);
      margin-bottom: 20px;
    }

    .lq-testi-author {
      font-size: 10px;
      letter-spacing: 2.5px;
      text-transform: uppercase;
      color: var(--w40);
    }

    .lq-testi-role {
      font-size: 9px;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      color: var(--w25);
      margin-top: 4px;
    }

    /* ─── FEATURES ─── */
    .lq-feats-section {
      padding: 0 80px 90px;
    }

    .lq-feats-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 1px;
      background: var(--gold-faint);
    }

    .lq-feat {
      background: var(--bg2);
      padding: 52px 44px;
      transition: background .3s;
    }

    .lq-feat:hover {
      background: var(--bg3);
    }

    .lq-feat-n {
      font-family: 'Cormorant Garamond', serif;
      font-size: 50px;
      font-weight: 300;
      color: rgba(197, 160, 89, .14);
      line-height: 1;
      margin-bottom: 22px;
    }

    .lq-feat-line {
      width: 28px;
      height: 1px;
      background: var(--gold);
      margin-bottom: 22px;
    }

    .lq-feat-title {
      font-family: 'Cormorant Garamond', serif;
      font-size: 22px;
      font-weight: 400;
      color: #fff;
      margin-bottom: 14px;
    }

    .lq-feat-desc {
      font-size: 12.5px;
      color: var(--w25);
      line-height: 1.85;
    }

    /* ─── CTA / NEWSLETTER ─── */
    .lq-cta {
      position: relative;
      background: var(--bg2);
      border-top: 1px solid var(--border);
      border-bottom: 1px solid var(--border);
      padding: 90px 80px;
      overflow: hidden;
    }

    .lq-cta-lines {
      position: absolute;
      inset: 0;
      background-image: repeating-linear-gradient(90deg, rgba(197, 160, 89, .03) 0px, transparent 1px, transparent 80px, rgba(197, 160, 89, .03) 81px);
    }

    .lq-cta-inner {
      position: relative;
      z-index: 2;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 60px;
    }

    .lq-cta-left {
      max-width: 480px;
    }

    .lq-cta-tag {
      font-size: 9px;
      letter-spacing: 4px;
      text-transform: uppercase;
      color: var(--gold);
      font-weight: 500;
      margin-bottom: 16px;
    }

    .lq-cta-left h2 {
      font-family: 'Cormorant Garamond', serif;
      font-size: 46px;
      font-weight: 300;
      line-height: 1.08;
      margin-bottom: 16px;
    }

    .lq-cta-left h2 em {
      font-style: italic;
      color: var(--gold);
    }

    .lq-cta-left p {
      color: var(--w40);
      font-size: 13.5px;
      line-height: 1.8;
    }

    .lq-cta-form {
      display: flex;
      flex-direction: column;
      gap: 14px;
      min-width: 320px;
    }

    .lq-cta-input {
      background: rgba(255, 255, 255, .04);
      border: 1px solid var(--border);
      color: #fff;
      padding: 14px 20px;
      font-family: 'Raleway', sans-serif;
      font-size: 13px;
      letter-spacing: .5px;
      outline: none;
      transition: border-color .3s;
    }

    .lq-cta-input::placeholder {
      color: var(--w25);
    }

    .lq-cta-input:focus {
      border-color: var(--gold-dim);
    }

    .lq-cta-submit {
      background: var(--gold);
      color: #090909;
      border: none;
      padding: 14px 32px;
      font-family: 'Raleway', sans-serif;
      font-size: 9px;
      letter-spacing: 3px;
      text-transform: uppercase;
      font-weight: 600;
      cursor: pointer;
      transition: all .3s;
    }

    .lq-cta-submit:hover {
      background: var(--gold-l);
      transform: translateY(-1px);
    }

    .lq-cta-note {
      font-size: 9px;
      letter-spacing: 1px;
      text-transform: uppercase;
      color: var(--w25);
    }

    /* ─── FOOTER ─── */
    .lq-footer {
      border-top: 1px solid var(--border);
    }

    .lq-footer-top {
      display: grid;
      grid-template-columns: 2fr 1fr 1fr 1fr;
      gap: 60px;
      padding: 70px 80px;
    }

    .lq-ft-logo {
      font-family: 'Cormorant Garamond', serif;
      font-size: 22px;
      font-weight: 300;
      letter-spacing: 6px;
      color: var(--gold);
      text-transform: uppercase;
      margin-bottom: 16px;
    }

    .lq-ft-logo span {
      color: #fff;
    }

    .lq-ft-tagline {
      font-size: 12.5px;
      color: var(--w25);
      line-height: 1.75;
      max-width: 240px;
      margin-bottom: 24px;
    }

    .lq-ft-social {
      display: flex;
      gap: 10px;
    }

    .lq-ft-soc {
      width: 34px;
      height: 34px;
      border: 1px solid var(--border);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 12px;
      color: var(--w40);
      transition: all .3s;
      cursor: pointer;
      background: none;
    }

    .lq-ft-soc:hover {
      border-color: var(--gold);
      color: var(--gold);
    }

    .lq-ft-col-title {
      font-size: 9px;
      letter-spacing: 3px;
      text-transform: uppercase;
      color: var(--gold);
      font-weight: 500;
      margin-bottom: 24px;
    }

    .lq-ft-links {
      list-style: none;
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    .lq-ft-links a {
      font-size: 12.5px;
      color: var(--w40);
      text-decoration: none;
      letter-spacing: .5px;
      transition: color .3s;
    }

    .lq-ft-links a:hover {
      color: #fff;
    }

    .lq-footer-bottom {
      border-top: 1px solid var(--border);
      padding: 22px 80px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .lq-ft-copy {
      font-size: 9px;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      color: var(--w25);
    }

    .lq-ft-legal {
      display: flex;
      gap: 24px;
    }

    .lq-ft-legal a {
      font-size: 9px;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      color: var(--w25);
      text-decoration: none;
      transition: color .3s;
    }

    .lq-ft-legal a:hover {
      color: var(--gold);
    }

    /* ─── RESPONSIVE ─── */
    @media (max-width: 1100px) {
      .lq-nav {
        padding: 20px 32px;
      }
      .lq-nav-links {
        display: none;
      }
      .lq-hero-content {
        padding: 60px 40px;
      }
      .lq-hero h1 {
        font-size: 52px;
      }
      .lq-hero-visual {
        display: none;
      }
      .lq-hero {
        height: auto;
        padding: 80px 0;
      }
      .lq-stats {
        flex-wrap: wrap;
      }
      .lq-stat {
        flex: 1 1 50%;
        border-right: none;
        border-bottom: 1px solid var(--border);
      }
      .lq-sec,
      .lq-process,
      .lq-cta,
      .lq-feats-section {
        padding: 60px 32px;
      }
      .lq-sec-hd {
        flex-direction: column;
        align-items: flex-start;
        gap: 20px;
      }
      .lq-products {
        grid-template-columns: repeat(2, 1fr);
      }
      .lq-cats-grid {
        grid-template-columns: repeat(2, 1fr);
      }
      .lq-process-grid {
        grid-template-columns: repeat(2, 1fr);
      }
      .lq-feats-grid {
        grid-template-columns: 1fr;
      }
      .lq-testi-grid {
        grid-template-columns: 1fr;
      }
      .lq-cta-inner {
        flex-direction: column;
        gap: 40px;
      }
      .lq-cta-form {
        min-width: auto;
        width: 100%;
      }
      .lq-footer-top {
        grid-template-columns: 1fr 1fr;
        gap: 40px;
        padding: 56px 32px;
      }
      .lq-footer-bottom {
        flex-direction: column;
        gap: 16px;
        text-align: center;
        padding: 22px 32px;
      }
      .lq-exp-visual {
        display: none;
      }
    }

    @media (max-width: 640px) {
      .lq-products {
        grid-template-columns: 1fr;
      }
      .lq-cats-grid {
        grid-template-columns: 1fr 1fr;
      }
      .lq-process-grid {
        grid-template-columns: 1fr;
      }
      .lq-hero h1 {
        font-size: 40px;
      }
      .lq-stat {
        flex: 1 1 100%;
      }
      .lq-footer-top {
        grid-template-columns: 1fr;
        padding: 40px 24px;
      }
    }
  </style>
</head>

<body>

  <!-- ══ NAV ══ -->
  <nav class="lq-nav">
    <a href="#inicio" class="lq-logo">LI<span>QUO</span>UR</a>
    <ul class="lq-nav-links">
      <li><a href="#inicio">Inicio</a></li>
      <li><a href="#catalogo">Catálogo</a></li>
      <li><a href="#colecciones">Colecciones</a></li>
      <li><a href="#maridaje">Maridaje</a></li>
      <li><a href="#nosotros">Nosotros</a></li>
    </ul>
  </nav>

  <!-- ══ HERO ══ -->
  <section id="inicio" class="lq-hero">
    <div class="lq-hero-bg"></div>
    <div class="lq-hero-lines"></div>
    <div class="lq-hero-content">
      <div class="lq-eyebrow">
        <div class="lq-eyebrow-line"></div>
        <span>Destilería Premium · Est. 1987</span>
      </div>
      <h1>El arte de<br>lo <em>excepcional</em></h1>
      <p class="lq-hero-desc">Una selección cuidadosamente curada de los licores más distinguidos del mundo, para paladares que exigen excelencia.</p>
      <div class="lq-btn-group">
        <a href="#catalogo" class="lq-btn-primary">Explorar catálogo</a>
        <a href="#nosotros" class="lq-btn-ghost">Nuestra historia</a>
      </div>
    </div>
    <div class="lq-hero-visual">
      <div class="lq-bottle-frame">
        <div class="lq-bottle-glow"></div>
        <svg class="lq-bottle-svg" viewBox="0 0 300 520" xmlns="http://www.w3.org/2000/svg">
          <defs>
            <linearGradient id="glassR" x1="0" y1="0" x2="1" y2="0">
              <stop offset="0%" stop-color="#111" />
              <stop offset="50%" stop-color="#2a1f10" />
              <stop offset="100%" stop-color="#111" />
            </linearGradient>
            <linearGradient id="liquidR" x1="0" y1="0" x2="0" y2="1">
              <stop offset="0%" stop-color="#b57b2a" />
              <stop offset="100%" stop-color="#5a3a0c" />
            </linearGradient>
          </defs>
          <rect x="130" y="5" width="40" height="30" rx="5" fill="#C5A059" />
          <rect x="120" y="35" width="60" height="120" rx="15" fill="url(#glassR)" />
          <path d="M100 150 Q80 200 80 260 L80 440 Q80 470 110 480 L190 480 Q220 470 220 440 L220 260 Q220 200 200 150 Z" fill="url(#glassR)" />
          <path d="M90 280 L90 440 Q90 460 110 465 L190 465 Q210 460 210 440 L210 280 Z" fill="url(#liquidR)" opacity="0.9" />
          <rect x="110" y="300" width="80" height="120" fill="#111" stroke="#C5A059" />
          <text x="150" y="330" text-anchor="middle" fill="#C5A059" font-size="12">RON</text>
          <text x="150" y="360" text-anchor="middle" fill="#C5A059" font-size="10">AÑEJO</text>
        </svg>
      </div>
      <div class="lq-hero-badge">
        <div class="lq-badge-num">87</div>
        <div class="lq-badge-txt">Años de<br>excelencia</div>
      </div>
    </div>
  </section>

  <!-- ══ STATS ══ -->
  <div class="lq-stats">
    <div class="lq-stat">
      <div class="lq-stat-icon"><i class="fa-solid fa-wine-bottle"></i></div>
      <div>
        <div class="lq-stat-num">500+</div>
        <div class="lq-stat-lbl">Etiquetas exclusivas</div>
      </div>
    </div>
    <div class="lq-stat">
      <div class="lq-stat-icon"><i class="fa-solid fa-globe"></i></div>
      <div>
        <div class="lq-stat-num">+35</div>
        <div class="lq-stat-lbl">Países de origen</div>
      </div>
    </div>
    <div class="lq-stat">
      <div class="lq-stat-icon"><i class="fa-solid fa-certificate"></i></div>
      <div>
        <div class="lq-stat-num">100%</div>
        <div class="lq-stat-lbl">Autenticidad garantizada</div>
      </div>
    </div>
    <div class="lq-stat">
      <div class="lq-stat-icon"><i class="fa-solid fa-users"></i></div>
      <div>
        <div class="lq-stat-num">12k+</div>
        <div class="lq-stat-lbl">Clientes satisfechos</div>
      </div>
    </div>
  </div>

  <!-- ══ BRAND TICKER ══ -->
  <div class="lq-brands">
    <div class="lq-brands-track">
      <span class="lq-brand-item">Macallan</span>
      <div class="lq-brand-dot"></div>
      <span class="lq-brand-item">Diplomatico</span>
      <div class="lq-brand-dot"></div>
      <span class="lq-brand-item">Patrón</span>
      <div class="lq-brand-dot"></div>
      <span class="lq-brand-item">Glenfiddich</span>
      <div class="lq-brand-dot"></div>
      <span class="lq-brand-item">Hennessy</span>
      <div class="lq-brand-dot"></div>
      <span class="lq-brand-item">Don Julio</span>
      <div class="lq-brand-dot"></div>
      <span class="lq-brand-item">Johnnie Walker</span>
      <div class="lq-brand-dot"></div>
      <span class="lq-brand-item">Bacardi</span>
      <div class="lq-brand-dot"></div>
      <span class="lq-brand-item">Grey Goose</span>
      <div class="lq-brand-dot"></div>
      <span class="lq-brand-item">Tanqueray</span>
      <div class="lq-brand-dot"></div>
      <span class="lq-brand-item">Macallan</span>
      <div class="lq-brand-dot"></div>
      <span class="lq-brand-item">Diplomatico</span>
      <div class="lq-brand-dot"></div>
      <span class="lq-brand-item">Patrón</span>
      <div class="lq-brand-dot"></div>
      <span class="lq-brand-item">Glenfiddich</span>
      <div class="lq-brand-dot"></div>
      <span class="lq-brand-item">Hennessy</span>
      <div class="lq-brand-dot"></div>
      <span class="lq-brand-item">Don Julio</span>
      <div class="lq-brand-dot"></div>
      <span class="lq-brand-item">Johnnie Walker</span>
      <div class="lq-brand-dot"></div>
      <span class="lq-brand-item">Bacardi</span>
      <div class="lq-brand-dot"></div>
      <span class="lq-brand-item">Grey Goose</span>
      <div class="lq-brand-dot"></div>
      <span class="lq-brand-item">Tanqueray</span>
      <div class="lq-brand-dot"></div>
    </div>
  </div>

  <!-- ══ CATEGORÍAS ══ -->
  <section id="colecciones" class="lq-sec" style="padding-bottom: 0;">
    <div class="lq-sec-hd">
      <div>
        <div class="lq-sec-lbl">
          <div class="lq-sec-lbl-dash"></div><span>Explorar por categoría</span>
        </div>
        <div class="lq-sec-title">Nuestra<br>Colección</div>
      </div>
      <a href="#" class="lq-sec-link">Ver todo →</a>
    </div>
    <div class="lq-cats-grid">
      <?php foreach ($categorias as $cat): ?>
      <div class="lq-cat-card">
        <div class="lq-cat-icon">
          <?php 
            $iconos = [
              'Whisky' => '🥃', 'Vino' => '🍷', 'Cerveza' => '🍺', 
              'Tequila' => '🥃', 'Vodka' => '🍸', 'Ron' => '🥃',
              'Ginebra' => '🍸', 'Champagne' => '🍾', 'Mezcal' => '🥃', 'Licor' => '🍹'
            ];
            echo $iconos[$cat['nombre']] ?? '🍾';
          ?>
        </div>
        <div class="lq-cat-name"><?php echo htmlspecialchars($cat['nombre']); ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- ══ PRODUCTOS DESTACADOS ══ -->
  <section id="catalogo" class="lq-sec">
    <div class="lq-sec-hd">
      <div>
        <div class="lq-sec-lbl">
          <div class="lq-sec-lbl-dash"></div><span>Selección curada</span>
        </div>
        <div class="lq-sec-title">Productos<br>Destacados</div>
      </div>
      <a href="#" class="lq-sec-link">Ver catálogo completo →</a>
    </div>
    <div class="lq-products">
      <?php if (!empty($destacados)): ?>
        <?php foreach ($destacados as $p): 
          $imagen = !empty($p['imagen']) ? htmlspecialchars($p['imagen']) : 'https://images.pexels.com/photos/11271794/pexels-photo-11271794.jpeg';
          $nombre = htmlspecialchars($p['nombre']);
          $cat = htmlspecialchars($p['categoria']);
          $precio = number_format((float)$p['precio_venta'], 2);
        ?>
        <div class="lq-product-card">
          <div class="lq-product-img">
            <img src="<?= $imagen ?>" alt="<?= $nombre ?>">
          </div>
          <div class="lq-product-body">
            <div class="lq-product-cat"><?= $cat ?></div>
            <div class="lq-product-name"><?= $nombre ?></div>
            <div class="lq-product-footer">
              <div class="lq-product-price">$<?= $precio ?></div>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="lq-empty">Nuestras reservas exclusivas se están actualizando. Vuelve pronto.</div>
      <?php endif; ?>
    </div>
  </section>

  <!-- ══ EXPERIENCE STRIP ══ -->
  <div class="lq-exp">
    <div class="lq-exp-lines"></div>
    <div class="lq-exp-content">
      <div class="lq-exp-tag">La experiencia Liquour</div>
      <h2>Más que un<br><em>licor</em>, un ritual</h2>
      <p>Cada botella en nuestra colección cuenta una historia de territorio, tiempo y maestría artesanal. Nuestros expertos seleccionan solo lo que supera nuestros estrictos estándares de calidad.</p>
      <div class="lq-exp-nums">
        <div>
          <div class="lq-exp-n">38°</div>
          <div class="lq-exp-nl">Temperatura ideal</div>
        </div>
        <div>
          <div class="lq-exp-n">48h</div>
          <div class="lq-exp-nl">Curaduría por lote</div>
        </div>
        <div>
          <div class="lq-exp-n">0%</div>
          <div class="lq-exp-nl">Intermediarios</div>
        </div>
      </div>
    </div>
    <div class="lq-exp-visual">
      <div class="lq-rings">
        <div class="lq-ring lq-ring-1"></div>
        <div class="lq-ring lq-ring-2"></div>
        <div class="lq-ring lq-ring-3"></div>
        <div class="lq-ring lq-ring-4"></div>
        <div class="lq-ring lq-ring-5"></div>
        <div class="lq-ring-center">
          <div class="lq-ring-center-txt">Arte &amp;<br>Tradición</div>
        </div>
      </div>
    </div>
  </div>

  <!-- ══ PROCESO ══ -->
  <section class="lq-process">
    <div class="lq-sec-hd" style="margin-bottom: 0;">
      <div>
        <div class="lq-sec-lbl">
          <div class="lq-sec-lbl-dash"></div><span>Cómo trabajamos</span>
        </div>
        <div class="lq-sec-title">Del productor<br>a tu mesa</div>
      </div>
    </div>
    <div class="lq-process-grid">
      <div class="lq-proc-step">
        <div class="lq-proc-n">01</div>
        <div class="lq-proc-line"></div>
        <div class="lq-proc-title">Selección en origen</div>
        <p class="lq-proc-desc">Visitamos destilerías en más de 35 países para elegir personalmente cada lote con criterio de experto.</p>
      </div>
      <div class="lq-proc-step">
        <div class="lq-proc-n">02</div>
        <div class="lq-proc-line"></div>
        <div class="lq-proc-title">Análisis de calidad</div>
        <p class="lq-proc-desc">Cada botella pasa por un proceso de cata con nuestro panel de maestros catadores internacionalmente certificados.</p>
      </div>
      <div class="lq-proc-step">
        <div class="lq-proc-n">03</div>
        <div class="lq-proc-line"></div>
        <div class="lq-proc-title">Almacenaje premium</div>
        <p class="lq-proc-desc">Bodegas con temperatura y humedad controladas para preservar cada nota aromática en su punto óptimo.</p>
      </div>
      <div class="lq-proc-step">
        <div class="lq-proc-n">04</div>
        <div class="lq-proc-line"></div>
        <div class="lq-proc-title">Entrega garantizada</div>
        <p class="lq-proc-desc">Embalaje especializado y cadena de frío para que cada botella llegue en condiciones perfectas a tu puerta.</p>
      </div>
    </div>
  </section>

  <!-- ══ TESTIMONIOS ══ -->
  <section id="nosotros" class="lq-sec">
    <div class="lq-sec-hd">
      <div>
        <div class="lq-sec-lbl">
          <div class="lq-sec-lbl-dash"></div><span>Lo que dicen</span>
        </div>
        <div class="lq-sec-title">Voces de<br>nuestros clientes</div>
      </div>
    </div>
    <div class="lq-testi-grid">
      <div class="lq-testi-card">
        <div class="lq-stars">
          <div class="lq-star"></div><div class="lq-star"></div><div class="lq-star"></div><div class="lq-star"></div><div class="lq-star"></div>
        </div>
        <div class="lq-testi-text">"La selección de whiskies es sencillamente inigualable. Encontré etiquetas que no había visto en ninguna otra tienda de la región."</div>
        <div class="lq-testi-divider"></div>
        <div class="lq-testi-author">Carlos M.</div>
        <div class="lq-testi-role">Coleccionista · San Salvador</div>
      </div>
      <div class="lq-testi-card">
        <div class="lq-stars">
          <div class="lq-star"></div><div class="lq-star"></div><div class="lq-star"></div><div class="lq-star"></div><div class="lq-star"></div>
        </div>
        <div class="lq-testi-text">"El asesoramiento de su equipo fue excepcional. Me ayudaron a elegir el maridaje perfecto para una cena de negocios muy importante."</div>
        <div class="lq-testi-divider"></div>
        <div class="lq-testi-author">Ana R.</div>
        <div class="lq-testi-role">Directora Ejecutiva · Guatemala</div>
      </div>
      <div class="lq-testi-card">
        <div class="lq-stars">
          <div class="lq-star"></div><div class="lq-star"></div><div class="lq-star"></div><div class="lq-star"></div><div class="lq-star"></div>
        </div>
        <div class="lq-testi-text">"Compramos para todos los eventos corporativos de nuestra empresa. La calidad y presentación siempre impresionan a nuestros invitados."</div>
        <div class="lq-testi-divider"></div>
        <div class="lq-testi-author">Roberto V.</div>
        <div class="lq-testi-role">Gerente de Eventos · Honduras</div>
      </div>
    </div>
  </section>

  <!-- ══ FEATURES ══ -->
  <section id="maridaje" class="lq-feats-section">
    <div class="lq-feats-grid">
      <div class="lq-feat">
        <div class="lq-feat-n">01</div>
        <div class="lq-feat-line"></div>
        <div class="lq-feat-title">Colección Premium</div>
        <p class="lq-feat-desc">Whiskies de malta, rones añejos y reservas exclusivas de las destilerías más reconocidas del mundo.</p>
      </div>
      <div class="lq-feat">
        <div class="lq-feat-n">02</div>
        <div class="lq-feat-line"></div>
        <div class="lq-feat-title">Calidad Garantizada</div>
        <p class="lq-feat-desc">Botellas 100% auténticas conservadas bajo los más altos estándares de temperatura y humedad controlada.</p>
      </div>
      <div class="lq-feat">
        <div class="lq-feat-n">03</div>
        <div class="lq-feat-line"></div>
        <div class="lq-feat-title">Asesoría Experta</div>
        <p class="lq-feat-desc">Especialistas certificados te guiarán para encontrar el maridaje perfecto para cada ocasión especial.</p>
      </div>
    </div>
  </section>

  <!-- ══ FOOTER ══ -->
  <footer class="lq-footer">
    <div class="lq-footer-top">
      <div>
        <div class="lq-ft-logo">LI<span>QUO</span>UR</div>
        <p class="lq-ft-tagline">La selección más exclusiva de licores premium para paladares que exigen excelencia.</p>
        <div class="lq-ft-social">
          <button class="lq-ft-soc"><i class="fa-brands fa-instagram"></i></button>
          <button class="lq-ft-soc"><i class="fa-brands fa-facebook-f"></i></button>
          <button class="lq-ft-soc"><i class="fa-brands fa-linkedin-in"></i></button>
        </div>
      </div>
      <div>
        <div class="lq-ft-col-title">Catálogo</div>
        <ul class="lq-ft-links">
          <li><a href="#">Whiskies</a></li>
          <li><a href="#">Rones</a></li>
          <li><a href="#">Tequilas</a></li>
          <li><a href="#">Cognac &amp; Brandy</a></li>
          <li><a href="#">Ediciones limitadas</a></li>
        </ul>
      </div>
      <div>
        <div class="lq-ft-col-title">Empresa</div>
        <ul class="lq-ft-links">
          <li><a href="#">Nuestra historia</a></li>
          <li><a href="#">Equipo</a></li>
          <li><a href="#">Prensa</a></li>
          <li><a href="#">Sostenibilidad</a></li>
        </ul>
      </div>
      <div>
        <div class="lq-ft-col-title">Soporte</div>
        <ul class="lq-ft-links">
          <li><a href="#">Contacto</a></li>
          <li><a href="#">Envíos</a></li>
          <li><a href="#">Devoluciones</a></li>
          <li><a href="#">FAQ</a></li>
        </ul>
      </div>
    </div>
    <div class="lq-footer-bottom">
      <div class="lq-ft-copy">© 2025 Liquour · Premium Spirits · Todos los derechos reservados</div>
      <div class="lq-ft-legal">
        <a href="#">Privacidad</a>
        <a href="#">Términos</a>
        <a href="#">Cookies</a>
      </div>
    </div>
  </footer>

</body>

</html>