let cart = [];

function addToCart(name, price, img) {
    const existing = cart.find(item => item.name === name);
    if (existing) {
        existing.quantity++;
    } else {
        cart.push({ name, price, img, quantity: 1 });
    }
    renderCart();
}

function renderCart() {
    const container = document.getElementById('cart-container');
    const totalLabel = document.getElementById('grand-total');
    if (!container || !totalLabel) return;

    container.innerHTML = '';
    let total = 0;
    cart.forEach(item => {
        total += item.price * item.quantity;
        const div = document.createElement('div');
        div.className = 'cart-item';
        div.innerHTML = `
            <img src="${item.img}">
            <div class="cart-item-info">
                <h4>${item.name}</h4>
                <div style="font-weight:bold; color:#a39678">$ ${(item.price * item.quantity).toFixed(2)}</div>
            </div>
        `;
        container.appendChild(div);
    });
    totalLabel.innerText = `$ ${total.toFixed(2)}`;
}


function updateQty(index, newQty) {
    if (newQty < 1) return;
    cart[index].quantity = parseInt(newQty);
    
   
    const subCell = document.getElementById(`subtotal-${index}`);
    if (subCell) {
        subCell.innerText = `$ ${(cart[index].price * cart[index].quantity).toFixed(2)}`;
    }
    renderCart();
}

function openCheckoutModal() {
    const modal = document.getElementById('modal-checkout');
    const tableBody = document.getElementById('checkout-table-body');

    if (cart.length === 0) {
        alert("El carrito está vacío. ¡Agrega unas botellas primero!");
        return;
    }

    tableBody.innerHTML = '';
    cart.forEach((item, index) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><img src="${item.img}" style="width:40px;height:50px;object-fit:contain;"></td>
            <td>${item.name}</td>
            <td>$ ${item.price.toFixed(2)}</td>
            <td>
                <input type="number" value="${item.quantity}" min="1" 
                onchange="updateQty(${index}, this.value)"
                style="width:50px; background:#000; color:#fff; border:1px solid #333; text-align:center;">
            </td>
            <td id="subtotal-${index}" style="font-weight:bold; color:#f1e4bc;">$ ${(item.price * item.quantity).toFixed(2)}</td>
        `;
        tableBody.appendChild(tr);
    });

    modal.style.display = 'flex';
}

function clearCart() {
    cart = [];
    renderCart();
    document.getElementById('modal-checkout').style.display = 'none';
}


document.addEventListener('DOMContentLoaded', () => {
   
    const modalCheckout = document.getElementById('modal-checkout');
    const modalPerfil = document.getElementById('modal-perfil');
    const modalPayment = document.getElementById('modal-payment');


    document.getElementById('close-checkout').onclick = () => modalCheckout.style.display = 'none';
    document.getElementById('btn-cancel-checkout').onclick = () => modalCheckout.style.display = 'none';
    
    
    const btnConfirmCheckout = document.getElementById('btn-confirm-checkout');
    if(btnConfirmCheckout) {
        btnConfirmCheckout.onclick = (e) => {
            e.preventDefault();
            modalCheckout.style.display = 'none';
            modalPayment.style.display = 'flex'; 
        }
    }


    const btnPerfil = document.getElementById('btn-mi-perfil');
    if(btnPerfil) btnPerfil.onclick = (e) => { e.preventDefault(); modalPerfil.style.display = 'flex'; }
    document.getElementById('close-modal').onclick = () => modalPerfil.style.display = 'none';

    
    const closePayment = document.getElementById('close-payment');
    if(closePayment) closePayment.onclick = () => modalPayment.style.display = 'none';

    const btnProcesarPago = document.getElementById('btn-procesar-pago');
    if(btnProcesarPago) {
        btnProcesarPago.onclick = (e) => {
            e.preventDefault();
            alert('¡Pago procesado con éxito! Preparando el pedido...');
            clearCart(); 
            modalPayment.style.display = 'none';
        }
    }

    
    window.onclick = (e) => {
        if (e.target === modalCheckout) modalCheckout.style.display = 'none';
        if (e.target === modalPerfil) modalPerfil.style.display = 'none';
        if (e.target === modalPayment) modalPayment.style.display = 'none';
    };
});