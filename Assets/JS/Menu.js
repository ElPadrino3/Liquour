document.addEventListener("DOMContentLoaded", function() {
    const roleElement = document.querySelector('.role');
    let rol = roleElement ? roleElement.innerText.trim().toLowerCase() : 'empleado';

    const cards = document.querySelectorAll('.card');

    cards.forEach(card => {
        card.addEventListener('click', function(e) {
            e.preventDefault();
            const titulo = this.querySelector('h3').innerText.trim();

            if (titulo === "PUNTO DE VENTA") {
                window.location.href = "../Include/Admin/Tienda_pos.php";
            }
            else if (titulo === "INVENTARIO") {
                if (rol === "administrador" || rol === "admin") {
                    window.location.href = "../Include/Admin/Catalogo_Admin.php";
                } else {
                    window.location.href = "../Include/empleado/Catalogo_Empleado.php";
                }
            }
            else if (titulo === "PÁGINA WEB") {
                window.location.href = "../Include/empleado/principal.php";
            }
            else if (titulo === "USUARIOS") {
                window.location.href = "../Include/Admin/empleados.php";
            }
            else if (titulo === "ESTADÍSTICAS") {
                window.location.href = "../Include/Admin/reportes.php";
            }
            else if (titulo === "VENTAS") {
                window.location.href = "../Include/Admin/dashboard.php"; 
            }
            else {
                Swal.fire({
                    title: '<span style="color:#e5c158; letter-spacing: 2px;">EN CONSTRUCCIÓN</span>',
                    html: '<span style="color:#cccccc; font-weight: 300;">Estamos preparándote una experiencia VIP. ¡Pronto estará lista! 🚧</span>',
                    icon: 'info',
                    iconColor: '#e5c158',
                    background: 'linear-gradient(145deg, rgba(20, 20, 20, 0.98), rgba(5, 5, 5, 0.98))',
                    confirmButtonColor: '#e5c158',
                    confirmButtonText: '<span style="color:#050505; font-weight: bold;">Aceptar</span>',
                    backdrop: `rgba(0,0,0,0.85)`,
                    customClass: {
                        popup: 'border-gold'
                    }
                });
            }
        });
    });

    const btnExit = document.querySelector('.btn-exit');
    if (btnExit) {
        btnExit.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.getAttribute('href');
            
            Swal.fire({
                title: '<span style="color:#e5c158; letter-spacing: 2px;">¿CERRAR SESIÓN?</span>',
                html: '<span style="color:#cccccc; font-weight: 300;">Saldrás de tu panel de Liquour.</span>',
                icon: 'warning',
                iconColor: '#e5c158',
                showCancelButton: true,
                background: 'linear-gradient(145deg, rgba(20, 20, 20, 0.98), rgba(5, 5, 5, 0.98))',
                confirmButtonColor: '#e5c158',
                cancelButtonColor: 'transparent',
                confirmButtonText: '<span style="color:#050505; font-weight: bold;">Sí, salir</span>',
                cancelButtonText: '<span style="color:#cccccc;">Cancelar</span>',
                backdrop: `rgba(0,0,0,0.85)`,
                customClass: {
                    cancelButton: 'border-gray',
                    popup: 'border-gold'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });
    }
});