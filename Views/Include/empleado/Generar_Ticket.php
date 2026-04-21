<?php
// Carga mPDF desde la carpeta vendor de Composer
require_once __DIR__ . '/../../../vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    $carrito = $data['carrito'] ?? [];
    $subtotal = $data['subtotal'] ?? 0;
    $descuento = $data['descuento'] ?? 0;
    $total = $data['total'] ?? 0;
    $recibido = $data['recibido'] ?? 0;
    $cambio = $data['cambio'] ?? 0;
    $vendedor = $data['vendedor'] ?? 'Usuario';
    $fecha = $data['fecha'] ?? date('d/m/Y H:i');
    $accion = $data['accion'] ?? 'I'; // 'I' para Imprimir/Ver, 'D' para Descargar

    $itemsHTML = '';
    foreach ($carrito as $item) {
        $sub = $item['qty'] * $item['price'];
        $itemsHTML .= '
            <tr>
                <td>'.$item['qty'].'x</td>
                <td>'.$item['name'].'</td>
                <td class="t-right">$'.number_format($sub, 2).'</td>
            </tr>';
    }

    $descuentoRow = '';
    if ($descuento > 0) {
        $descuentoRow = '<div class="row discount"><span>Descuento:</span><span>-$'.number_format($descuento, 2).'</span></div>';
    }

    $html = '
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <style>
            body { font-family: "Helvetica", sans-serif; margin: 0; padding: 0; font-size: 12px; color: #1a1a1a; }
            .t-header { text-align: center; border-bottom: 1px dashed #1a1a1a; padding-bottom: 10px; margin-bottom: 10px; }
            .t-header h1 { font-size: 24px; margin: 0; letter-spacing: 1px; }
            .t-header p { margin: 5px 0 0; font-weight: bold; }
            .t-vendedor { font-size: 10px; color: #444; margin-top: 5px; font-style: italic; }
            .t-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; font-size: 11px; }
            .t-table th { text-align: left; border-bottom: 1px solid #1a1a1a; padding-bottom: 5px; }
            .t-table td { padding: 5px 0; border-bottom: 1px solid #ddd; }
            .t-right { text-align: right; }
            .t-totals { font-size: 12px; line-height: 1.5; border-bottom: 1px dashed #1a1a1a; padding-bottom: 10px; margin-bottom: 10px; }
            .row { width: 100%; clear: both; display: block; overflow: hidden; margin-bottom: 2px; }
            .row span:first-child { float: left; }
            .row span:last-child { float: right; }
            .discount { color: #555; }
            .grand-total { font-size: 16px; font-weight: bold; margin-top: 5px; }
            .t-footer { text-align: center; font-size: 10px; font-style: italic; margin-top: 10px; }
        </style>
    </head>
    <body>
        <div class="t-header">
            <h1>LIQUOUR</h1>
            <p>COMPROBANTE DE COMPRA</p>
            <p style="font-weight: normal;">'.$fecha.'</p>
            <div class="t-vendedor">Atendido por: '.$vendedor.'</div>
        </div>
        <table class="t-table">
            <thead><tr><th>Cant.</th><th>Desc.</th><th class="t-right">Importe</th></tr></thead>
            <tbody>'.$itemsHTML.'</tbody>
        </table>
        <div class="t-totals">
            <div class="row"><span>Subtotal:</span><span>$'.number_format($subtotal, 2).'</span></div>
            '.$descuentoRow.'
            <div class="row grand-total"><span>TOTAL:</span><span>$'.number_format($total, 2).'</span></div>
        </div>
        <div class="t-totals" style="border: none;">
            <div class="row"><span>Efectivo:</span><span>$'.number_format($recibido, 2).'</span></div>
            <div class="row"><span>Cambio:</span><span>$'.number_format($cambio, 2).'</span></div>
        </div>
        <div class="t-footer">
            <p>¡Gracias por su compra!</p>
        </div>
    </body>
    </html>';

    $mpdf = new \Mpdf\Mpdf([
        'mode' => 'utf-8',
        'format' => [72, 150],
        'margin_left' => 4,
        'margin_right' => 4,
        'margin_top' => 5,
        'margin_bottom' => 5,
    ]);

    $mpdf->WriteHTML($html);
    $nombre_archivo = 'Ticket_Liquour_' . time() . '.pdf';
    
    $mpdf->Output($nombre_archivo, $accion);
}
?>