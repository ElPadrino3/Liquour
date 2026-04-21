<?php

require_once __DIR__ . "/../../Config/Liquour_bdd.php";
require_once __DIR__ . "/../../vendor/autoload.php";
require_once __DIR__ . "/../../Model/VentaModel.php";

use Dompdf\Dompdf;
use Dompdf\Options;

$db = new BDD();
$conexion = $db->conectar();

$tipoReporte = isset($_GET['reporte']) ? $_GET['reporte'] : 'ventas';
$busqueda = isset($_GET['busqueda']) ? $_GET['busqueda'] : '';

$logoPath = __DIR__ . '/../../Assets/IMG/Logo.jpeg';
$logoBase64 = '';
if (file_exists($logoPath)) {
    $logoData = base64_encode(file_get_contents($logoPath));
    $logoBase64 = 'data:image/jpeg;base64,' . $logoData;
}

$datos = [];
$tituloReporte = "";

switch ($tipoReporte) {
    case 'compras':
        $tituloReporte = "Historial de Compras";
        $stmt = $conexion->query("
            SELECT DATE(c.fecha) as fecha, c.id_compra as id, p.nombre as producto, 
                   dc.cantidad, dc.precio_compra as precio, dc.subtotal, prov.nombre as usuario_rel
            FROM detalle_compras dc
            JOIN compras c ON dc.id_compra = c.id_compra
            JOIN productos p ON dc.id_producto = p.id_producto
            JOIN proveedores prov ON dc.id_proveedor = prov.id_proveedor
            ORDER BY c.fecha DESC
        ");
        break;

    case 'productos':
        $tituloReporte = "Productos Más Vendidos";
        $stmt = $conexion->query("
            SELECT MAX(DATE(v.fecha)) as fecha, p.codigo_barras as id, p.nombre as producto, 
                   SUM(dv.cantidad) as cantidad, p.precio_venta as precio, SUM(dv.subtotal) as subtotal, '-' as usuario_rel
            FROM detalle_ventas dv
            JOIN productos p ON dv.id_producto = p.id_producto
            JOIN ventas v ON dv.id_venta = v.id_venta
            GROUP BY p.id_producto ORDER BY cantidad DESC
        ");
        break;

    case 'inventario':
        $tituloReporte = "Inventario de Almacén";
        $stmt = $conexion->query("
            SELECT DATE(NOW()) as fecha, p.codigo_barras as id, p.nombre as producto, 
                   p.stock as cantidad, p.precio_venta as precio, (p.stock * p.precio_venta) as subtotal, 'Almacén' as usuario_rel
            FROM productos p ORDER BY p.stock DESC
        ");
        break;

    case 'ventas':
    default:
        $tituloReporte = "Historial de Ventas";
        $stmt = $conexion->query("
            SELECT DATE(v.fecha) as fecha, v.id_venta as id, p.nombre as producto, 
                   dv.cantidad, dv.precio as precio, dv.subtotal, u.nombre as usuario_rel
            FROM detalle_ventas dv
            JOIN ventas v ON dv.id_venta = v.id_venta
            JOIN productos p ON dv.id_producto = p.id_producto
            JOIN usuarios u ON v.id_usuario = u.id_usuario
            ORDER BY v.fecha DESC
        ");
        break;
}
$datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$html = '
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        /* Paleta de colores aplicada */
        @page { margin: 120px 30px 80px 30px; }
        
        header { 
            position: fixed; top: -100px; left: 0px; right: 0px; height: 90px; 
            border-bottom: 3px solid #C5A059; padding-bottom: 10px;
        }
        
        footer { 
            position: fixed; bottom: -50px; left: 0px; right: 0px; height: 40px; 
            text-align: center; font-size: 9px; color: #4A4A4A; border-top: 1px solid #C5A059;
            padding-top: 10px;
        }
        
        body { font-family: "Helvetica", sans-serif; background-color: #FFFFFF; color: #1A1A1A; font-size: 10px; margin: 0; }
        
        .logo { float: left; width: 75px; height: 75px; border: 1px solid #C5A059; }
        
        .header-info { float: right; text-align: right; width: 350px; }
        .header-info h1 { margin: 0; color: #9A7A3F; font-size: 24px; text-transform: uppercase; letter-spacing: 1px; }
        .header-info p { margin: 2px 0; color: #4A4A4A; font-weight: bold; }

        .report-header { clear: both; text-align: center; margin-top: 30px; margin-bottom: 10px; }
        .report-header h2 { 
            display: inline-block; border-bottom: 2px solid #D4B577; 
            padding-bottom: 5px; color: #1A1A1A; text-transform: uppercase; font-size: 16px;
        }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { 
            background-color: #1A1A1A; color: #C5A059; padding: 12px 5px; 
            font-size: 9px; text-transform: uppercase; border: 1px solid #4A4A4A;
        }
        td { padding: 10px 5px; border-bottom: 1px solid #D4B577; color: #1A1A1A; }
        tr:nth-child(even) { background-color: #F5F5DC; } /* Color Cream de la paleta */

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .gold-text { color: #9A7A3F; font-weight: bold; }
        .id-cell { font-family: "Courier", monospace; color: #4A4A4A; }

        .total-container { margin-top: 30px; float: right; width: 280px; }
        .total-table { width: 100%; border: 2px solid #C5A059; }
        .total-table td { background: #1A1A1A; color: #F5F5DC; padding: 15px; font-size: 13px; border: none; }
        .total-amount { color: #C5A059; font-weight: bold; font-size: 16px; }
    </style>
</head>
<body>

<header>
    <div style="float:left;">
        ' . ($logoBase64 ? '<img src="'.$logoBase64.'" class="logo">' : '<div style="width:75px;height:75px;background:#1A1A1A;border:1px solid #C5A059;"></div>') . '
    </div>
    <div class="header-info">
        <h1>LIQUOUR STORE</h1>
        <p>Premium Collection & Inventory</p>
        <p>Fecha de emisión: ' . date("d/m/Y h:i A") . '</p>
    </div>
</header>

<footer>
    © ' . date("Y") . ' Liquour POS - Este documento es un reporte oficial de gestión interna. <br>
    Página generada por el sistema automatizado de reportes.
</footer>

<main>
    <div class="report-header">
        <h2>' . $tituloReporte . '</h2>
    </div>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>ID / Código</th>
                <th>Producto</th>
                <th class="text-center">Cant.</th>
                <th class="text-right">Precio Unit.</th>
                <th class="text-right">Subtotal</th>
                <th>Usuario / Relación</th>
            </tr>
        </thead>
        <tbody>';

$granTotal = 0;
foreach ($datos as $row) {
    $precio = (float)$row['precio'];
    $subtotal = (float)$row['subtotal'];
    $granTotal += $subtotal;
    
    $html .= '
    <tr>
        <td class="text-center">' . $row['fecha'] . '</td>
        <td class="text-center id-cell"># ' . $row['id'] . '</td>
        <td>' . htmlspecialchars($row['producto']) . '</td>
        <td class="text-center">' . $row['cantidad'] . '</td>
        <td class="text-right">$ ' . number_format($precio, 2) . '</td>
        <td class="text-right gold-text">$ ' . number_format($subtotal, 2) . '</td>
        <td>' . htmlspecialchars($row['usuario_rel']) . '</td>
    </tr>';
}

$html .= '
        </tbody>
    </table>

    <div class="total-container">
        <table class="total-table">
            <tr>
                <td>MONTO TOTAL DEL REPORTE</td>
                <td class="text-right total-amount">$ ' . number_format($granTotal, 2) . '</td>
            </tr>
        </table>
    </div>
</main>

</body>
</html>';

try {
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', true);
    $options->set('defaultFont', 'Helvetica');

    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('letter', 'portrait');
    $dompdf->render();

    $dompdf->stream("Reporte_" . $tipoReporte . "_" . date("dmY") . ".pdf", ["Attachment" => false]);
} catch (Exception $e) {
    echo "Hubo un error al generar el PDF: " . $e->getMessage();
}