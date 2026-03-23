<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['usuario'];
    $pass = $_POST['password'];

    if ($user == "admin" && $pass == "admin123") {
        $_SESSION['usuario'] = $user;
        $_SESSION['rol'] = "admin";
        
        header("Location: ../Admin/Catalogo_Admin.php");
        exit();
    } 
    else if ($user == "empleado" && $pass == "emp123") {
        $_SESSION['usuario'] = $user;
        $_SESSION['rol'] = "empleado";
        
        header("Location: ../empleado/Catalogo_Empleado.php");
        exit();
    } 
    else {
        echo "<script>
                alert('Usuario o contraseña incorrectos');
                window.location.href='login.php';
              </script>";
    }
} else {
    header("Location: login.php");
    exit();
}