let cart = [];

function addToCart(name, price, img) {
    cart.push({ name, price, img });
    renderCart();
}

function renderCart() {
    const container = document.getElementById('cart-container');
    const totalLabel = document.getElementById('grand-total');
    
    container.innerHTML = '';
    let total = 0;

    cart.forEach(item => {
        total += item.price;
        const div = document.createElement('div');
        div.className = 'cart-item';
        div.innerHTML = `
            <img src="${item.img}">
            <div class="cart-item-data">
                <div>${item.name}</div>
                <div style="font-weight:bold">$ ${item.price.toFixed(2)}</div>
            </div>
        `;
        container.appendChild(div);
    });

    totalLabel.innerText = `$ ${total.toFixed(2)}`;
}