

<?php include '../../Layout/head.php'; ?>

<link rel="stylesheet" href="../../../Assets/CSS/nav.css">
<link rel="stylesheet" href="../../../Assets/CSS/-Catalogo_Admin.css">

<!-- 🔗 CHART JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<!-- 🔗 CSS -->
<link rel="stylesheet" href="../../../assets/css/dashboard.css">
<!-- ⚠️ Si no carga, revisa mayúsculas/minúsculas o ajusta la ruta -->


<?php include '../../Layout/nav_admin.php'; ?> 

<div id="modal-perfil" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-header-perfil">
            <h3>Mi Perfil</h3>
            <button id="close-modal" class="close-modal">&times;</button>
        </div>
        <div class="modal-body">
            <p>Admin Liquour</p>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Liquour — Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="../../../Assets/CSS/style.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body>

<?php include '../../Layout/header_admin.php'; ?>

<div class="app">

  <main class="main">

    <div class="kpi-row">
      <div class="kpi">
        <div class="kpi-label">Ventas Hoy</div>
        <div class="kpi-value"><sup>$</sup>245.50</div>
        <div class="kpi-meta">32 transacciones</div>
        <div class="kpi-tag">↑ 12% vs ayer</div>
      </div>
      <div class="kpi">
        <div class="kpi-label">Última Semana</div>
        <div class="kpi-value"><sup>$</sup>1,580.75</div>
        <div class="kpi-meta">224 transacciones</div>
        <div class="kpi-tag">↑ 8% vs anterior</div>
      </div>
      <div class="kpi">
        <div class="kpi-label">Este Mes</div>
        <div class="kpi-value"><sup>$</sup>3,674.25</div>
        <div class="kpi-meta">Meta: $2,980.06</div>
        <div class="kpi-tag">✓ Meta superada</div>
      </div>
    </div>

    <div class="charts-row">
      <div class="card">
        <div class="card-title">
          Ventas Mensuales
          <div class="legend">
            <div class="leg-item"><div class="leg-dot" style="background:var(--gold)"></div>Este mes</div>
            <div class="leg-item"><div class="leg-dot" style="background:var(--oxford)"></div>Mes pasado</div>
          </div>

        </div>
    </div>
</div>
    


<!-- 🔗 JS -->
<script src="../../../assets/js/dashboard.js"></script>
<!-- ⚠️ SI NO FUNCIONA: revisa ruta o usa ../../../../ según tu estructura -->

    <div class="card">
      <div class="tbl-header">
        <div class="card-title" style="margin:0">5 Productos Más Vendidos</div>
        <button class="tbl-btn">Ver todos</button>
      </div>
      <table>
        <thead>
          <tr>
            <th>Producto</th>
            <th>Precio</th>
            <th>Categoría</th>
            <th>Unidades vendidas</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><span class="avatar-sm">JD</span>Jack Daniel's</td>
            <td class="price">$20.00</td>
            <td><span class="cat">Whiskey</span></td>
            <td><div class="unit-wrap">120<div class="bar-bg"><div class="bar-fg" style="width:100%"></div></div></div></td>
          </tr>
          <tr>
            <td><span class="avatar-sm">JW</span>Johnnie Walker Black</td>
            <td class="price">$30.00</td>
            <td><span class="cat">Whiskey</span></td>
            <td><div class="unit-wrap">95<div class="bar-bg"><div class="bar-fg" style="width:79%"></div></div></div></td>
          </tr>
          <tr>
            <td><span class="avatar-sm">AV</span>Absolut Vodka</td>
            <td class="price">$18.00</td>
            <td><span class="cat">Vodka</span></td>
            <td><div class="unit-wrap">87<div class="bar-bg"><div class="bar-fg" style="width:72%"></div></div></div></td>
          </tr>
          <tr>
            <td><span class="avatar-sm">BC</span>Bacardí Carta Blanca</td>
            <td class="price">$15.00</td>
            <td><span class="cat">Ron</span></td>
            <td><div class="unit-wrap">74<div class="bar-bg"><div class="bar-fg" style="width:62%"></div></div></div></td>
          </tr>
          <tr>
            <td><span class="avatar-sm">MC</span>Moët &amp; Chandon</td>
            <td class="price">$55.00</td>
            <td><span class="cat">Champagne</span></td>
            <td><div class="unit-wrap">60<div class="bar-bg"><div class="bar-fg" style="width:50%"></div></div></div></td>
          </tr>
        </tbody>
      </table>
    </div>

<script src="../../../Assets/JS/Catalogo_Admin.js"></script>


  <aside class="panel">

    <div>
      <div class="panel-title">Stock Crítico</div>
      <div class="stock-box">
        <div class="stock-box-title">Próximos a Agotarse</div>
        <div class="stock-row">
          <div class="stock-info">
            <div class="status-dot red"></div>
            <div><div class="stock-name">Absolut Vodka</div><div class="stock-sub">Última unidad</div></div>
          </div>
          <div class="stock-qty">6</div>
        </div>
        <div class="stock-row">
          <div class="stock-info">
            <div class="status-dot red"></div>
            <div><div class="stock-name">Chivas Regal 18</div><div class="stock-sub">Stock mínimo</div></div>
          </div>
          <div class="stock-qty">11</div>
        </div>
        <div class="stock-row">
          <div class="stock-info">
            <div class="status-dot amber"></div>
            <div><div class="stock-name">Buchanan's 12</div><div class="stock-sub">Reponer pronto</div></div>
          </div>
          <div class="stock-qty">8</div>
        </div>
        <div class="stock-row">
          <div class="stock-info">
            <div class="status-dot green"></div>
            <div><div class="stock-name">Moët &amp; Chandon</div><div class="stock-sub">Nivel aceptable</div></div>
          </div>
          <div class="stock-qty">66</div>
        </div>
      </div>
    </div>

    <div class="divider"></div>

    <div>
      <div class="panel-title">Resumen Rápido</div>
      <div class="mini-grid">
        <div class="mini-card"><div class="mini-lbl">Productos</div><div class="mini-val">148</div></div>
        <div class="mini-card"><div class="mini-lbl">Categorías</div><div class="mini-val">12</div></div>
        <div class="mini-card"><div class="mini-lbl">Clientes</div><div class="mini-val">340</div></div>
        <div class="mini-card"><div class="mini-lbl">Pedidos</div><div class="mini-val">27</div></div>
      </div>
    </div>

    <div class="divider"></div>

    <div>
      <div class="panel-title">Actividad Reciente</div>
      <div class="activity">
        <div class="act-item">
          <div class="act-icon">↑</div>
          <div>
            <div class="act-text">Venta: Chivas Regal 18 × 2 confirmada</div>
            <div class="act-time">Hace 5 minutos</div>
          </div>
        </div>
        <div class="act-item">
          <div class="act-icon">!</div>
          <div>
            <div class="act-text">Stock bajo en 3 productos detectado</div>
            <div class="act-time">Hace 22 minutos</div>
          </div>
        </div>
        <div class="act-item">
          <div class="act-icon">✓</div>
          <div>
            <div class="act-text">Pedido #1042 entregado correctamente</div>
            <div class="act-time">Hace 1 hora</div>
          </div>
        </div>
        <div class="act-item">
          <div class="act-icon">+</div>
          <div>
            <div class="act-text">Nuevo cliente registrado: R. Morales</div>
            <div class="act-time">Hace 2 horas</div>
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
>>>>>>> origin/frontend1
</body>
</html>