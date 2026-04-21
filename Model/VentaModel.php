<?php
class VentaModel {
    private $db;

    public function __construct($db_conexion) {
        $this->db = $db_conexion;
    }

    public function obtenerVentaPorId($id) {
        $sql = "SELECT * FROM ventas WHERE id_venta = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(); 
    }

    public function obtenerDetallesVenta($id) {
        $sql = "SELECT dv.*, p.nombre_producto 
                FROM detalle_ventas dv
                LEFT JOIN productos p ON dv.id_producto = p.id_producto
                WHERE dv.id_venta = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetchAll(); 
    }
public function obtenerDatosFiltrados($tipo, $busqueda, $vendedor) {
    $where = " WHERE 1=1 ";
    $params = [];

    if (!empty($busqueda)) {
        $where .= " AND (p.nombre LIKE :busqueda OR p.codigo_barras LIKE :busqueda) ";
        $params[':busqueda'] = "%$busqueda%";
    }

    if (!empty($vendedor) && ($tipo == 'ventas' || $tipo == 'compras')) {
        $where .= ($tipo == 'ventas') ? " AND u.nombre = :vendedor " : " AND prov.nombre = :vendedor ";
        $params[':vendedor'] = $vendedor;
    }

    switch ($tipo) {
        case 'ventas':
            $sql = "SELECT DATE(v.fecha) as fecha, v.id_venta as id, p.nombre as producto, dv.cantidad, dv.precio, dv.subtotal, u.nombre as vendedor 
                    FROM detalle_ventas dv 
                    JOIN ventas v ON dv.id_venta = v.id_venta 
                    JOIN productos p ON dv.id_producto = p.id_producto 
                    JOIN usuarios u ON v.id_usuario = u.id_usuario" . $where . " ORDER BY v.fecha DESC";
            break;
    }

    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}