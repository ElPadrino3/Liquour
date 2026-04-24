// ============================================
// THEME MANAGER - LIQUOUR
// ============================================

// COLORES ORIGINALES (sin azul)
const DEFAULT_COLORS = {
    '--color-dorado': '#C5A059',
    '--color-dorado-oscuro': '#A8883A',
    '--color-dorado-claro': '#D4B87A',
    '--bg-carbon': '#1A1A1A',
    '--bg-carbon-claro': '#2A2A2A',
    '--bg-carbon-oscuro': '#0D0D0D',
    '--bg-gris-oxford': '#4A4A4A',
    '--text-blanco-crema': '#F5F5DC',
    '--text-gris-claro': '#D0D0D0',
    '--border-fuerte': '#4A4A4A'
};

// Aplicar colores
function applyTheme(colors) {
    const root = document.documentElement;
    for (const [key, value] of Object.entries(colors)) {
        root.style.setProperty(key, value);
    }
}

// Cargar tema guardado
function loadTheme() {
    const saved = localStorage.getItem('liquour_colors');
    if (saved) {
        try {
            const colors = JSON.parse(saved);
            applyTheme(colors);
        } catch(e) {
            applyTheme(DEFAULT_COLORS);
        }
    } else {
        applyTheme(DEFAULT_COLORS);
    }
}

// Guardar tema
function saveTheme(colors) {
    localStorage.setItem('liquour_colors', JSON.stringify(colors));
    applyTheme(colors);
}

// Resetear a colores originales
function resetTheme() {
    saveTheme(DEFAULT_COLORS);
    location.reload();
}

// Inicializar
document.addEventListener('DOMContentLoaded', function() {
    loadTheme();
    
    // Cargar logo guardado
    const savedLogo = localStorage.getItem('liquour_logo');
    if (savedLogo) {
        const logoImg = document.getElementById('logo-sistema');
        if (logoImg) logoImg.src = savedLogo;
    }
});

// Exponer funciones globalmente
window.saveTheme = saveTheme;
window.resetTheme = resetTheme;
window.loadTheme = loadTheme;