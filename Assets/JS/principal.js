document.addEventListener("DOMContentLoaded", function() {
    const header = document.getElementById('mainHeader');

    // Efecto de Navbar al hacer Scroll
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });

    // Pequeño efecto de entrada para las tarjetas (opcional, le da un toque muy pro)
    const cards = document.querySelectorAll('.feature-card');
    
    // Configuramos las tarjetas transparentes y un poco abajo al inicio
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = 'all 0.6s ease ' + (index * 0.2) + 's'; // Retraso en cascada
    });

    // Animamos un poco después de cargar la página
    setTimeout(() => {
        cards.forEach(card => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        });
    }, 300);
});