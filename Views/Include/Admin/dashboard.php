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

$bdd->desconectar();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Liquour — Dashboard</title>

    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <?php include '../../Layout/nav_admin.php'; ?> 
    <link rel="stylesheet" href="../../../Assets/CSS/style.css">
    <link rel="stylesheet" href="../../../Assets/CSS/dashboard.css">
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
          Ventas Mensuales
          <div class="legend">
            <div class="leg-item"><div class="leg-dot" style="background:#C5A059"></div>Este mes</div>
            <div class="leg-item"><div class="leg-dot" style="background:#4A4A4A"></div>Mes pasado</div>
          </div>
        </div>
        <canvas id="lineChart" height="120"></canvas>
      </div>
      <div class="card">
        <div class="card-title">Distribución Diaria</div>
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
            <p style="font-size: 12px; color: var(--cream); padding: 10px;">Todo el stock está en niveles óptimos. ✓</p>
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
  Chart.defaults.color = '#4A4A4A';
  Chart.defaults.font.family = 'Montserrat';
  Chart.defaults.font.size = 10;

  const gold   = '#C5A059';
  const oxford = '#4A4A4A';

  new Chart(document.getElementById('lineChart'), {
    type: 'line',
    data: {
      labels: ['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'],
      datasets: [
        {
          label: 'Este mes',
          data: [420, 580, 510, 690, 750, 870, 920],
          borderColor: gold,
          backgroundColor: 'rgba(197,160,89,.08)',
          borderWidth: 2,
          pointBackgroundColor: gold,
          pointRadius: 3.5,
          tension: 0.42,
          fill: true
        },
        {
          label: 'Mes pasado',
          data: [350, 490, 430, 600, 620, 710, 780],
          borderColor: oxford,
          backgroundColor: 'transparent',
          borderWidth: 1.5,
          borderDash: [4, 4],
          pointBackgroundColor: oxford,
          pointRadius: 2,
          tension: 0.42
        }
      ]
    },
    options: {
      responsive: true,
      plugins: { legend: { display: false } },
      scales: {
        x: { grid: { color: 'rgba(255,255,255,.03)' }, ticks: { color: oxford } },
        y: { grid: { color: 'rgba(255,255,255,.03)' }, ticks: { color: oxford, callback: v => '$'+v } }
      }
    }
  });

  new Chart(document.getElementById('barChart'), {
    type: 'bar',
    data: {
      labels: ['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'],
      datasets: [{
        data: [180, 240, 210, 290, 320, 410, 380],
        backgroundColor: 'rgba(197,160,89,.22)',
        borderColor: gold,
        borderWidth: 1,
        borderRadius: 4
      }]
    },
    options: {
      responsive: true,
      plugins: { legend: { display: false } },
      scales: {
        x: { grid: { display: false }, ticks: { color: oxford } },
        y: { grid: { color: 'rgba(255,255,255,.03)' }, ticks: { color: oxford } }
      }
    }
  });
</script>

<script src="../../../Assets/JS/dashboard.js"></script>

</body>
</html>