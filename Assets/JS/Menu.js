document.addEventListener("DOMContentLoaded", function() {
    
    // ============================================
    // SISTEMA DE TEMAS COMPLETO - MÚLTIPLES COLORES
    // ============================================
    
    // Colores por defecto del sistema
 // Colores originales de Liquour
const DEFAULT_COLORS = {
    '--color-dorado': '#C5A059',      // Dorado mate
    '--bg-carbon': '#1A1A1A',         // Negro carbón
    '--bg-carbon-claro': '#2A2A2A',   // Negro carbón claro
    '--bg-gris-oxford': '#4A4A4A',    // Gris oxford
    '--text-blanco-crema': '#F5F5DC', // Blanco crema
    '--border-fuerte': '#4A4A4A'      // Bordes gris
};
    
    // Cargar colores guardados o usar defaults
    function loadAllColors() {
        const savedColors = localStorage.getItem('liquour_colors_complete');
        if (savedColors) {
            try {
                const colors = JSON.parse(savedColors);
                for (const [variable, value] of Object.entries(colors)) {
                    document.documentElement.style.setProperty(variable, value);
                }
                return colors;
            } catch(e) {}
        }
        // Si no hay guardados, aplicar defaults
        for (const [variable, value] of Object.entries(defaultColors)) {
            document.documentElement.style.setProperty(variable, value);
        }
        return defaultColors;
    }
    
    // Guardar todos los colores
    function saveAllColors(colors) {
        localStorage.setItem('liquour_colors_complete', JSON.stringify(colors));
        for (const [variable, value] of Object.entries(colors)) {
            document.documentElement.style.setProperty(variable, value);
        }
    }
    
    // Obtener colores actuales del sistema
    function getCurrentColors() {
        const root = getComputedStyle(document.documentElement);
        return {
            '--color-dorado': root.getPropertyValue('--color-dorado').trim() || '#C5A059',
            '--bg-carbon': root.getPropertyValue('--bg-carbon').trim() || '#1A1A1A',
            '--bg-carbon-claro': root.getPropertyValue('--bg-carbon-claro').trim() || '#2A2A2A',
            '--bg-gris-oxford': root.getPropertyValue('--bg-gris-oxford').trim() || '#4A4A4A',
            '--text-blanco-crema': root.getPropertyValue('--text-blanco-crema').trim() || '#F5F5DC',
            '--border-fuerte': root.getPropertyValue('--border-fuerte').trim() || '#4A4A4A'
        };
    }
    
    // Aplicar paleta predefinida
    function applyPalette(paletteName) {
        const palettes = {
            original: {
                '--color-dorado': '#C5A059',
                '--bg-carbon': '#1A1A1A',
                '--bg-carbon-claro': '#2A2A2A',
                '--bg-gris-oxford': '#4A4A4A',
                '--text-blanco-crema': '#F5F5DC',
                '--border-fuerte': '#4A4A4A'
            },
            noche: {
                '--color-dorado': '#D4AF37',
                '--bg-carbon': '#0A0A0A',
                '--bg-carbon-claro': '#1A1A1A',
                '--bg-gris-oxford': '#333333',
                '--text-blanco-crema': '#FFFFFF',
                '--border-fuerte': '#333333'
            },
            vino: {
                '--color-dorado': '#8B1A1A',
                '--bg-carbon': '#1A0A0A',
                '--bg-carbon-claro': '#2A1515',
                '--bg-gris-oxford': '#5A3030',
                '--text-blanco-crema': '#F5E6E6',
                '--border-fuerte': '#5A3030'
            },
            esmeralda: {
                '--color-dorado': '#8B9A46',
                '--bg-carbon': '#1A1A0A',
                '--bg-carbon-claro': '#2A2A15',
                '--bg-gris-oxford': '#4A4A30',
                '--text-blanco-crema': '#F0F0E0',
                '--border-fuerte': '#4A4A30'
            },
            azul: {
                '--color-dorado': '#C5A059',
                '--bg-carbon': '#0A1A2A',
                '--bg-carbon-claro': '#152A3A',
                '--bg-gris-oxford': '#2A4A5A',
                '--text-blanco-crema': '#E6F0F5',
                '--border-fuerte': '#2A4A5A'
            }
        };
        
        const palette = palettes[paletteName];
        if (palette) {
            saveAllColors(palette);
            return palette;
        }
        return null;
    }
    
    // Función para ajustar color (oscurecer/clarecer)
    function adjustColor(hex, percent) {
        let r = parseInt(hex.slice(1, 3), 16);
        let g = parseInt(hex.slice(3, 5), 16);
        let b = parseInt(hex.slice(5, 7), 16);
        
        r = Math.min(255, Math.max(0, r + (r * percent / 100)));
        g = Math.min(255, Math.max(0, g + (g * percent / 100)));
        b = Math.min(255, Math.max(0, b + (b * percent / 100)));
        
        return `#${Math.round(r).toString(16).padStart(2, '0')}${Math.round(g).toString(16).padStart(2, '0')}${Math.round(b).toString(16).padStart(2, '0')}`;
    }
    
    // Cargar colores al iniciar
    loadAllColors();
    
    // Cargar logo guardado
    const logoGuardado = localStorage.getItem('temaLogoLiquour');
    if (logoGuardado) {
        const logoImg = document.getElementById('logo-sistema');
        if (logoImg) logoImg.src = logoGuardado;
    }
    
    // Obtener rol del usuario
    const roleElement = document.querySelector('.role');
    let rol = roleElement ? roleElement.innerText.trim().toLowerCase() : 'empleado';
    
    // Manejador de clics en tarjetas
    const cards = document.querySelectorAll('.card');
    
    cards.forEach(card => {
        card.addEventListener('click', function(e) {
            e.preventDefault();
            const titulo = this.querySelector('h3').innerText.trim();
            
            // Navegación normal
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
            else if (titulo === "COMPRAS") {
                window.location.href = "../Include/Admin/compras.php";
            }
            // ============================================
            // BOTÓN DE AJUSTES - NUEVO CON MÚLTIPLES COLORES
            // ============================================
            else if (titulo === "AJUSTES") {
                const currentColors = getCurrentColors();
                
                Swal.fire({
                    title: '<span style="letter-spacing: 2px;">🎨 PERSONALIZAR LIQUOUR</span>',
                    html: `
                        <div style="text-align: left; padding: 10px; max-height: 65vh; overflow-y: auto;">
                            <!-- PALETAS RÁPIDAS -->
                            <div style="margin-bottom: 25px;">
                                <h3 style="color: var(--color-dorado); margin-bottom: 12px; font-size: 0.9rem; display: flex; align-items: center; gap: 8px;">
                                    <i class="fa-solid fa-palette"></i> Paletas Rápidas
                                </h3>
                                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px;">
                                    <button onclick="window.applyPaletteAndClose('original')" class="palette-btn" style="background: linear-gradient(135deg, #1A1A1A, #C5A059); border: none; padding: 10px; border-radius: 10px; color: white; cursor: pointer; font-size: 12px;">⚜️ Original</button>
                                    <button onclick="window.applyPaletteAndClose('noche')" class="palette-btn" style="background: linear-gradient(135deg, #0A0A0A, #D4AF37); border: none; padding: 10px; border-radius: 10px; color: white; cursor: pointer; font-size: 12px;">🌙 Noche</button>
                                    <button onclick="window.applyPaletteAndClose('vino')" class="palette-btn" style="background: linear-gradient(135deg, #1A0A0A, #8B1A1A); border: none; padding: 10px; border-radius: 10px; color: white; cursor: pointer; font-size: 12px;">🍷 Vino</button>
                                    <button onclick="window.applyPaletteAndClose('esmeralda')" class="palette-btn" style="background: linear-gradient(135deg, #1A1A0A, #8B9A46); border: none; padding: 10px; border-radius: 10px; color: white; cursor: pointer; font-size: 12px;">🫒 Esmeralda</button>
                                    <button onclick="window.applyPaletteAndClose('azul')" class="palette-btn" style="background: linear-gradient(135deg, #0A1A2A, #C5A059); border: none; padding: 10px; border-radius: 10px; color: white; cursor: pointer; font-size: 12px;">🌊 Azul</button>
                                </div>
                            </div>
                            
                            <div style="border-top: 1px solid var(--border-suave); margin: 15px 0;"></div>
                            
                            <!-- COLORES PERSONALIZADOS -->
                            <h3 style="color: var(--color-dorado); margin-bottom: 15px; font-size: 0.9rem; display: flex; align-items: center; gap: 8px;">
                                <i class="fa-solid fa-sliders-h"></i> Colores Personalizados
                            </h3>
                            
                            <!-- Color Dorado -->
                            <div style="margin-bottom: 18px;">
                                <label style="display: block; margin-bottom: 6px; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px;">
                                    ✨ Dorado Mate (Acentos / Botones)
                                </label>
                                <div style="display: flex; gap: 10px; align-items: center;">
                                    <input type="color" id="color-dorado" value="${currentColors['--color-dorado']}" style="width: 50px; height: 45px; border-radius: 8px; border: 1px solid #333; cursor: pointer;">
                                    <input type="text" id="color-dorado-hex" value="${currentColors['--color-dorado']}" style="flex: 1; padding: 10px; border-radius: 8px; background: #111; border: 1px solid #333; color: #fff; font-family: monospace;">
                                </div>
                                <small style="color: #888; font-size: 0.7rem;">Usado en: botones, enlaces activos, bordes decorativos</small>
                            </div>
                            
                            <!-- Fondo Negro Carbón -->
                            <div style="margin-bottom: 18px;">
                                <label style="display: block; margin-bottom: 6px; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px;">
                                    🖤 Negro Carbón (Fondo Principal)
                                </label>
                                <div style="display: flex; gap: 10px; align-items: center;">
                                    <input type="color" id="bg-carbon" value="${currentColors['--bg-carbon']}" style="width: 50px; height: 45px; border-radius: 8px; border: 1px solid #333; cursor: pointer;">
                                    <input type="text" id="bg-carbon-hex" value="${currentColors['--bg-carbon']}" style="flex: 1; padding: 10px; border-radius: 8px; background: #111; border: 1px solid #333; color: #fff; font-family: monospace;">
                                </div>
                                <small style="color: #888; font-size: 0.7rem;">Usado en: fondo general del sistema</small>
                            </div>
                            
                            <!-- Gris Oxford (Tarjetas) -->
                            <div style="margin-bottom: 18px;">
                                <label style="display: block; margin-bottom: 6px; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px;">
                                    📦 Gris Oxford (Fondo Tarjetas)
                                </label>
                                <div style="display: flex; gap: 10px; align-items: center;">
                                    <input type="color" id="bg-gris" value="${currentColors['--bg-gris-oxford']}" style="width: 50px; height: 45px; border-radius: 8px; border: 1px solid #333; cursor: pointer;">
                                    <input type="text" id="bg-gris-hex" value="${currentColors['--bg-gris-oxford']}" style="flex: 1; padding: 10px; border-radius: 8px; background: #111; border: 1px solid #333; color: #fff; font-family: monospace;">
                                </div>
                                <small style="color: #888; font-size: 0.7rem;">Usado en: tarjetas, paneles, modales</small>
                            </div>
                            
                            <!-- Blanco Crema (Texto) -->
                            <div style="margin-bottom: 18px;">
                                <label style="display: block; margin-bottom: 6px; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px;">
                                    📝 Blanco Crema (Texto Principal)
                                </label>
                                <div style="display: flex; gap: 10px; align-items: center;">
                                    <input type="color" id="text-color" value="${currentColors['--text-blanco-crema']}" style="width: 50px; height: 45px; border-radius: 8px; border: 1px solid #333; cursor: pointer;">
                                    <input type="text" id="text-color-hex" value="${currentColors['--text-blanco-crema']}" style="flex: 1; padding: 10px; border-radius: 8px; background: #111; border: 1px solid #333; color: #fff; font-family: monospace;">
                                </div>
                                <small style="color: #888; font-size: 0.7rem;">Usado en: textos principales, títulos</small>
                            </div>
                            
                            <!-- Color de Bordes -->
                            <div style="margin-bottom: 18px;">
                                <label style="display: block; margin-bottom: 6px; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px;">
                                    🔲 Color de Bordes
                                </label>
                                <div style="display: flex; gap: 10px; align-items: center;">
                                    <input type="color" id="border-color" value="${currentColors['--border-fuerte']}" style="width: 50px; height: 45px; border-radius: 8px; border: 1px solid #333; cursor: pointer;">
                                    <input type="text" id="border-color-hex" value="${currentColors['--border-fuerte']}" style="flex: 1; padding: 10px; border-radius: 8px; background: #111; border: 1px solid #333; color: #fff; font-family: monospace;">
                                </div>
                                <small style="color: #888; font-size: 0.7rem;">Usado en: bordes de tarjetas, inputs, tablas</small>
                            </div>
                            
                            <!-- Logo -->
                            <div style="border-top: 1px solid var(--border-suave); margin: 15px 0; padding-top: 15px;">
                                <label style="display: block; margin-bottom: 8px; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px;">
                                    🏷️ Logotipo de la Empresa
                                </label>
                                <input type="text" id="input-logo" placeholder="URL del logo..." value="${logoGuardado || '/LIQUOUR/Assets/IMG/Logo.jpeg'}" style="width: 100%; padding: 10px; border-radius: 8px; background: #111; border: 1px solid #333; color: #fff;">
                                <small style="color: #888; font-size: 0.7rem;">Pega la URL de tu logo o cambia la imagen</small>
                            </div>
                        </div>
                    `,
                    background: '#1a1a1a',
                    showCancelButton: true,
                    confirmButtonText: '<i class="fa-solid fa-check"></i> Guardar Cambios',
                    cancelButtonText: 'Cancelar',
                    showDenyButton: true,
                    denyButtonText: '<i class="fa-solid fa-undo"></i> Restablecer',
                    buttonsStyling: false,
                    customClass: {
                        popup: 'modal-elegante',
                        confirmButton: 'swal2-confirm btn-guardar',
                        cancelButton: 'swal2-cancel btn-cancelar',
                        denyButton: 'swal2-deny btn-restablecer'
                    },
                    didOpen: () => {
                        // Función para sincronizar inputs de color
                        const syncColorInputs = (colorInput, hexInput, variable) => {
                            colorInput.addEventListener('input', () => {
                                hexInput.value = colorInput.value;
                                document.documentElement.style.setProperty(variable, colorInput.value);
                            });
                            hexInput.addEventListener('input', () => {
                                if (hexInput.value.match(/^#[0-9A-Fa-f]{6}$/)) {
                                    colorInput.value = hexInput.value;
                                    document.documentElement.style.setProperty(variable, hexInput.value);
                                }
                            });
                        };
                        
                        // Sincronizar todos los selectores
                        syncColorInputs(
                            document.getElementById('color-dorado'),
                            document.getElementById('color-dorado-hex'),
                            '--color-dorado'
                        );
                        syncColorInputs(
                            document.getElementById('bg-carbon'),
                            document.getElementById('bg-carbon-hex'),
                            '--bg-carbon'
                        );
                        syncColorInputs(
                            document.getElementById('bg-gris'),
                            document.getElementById('bg-gris-hex'),
                            '--bg-gris-oxford'
                        );
                        syncColorInputs(
                            document.getElementById('text-color'),
                            document.getElementById('text-color-hex'),
                            '--text-blanco-crema'
                        );
                        syncColorInputs(
                            document.getElementById('border-color'),
                            document.getElementById('border-color-hex'),
                            '--border-fuerte'
                        );
                    },
                    preConfirm: () => {
                        const newColors = {
                            '--color-dorado': document.getElementById('color-dorado').value,
                            '--bg-carbon': document.getElementById('bg-carbon').value,
                            '--bg-carbon-claro': adjustColor(document.getElementById('bg-carbon').value, 20),
                            '--bg-gris-oxford': document.getElementById('bg-gris').value,
                            '--text-blanco-crema': document.getElementById('text-color').value,
                            '--border-fuerte': document.getElementById('border-color').value
                        };
                        
                        const newLogo = document.getElementById('input-logo').value;
                        
                        return { colors: newColors, logo: newLogo };
                    }
                }).then((result) => {
                    if (result.isConfirmed && result.value) {
                        // Guardar colores
                        saveAllColors(result.value.colors);
                        // Guardar logo
                        localStorage.setItem('temaLogoLiquour', result.value.logo);
                        const logoImg = document.getElementById('logo-sistema');
                        if (logoImg) logoImg.src = result.value.logo;
                        
                        Swal.fire({
                            title: '¡Configuración Guardada!',
                            html: 'Los cambios se han aplicado en todo el sistema.',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false,
                            background: '#1a1a1a',
                            color: '#fff'
                        });
                    } else if (result.isDenied) {
                        // Restablecer colores por defecto
                        saveAllColors(defaultColors);
                        localStorage.removeItem('temaLogoLiquour');
                        localStorage.removeItem('liquour_colors_complete');
                        
                        Swal.fire({
                            title: 'Tema Restablecido',
                            html: 'Se ha vuelto al tema original de Liquour.',
                            icon: 'info',
                            timer: 1500,
                            showConfirmButton: false,
                            background: '#1a1a1a',
                            color: '#fff'
                        }).then(() => {
                            location.reload();
                        });
                    }
                });
            }
            else {
                Swal.fire({
                    title: '<span style="color:var(--color-dorado); letter-spacing: 2px;">EN CONSTRUCCIÓN</span>',
                    html: '<span style="color:#cccccc; font-weight: 300;">Estamos preparándote una experiencia VIP. ¡Pronto estará lista! 🚧</span>',
                    icon: 'info',
                    iconColor: 'var(--color-dorado)',
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
    
    // Función global para aplicar paleta y cerrar
    window.applyPaletteAndClose = function(paletteName) {
        const palette = applyPalette(paletteName);
        if (palette) {
            Swal.fire({
                title: '🎨 Paleta Aplicada',
                html: 'Los colores se han actualizado',
                icon: 'success',
                timer: 1200,
                showConfirmButton: false,
                background: '#1a1a1a'
            });
            // Cerrar cualquier modal abierto
            const modal = Swal.getPopup();
            if (modal) Swal.close();
        }
    };
    
    // Botón de salir
    const btnExit = document.querySelector('.btn-exit');
    if (btnExit) {
        btnExit.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.getAttribute('href');
            
            Swal.fire({
                title: '<span style="color:var(--color-dorado); letter-spacing: 2px;">¿CERRAR SESIÓN?</span>',
                html: '<span style="color:#cccccc; font-weight: 300;">Saldrás de tu panel de Liquour.</span>',
                icon: 'warning',
                iconColor: 'var(--color-dorado)',
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