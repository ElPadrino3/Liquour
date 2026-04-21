<?php
require_once '../vendor/autoload.php';
require_once '../Config/Liquour_bdd.php';
require_once '../Models/VentaModel.php';

use Dompdf\Dompdf;
use Dompdf\Options;

/**
 */
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

            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true); 
            $options->set('defaultFont', 'Helvetica');

            $dompdf = new Dompdf($options);

            
            ob_start();
            $tituloReporte = $this->obtenerTitulo($tipoReporte);
            include '../Views/Reportes/plantilla_pdf.php';
            $html = ob_get_clean();

            $dompdf->loadHtml($html);
            $dompdf->setPaper('letter', 'portrait');
            $dompdf->render();

            $dompdf->stream("Reporte_{$tipoReporte}_" . date('Ymd') . ".pdf", ["Attachment" => false]);

        } catch (Exception $e) {
            die("Error al generar el reporte: " . $e->getMessage());
        }
    }

    /**
     */
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