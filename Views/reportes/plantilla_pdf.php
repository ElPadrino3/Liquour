<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 100px 25px; }
        header { position: fixed; top: -60px; left: 0px; right: 0px; height: 50px; text-align: center; border-bottom: 1px solid #333; }
        footer { position: fixed; bottom: -60px; left: 0px; right: 0px; height: 30px; text-align: center; font-size: 10px; color: #777; }
        
        body { font-family: 'Helvetica', sans-serif; font-size: 11px; color: #333; }
        .title-container { text-align: center; margin-bottom: 20px; }
        .report-title { font-size: 18px; font-weight: bold; text-transform: uppercase; color: #2c3e50; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #2c3e50; color: white; padding: 8px; text-transform: uppercase; font-size: 10px; }
        td { border: 1px solid #bdc3c7; padding: 6px; text-align: left; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .total-box { margin-top: 20px; text-align: right; font-size: 14px; font-weight: bold; border-top: 2px solid #2c3e50; padding-top: 5px; }
        
        .metadata { font-size: 9px; margin-bottom: 10px; color: #555; }
    </style>
</head>
<body>

    <header>
        <strong>LIQUOUR STORE</strong> - Sistema de Control de Inventario y Ventas
    </header>

    <footer>
        Página <?php echo date('d/m/Y H:i:s'); ?> - Reporte generado por el sistema.
    </footer>

    <main>
        <div class="title-container">
            <div class="report-title"><?php echo $tituloReporte; ?></div>
        </div>

        <div class="metadata">
            <strong>Filtros aplicados:</strong> <br>
            Búsqueda: <?php echo $busqueda ?: 'Ninguna'; ?> | 
            Vendedor/Relación: <?php echo $vendedor ?: 'Todos'; ?>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>ID / Código</th>
                    <th>Producto</th>
                    <th class="text-center">Cant.</th>
                    <th class="text-right">Precio</th>
                    <th class="text-right">Subtotal</th>
                    <th>Usuario / Relación</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $granTotal = 0;
                if (!empty($datos)): 
                    foreach ($datos as $row): 
                        $precioNum = (float)str_replace(['$', ','], '', $row['precio']);
                        $subtotalNum = (float)str_replace(['$', ','], '', $row['subtotal']);
                        $granTotal += $subtotalNum;
                ?>
                <tr>
                    <td><?php echo $row['fecha']; ?></td>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['producto']; ?></td>
                    <td class="text-center"><?php echo $row['cantidad']; ?></td>
                    <td class="text-right">$<?php echo number_format($precioNum, 2); ?></td>
                    <td class="text-right">$<?php echo number_format($subtotalNum, 2); ?></td>
                    <td><?php echo $row['vendedor']; ?></td>
                </tr>
                <?php endforeach; else: ?>
                <tr>
                    <td colspan="7" style="text-align:center;">No se encontraron registros con los filtros aplicados.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <?php if ($granTotal > 0): ?>
        <div class="total-box">
            TOTAL DEL REPORTE: $<?php echo number_format($granTotal, 2); ?>
        </div>
        <?php endif; ?>
    </main>

</body>
</html>