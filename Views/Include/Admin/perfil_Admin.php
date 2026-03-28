<?php
$rol = 'admin';
$nombre = "Tu Nombre";
$avatar = "https://ui-avatars.com/api/?name=Tu+Nombre&background=C5A059&color=1A1A1A&size=128";
?>
<link rel="stylesheet" href="../../../Assets/CSS/perfil.css">

<div class="liquour-page-bg"></div>

<div class="liquour-profile">
    <div class="liquour-bg-animated"></div>
    <div class="liquour-avatar">
        <img src="<?php echo $avatar; ?>" alt="Avatar">
    </div>
    
    <div class="liquour-info">
        <h2><?php echo $nombre; ?></h2>
        <?php if ($rol === 'admin') { ?>
            <p class="rango">Gerente de la Licorería</p>
        <?php } else { ?>
            <p class="rango">Cajero Premium</p>
        <?php } ?>
    </div>
    
    <div class="liquour-stats">
        <?php if ($rol === 'admin') { ?>
            <div class="stat-box">
                <h3>$15,420</h3>
                <span>Ventas Mes</span>
            </div>
            <div class="stat-box">
                <h3>842</h3>
                <span>Inventario</span>
            </div>
            <div class="stat-box">
                <h3>3</h3>
                <span>Alertas</span>
            </div>
        <?php } else { ?>
            <div class="stat-box">
                <h3>$850</h3>
                <span>Caja Hoy</span>
            </div>
            <div class="stat-box">
                <h3>42</h3>
                <span>Tickets</span>
            </div>
            <div class="stat-box">
                <h3>38</h3>
                <span>Clientes</span>
            </div>
        <?php } ?>
    </div>
    
    <div class="liquour-charts">
        <?php if ($rol === 'admin') { ?>
            <div class="chart-container">
                <div class="chart-label">
                    <span>Meta Mensual</span>
                    <span>85%</span>
                </div>
                <div class="chart-bar-bg">
                    <div class="chart-bar-fill" data-width="85%"></div>
                </div>
            </div>
            <div class="chart-container">
                <div class="chart-label">
                    <span>Stock Licores</span>
                    <span>60%</span>
                </div>
                <div class="chart-bar-bg">
                    <div class="chart-bar-fill" data-width="60%"></div>
                </div>
            </div>
            <div class="chart-container">
                <div class="chart-label">
                    <span>Crecimiento</span>
                    <span>92%</span>
                </div>
                <div class="chart-bar-bg">
                    <div class="chart-bar-fill" data-width="92%"></div>
                </div>
            </div>
        <?php } else { ?>
            <div class="chart-container">
                <div class="chart-label">
                    <span>Meta Diaria</span>
                    <span>75%</span>
                </div>
                <div class="chart-bar-bg">
                    <div class="chart-bar-fill" data-width="75%"></div>
                </div>
            </div>
            <div class="chart-container">
                <div class="chart-label">
                    <span>Efectividad</span>
                    <span>90%</span>
                </div>
                <div class="chart-bar-bg">
                    <div class="chart-bar-fill" data-width="90%"></div>
                </div>
            </div>
            <div class="chart-container">
                <div class="chart-label">
                    <span>Puntualidad</span>
                    <span>100%</span>
                </div>
                <div class="chart-bar-bg">
                    <div class="chart-bar-fill" data-width="100%"></div>
                </div>
            </div>
        <?php } ?>
    </div>
    
    <div class="liquour-action">
        <button id="btnLiquourAccion" onclick="ejecutarAccion()">Cerrar Turno</button>
    </div>
</div>

<script src="../../Assets/JS/perfil.js"></script>