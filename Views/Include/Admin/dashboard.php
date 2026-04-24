<?php
require_once '../../../Config/Liquour_bdd.php';

$bdd = new BDD();
$conn = $bdd->conectar();

$stmtHoy = $conn->query("SELECT COALESCE(SUM(total), 0) as total_hoy, COUNT(id_venta) as transacciones FROM ventas WHERE DATE(fecha) = CURDATE()");
$dataHoy = $stmtHoy->fetch();

$stmtMes = $conn->query("SELECT COALESCE(SUM(total), 0) as total_mes FROM ventas WHERE MONTH(fecha) = MONTH(CURDATE()) AND YEAR(fecha) = YEAR(CURDATE())");
$dataMes = $stmtMes->fetch();

$stmtTop = $conn->query("
    SELECT p.nombre, p.precio_venta, c.nombre as categoria, SUM(dv.cantidad) as unidades_vendidas
    FROM detalle_ventas dv
    JOIN productos p ON dv.id_producto = p.id_producto
    LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
    GROUP BY p.id_producto
    ORDER BY unidades_vendidas DESC
    LIMIT 5
");
$topProductos = $stmtTop->fetchAll();

$stmtStock = $conn->query("SELECT nombre, stock FROM productos WHERE stock <= 15 ORDER BY stock ASC LIMIT 4");
$stockCritico = $stmtStock->fetchAll();

$totalProductos = $conn->query("SELECT COUNT(*) FROM productos")->fetchColumn();
$totalCategorias = $conn->query("SELECT COUNT(*) FROM categorias")->fetchColumn();
$totalPedidos = $conn->query("SELECT COUNT(*) FROM encargos")->fetchColumn();
$totalVentas = $conn->query("SELECT COUNT(*) FROM ventas")->fetchColumn();

$maxUnidades = (count($topProductos) > 0) ? $topProductos[0]['unidades_vendidas'] : 1;

$ventas_semana_actual = array_fill(0, 7, 0);
$ventas_semana_pasada = array_fill(0, 7, 0);
$transacciones_semana = array_fill(0, 7, 0);

$stmtCurrentWeek = $conn->query("
    SELECT WEEKDAY(fecha) as dia, SUM(total) as total, COUNT(id_venta) as transacciones
    FROM ventas 
    WHERE YEARWEEK(fecha, 1) = YEARWEEK(CURDATE(), 1)
    GROUP BY dia
");
while($row = $stmtCurrentWeek->fetch()) {
    $ventas_semana_actual[$row['dia']] = (float)$row['total'];
    $transacciones_semana[$row['dia']] = (int)$row['transacciones'];
}

$stmtPrevWeek = $conn->query("
    SELECT WEEKDAY(fecha) as dia, SUM(total) as total
    FROM ventas 
    WHERE YEARWEEK(fecha, 1) = YEARWEEK(CURDATE() - INTERVAL 1 WEEK, 1)
    GROUP BY dia
");
while($row = $stmtPrevWeek->fetch()) {
    $ventas_semana_pasada[$row['dia']] = (float)$row['total'];
}

$jsonCurrentWeek = json_encode(array_values($ventas_semana_actual));
$jsonPrevWeek = json_encode(array_values($ventas_semana_pasada));
$jsonTransacciones = json_encode(array_values($transacciones_semana));

$bdd->desconectar();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <script>
(function() {
    const coloresGuardados = localStorage.getItem('liquour_colors');
    if (coloresGuardados) {
        try {
            const colores = JSON.parse(coloresGuardados);
            const dorado = colores['--color-dorado'] || '#C5A059';
            const fondo = colores['--bg-carbon'] || '#1A1A1A';
            const texto = colores['--text-blanco-crema'] || '#F5F5DC';
            const borde = colores['--border-fuerte'] || '#4A4A4A';
            
            function lightenColor(hex, percent) {
                let r = parseInt(hex.slice(1, 3), 16);
                let g = parseInt(hex.slice(3, 5), 16);
                let b = parseInt(hex.slice(5, 7), 16);
                r = Math.min(255, r + (r * percent / 100));
                g = Math.min(255, g + (g * percent / 100));
                b = Math.min(255, b + (b * percent / 100));
                return '#' + Math.round(r).toString(16).padStart(2, '0') + 
                           Math.round(g).toString(16).padStart(2, '0') + 
                           Math.round(b).toString(16).padStart(2, '0');
            }
            
            function darkenColor(hex, percent) {
                let r = parseInt(hex.slice(1, 3), 16);
                let g = parseInt(hex.slice(3, 5), 16);
                let b = parseInt(hex.slice(5, 7), 16);
                r = Math.max(0, r - (r * percent / 100));
                g = Math.max(0, g - (g * percent / 100));
                b = Math.max(0, b - (b * percent / 100));
                return '#' + Math.round(r).toString(16).padStart(2, '0') + 
                           Math.round(g).toString(16).padStart(2, '0') + 
                           Math.round(b).toString(16).padStart(2, '0');
            }
            
            document.documentElement.style.setProperty('--gold', dorado);
            document.documentElement.style.setProperty('--gold-lt', lightenColor(dorado, 15));
            document.documentElement.style.setProperty('--gold-dk', darkenColor(dorado, 20));
            document.documentElement.style.setProperty('--carbon', fondo);
            document.documentElement.style.setProperty('--cream', texto);
            document.documentElement.style.setProperty('--oxford', borde);
            document.documentElement.style.setProperty('--border', dorado + '26');
            document.documentElement.style.setProperty('--border-md', dorado + '4D');
            
        } catch(e) {}
    }
})();

window.addEventListener('storage', function(e) {
    if (e.key === 'liquour_colors') location.reload();
});
</script>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Liquour — Dashboard</title>

    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <?php include '../../Layout/nav_admin.php'; ?> 
    <link rel="stylesheet" href="../../../Assets/CSS/style.css">
   
    
    <!-- =========================================== -->
    <!-- SISTEMA DE TEMAS - SINCRONIZAR CON MENÚ -->
    <!-- =========================================== -->
    <style>
        /* Variables por defecto - se actualizarán con JS */
        :root {
            --dorado-mate: #C5A059;
            --negro-carbon: #1A1A1A;
            --blanco-crema: #F5F5DC;
            --gris-oxford: #4A4A4A;
        }
    </style>
    
    <script>
    // ============================================
    // SINCRONIZAR COLORES DEL TEMA CON DASHBOARD
    // ============================================
    (function sincronizarColoresDashboard() {
        const coloresGuardados = localStorage.getItem('liquour_colors');
        
        if (coloresGuardados) {
            try {
                const colores = JSON.parse(coloresGuardados);
                
                // Mapear nombres de variables del menú a los nombres del dashboard
                const dorado = colores['--color-dorado'] || '#C5A059';
                const fondo = colores['--bg-carbon'] || '#1A1A1A';
                const texto = colores['--text-blanco-crema'] || '#F5F5DC';
                const borde = colores['--border-fuerte'] || '#4A4A4A';
                
                // Aplicar a las variables que usa el dashboard
                document.documentElement.style.setProperty('--dorado-mate', dorado);
                document.documentElement.style.setProperty('--negro-carbon', fondo);
                document.documentElement.style.setProperty('--blanco-crema', texto);
                document.documentElement.style.setProperty('--gris-oxford', borde);
                
                // También actualizar las variables del dashboard.css si las usa
                document.documentElement.style.setProperty('--gold', dorado);
                document.documentElement.style.setProperty('--gold-dim', dorado + '33');
                document.documentElement.style.setProperty('--bg-dark', fondo);
                document.documentElement.style.setProperty('--w40', texto + '99');
                document.documentElement.style.setProperty('--w25', texto + '66');
                
                console.log('🎨 Colores sincronizados con Dashboard:', { dorado, fondo, texto });
                
                // Recargar gráficos si es necesario (para que usen el nuevo color)
                setTimeout(() => {
                    if (typeof window.recargarGraficos === 'function') {
                        window.recargarGraficos();
                    } else {
                        // Forzar recarga de página para aplicar todos los cambios
                        // location.reload();
                    }
                }, 100);
                
            } catch(e) {
                console.log('Error cargando colores:', e);
            }
        }
    })();
    
    // Escuchar cambios en tiempo real desde otra pestaña
    window.addEventListener('storage', function(e) {
        if (e.key === 'liquour_colors') {
            location.reload();
        }
    });
    </script>
</head>
<body>

<?php @include '../../../Layout/nav_admin.php'; ?>
<?php @include '../../../Layout/header_admin.php'; ?>

<div id="modal-perfil" class="modal-overlay" style="display: none;">
    <div class="modal-container">
        <div class="modal-header-perfil">
            <h3>Mi Perfil</h3>
            <button id="close-modal" class="close-modal">&times;</button>
        </div>
        <div class="modal-body">
            <p>Admin Liquour</p>
        </div>
    </div>
</div>

<div class="app">
  <main class="main">

    <div class="kpi-row">
      <div class="kpi">
        <div class="kpi-label">Ventas Hoy</div>
        <div class="kpi-value"><sup>$</sup><?= number_format($dataHoy['total_hoy'], 2) ?></div>
        <div class="kpi-meta"><?= $dataHoy['transacciones'] ?> transacciones</div>
        <div class="kpi-tag">Actualizado</div>
      </div>
      <div class="kpi">
        <div class="kpi-label">Total Órdenes/Ventas</div>
        <div class="kpi-value"><?= $totalVentas ?></div>
        <div class="kpi-meta">Registros históricos</div>
        <div class="kpi-tag">Global</div>
      </div>
      <div class="kpi">
        <div class="kpi-label">Este Mes</div>
        <div class="kpi-value"><sup>$</sup><?= number_format($dataMes['total_mes'], 2) ?></div>
        <div class="kpi-meta">Acumulado mensual</div>
        <div class="kpi-tag">✓ Monitoreo activo</div>
      </div>
    </div>

    <div class="charts-row">
      <div class="card">
        <div class="card-title">
          Ventas Semanales
          <div class="legend">
            <div class="leg-item"><div class="leg-dot" style="background:var(--dorado-mate, #C5A059)"></div>Esta Semana</div>
            <div class="leg-item"><div class="leg-dot" style="background:var(--gris-oxford, #4A4A4A)"></div>Semana Pasada</div>
          </div>
        </div>
        <canvas id="lineChart" height="120"></canvas>
      </div>
      <div class="card">
        <div class="card-title">Transacciones Diarias</div>
        <canvas id="barChart" height="120"></canvas>
      </div>
    </div>

    <div class="card">
      <div class="tbl-header">
        <div class="card-title" style="margin:0">5 Productos Más Vendidos</div>
        <button class="tbl-btn">Ver todos</button>
      </div>
      <table>
        <thead>
          <tr>
            <th>Producto</th>
            <th>Precio Unit.</th>
            <th>Categoría</th>
            <th>Unidades vendidas</th>
          </tr>
        </thead>
        <tbody>
          <?php if (count($topProductos) > 0): ?>
              <?php foreach($topProductos as $prod): 
                  $porcentaje = ($prod['unidades_vendidas'] / $maxUnidades) * 100;
                  $iniciales = strtoupper(substr($prod['nombre'], 0, 2));
              ?>
              <tr>
                <td><span class="avatar-sm"><?= $iniciales ?></span><?= htmlspecialchars($prod['nombre']) ?></td>
                <td class="price">$<?= number_format($prod['precio_venta'], 2) ?></td>
                <td><span class="cat"><?= htmlspecialchars($prod['categoria'] ?? 'Sin categoría') ?></span></td>
                <td><div class="unit-wrap"><?= $prod['unidades_vendidas'] ?><div class="bar-bg"><div class="bar-fg" style="width:<?= $porcentaje ?>%"></div></div></div></td>
              </tr>
              <?php endforeach; ?>
          <?php else: ?>
              <tr><td colspan="4" style="text-align:center;">No hay ventas registradas aún.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

  </main>

  <aside class="panel">

    <div>
      <div class="panel-title">Stock Crítico</div>
      <div class="stock-box">
        <div class="stock-box-title">Próximos a Agotarse</div>
        
        <?php if (count($stockCritico) > 0): ?>
            <?php foreach($stockCritico as $item): 
                $color = ($item['stock'] <= 5) ? 'red' : (($item['stock'] <= 10) ? 'amber' : 'green');
                $mensaje = ($item['stock'] <= 5) ? 'Urgente' : 'Reponer pronto';
            ?>
            <div class="stock-row">
              <div class="stock-info">
                <div class="status-dot <?= $color ?>"></div>
                <div><div class="stock-name"><?= htmlspecialchars($item['nombre']) ?></div><div class="stock-sub"><?= $mensaje ?></div></div>
              </div>
              <div class="stock-qty"><?= $item['stock'] ?></div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="font-size: 12px; color: var(--blanco-crema, #F5F5DC); padding: 10px;">Todo el stock está en niveles óptimos. ✓</p>
        <?php endif; ?>

      </div>
    </div>

    <div class="divider"></div>

    <div>
      <div class="panel-title">Resumen Rápido</div>
      <div class="mini-grid">
        <div class="mini-card"><div class="mini-lbl">Productos</div><div class="mini-val"><?= $totalProductos ?></div></div>
        <div class="mini-card"><div class="mini-lbl">Categorías</div><div class="mini-val"><?= $totalCategorias ?></div></div>
        <div class="mini-card"><div class="mini-lbl">Encargos</div><div class="mini-val"><?= $totalPedidos ?></div></div>
        <div class="mini-card"><div class="mini-lbl">Ventas</div><div class="mini-val"><?= $totalVentas ?></div></div>
      </div>
    </div>

    <div class="divider"></div>

    <div>
      <div class="panel-title">Actividad Reciente</div>
      <div class="activity">
        <div class="act-item">
          <div class="act-icon">✓</div>
          <div>
            <div class="act-text">Sistema conectado a la Base de Datos</div>
            <div class="act-time">Justo ahora</div>
          </div>
        </div>
      </div>
    </div>

  </aside>

</div>

<script>
  // ============================================
  // CONFIGURACIÓN DE GRÁFICOS CON COLOR DINÁMICO
  // ============================================
  
  // Obtener colores actuales del tema
  const goldActual = getComputedStyle(document.documentElement).getPropertyValue('--dorado-mate').trim() || '#C5A059';
  const oxfordActual = getComputedStyle(document.documentElement).getPropertyValue('--gris-oxford').trim() || '#4A4A4A';
  
  Chart.defaults.color = oxfordActual;
  Chart.defaults.font.family = 'Montserrat';
  Chart.defaults.font.size = 10;

  new Chart(document.getElementById('lineChart'), {
    type: 'line',
    data: {
      labels: ['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'],
      datasets: [
        {
          label: 'Esta semana',
          data: <?= $jsonCurrentWeek ?>,
          borderColor: goldActual,
          backgroundColor: 'rgba(197,160,89,.08)',
          borderWidth: 2,
          pointBackgroundColor: goldActual,
          pointRadius: 3.5,
          tension: 0.42,
          fill: true
        },
        {
          label: 'Semana pasada',
          data: <?= $jsonPrevWeek ?>,
          borderColor: oxfordActual,
          backgroundColor: 'transparent',
          borderWidth: 1.5,
          borderDash: [4, 4],
          pointBackgroundColor: oxfordActual,
          pointRadius: 2,
          tension: 0.42
        }
      ]
    },
    options: {
      responsive: true,
      plugins: { legend: { display: false } },
      scales: {
        x: { grid: { color: 'rgba(255,255,255,.03)' }, ticks: { color: oxfordActual } },
        y: { grid: { color: 'rgba(255,255,255,.03)' }, ticks: { color: oxfordActual, callback: v => '$'+v } }
      }
    }
  });

  new Chart(document.getElementById('barChart'), {
    type: 'bar',
    data: {
      labels: ['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'],
      datasets: [{
        data: <?= $jsonTransacciones ?>,
        backgroundColor: goldActual + '38',
        borderColor: goldActual,
        borderWidth: 1,
        borderRadius: 4
      }]
    },
    options: {
      responsive: true,
      plugins: { legend: { display: false } },
      scales: {
        x: { grid: { display: false }, ticks: { color: oxfordActual } },
        y: { grid: { color: 'rgba(255,255,255,.03)' }, ticks: { color: oxfordActual } }
      }
    }
  });
</script>

<script src="../../../Assets/JS/dashboard.js"></script>

</body>
</html>