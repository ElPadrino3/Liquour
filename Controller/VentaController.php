<?php
require_once '../vendor/autoload.php';
require_once '../Config/Liquour_bdd.php';
require_once '../Models/VentaModel.php';

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
                'margin_top' => 35
            ]);

            $html = '
            <style>
                body { font-family: "Montserrat", sans-serif; color: #1a1a1a; }
                .header { text-align: center; border-bottom: 2px solid #C5A059; padding-bottom: 10px; }
                .logo { font-size: 24px; font-weight: bold; letter-spacing: 3px; color: #1a1a1a; }
                .title { font-size: 12px; color: #C5A059; text-transform: uppercase; margin-top: 5px; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th { background: #1a1a1a; color: #C5A059; padding: 10px; text-align: left; font-size: 10px; text-transform: uppercase; }
                td { padding: 10px; border-bottom: 1px dotted #ccc; font-size: 9px; }
                .total-box { margin-top: 20px; text-align: right; font-size: 12px; border-top: 2px solid #C5A059; padding-top: 10px; }
            </style>
            <div class="header">
                <div class="logo">LIQUOUR</div>
                <div class="title">' . $tituloReporte . '</div>
                <div style="font-size: 8px; color: #666;">Fecha: ' . date('d/m/Y H:i A') . '</div>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>ID / Fecha</th>
                        <th>' . ($tipoReporte == 'ventas' ? 'Vendedor' : 'Proveedor') . '</th>
                        <th>Producto</th>
                        <th style="text-align:center">Cant.</th>
                        <th>Precio</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>';

            $totalAcumulado = 0;
            foreach ($datos as $row) {
                $totalAcumulado += $row['subtotal'];
                $html .= '
                <tr>
                    <td>' . ($row['id'] ?? $row['orden']) . '<br><small>' . $row['fecha'] . '</small></td>
                    <td>' . ($tipoReporte == 'ventas' ? $row['vendedor'] : $row['proveedor']) . '</td>
                    <td>' . $row['producto'] . '</td>
                    <td style="text-align:center">' . $row['cantidad'] . '</td>
                    <td>$' . number_format($row['precio'], 2) . '</td>
                    <td style="font-weight:bold;">$' . number_format($row['subtotal'], 2) . '</td>
                </tr>';
            }

            $html .= '
                </tbody>
            </table>
            <div class="total-box">
                <strong>MONTO TOTAL DEL REPORTE: $' . number_format($totalAcumulado, 2) . '</strong>
            </div>';

            $mpdf->WriteHTML($html);
            $mpdf->Output("Reporte_{$tipoReporte}_" . date('Ymd') . ".pdf", 'I');

        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    private function obtenerTitulo($tipo) {
        $titulos = [
            'ventas'     => 'HISTORIAL DE VENTAS',
            'productos'  => 'PRODUCTOS MÁS VENDIDOS',
            'inventario' => 'REPORTE DE INVENTARIO ACTUAL',
            'compras'    => 'HISTORIAL DE COMPRAS'
        ];
        return $titulos[$tipo] ?? 'REPORTE GENERAL';
    }
}

if (isset($_GET['reporte'])) {
    $controller = new ReporteController();
    $controller->exportarPDF();
}