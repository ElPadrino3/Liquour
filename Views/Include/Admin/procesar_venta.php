<?php
session_start();
require_once '../../../Config/Liquour_bdd.php';

header('Content-Type: application/json');

$inputJSON = file_get_contents('php://input');
$data = json_decode($inputJSON, true);

if (!$data || empty($data['carrito'])) {
    echo json_encode(['success' => false, 'error' => 'Datos de venta inválidos o carrito vacío.']);
    exit;
}

try {
    $db = new BDD();
    $conn = $db->conectar();
    
    $conn->beginTransaction();

    // Obtenemos el ID del usuario que está logueado
    $id_usuario_actual = isset($_SESSION['id_usuario']) ? $_SESSION['id_usuario'] : null; 

    // Insertamos la venta asegurándonos de usar 'id_usuario' como lo pide tu perfil
    if ($id_usuario_actual) {
        $stmtVenta = $conn->prepare("INSERT INTO ventas (fecha, total, id_usuario) VALUES (NOW(), :total, :id_usuario)");
        $stmtVenta->execute([
            ':total' => (float)$data['total'],
            ':id_usuario' => $id_usuario_actual
        ]);
    } else {
        // Si por alguna razón no hay sesión, se guarda sin usuario (no recomendado, pero evita que falle)
        $stmtVenta = $conn->prepare("INSERT INTO ventas (fecha, total) VALUES (NOW(), :total)");
        $stmtVenta->execute([
            ':total' => (float)$data['total']
        ]);
    }
    
    $id_venta = $conn->lastInsertId();

    $stmtDetalle = $conn->prepare("INSERT INTO detalle_ventas (id_venta, id_producto, cantidad, precio, subtotal) VALUES (:id_venta, :id_producto, :cantidad, :precio, :subtotal)");
    
    $stmtUpdateStock = $conn->prepare("UPDATE productos SET stock = stock - :cantidad_restar WHERE id_producto = :id_prod AND stock >= :cantidad_validar");

    foreach ($data['carrito'] as $item) {
        if (!isset($item['id']) || !isset($item['qty'])) {
             throw new Exception("Faltan datos del producto en el carrito.");
        }

        $subtotal_item = (int)$item['qty'] * (float)$item['price'];

        $stmtDetalle->execute([
            ':id_venta' => $id_venta,
            ':id_producto' => (int)$item['id'],
            ':cantidad' => (int)$item['qty'],
            ':precio' => (float)$item['price'],
            ':subtotal' => $subtotal_item
        ]);

        $stmtUpdateStock->execute([
            ':cantidad_restar' => (int)$item['qty'],
            ':id_prod' => (int)$item['id'],
            ':cantidad_validar' => (int)$item['qty']
        ]);

        if ($stmtUpdateStock->rowCount() === 0) {
            throw new Exception("Stock insuficiente o producto no encontrado: " . $item['name']);
        }
    }

    $conn->commit();
    
    echo json_encode(['success' => true, 'mensaje' => 'Venta registrada y stock descontado con éxito']);

} catch (Exception $e) {
    if (isset($conn) && $conn->inTransaction()) {
        $conn->rollBack();
    }
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>