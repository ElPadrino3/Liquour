
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
        </div>
    </div>
</div>
    

<!-- 🔗 JS -->
<script src="../../../assets/js/dashboard.js"></script>
<!-- ⚠️ SI NO FUNCIONA: revisa ruta o usa ../../../../ según tu estructura -->

<script src="../../../Assets/JS/Catalogo_Admin.js"></script>

</body>
</html>