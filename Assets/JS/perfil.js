document.addEventListener("DOMContentLoaded", () => {
    const barras = document.querySelectorAll('.chart-bar-fill');
    setTimeout(() => {
        barras.forEach(barra => {
            const contenedor = barra.closest('.chart-container');
            if (contenedor) {
                const porcentajeTexto = contenedor.querySelector('.chart-label span:last-child').textContent;
                barra.style.width = porcentajeTexto;
            }
        });
    }, 500);
});

window.ejecutarAccion = () => {
    if (confirm("¿Estás seguro de que deseas cerrar el turno y salir del sistema?")) {
        const btn = document.getElementById("btnLiquourAccion");
        const overlay = document.getElementById("logout-overlay");
        
        btn.classList.add("action-success");
        btn.textContent = "CERRANDO...";
        overlay.style.display = "flex";
        
        setTimeout(() => {
            window.location.href = "../../../Controller/Public/Logout.php";
        }, 2500);
    }
}