document.addEventListener("DOMContentLoaded", function() {
    
    const colorGuardado = localStorage.getItem('temaColorLiquour');
    const logoGuardado = localStorage.getItem('temaLogoLiquour');

    if (colorGuardado) {
        document.documentElement.style.setProperty('--tema-color', colorGuardado);
    }
    if (logoGuardado) {
        const logoImg = document.getElementById('logo-sistema');
        if (logoImg) logoImg.src = logoGuardado;
    }

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
                window.location.href = "../Include/Admin/dashboard.php"; 
            }
            else if (titulo === "VENTAS") {
                window.location.href = "../Include/Admin/reportes.php";
            }
            else if (titulo === "VENTAS") {
                window.location.href = "../Include/Admin/reportes.php";
            }
            else if (titulo === "COMPRAS") {
                window.location.href = "../Include/Admin/compras.php";
            }
            else if (titulo === "AJUSTES") {
                Swal.fire({
                    html: `
                        <div style="text-align: left; padding: 10px;">
                            <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 30px;">
                                <i class="fa-solid fa-sliders" style="font-size: 2rem; color: var(--tema-color);"></i>
                                <h2 style="margin: 0; color: #fff; font-size: 1.5rem; letter-spacing: 1px;">Configuración del Sistema</h2>
                            </div>
                            
                            <div style="background: rgba(255,255,255,0.03); padding: 20px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.05); margin-bottom: 20px;">
                                <label style="display: block; color: #aaa; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px;">Color de Acento Global</label>
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <input type="color" id="input-color" value="${colorGuardado || '#e5c158'}" style="width: 50px; height: 50px; border: none; border-radius: 50%; cursor: pointer; background: transparent; padding: 0;">
                                    <span style="color: #fff; font-size: 0.9rem;">Selecciona el color que definirá la identidad visual del POS.</span>
                                </div>
                            </div>
                            
                            <div style="background: rgba(255,255,255,0.03); padding: 20px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.05);">
                                <label style="display: block; color: #aaa; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px;">Logotipo de la Empresa</label>
                                <input type="text" id="input-logo" placeholder="Ingresa la URL de la imagen..." value="${logoGuardado || '/LIQUOUR/Assets/IMG/Logo.jpeg'}" style="width: 100%; padding: 12px 15px; border-radius: 6px; border: 1px solid #333; background: #111; color: #fff; outline: none; font-size: 0.95rem; box-sizing: border-box;">
                            </div>
                        </div>
                    `,
                    background: '#1a1a1a',
                    showCancelButton: true,
                    confirmButtonText: '<i class="fa-solid fa-check"></i> Aplicar Cambios',
                    cancelButtonText: 'Cancelar',
                    buttonsStyling: false,
                    customClass: {
                        popup: 'modal-elegante',
                        confirmButton: 'swal2-confirm btn-guardar',
                        cancelButton: 'swal2-cancel btn-cancelar'
                    },
                    showClass: {
                        popup: '' 
                    },
                    hideClass: {
                        popup: 'swal2-hide'
                    },
                    preConfirm: () => {
                        return {
                            color: document.getElementById('input-color').value,
                            logo: document.getElementById('input-logo').value
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        localStorage.setItem('temaColorLiquour', result.value.color);
                        localStorage.setItem('temaLogoLiquour', result.value.logo);
                        
                        Swal.fire({
                            title: 'Aplicando...',
                            html: 'Actualizando la interfaz del sistema.',
                            timer: 1000,
                            timerProgressBar: true,
                            showConfirmButton: false,
                            background: '#1a1a1a',
                            color: '#fff',
                            customClass: { popup: 'modal-elegante' },
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        }).then(() => {
                            location.reload();
                        });
                    }
                });
            }
            else {
                Swal.fire({
                    title: '<span style="color:var(--tema-color); letter-spacing: 2px;">EN CONSTRUCCIÓN</span>',
                    html: '<span style="color:#cccccc; font-weight: 300;">Estamos preparándote una experiencia VIP. ¡Pronto estará lista! 🚧</span>',
                    icon: 'info',
                    iconColor: 'var(--tema-color)',
                    background: '#1a1a1a',
                    confirmButtonText: 'Aceptar',
                    buttonsStyling: false,
                    customClass: {
                        popup: 'modal-elegante',
                        confirmButton: 'swal2-confirm btn-guardar'
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
                title: '<span style="color:var(--tema-color); letter-spacing: 2px;">¿CERRAR SESIÓN?</span>',
                html: '<span style="color:#cccccc; font-weight: 300;">Saldrás de tu panel de Liquour.</span>',
                icon: 'warning',
                iconColor: 'var(--tema-color)',
                showCancelButton: true,
                background: '#1a1a1a',
                confirmButtonText: 'Sí, salir',
                cancelButtonText: 'Cancelar',
                buttonsStyling: false,
                customClass: {
                    popup: 'modal-elegante',
                    confirmButton: 'swal2-confirm btn-guardar',
                    cancelButton: 'swal2-cancel btn-cancelar'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });
    }
});