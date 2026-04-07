document.addEventListener('DOMContentLoaded', () => {
    const filterLinks = document.querySelectorAll('.categories a');
    const products = document.querySelectorAll('.product-card');

    function mostrarNotificacion(mensaje, tipo = 'error') {
        const toast = document.createElement('div');
        const bg = tipo === 'exito' ? '#C5A059' : '#ff4444';
        const color = tipo === 'exito' ? '#1A1A1A' : '#fff';
        const icon = tipo === 'exito' ? '<i class="fas fa-check-circle"></i> ' : '<i class="fas fa-exclamation-circle"></i> ';
        
        toast.innerHTML = icon + mensaje;
        toast.style.cssText = `position:fixed; top:20px; right:20px; background:${bg}; color:${color}; padding:15px 25px; border-radius:8px; font-weight:bold; z-index:99999; box-shadow:0 5px 15px rgba(0,0,0,0.5); transform:translateX(150%); transition:0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); display:flex; align-items:center; gap:10px; font-size:1.1rem;`;
        
        document.body.appendChild(toast);
        setTimeout(() => toast.style.transform = 'translateX(0)', 50);
        setTimeout(() => {
            toast.style.transform = 'translateX(150%)';
            setTimeout(() => toast.remove(), 400);
        }, 3000);
    }

    filterLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const filterValue = link.getAttribute('data-filter');
            filterLinks.forEach(l => l.style.color = ''); 
            link.style.color = '#C5A059';
            products.forEach(prod => {
                const prodCat = prod.getAttribute('data-categoria');
                if (filterValue === 'Todos' || prodCat === filterValue) {
                    prod.style.display = 'flex'; 
                } else {
                    prod.style.display = 'none';
                }
            });
        });
    });

    const displayTotal = document.getElementById('display-total');
    const cartBody = document.getElementById('cart-body');
    const btnReset = document.querySelector('.op-reset');
    const btnVoid = document.querySelector('.op-void');
    const btnOpPlus = document.querySelector('.op-plus');
    const btnOpMinus = document.querySelector('.op-minus');
    const numButtons = document.querySelectorAll('.btn-num');
    const btnEqual = document.querySelector('.op-equal');
    
    let cart = [];
    let inputCalculadora = "";
    window.totalGeneral = 0;

    function renderCart() {
        cartBody.innerHTML = `<tr><td>Cant.</td><td>Producto</td><td>P. Unit</td><td>Subtotal</td></tr>`;
        window.totalGeneral = 0;
        cart.forEach((item, index) => {
            const subtotal = item.qty * item.price;
            window.totalGeneral += subtotal;
            const tr = document.createElement('tr');
            tr.innerHTML = `<td>${item.qty}</td><td>${item.name}</td><td>$${item.price.toFixed(2)}</td><td>$${subtotal.toFixed(2)}</td>`;
            tr.style.cursor = "pointer";
            tr.addEventListener('click', () => {
                cart.splice(index, 1);
                renderCart();
                updateAllCounters();
            });
            cartBody.appendChild(tr);
        });
        displayTotal.innerText = "$" + window.totalGeneral.toFixed(2);
        inputCalculadora = "";
    }

    function updateAllCounters() {
        products.forEach(item => {
            const itemName = item.querySelector('.item').innerText;
            const qtyCounter = item.querySelector('.qty-counter');
            const cartItem = cart.find(i => i.name === itemName);
            qtyCounter.innerText = cartItem ? cartItem.qty : "";
        });
    }

    products.forEach(item => {
        const btnPlus = item.querySelector('.btn-plus');
        const btnMinus = item.querySelector('.btn-minus');
        const itemName = item.querySelector('.item').innerText;
        const itemPriceText = item.querySelector('.product-price').innerText;
        const itemPrice = parseFloat(itemPriceText.replace('$', '').trim());
        const idProducto = btnPlus.getAttribute('data-id');
        const stockMaximo = parseInt(btnPlus.getAttribute('data-stock')) || 0;

        btnPlus.addEventListener('click', (e) => {
            e.stopPropagation();
            const cartItem = cart.find(i => i.name === itemName);
            
            if (cartItem) {
                if (cartItem.qty < stockMaximo) {
                    cartItem.qty++;
                } else {
                    mostrarNotificacion("Stock insuficiente. Solo hay " + stockMaximo + " disponibles.");
                }
            } else {
                if (stockMaximo > 0) {
                    cart.push({ id: idProducto, name: itemName, price: itemPrice, qty: 1 });
                } else {
                    mostrarNotificacion("Producto agotado.");
                }
            }
            renderCart();
            updateAllCounters();
        });

        btnMinus.addEventListener('click', (e) => {
            e.stopPropagation();
            const cartIndex = cart.findIndex(i => i.name === itemName);
            if (cartIndex > -1) {
                cart[cartIndex].qty--;
                if (cart[cartIndex].qty <= 0) cart.splice(cartIndex, 1);
                renderCart();
                updateAllCounters();
            }
        });

        item.addEventListener('click', () => { 
            if (stockMaximo > 0) {
                const cartItem = cart.find(i => i.name === itemName);
                if (cartItem && cartItem.qty >= stockMaximo) {
                    mostrarNotificacion("Stock insuficiente. Solo hay " + stockMaximo + " disponibles.");
                    return;
                }
                btnPlus.click(); 
                item.style.backgroundColor = "#fff"; 
                setTimeout(() => { item.style.backgroundColor = ""; }, 100); 
            } else {
                mostrarNotificacion("Producto agotado.");
            }
        });
    });

    btnReset.addEventListener('click', () => { cart = []; renderCart(); updateAllCounters(); });
    btnVoid.addEventListener('click', () => { if(cart.length > 0) { cart.pop(); renderCart(); updateAllCounters(); } });
    btnOpPlus.addEventListener('click', () => { if(cart.length > 0) { cart[cart.length - 1].qty++; renderCart(); updateAllCounters(); } });
    btnOpMinus.addEventListener('click', () => { if(cart.length > 0) { cart[cart.length - 1].qty--; if(cart[cart.length - 1].qty <= 0) cart.pop(); renderCart(); updateAllCounters(); } });

    numButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            let valor = btn.innerText.trim();
            if (valor === ".00") { if (!inputCalculadora.includes(".")) inputCalculadora += ".00"; } else { inputCalculadora += valor; }
            displayTotal.innerText = "$" + inputCalculadora;
        });
    });

    btnEqual.addEventListener('click', () => {
        if (inputCalculadora !== "") {
            const pago = parseFloat(inputCalculadora);
            const cambio = pago - window.totalGeneral;
            displayTotal.innerText = cambio >= 0 ? "Cmb: $" + cambio.toFixed(2) : "Falta: $" + Math.abs(cambio).toFixed(2);
            inputCalculadora = "";
        }
    });

    const modalPago = document.getElementById('modal-pago-efectivo');
    const inputRecibido = document.getElementById('efectivo-recibido');
    const spanCambio = document.getElementById('pago-cambio');

    window.abrirModalEfectivo = function() {
        if (window.totalGeneral <= 0) { 
            mostrarNotificacion("El carrito está vacío."); 
            return; 
        }
        document.getElementById('pago-total-mostrar').innerText = "$" + window.totalGeneral.toFixed(2);
        inputRecibido.value = "";
        spanCambio.innerText = "$0.00";
        spanCambio.style.color = "#F5F5DC"; 
        modalPago.style.display = 'flex';
        setTimeout(() => inputRecibido.focus(), 300);
    };

    const cerrarModal = function() {
        modalPago.style.display = 'none';
    };
    
    document.getElementById('cerrar-modal-pago').addEventListener('click', cerrarModal);
    document.getElementById('btn-cancelar-pago').addEventListener('click', cerrarModal);

    inputRecibido.addEventListener('input', function() {
        const recibido = parseFloat(this.value) || 0;
        const cambio = recibido - window.totalGeneral;
        
        if (cambio >= 0) {
            spanCambio.innerText = "$" + cambio.toFixed(2);
            spanCambio.style.color = "#C5A059";
        } else {
            spanCambio.innerText = "Falta: $" + Math.abs(cambio).toFixed(2);
            spanCambio.style.color = "#ff4444";
        }
    });

    document.getElementById('btn-finalizar-venta').addEventListener('click', function() {
        const recibido = parseFloat(inputRecibido.value) || 0;
        
        if (recibido < window.totalGeneral) { 
            mostrarNotificacion("El monto recibido es insuficiente."); 
            return; 
        }

        const datosVenta = {
            total: window.totalGeneral,
            carrito: cart
        };

        fetch('procesar_venta.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(datosVenta)
        })
        .then(response => response.text())
        .then(text => {
            try {
                const data = JSON.parse(text);
                if (data.success) {
                    cerrarModal();
                    mostrarNotificacion("¡Venta Exitosa!", "exito");
                    setTimeout(() => location.reload(), 1500);
                } else {
                    cerrarModal();
                    mostrarNotificacion(data.error);
                }
            } catch (e) {
                cerrarModal();
                console.error("Respuesta cruda del servidor:", text);
                mostrarNotificacion("Fallo de base de datos. Revisa la consola F12.");
            }
        })
        .catch(error => {
            cerrarModal();
            mostrarNotificacion("Error de conexión con el servidor.");
            console.error(error);
        });
    });

    renderCart();
});