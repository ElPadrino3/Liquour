<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>LIQUOUR - Acceso</title>
    <link rel="stylesheet" href="../../Assets/CSS/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
</head>
<body>
    <div class="login-container">
        <div class="logo-container">
            
        <img src="../../Assets/IMG/WhatsApp Image 2026-03-13 at 9.11.48 AM.jpeg" class="animate__animated animate__jackInTheBox">

        </div>
        <h2>ACCESO AL SISTEMA</h2>
        <form action="login_validar.php" method="POST">
            <div class="form-group">
                <label>Usuario</label>
                <input type="text" name="usuario" required>
            </div>
            <div class="form-group">
                <label>Contraseña</label>
                <input type="password" name="password" required>
            </div>
            <button class="btn-ingresar animate__animated animate__pulse animate__infinite">
            INGRESAR
            </button>
        </form>
        <div class="footer-text">
            LIQUOUR POS V1.0 CALIDAD Y EXCLUSIVIDAD
        </div>
    </div>
</body>
</html>


