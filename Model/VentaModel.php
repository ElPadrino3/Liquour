<?php
class VentaModel {
    private $db;

    public function __construct($db_conexion) {
        $this->db = $db_conexion;
    }

    public function obtenerDatosFiltrados($tipo, $busqueda, $vendedor) {
        $where = " WHERE 1=1 ";
        $params = [];

        if (!empty($busqueda)) {
            $where .= " AND (p.nombre LIKE :busqueda OR p.codigo_barras LIKE :busqueda) ";
            $params[':busqueda'] = "%$busqueda%";
        }

        if (!empty($vendedor)) {
            if ($tipo == 'ventas') {
                $where .= " AND u.nombre = :vendedor ";
            } else if ($tipo == 'compras') {
                $where .= " AND prov.nombre = :vendedor ";
            }
            $params[':vendedor'] = $vendedor;
        }

        if ($tipo == 'compras') {
            $sql = "SELECT 
                        c.id_compra as id, 
                        DATE(c.fecha) as fecha, 
                        prov.nombre as proveedor, 
                        p.nombre as producto, 
                        dc.cantidad, 
                        dc.precio_compra as precio, 
                        dc.subtotal
                    FROM detalle_compras dc
                    JOIN compras c ON dc.id_compra = c.id_compra
                    JOIN productos p ON dc.id_producto = p.id_producto
                    JOIN proveedores prov ON dc.id_proveedor = prov.id_proveedor" 
                    . $where . " ORDER BY c.fecha DESC";
        } else {
            $sql = "SELECT 
                        v.id_venta as id, 
                        DATE(v.fecha) as fecha, 
                        u.nombre as vendedor, 
                        p.nombre as producto, 
                        dv.cantidad, 
                        dv.precio, 
                        dv.subtotal
                    FROM detalle_ventas dv
                    JOIN ventas v ON dv.id_venta = v.id_venta
                    JOIN productos p ON dc.id_producto = p.id_producto
                    JOIN usuarios u ON v.id_usuario = u.id_usuario" 
                    . $where . " ORDER BY v.fecha DESC";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}