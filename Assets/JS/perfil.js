document.addEventListener("DOMContentLoaded", () => {
    const barras = document.querySelectorAll('.chart-bar-fill');
    setTimeout(() => {
        barras.forEach(barra => {
            barra.style.width = barra.getAttribute('data-width');
        });
    }, 300);
});

const ejecutarAccion = () => {
    const btn = document.getElementById("btnLiquourAccion");
    btn.classList.toggle("action-success");
    btn.textContent = btn.classList.contains("action-success") ? "TURNO CERRADO" : "CERRAR TURNO";
}