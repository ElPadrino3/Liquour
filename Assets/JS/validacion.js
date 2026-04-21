document.addEventListener("DOMContentLoaded", function() {
    const loginForm = document.getElementById('loginForm');

    if (loginForm) {
        loginForm.addEventListener('submit', function(event) {
            event.preventDefault();

            const user = document.getElementById('usuario').value;
            const pass = document.getElementById('password').value;

            if (user === "admin" && pass === "admin123") {
                sessionStorage.setItem('usuario', user);
                sessionStorage.setItem('rol', 'admin');
                alert('¡Bienvenido, jefe! Abriendo las puertas VIP... ✨');
                // Redirige al menú principal en Layout
                window.location.href = "../../Views/Layout/menu.php";
            } 
            else if (user === "empleado" && pass === "emp123") {
                sessionStorage.setItem('usuario', user);
                sessionStorage.setItem('rol', 'empleado');
                alert('¡Bienvenido al turno! A vender se ha dicho. 🍾');
                // Los empleados también van al menú, el JS del menú filtrará qué pueden tocar
                window.location.href = "../../Views/Layout/menu.php";
            } 
            else {
                alert('Credenciales incorrectas. ¿Te tomaste el inventario antes de entrar? 🥴');
            }
        });
    }
});