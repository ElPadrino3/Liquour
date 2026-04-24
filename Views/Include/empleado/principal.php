<?php
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

$query_destacados = "SELECT p.id_producto, p.nombre, p.precio_venta, p.imagen, c.nombre as categoria 
                      FROM productos p 
                      LEFT JOIN categorias c ON p.id_categoria = c.id_categoria 
                      WHERE p.estado = 1 
                      ORDER BY RAND() 
                      LIMIT 6";
$stmt_destacados = $conexion->prepare($query_destacados);
$stmt_destacados->execute();
$destacados = $stmt_destacados->fetchAll(PDO::FETCH_ASSOC);

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
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=Raleway:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <style>
        *, *::before, *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --gold: #C5A059;
            --gold-l: #e5c158;
            --gold-dim: rgba(197, 160, 89, 0.22);
            --gold-faint: rgba(197, 160, 89, 0.08);
            --w60: rgba(255, 255, 255, 0.6);
            --w40: rgba(255, 255, 255, 0.4);
            --w25: rgba(255, 255, 255, 0.22);
        }

        body {
            background: transparent;
            color: #fff;
            font-family: 'Raleway', sans-serif;
            overflow-x: hidden;
            cursor: none;
        }

        #webGLApp {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            pointer-events: none;
        }

        .custom-cursor {
            position: fixed;
            width: 35px;
            height: 35px;
            border: 1.5px solid var(--gold-l);
            border-radius: 50%;
            pointer-events: none;
            z-index: 10000;
            transform: translate(-50%, -50%);
            background: rgba(229, 193, 88, 0.05);
            backdrop-filter: blur(3px);
            transition: width 0.2s, height 0.2s;
        }

        .lq-nav, main, section, footer {
            position: relative;
            z-index: 10;
            background: transparent !important;
        }

        .lq-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 22px 64px;
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(5, 5, 5, 0.7);
            backdrop-filter: blur(15px);
            border-bottom: 1px solid var(--gold-dim);
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

        .color-palette-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1000;
            width: 55px;
            height: 55px;
            border-radius: 50%;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(10px);
            border: 1px solid var(--gold);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            color: var(--gold);
            font-size: 24px;
        }

        .color-palette-btn:hover {
            transform: scale(1.1);
            background: var(--gold);
            color: #000;
        }

        .color-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(8px);
            z-index: 2000;
            display: none;
            align-items: center;
            justify-content: center;
        }

        .color-modal.active {
            display: flex;
        }

        .modal-content {
            background: rgba(8, 8, 8, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid var(--gold);
            border-radius: 24px;
            padding: 32px;
            width: 90%;
            max-width: 500px;
            animation: modalSlideIn 0.3s ease;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
        }

        @keyframes modalSlideIn {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--gold-dim);
        }

        .modal-header h3 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 24px;
            font-weight: 300;
            color: var(--gold);
            letter-spacing: 2px;
        }

        .modal-close {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--gold-dim);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            color: var(--w40);
        }

        .modal-close:hover {
            background: var(--gold);
            color: #000;
            transform: rotate(90deg);
        }

        .color-section {
            margin-bottom: 28px;
        }

        .color-label {
            font-size: 10px;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: var(--gold);
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .color-label span {
            background: var(--gold-dim);
            width: 30px;
            height: 1px;
        }

        .color-preview-row {
            display: flex;
            gap: 16px;
            align-items: center;
            flex-wrap: wrap;
        }

        .color-preview {
            width: 70px;
            height: 70px;
            border-radius: 12px;
            border: 2px solid var(--gold);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            transition: all 0.2s;
        }

        .color-controls {
            flex: 1;
            min-width: 200px;
        }

        .rgb-controls {
            display: flex;
            gap: 10px;
            margin-bottom: 12px;
        }

        .rgb-input {
            flex: 1;
            text-align: center;
        }

        .rgb-input label {
            display: block;
            font-size: 9px;
            letter-spacing: 2px;
            color: var(--w40);
            margin-bottom: 5px;
        }

        .rgb-input input {
            width: 100%;
            padding: 6px 4px;
            background: rgba(0, 0, 0, 0.6);
            border: 1px solid var(--gold-dim);
            border-radius: 6px;
            color: #fff;
            font-family: monospace;
            font-size: 12px;
            text-align: center;
        }

        .rgb-input input:focus {
            outline: none;
            border-color: var(--gold);
        }

        .hex-control {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .hex-control input {
            flex: 1;
            padding: 8px 10px;
            background: rgba(0, 0, 0, 0.6);
            border: 1px solid var(--gold-dim);
            border-radius: 6px;
            color: #fff;
            font-family: monospace;
            font-size: 12px;
            text-transform: uppercase;
        }

        .hex-control button {
            padding: 8px 14px;
            background: rgba(197, 160, 89, 0.15);
            border: 1px solid var(--gold-dim);
            border-radius: 6px;
            color: var(--gold);
            font-size: 10px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .hex-control button:hover {
            background: var(--gold);
            color: #000;
        }

        .modal-actions {
            display: flex;
            gap: 14px;
            margin-top: 28px;
            padding-top: 20px;
            border-top: 1px solid var(--gold-dim);
        }

        .modal-btn {
            flex: 1;
            padding: 12px;
            font-family: 'Raleway', sans-serif;
            font-size: 10px;
            letter-spacing: 3px;
            text-transform: uppercase;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
            border-radius: 8px;
        }

        .modal-btn-primary {
            background: var(--gold);
            color: #090909;
        }

        .modal-btn-primary:hover {
            background: var(--gold-l);
            transform: translateY(-2px);
        }

        .modal-btn-secondary {
            background: transparent;
            border: 1px solid var(--gold-dim);
            color: var(--w40);
        }

        .modal-btn-secondary:hover {
            border-color: var(--gold);
            color: var(--gold);
        }

        .lq-hero {
            position: relative;
            height: 90vh;
            display: flex;
            align-items: center;
            overflow: hidden;
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
            background: radial-gradient(ellipse, rgba(197, 160, 89, .1) 0%, transparent 70%);
            border-radius: 50%;
            animation: lq-pulse 4s ease-in-out infinite alternate;
        }

        @keyframes lq-pulse {
            from { opacity: .4; transform: scale(.95); }
            to { opacity: 0.8; transform: scale(1.05); }
        }

        .lq-bottle-svg {
            position: relative;
            z-index: 2;
            width: 100%;
            height: 100%;
            animation: lq-float 6s ease-in-out infinite;
            filter: drop-shadow(0 20px 30px rgba(0,0,0,0.5));
        }

        @keyframes lq-float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-14px) rotate(2deg); }
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
            background: rgba(5, 5, 5, 0.6);
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

        .lq-stats {
            display: flex;
            border-top: 1px solid var(--gold-dim);
            border-bottom: 1px solid var(--gold-dim);
        }

        .lq-stat {
            flex: 1;
            padding: 32px 40px;
            border-right: 1px solid var(--gold-dim);
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

        .lq-brands {
            border-bottom: 1px solid var(--gold-dim);
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
            background: linear-gradient(to right, rgba(5,5,5,0.9), transparent);
        }

        .lq-brands::after {
            right: 0;
            background: linear-gradient(to left, rgba(5,5,5,0.9), transparent);
        }

        .lq-brands-track {
            display: flex;
            gap: 64px;
            align-items: center;
            animation: lq-scroll 28s linear infinite;
            width: max-content;
        }

        @keyframes lq-scroll {
            from { transform: translateX(0); }
            to { transform: translateX(-50%); }
        }

        .lq-brand-item {
            font-family: 'Cormorant Garamond', serif;
            font-size: 18px;
            font-weight: 300;
            letter-spacing: 5px;
            text-transform: uppercase;
            color: rgba(255, 255, 255, .15);
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

        .lq-cats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 2px;
            background: var(--gold-dim);
        }

        .lq-cat-card {
            background: rgba(8, 8, 8, 0.5);
            backdrop-filter: blur(8px);
            padding: 44px 32px;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            transition: all .3s;
            text-align: center;
        }

        .lq-cat-card:hover {
            background: rgba(197, 160, 89, 0.1);
            border-bottom-color: var(--gold);
        }

        .lq-cat-icon {
            font-size: 38px;
            margin-bottom: 16px;
            transition: transform .3s;
        }

        .lq-cat-card:hover .lq-cat-icon {
            transform: scale(1.1);
        }

        .lq-cat-name {
            font-family: 'Cormorant Garamond', serif;
            font-size: 22px;
            font-weight: 300;
            color: #fff;
            margin-bottom: 8px;
        }

        .lq-products {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }

        .lq-product-card {
            background: rgba(10, 10, 10, 0.4);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 4px;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1);
        }

        .lq-product-card:hover {
            background: rgba(197, 160, 89, 0.08);
            transform: translateY(-10px);
            border-color: var(--gold);
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
            filter: drop-shadow(0 10px 20px rgba(0,0,0,0.4));
        }

        .lq-product-card:hover .lq-product-img img {
            transform: scale(1.08);
            filter: drop-shadow(0 0 20px rgba(197, 160, 89, 0.25));
        }

        .lq-product-body {
            padding: 24px 28px 30px;
            border-top: 1px solid rgba(197, 160, 89, 0.3);
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
            font-size: 22px;
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
            font-size: 26px;
            font-weight: 300;
            color: var(--gold);
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

        .lq-exp {
            position: relative;
            min-height: 420px;
            display: flex;
            align-items: center;
            overflow: hidden;
            margin: 40px 0;
        }

        .lq-exp-content {
            position: relative;
            z-index: 2;
            padding: 60px 80px;
            max-width: 550px;
            background: rgba(5, 5, 5, 0.4);
            backdrop-filter: blur(12px);
            border-radius: 4px;
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

        .lq-bottle-mini {
            width: 200px;
            height: auto;
            animation: lq-float 5s ease-in-out infinite;
            filter: drop-shadow(0 20px 30px rgba(0,0,0,0.5));
        }

        .lq-process {
            background: transparent;
            padding: 90px 80px;
        }

        .lq-process-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
            margin-top: 56px;
        }

        .lq-proc-step {
            background: rgba(10, 10, 10, 0.4);
            backdrop-filter: blur(8px);
            padding: 40px 32px;
            transition: all .3s;
            border: 1px solid rgba(255, 255, 255, 0.03);
            border-radius: 4px;
        }

        .lq-proc-step:hover {
            background: rgba(197, 160, 89, 0.06);
            transform: translateY(-5px);
            border-color: rgba(197, 160, 89, 0.2);
        }

        .lq-proc-n {
            font-family: 'Cormorant Garamond', serif;
            font-size: 60px;
            font-weight: 300;
            color: rgba(197, 160, 89, 0.15);
            line-height: 1;
            margin-bottom: 20px;
        }

        .lq-proc-line {
            width: 40px;
            height: 2px;
            background: var(--gold);
            margin-bottom: 24px;
        }

        .lq-proc-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 22px;
            font-weight: 400;
            color: #fff;
            margin-bottom: 16px;
        }

        .lq-proc-desc {
            font-size: 13px;
            color: var(--w40);
            line-height: 1.8;
        }

        .lq-testi-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }

        .lq-testi-card {
            background: rgba(10, 10, 10, 0.4);
            backdrop-filter: blur(8px);
            padding: 40px 36px;
            transition: all .3s;
            border: 1px solid rgba(255, 255, 255, 0.03);
            border-radius: 4px;
        }

        .lq-testi-card:hover {
            background: rgba(197, 160, 89, 0.06);
            transform: translateY(-5px);
        }

        .lq-stars {
            display: flex;
            gap: 6px;
            margin-bottom: 24px;
        }

        .lq-star {
            width: 12px;
            height: 12px;
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
            width: 30px;
            height: 2px;
            background: var(--gold);
            margin-bottom: 20px;
        }

        .lq-testi-author {
            font-size: 11px;
            letter-spacing: 2.5px;
            text-transform: uppercase;
            color: var(--w40);
        }

        .lq-testi-role {
            font-size: 9px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--w25);
            margin-top: 6px;
        }

        .lq-feats-section {
            padding: 0 80px 90px;
        }

        .lq-feats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }

        .lq-feat {
            background: rgba(10, 10, 10, 0.4);
            backdrop-filter: blur(8px);
            padding: 48px 40px;
            transition: all .3s;
            border: 1px solid rgba(255, 255, 255, 0.03);
            border-radius: 4px;
        }

        .lq-feat:hover {
            background: rgba(197, 160, 89, 0.06);
            transform: translateY(-5px);
        }

        .lq-feat-n {
            font-family: 'Cormorant Garamond', serif;
            font-size: 52px;
            font-weight: 300;
            color: rgba(197, 160, 89, 0.15);
            line-height: 1;
            margin-bottom: 24px;
        }

        .lq-feat-line {
            width: 40px;
            height: 2px;
            background: var(--gold);
            margin-bottom: 24px;
        }

        .lq-feat-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 24px;
            font-weight: 400;
            color: #fff;
            margin-bottom: 16px;
        }

        .lq-feat-desc {
            font-size: 13px;
            color: var(--w40);
            line-height: 1.85;
        }

        .lq-footer {
            border-top: 1px solid var(--gold-dim);
            background: linear-gradient(to top, rgba(0,0,0,0.8), transparent) !important;
        }

        .lq-footer-top {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 60px;
            padding: 70px 80px 50px;
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
            font-size: 13px;
            color: var(--w40);
            line-height: 1.75;
            max-width: 260px;
            margin-bottom: 24px;
        }

        .lq-ft-social {
            display: flex;
            gap: 12px;
        }

        .lq-ft-soc {
            width: 36px;
            height: 36px;
            border: 1px solid var(--gold-dim);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: var(--w40);
            transition: all .3s;
            cursor: pointer;
            background: none;
            border-radius: 50%;
        }

        .lq-ft-soc:hover {
            border-color: var(--gold);
            color: var(--gold);
            transform: translateY(-3px);
        }

        .lq-ft-col-title {
            font-size: 10px;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: var(--gold);
            font-weight: 600;
            margin-bottom: 24px;
        }

        .lq-ft-links {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .lq-ft-links a {
            font-size: 13px;
            color: var(--w40);
            text-decoration: none;
            transition: all .3s;
        }

        .lq-ft-links a:hover {
            color: var(--gold);
            transform: translateX(8px);
            display: inline-block;
        }

        .lq-footer-bottom {
            border-top: 1px solid var(--gold-dim);
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

        @media (max-width: 1100px) {
            .lq-nav { padding: 20px 32px; }
            .lq-nav-links { display: none; }
            .lq-hero-content { padding: 60px 40px; }
            .lq-hero h1 { font-size: 52px; }
            .lq-hero-visual { display: none; }
            .lq-hero { height: auto; padding: 80px 0; }
            .lq-stats { flex-wrap: wrap; }
            .lq-stat { flex: 1 1 50%; border-right: none; border-bottom: 1px solid var(--gold-dim); }
            .lq-sec, .lq-process { padding: 60px 32px; }
            .lq-sec-hd { flex-direction: column; align-items: flex-start; gap: 20px; }
            .lq-products { grid-template-columns: repeat(2, 1fr); gap: 16px; }
            .lq-cats-grid { grid-template-columns: repeat(2, 1fr); }
            .lq-process-grid { grid-template-columns: repeat(2, 1fr); gap: 16px; }
            .lq-feats-grid { grid-template-columns: 1fr; gap: 16px; }
            .lq-testi-grid { grid-template-columns: 1fr; gap: 16px; }
            .lq-feats-section { padding: 0 32px 60px; }
            .lq-footer-top { grid-template-columns: 1fr 1fr; gap: 40px; padding: 56px 32px; }
            .lq-footer-bottom { flex-direction: column; gap: 16px; text-align: center; padding: 22px 32px; }
            .lq-exp-visual { display: none; }
            .lq-exp-content { padding: 40px 32px; margin: 0 20px; }
            .color-palette-btn { bottom: 20px; right: 20px; width: 45px; height: 45px; font-size: 20px; }
            .modal-content { padding: 24px; }
        }

        @media (max-width: 640px) {
            .lq-products { grid-template-columns: 1fr; gap: 16px; }
            .lq-cats-grid { grid-template-columns: 1fr 1fr; }
            .lq-process-grid { grid-template-columns: 1fr; gap: 16px; }
            .lq-hero h1 { font-size: 40px; }
            .lq-stat { flex: 1 1 100%; }
            .lq-footer-top { grid-template-columns: 1fr; padding: 40px 24px; }
            .color-preview-row { flex-direction: column; align-items: flex-start; }
            .color-preview { width: 100%; height: 60px; }
        }
    </style>
</head>
<body>

<nav class="lq-nav">
    <a href="#inicio" class="lq-logo">LI<span>QUO</span>UR</a>
    <ul class="lq-nav-links">
        <li><a href="../../Layout/menu.php">Inicio</a></li>
        <li><a href="#catalogo">Catálogo</a></li>
        <li><a href="#colecciones">Colecciones</a></li>
        <li><a href="#maridaje">Maridaje</a></li>
        <li><a href="#nosotros">Nosotros</a></li>
    </ul>
</nav>

<div class="color-palette-btn" id="colorPaletteBtn">
    <i class="fas fa-palette"></i>
</div>

<div class="color-modal" id="colorModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-tint" style="margin-right: 10px;"></i> COLOR ADJUSTER</h3>
            <div class="modal-close" id="modalClose">
                <i class="fas fa-times"></i>
            </div>
        </div>

        <div class="color-section">
            <div class="color-label">
                <span></span> COLOR 1
            </div>
            <div class="color-preview-row">
                <div class="color-preview" id="previewColor1" style="background: #1a0b00;"></div>
                <div class="color-controls">
                    <div class="rgb-controls">
                        <div class="rgb-input"><label>R</label><input type="number" id="r1" min="0" max="255" value="26"></div>
                        <div class="rgb-input"><label>G</label><input type="number" id="g1" min="0" max="255" value="11"></div>
                        <div class="rgb-input"><label>B</label><input type="number" id="b1" min="0" max="255" value="0"></div>
                    </div>
                    <div class="hex-control">
                        <input type="text" id="hex1" maxlength="7" value="#1A0B00">
                        <button id="copyHex1"><i class="far fa-copy"></i> Copy</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="color-section">
            <div class="color-label">
                <span></span> COLOR 2
            </div>
            <div class="color-preview-row">
                <div class="color-preview" id="previewColor2" style="background: #4a2000;"></div>
                <div class="color-controls">
                    <div class="rgb-controls">
                        <div class="rgb-input"><label>R</label><input type="number" id="r2" min="0" max="255" value="74"></div>
                        <div class="rgb-input"><label>G</label><input type="number" id="g2" min="0" max="255" value="32"></div>
                        <div class="rgb-input"><label>B</label><input type="number" id="b2" min="0" max="255" value="0"></div>
                    </div>
                    <div class="hex-control">
                        <input type="text" id="hex2" maxlength="7" value="#4A2000">
                        <button id="copyHex2"><i class="far fa-copy"></i> Copy</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="color-section">
            <div class="color-label">
                <span></span> COLOR 3
            </div>
            <div class="color-preview-row">
                <div class="color-preview" id="previewColor3" style="background: #0d0d0d;"></div>
                <div class="color-controls">
                    <div class="rgb-controls">
                        <div class="rgb-input"><label>R</label><input type="number" id="r3" min="0" max="255" value="13"></div>
                        <div class="rgb-input"><label>G</label><input type="number" id="g3" min="0" max="255" value="13"></div>
                        <div class="rgb-input"><label>B</label><input type="number" id="b3" min="0" max="255" value="13"></div>
                    </div>
                    <div class="hex-control">
                        <input type="text" id="hex3" maxlength="7" value="#0D0D0D">
                        <button id="copyHex3"><i class="far fa-copy"></i> Copy</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-actions">
            <button class="modal-btn modal-btn-secondary" id="resetColors">Reset</button>
            <button class="modal-btn modal-btn-primary" id="applyColors">Apply</button>
        </div>
    </div>
</div>

<section id="inicio" class="lq-hero">
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
                <path d="M90 280 L90 440 Q90 460 110 465 L190 465 Q210 460 210 440 L210 280 Z" fill="url(#liquidR)" opacity="0.85" />
                <rect x="110" y="300" width="80" height="120" fill="#111" stroke="#C5A059" rx="4" />
                <text x="150" y="330" text-anchor="middle" fill="#C5A059" font-size="12" font-family="serif">RON</text>
                <text x="150" y="360" text-anchor="middle" fill="#C5A059" font-size="10" font-family="serif">AÑEJO</text>
            </svg>
        </div>
        <div class="lq-hero-badge">
            <div class="lq-badge-num">87</div>
            <div class="lq-badge-txt">Años de<br>excelencia</div>
        </div>
    </div>
</section>

<div class="lq-stats">
    <div class="lq-stat">
        <div class="lq-stat-icon"><i class="fa-solid fa-wine-bottle"></i></div>
        <div><div class="lq-stat-num">500+</div><div class="lq-stat-lbl">Etiquetas exclusivas</div></div>
    </div>
    <div class="lq-stat">
        <div class="lq-stat-icon"><i class="fa-solid fa-globe"></i></div>
        <div><div class="lq-stat-num">+35</div><div class="lq-stat-lbl">Países de origen</div></div>
    </div>
    <div class="lq-stat">
        <div class="lq-stat-icon"><i class="fa-solid fa-certificate"></i></div>
        <div><div class="lq-stat-num">100%</div><div class="lq-stat-lbl">Autenticidad garantizada</div></div>
    </div>
    <div class="lq-stat">
        <div class="lq-stat-icon"><i class="fa-solid fa-users"></i></div>
        <div><div class="lq-stat-num">12k+</div><div class="lq-stat-lbl">Clientes satisfechos</div></div>
    </div>
</div>

<div class="lq-brands">
    <div class="lq-brands-track">
        <span class="lq-brand-item">Macallan</span><div class="lq-brand-dot"></div>
        <span class="lq-brand-item">Diplomatico</span><div class="lq-brand-dot"></div>
        <span class="lq-brand-item">Patrón</span><div class="lq-brand-dot"></div>
        <span class="lq-brand-item">Glenfiddich</span><div class="lq-brand-dot"></div>
        <span class="lq-brand-item">Hennessy</span><div class="lq-brand-dot"></div>
        <span class="lq-brand-item">Don Julio</span><div class="lq-brand-dot"></div>
        <span class="lq-brand-item">Johnnie Walker</span><div class="lq-brand-dot"></div>
        <span class="lq-brand-item">Bacardi</span><div class="lq-brand-dot"></div>
        <span class="lq-brand-item">Grey Goose</span><div class="lq-brand-dot"></div>
        <span class="lq-brand-item">Tanqueray</span><div class="lq-brand-dot"></div>
        <span class="lq-brand-item">Macallan</span><div class="lq-brand-dot"></div>
        <span class="lq-brand-item">Diplomatico</span><div class="lq-brand-dot"></div>
        <span class="lq-brand-item">Patrón</span><div class="lq-brand-dot"></div>
        <span class="lq-brand-item">Glenfiddich</span><div class="lq-brand-dot"></div>
        <span class="lq-brand-item">Hennessy</span><div class="lq-brand-dot"></div>
        <span class="lq-brand-item">Don Julio</span><div class="lq-brand-dot"></div>
        <span class="lq-brand-item">Johnnie Walker</span><div class="lq-brand-dot"></div>
    </div>
</div>

<section id="colecciones" class="lq-sec" style="padding-bottom: 0;">
    <div class="lq-sec-hd">
        <div><div class="lq-sec-lbl"><div class="lq-sec-lbl-dash"></div><span>Explorar por categoría</span></div><div class="lq-sec-title">Nuestra<br>Colección</div></div>
        <a href="#" class="lq-sec-link">Ver todo →</a>
    </div>
    <div class="lq-cats-grid">
        <?php foreach ($categorias as $cat): ?>
        <div class="lq-cat-card">
            <div class="lq-cat-icon"><?php $iconos = ['Whisky' => '🥃', 'Vino' => '🍷', 'Cerveza' => '🍺', 'Tequila' => '🥃', 'Vodka' => '🍸', 'Ron' => '🥃', 'Ginebra' => '🍸', 'Champagne' => '🍾', 'Mezcal' => '🥃', 'Licor' => '🍹']; echo $iconos[$cat['nombre']] ?? '🍾'; ?></div>
            <div class="lq-cat-name"><?php echo htmlspecialchars($cat['nombre']); ?></div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<section id="catalogo" class="lq-sec">
    <div class="lq-sec-hd">
        <div><div class="lq-sec-lbl"><div class="lq-sec-lbl-dash"></div><span>Selección curada</span></div><div class="lq-sec-title">Productos<br>Destacados</div></div>
        <a href="#" class="lq-sec-link">Ver catálogo completo →</a>
    </div>
    <div class="lq-products">
        <?php if (!empty($destacados)): ?>
            <?php foreach ($destacados as $p): $imagen = !empty($p['imagen']) ? htmlspecialchars($p['imagen']) : 'https://images.pexels.com/photos/11271794/pexels-photo-11271794.jpeg'; ?>
            <div class="lq-product-card">
                <div class="lq-product-img"><img src="<?= $imagen ?>" alt="<?= htmlspecialchars($p['nombre']) ?>"></div>
                <div class="lq-product-body"><div class="lq-product-cat"><?= htmlspecialchars($p['categoria']) ?></div><div class="lq-product-name"><?= htmlspecialchars($p['nombre']) ?></div><div class="lq-product-footer"><div class="lq-product-price">$<?= number_format((float)$p['precio_venta'], 2) ?></div></div></div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="lq-empty">Nuestras reservas exclusivas se están actualizando. Vuelve pronto.</div>
        <?php endif; ?>
    </div>
</section>

<div class="lq-exp">
    <div class="lq-exp-content">
        <div class="lq-exp-tag">La experiencia Liquour</div>
        <h2>Más que un<br><em>licor</em>, un ritual</h2>
        <p>Cada botella en nuestra colección cuenta una historia de territorio, tiempo y maestría artesanal. Nuestros expertos seleccionan solo lo que supera nuestros estrictos estándares de calidad.</p>
        <div class="lq-exp-nums"><div><div class="lq-exp-n">38°</div><div class="lq-exp-nl">Temperatura ideal</div></div><div><div class="lq-exp-n">48h</div><div class="lq-exp-nl">Curaduría por lote</div></div><div><div class="lq-exp-n">0%</div><div class="lq-exp-nl">Intermediarios</div></div></div>
    </div>
    <div class="lq-exp-visual">
        <svg class="lq-bottle-mini" viewBox="0 0 200 400"><defs><linearGradient id="glassMini" x1="0" y1="0" x2="1" y2="0"><stop offset="0%" stop-color="#111"/><stop offset="50%" stop-color="#2a1f10"/><stop offset="100%" stop-color="#111"/></linearGradient><linearGradient id="liquidMini" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="#b57b2a"/><stop offset="100%" stop-color="#5a3a0c"/></linearGradient></defs><rect x="80" y="10" width="40" height="25" rx="4" fill="#C5A059"/><rect x="75" y="35" width="50" height="90" rx="10" fill="url(#glassMini)"/><path d="M60 120 Q50 160 50 200 L50 340 Q50 360 70 365 L130 365 Q150 360 150 340 L150 200 Q150 160 140 120 Z" fill="url(#glassMini)"/><path d="M55 220 L55 340 Q55 355 70 358 L130 358 Q145 355 145 340 L145 220 Z" fill="url(#liquidMini)" opacity="0.85"/><text x="100" y="250" text-anchor="middle" fill="#C5A059" font-size="10" font-family="serif">PREMIUM</text></svg>
    </div>
</div>

<section class="lq-process">
    <div class="lq-sec-hd" style="margin-bottom: 0;"><div><div class="lq-sec-lbl"><div class="lq-sec-lbl-dash"></div><span>Cómo trabajamos</span></div><div class="lq-sec-title">Del productor<br>a tu mesa</div></div></div>
    <div class="lq-process-grid"><div class="lq-proc-step"><div class="lq-proc-n">01</div><div class="lq-proc-line"></div><div class="lq-proc-title">Selección en origen</div><p class="lq-proc-desc">Visitamos destilerías en más de 35 países para elegir personalmente cada lote con criterio de experto.</p></div><div class="lq-proc-step"><div class="lq-proc-n">02</div><div class="lq-proc-line"></div><div class="lq-proc-title">Análisis de calidad</div><p class="lq-proc-desc">Cada botella pasa por un proceso de cata con nuestro panel de maestros catadores internacionalmente certificados.</p></div><div class="lq-proc-step"><div class="lq-proc-n">03</div><div class="lq-proc-line"></div><div class="lq-proc-title">Almacenaje premium</div><p class="lq-proc-desc">Bodegas con temperatura y humedad controladas para preservar cada nota aromática en su punto óptimo.</p></div><div class="lq-proc-step"><div class="lq-proc-n">04</div><div class="lq-proc-line"></div><div class="lq-proc-title">Entrega garantizada</div><p class="lq-proc-desc">Embalaje especializado y cadena de frío para que cada botella llegue en condiciones perfectas a tu puerta.</p></div></div>
</section>

<section id="nosotros" class="lq-sec">
    <div class="lq-sec-hd"><div><div class="lq-sec-lbl"><div class="lq-sec-lbl-dash"></div><span>Lo que dicen</span></div><div class="lq-sec-title">Voces de<br>nuestros clientes</div></div></div>
    <div class="lq-testi-grid"><div class="lq-testi-card"><div class="lq-stars"><div class="lq-star"></div><div class="lq-star"></div><div class="lq-star"></div><div class="lq-star"></div><div class="lq-star"></div></div><div class="lq-testi-text">"La selección de whiskies es sencillamente inigualable. Encontré etiquetas que no había visto en ninguna otra tienda de la región."</div><div class="lq-testi-divider"></div><div class="lq-testi-author">Carlos M.</div><div class="lq-testi-role">Coleccionista · San Salvador</div></div><div class="lq-testi-card"><div class="lq-stars"><div class="lq-star"></div><div class="lq-star"></div><div class="lq-star"></div><div class="lq-star"></div><div class="lq-star"></div></div><div class="lq-testi-text">"El asesoramiento de su equipo fue excepcional. Me ayudaron a elegir el maridaje perfecto para una cena de negocios muy importante."</div><div class="lq-testi-divider"></div><div class="lq-testi-author">Ana R.</div><div class="lq-testi-role">Directora Ejecutiva · Guatemala</div></div><div class="lq-testi-card"><div class="lq-stars"><div class="lq-star"></div><div class="lq-star"></div><div class="lq-star"></div><div class="lq-star"></div><div class="lq-star"></div></div><div class="lq-testi-text">"Compramos para todos los eventos corporativos de nuestra empresa. La calidad y presentación siempre impresionan a nuestros invitados."</div><div class="lq-testi-divider"></div><div class="lq-testi-author">Roberto V.</div><div class="lq-testi-role">Gerente de Eventos · Honduras</div></div></div>
</section>

<section id="maridaje" class="lq-feats-section">
    <div class="lq-feats-grid"><div class="lq-feat"><div class="lq-feat-n">01</div><div class="lq-feat-line"></div><div class="lq-feat-title">Colección Premium</div><p class="lq-feat-desc">Whiskies de malta, rones añejos y reservas exclusivas de las destilerías más reconocidas del mundo.</p></div><div class="lq-feat"><div class="lq-feat-n">02</div><div class="lq-feat-line"></div><div class="lq-feat-title">Calidad Garantizada</div><p class="lq-feat-desc">Botellas 100% auténticas conservadas bajo los más altos estándares de temperatura y humedad controlada.</p></div><div class="lq-feat"><div class="lq-feat-n">03</div><div class="lq-feat-line"></div><div class="lq-feat-title">Asesoría Experta</div><p class="lq-feat-desc">Especialistas certificados te guiarán para encontrar el maridaje perfecto para cada ocasión especial.</p></div></div>
</section>

<footer class="lq-footer">
    <div class="lq-footer-top"><div><div class="lq-ft-logo">LI<span>QUO</span>UR</div><p class="lq-ft-tagline">La selección más exclusiva de licores premium para paladares que exigen excelencia desde 1987.</p><div class="lq-ft-social"><button class="lq-ft-soc"><i class="fa-brands fa-instagram"></i></button><button class="lq-ft-soc"><i class="fa-brands fa-facebook-f"></i></button></div></div><div><div class="lq-ft-col-title">Nuestra Cava</div><ul class="lq-ft-links"><li><a href="#catalogo">Whiskies</a></li><li><a href="#catalogo">Rones Añejos</a></li><li><a href="#catalogo">Ediciones Limitadas</a></li></ul></div><div><div class="lq-ft-col-title">Experiencia</div><ul class="lq-ft-links"><li><a href="#nosotros">Nuestra Historia</a></li><li><a href="#maridaje">Servicio VIP</a></li></ul></div></div>
    <div class="lq-footer-bottom"><div class="lq-ft-copy">© 2026 Liquour · Arte & Tradición en Destilados</div></div>
</footer>

<script>
    class LiquidGradient {
        constructor() {
            this.scene = new THREE.Scene();
            this.camera = new THREE.OrthographicCamera(-1, 1, 1, -1, 0, 1);
            this.renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
            this.renderer.setSize(window.innerWidth, window.innerHeight);
            this.renderer.domElement.id = 'webGLApp';
            document.body.insertBefore(this.renderer.domElement, document.body.firstChild);
            this.mouse = new THREE.Vector2(0.5, 0.5);
            this.time = 0;
            this.colors = {
                color1: new THREE.Color(0x1A0B00),
                color2: new THREE.Color(0x4A2000),
                color3: new THREE.Color(0x0D0D0D)
            };
            this.init();
            this.animate();
            window.addEventListener('resize', () => this.onResize());
            window.addEventListener('mousemove', (e) => this.onMouseMove(e));
        }

        init() {
            const geometry = new THREE.PlaneGeometry(2, 2);
            this.uniforms = {
                uTime: { value: 0 },
                uMouse: { value: new THREE.Vector2(0.5, 0.5) },
                uColor1: { value: this.colors.color1 },
                uColor2: { value: this.colors.color2 },
                uColor3: { value: this.colors.color3 }
            };
            this.material = new THREE.ShaderMaterial({
                uniforms: this.uniforms,
                vertexShader: `varying vec2 vUv; void main() { vUv = uv; gl_Position = vec4(position, 1.0); }`,
                fragmentShader: `
                    uniform float uTime;
                    uniform vec2 uMouse;
                    uniform vec3 uColor1;
                    uniform vec3 uColor2;
                    uniform vec3 uColor3;
                    varying vec2 vUv;
                    
                    void main() {
                        vec2 st = vUv;
                        float time = uTime * 0.08;
                        
                        float noise = sin(st.x * 2.5 + time) * cos(st.y * 2.0 + time);
                        noise += sin(st.y * 3.5 - time * 1.0) * 0.4;
                        
                        float dist = distance(st, uMouse);
                        noise += (1.0 - smoothstep(0.0, 0.6, dist)) * 0.25;
                        
                        vec3 color = mix(uColor1, uColor2, noise * 0.8);
                        color = mix(color, uColor3, sin(time * 0.5 + noise) * 0.5);
                        
                        color = color * 0.7;
                        
                        gl_FragColor = vec4(color, 1.0);
                    }
                `
            });
            this.scene.add(new THREE.Mesh(geometry, this.material));
        }

        setColors(color1Hex, color2Hex, color3Hex) {
            this.colors.color1.set(color1Hex);
            this.colors.color2.set(color2Hex);
            this.colors.color3.set(color3Hex);
            this.uniforms.uColor1.value = this.colors.color1;
            this.uniforms.uColor2.value = this.colors.color2;
            this.uniforms.uColor3.value = this.colors.color3;
        }

        onMouseMove(e) {
            this.mouse.x = e.clientX / window.innerWidth;
            this.mouse.y = 1.0 - (e.clientY / window.innerHeight);
            const cursor = document.querySelector('.custom-cursor');
            if(cursor) {
                cursor.style.left = e.clientX + 'px';
                cursor.style.top = e.clientY + 'px';
            }
        }

        onResize() {
            this.renderer.setSize(window.innerWidth, window.innerHeight);
        }

        animate() {
            this.time += 0.03;
            this.uniforms.uTime.value = this.time;
            this.uniforms.uMouse.value.lerp(this.mouse, 0.08);
            this.renderer.render(this.scene, this.camera);
            requestAnimationFrame(() => this.animate());
        }
    }

    function rgbToHex(r, g, b) {
        return "#" + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1).toUpperCase();
    }

    function hexToRgb(hex) {
        let result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
        return result ? { r: parseInt(result[1], 16), g: parseInt(result[2], 16), b: parseInt(result[3], 16) } : null;
    }

    function updatePreviewAndHex(colorNum, r, g, b) {
        const hex = rgbToHex(r, g, b);
        document.getElementById(`previewColor${colorNum}`).style.backgroundColor = hex;
        document.getElementById(`hex${colorNum}`).value = hex;
        document.getElementById(`r${colorNum}`).value = r;
        document.getElementById(`g${colorNum}`).value = g;
        document.getElementById(`b${colorNum}`).value = b;
        return hex;
    }

    const cursorDiv = document.createElement('div');
    cursorDiv.className = 'custom-cursor';
    document.body.appendChild(cursorDiv);
    
    let liquidGradient;
    
    window.onload = () => { 
        liquidGradient = new LiquidGradient();
        
        const btn = document.getElementById('colorPaletteBtn');
        const modal = document.getElementById('colorModal');
        const modalClose = document.getElementById('modalClose');
        const applyBtn = document.getElementById('applyColors');
        const resetBtn = document.getElementById('resetColors');
        
        btn.addEventListener('click', () => modal.classList.add('active'));
        modalClose.addEventListener('click', () => modal.classList.remove('active'));
        modal.addEventListener('click', (e) => { if (e.target === modal) modal.classList.remove('active'); });
        
        const r1 = document.getElementById('r1'), g1 = document.getElementById('g1'), b1 = document.getElementById('b1'), hex1 = document.getElementById('hex1');
        const r2 = document.getElementById('r2'), g2 = document.getElementById('g2'), b2 = document.getElementById('b2'), hex2 = document.getElementById('hex2');
        const r3 = document.getElementById('r3'), g3 = document.getElementById('g3'), b3 = document.getElementById('b3'), hex3 = document.getElementById('hex3');
        
        function syncColor1() { updatePreviewAndHex(1, parseInt(r1.value)||0, parseInt(g1.value)||0, parseInt(b1.value)||0); }
        function syncColor2() { updatePreviewAndHex(2, parseInt(r2.value)||0, parseInt(g2.value)||0, parseInt(b2.value)||0); }
        function syncColor3() { updatePreviewAndHex(3, parseInt(r3.value)||0, parseInt(g3.value)||0, parseInt(b3.value)||0); }
        
        r1.addEventListener('input', syncColor1); g1.addEventListener('input', syncColor1); b1.addEventListener('input', syncColor1);
        r2.addEventListener('input', syncColor2); g2.addEventListener('input', syncColor2); b2.addEventListener('input', syncColor2);
        r3.addEventListener('input', syncColor3); g3.addEventListener('input', syncColor3); b3.addEventListener('input', syncColor3);
        
        hex1.addEventListener('input', () => { let rgb = hexToRgb(hex1.value); if(rgb) updatePreviewAndHex(1, rgb.r, rgb.g, rgb.b); });
        hex2.addEventListener('input', () => { let rgb = hexToRgb(hex2.value); if(rgb) updatePreviewAndHex(2, rgb.r, rgb.g, rgb.b); });
        hex3.addEventListener('input', () => { let rgb = hexToRgb(hex3.value); if(rgb) updatePreviewAndHex(3, rgb.r, rgb.g, rgb.b); });
        
        document.getElementById('copyHex1').addEventListener('click', () => navigator.clipboard.writeText(hex1.value));
        document.getElementById('copyHex2').addEventListener('click', () => navigator.clipboard.writeText(hex2.value));
        document.getElementById('copyHex3').addEventListener('click', () => navigator.clipboard.writeText(hex3.value));
        
        applyBtn.addEventListener('click', () => {
            if(liquidGradient) liquidGradient.setColors(hex1.value, hex2.value, hex3.value);
            modal.classList.remove('active');
        });
        
        resetBtn.addEventListener('click', () => {
            updatePreviewAndHex(1, 26, 11, 0);
            updatePreviewAndHex(2, 74, 32, 0);
            updatePreviewAndHex(3, 13, 13, 13);
            if(liquidGradient) liquidGradient.setColors('#1A0B00', '#4A2000', '#0D0D0D');
        });
    };
</script>
<script src="../JS/Principal.js"></script>
</body>
</html>