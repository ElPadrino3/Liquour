<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../Config/Liquour_bdd.php';

$db = new BDD();
$conexion = $db->conectar();

$search = $_POST['search'] ?? '';
$status = $_POST['status'] ?? '';
$provider = $_POST['provider'] ?? '';

$query = "SELECT 
            CONCAT('ORD-', c.id_compra) as orden,
            DATE(c.fecha) as fecha,
            prov.nombre as proveedor,
            p.nombre as producto,
            dc.cantidad as qty,
            dc.precio_compra as precio,
            dc.subtotal as total,
            'Recibido' as estado 
          FROM detalle_compras dc
          JOIN compras c ON dc.id_compra = c.id_compra
          JOIN productos p ON dc.id_producto = p.id_producto
          JOIN proveedores prov ON dc.id_proveedor = prov.id_proveedor";

$where = [];
$params = [];

if (!empty($search)) {
    $where[] = "(p.nombre LIKE :search OR prov.nombre LIKE :search OR c.id_compra LIKE :search)";
    $params['search'] = "%$search%";
}
if (!empty($status)) {
    $where[] = "'Recibido' = :status";
    $params['status'] = $status;
}
if (!empty($provider)) {
    $where[] = "prov.nombre = :provider";
    $params['provider'] = $provider;
}

if (!empty($where)) {
    $query .= " WHERE " . implode(" AND ", $where);
}
$query .= " ORDER BY c.fecha DESC";

$stmt = $conexion->prepare($query);
$stmt->execute($params);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    <div class="title">Reporte de Gestión de Compras</div>
    <div style="font-size: 8px; color: #666;">Generado el: ' . date('d/m/Y H:i A') . '</div>
</div>

<table>
    <thead>
        <tr>
            <th>Orden</th>
            <th>Fecha</th>
            <th>Proveedor</th>
            <th>Producto</th>
            <th>Qty</th>
            <th>Precio</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>';

$sum = 0;
foreach ($data as $row) {
    $sum += $row['total'];
    $html .= '
        <tr>
            <td>' . $row['orden'] . '</td>
            <td>' . $row['fecha'] . '</td>
            <td>' . $row['proveedor'] . '</td>
            <td>' . $row['producto'] . '</td>
            <td style="text-align:center">' . $row['qty'] . '</td>
            <td>$' . number_format($row['precio'], 2) . '</td>
            <td style="font-weight:bold;">$' . number_format($row['total'], 2) . '</td>
        </tr>';
}

$html .= '
    </tbody>
</table>
<div class="total-box">
    <strong>TOTAL COMPRADO EN REPORTE: $' . number_format($sum, 2) . '</strong>
</div>';

$mpdf->WriteHTML($html);
$mpdf->Output('../../Liquour/Model/Compras.php', 'I');