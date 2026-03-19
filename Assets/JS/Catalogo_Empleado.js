let cart = [];

function addToCart(name, price, img) {
    cart.push({ name, price, img });
    renderCart();
}

function renderCart() {
    const container = document.getElementById('cart-container');
    const totalLabel = document.getElementById('grand-total');
    
    if (!container || !totalLabel) return;

    container.innerHTML = '';
    let total = 0;

    cart.forEach(item => {
        total += item.price;
        const div = document.createElement('div');
        div.className = 'cart-item';
        div.innerHTML = `
            <img src="${item.img}">
            <div class="cart-item-info">
                <h4>${item.name}</h4>
                <div style="font-weight:bold; color:#a39678">$ ${item.price.toFixed(2)}</div>
            </div>
        `;
        container.appendChild(div);
    });

    totalLabel.innerText = `$ ${total.toFixed(2)}`;
}

document.addEventListener('DOMContentLoaded', () => {
    const btnPerfil = document.getElementById('btn-mi-perfil');
    const modalPerfil = document.getElementById('modal-perfil');
    const btnCerrar = document.getElementById('close-modal');

    if (btnPerfil && modalPerfil) {
        btnPerfil.addEventListener('click', (e) => {
            e.preventDefault();
            modalPerfil.style.display = 'flex';
        });
    }

    if (btnCerrar && modalPerfil) {
        btnCerrar.addEventListener('click', () => {
            modalPerfil.style.display = 'none';
        });
    }

    window.addEventListener('click', (e) => {
        if (modalPerfil && e.target === modalPerfil) {
            modalPerfil.style.display = 'none';
        }
    });
});