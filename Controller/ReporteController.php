<?php
require_once '../vendor/autoload.php';
require_once '../Config/Liquour_bdd.php';
require_once '../Model/VentaModel.php';

class ReporteController {

    public function exportarPDF() {
        $tipoReporte = $_GET['reporte'] ?? 'ventas';
        $busqueda    = $_GET['busqueda'] ?? '';
        $vendedor    = $_GET['vendedor'] ?? '';

        try {
            $dbClass = new BDD();
            $conexion = $dbClass->conectar();
            $model = new VentaModel($conexion);

            $datos = $model->obtenerDatosFiltrados($tipoReporte, $busqueda, $vendedor);
            $tituloReporte = $this->obtenerTitulo($tipoReporte);

            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => 'Letter',
                'margin_top' => 15,
                'margin_header' => 0,
                'margin_footer' => 10
            ]);

            $mpdf->setAutoTopMargin = 'stretch';

            $html = '
            <style>
                body { font-family: "Montserrat", sans-serif; color: #4A4A4A; margin: 0; padding: 0; }
                .header-container { border-bottom: 3px solid #C5A059; padding-bottom: 5px; margin-bottom: 15px; width: 100%; border-collapse: collapse; }
                .header-right { text-align: right; vertical-align: middle; }
                .logo-text { font-size: 24px; font-weight: bold; color: #C5A059; margin: 0; text-transform: uppercase; line-height: 1; }
                .tagline { font-size: 10px; color: #4A4A4A; margin: 2px 0; }
                .date-text { font-size: 9px; color: #1a1a1a; font-weight: bold; }
                
                .title-container { text-align: center; margin: 10px 0 20px 0; }
                .report-title { font-size: 16px; font-weight: bold; color: #1a1a1a; text-transform: uppercase; border-bottom: 2px solid #C5A059; display: inline-block; padding-bottom: 3px; }
                
                table { width: 100%; border-collapse: collapse; }
                th { background: #1a1a1a; color: #C5A059; padding: 10px; text-align: left; font-size: 9px; text-transform: uppercase; letter-spacing: 1px; }
                td { padding: 8px 10px; border-bottom: 1px solid #EFEFEF; font-size: 9px; color: #4A4A4A; }
                
                tr:nth-child(even) { background-color: #FDFBEE; }
                
                .td-id { color: #8F8F8F; font-size: 8px; }
                .td-id span { color: #C5A059; }
                .td-product { font-weight: 500; color: #1a1a1a; }
                .td-price { text-align: right; }
                .td-price sup { font-size: 7px; color: #C5A059; }
                .subtotal-val { font-weight: bold; color: #C5A059; }
                
                .footer { margin-top: 25px; text-align: right; border-top: 2px solid #C5A059; padding-top: 10px; }
                .total-label { font-size: 10px; font-weight: bold; text-transform: uppercase; margin-right: 15px; }
                .total-val { font-size: 13px; font-weight: bold; color: #C5A059; }
            </style>

            <table class="header-container">
                <tr>
                    <td width="50%" style="border:none; padding:0; vertical-align: top;">
                        <img src="../Assets/IMG/Logo.jpeg" width="75">
                    </td>
                    <td width="50%" class="header-right" style="border:none; padding:0; vertical-align: top;">
                        <div class="logo-text">LIQUOUR STORE</div>
                        <div class="tagline">Premium Collection & Inventory</div>
                        <div class="date-text">Fecha de emisión: ' . date('d/m/Y H:i A') . '</div>
                    </td>
                </tr>
            </table>

            <div class="title-container">
                <div class="report-title">' . $tituloReporte . '</div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th width="12%">FECHA</th>
                        <th width="12%">ID / CÓDIGO</th>
                        <th width="31%">PRODUCTO</th>
                        <th width="7%" style="text-align:center">CANT.</th>
                        <th width="12%" style="text-align:right">PRECIO UNIT.</th>
                        <th width="12%" style="text-align:right">SUBTOTAL</th>
                        <th width="14%">' . ($tipoReporte == 'ventas' ? 'USUARIO' : 'PROVEEDOR') . '</th>
                    </tr>
                </thead>
                <tbody>';

            $totalAcumulado = 0;
            foreach ($datos as $row) {
                $totalAcumulado += $row['subtotal'];
                $html .= '
                <tr>
                    <td>' . $row['fecha'] . '</td>
                    <td class="td-id"># <span>' . ($row['id'] ?? $row['orden']) . '</span></td>
                    <td class="td-product">' . $row['producto'] . '</td>
                    <td style="text-align:center">' . $row['cantidad'] . '</td>
                    <td class="td-price"><sup>$</sup>' . number_format($row['precio'], 2) . '</td>
                    <td class="td-price subtotal-val"><sup>$</sup>' . number_format($row['subtotal'], 2) . '</td>
                    <td>' . ($tipoReporte == 'ventas' ? $row['vendedor'] : $row['proveedor']) . '</td>
                </tr>';
            }

            $html .= '
                </tbody>
            </table>

            <div class="footer">
                <span class="total-label">Total Acumulado del Reporte:</span>
                <span class="total-val">$ ' . number_format($totalAcumulado, 2) . '</span>
            </div>';

            $mpdf->WriteHTML($html);
            $mpdf->Output("Reporte_{$tipoReporte}_" . date('Ymd_His') . ".pdf", 'I');

        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    private function obtenerTitulo($tipo) {
        $titulos = [
            'ventas'     => 'Historial de Ventas',
            'productos'  => 'Productos más Vendidos',
            'inventario' => 'Reporte de Inventario',
            'compras'    => 'Historial de Compras'
        ];
        return $titulos[$tipo] ?? 'Reporte General';
    }
}

if (isset($_GET['reporte'])) {
    $controller = new ReporteController();
    $controller->exportarPDF();
}