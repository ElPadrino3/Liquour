<?php
session_start();
require_once '../../Config/Liquour_bdd.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario']);
    $password = trim($_POST['password']);

    $bdd = new BDD();
    $conn = $bdd->conectar();

    $stmt = $conn->prepare("SELECT id_usuario, nombre, usuario, rol, estado FROM usuarios WHERE usuario = ? AND password = ?");
    $stmt->execute([$usuario, $password]);
    $user = $stmt->fetch();

    if ($user) {
        if ($user['estado'] == 1) {
            $_SESSION['id_usuario'] = $user['id_usuario'];
            $_SESSION['nombre'] = $user['nombre'];
            $_SESSION['usuario'] = $user['usuario'];
            $_SESSION['rol'] = $user['rol'];

            if ($user['rol'] === 'admin') {
                header("Location: ../Layout/menu.php");
            } else {
                header("Location: ../Layout/menu.php");
            }
            exit;
        } else {
            $error = "Tu cuenta está inactiva. Contacta al administrador.";
        }
    } else {
        $error = "Usuario o contraseña incorrectos.";
    }
    $bdd->desconectar();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>LIQUOUR - Acceso</title>
    <link rel="stylesheet" href="../../Assets/CSS/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
</head>
<body>
    
    <div class="liquour-page-bg"></div>

    <div class="login-container">
        <div class="logo-container">
            <img src="../../Assets/IMG/Logo.jpeg" class="animate__animated animate__jackInTheBox">
        </div>
        <h2>ACCESO AL SISTEMA</h2>

        <?php if(!empty($error)): ?>
            <div style="color: #e08080; background: rgba(192,80,80,.1); border: 1px solid rgba(192,80,80,.3); padding: 10px; border-radius: 8px; margin-bottom: 15px; text-align: center; font-size: 0.9rem;">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label>Usuario</label>
                <input type="text" id="usuario" name="usuario" required>
            </div>
            <div class="form-group">
                <label>Contraseña</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn-ingresar animate__animated animate__pulse animate__infinite">
                INGRESAR
            </button>
        </form>
        <div class="footer-text">
            LIQUOUR POS V1.0 CALIDAD Y EXCLUSIVIDAD
        </div>
    </div>
    
</body>
</html>