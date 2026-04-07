<?php
error_reporting(0);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');
require_once '../../../Config/Liquour_bdd.php';

try {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (!$data || empty($data['carrito'])) {
        throw new Exception("El carrito está vacío.");
    }

    $db = new BDD();
    $conexion = $db->conectar();
    $conexion->beginTransaction();

    $id_usuario = $_SESSION['id_usuario'] ?? 1;
    $total = (float)$data['total'];

    $stmtVenta = $conexion->prepare("INSERT INTO ventas (total, id_usuario) VALUES (?, ?)");
    $stmtVenta->execute([$total, $id_usuario]);
    $id_venta = $conexion->lastInsertId();

    $stmtDetalle = $conexion->prepare("INSERT INTO detalle_ventas (id_venta, id_producto, cantidad, precio, subtotal) VALUES (?, ?, ?, ?, ?)");
    $stmtStock = $conexion->prepare("UPDATE productos SET stock = stock - ? WHERE id_producto = ?");

    foreach ($data['carrito'] as $item) {
        $id_producto = (int)$item['id'];
        $cantidad = (int)$item['qty'];
        $precio = (float)$item['price'];
        $subtotal = $cantidad * $precio;

        $stmtDetalle->execute([$id_venta, $id_producto, $cantidad, $precio, $subtotal]);
        $stmtStock->execute([$cantidad, $id_producto]);
    }

    $conexion->commit();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    if (isset($conexion) && $conexion->inTransaction()) {
        $conexion->rollBack();
    }
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>