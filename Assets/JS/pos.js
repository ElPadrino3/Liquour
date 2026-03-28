document.addEventListener('DOMContentLoaded', () => {
    const filterLinks = document.querySelectorAll('.categories a');
    const products = document.querySelectorAll('.product-card');

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
    let totalGeneral = 0;

    function renderCart() {
        cartBody.innerHTML = `
            <tr>
                <td>Cant.</td>
                <td>Producto</td>
                <td>P. Unit</td>
                <td>Subtotal</td>
            </tr>
        `;
        totalGeneral = 0;

        cart.forEach((item, index) => {
            const subtotal = item.qty * item.price;
            totalGeneral += subtotal;

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${item.qty}</td>
                <td>${item.name}</td>
                <td>$${item.price.toFixed(2)}</td>
                <td>$${subtotal.toFixed(2)}</td>
            `;
            tr.style.cursor = "pointer";
            tr.addEventListener('click', () => {
                cart.splice(index, 1);
                renderCart();
                updateAllCounters();
            });
            cartBody.appendChild(tr);
        });

        displayTotal.innerText = "$" + totalGeneral.toFixed(2);
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

        btnPlus.addEventListener('click', (e) => {
            e.stopPropagation();
            const cartItem = cart.find(i => i.name === itemName);
            if (cartItem) {
                cartItem.qty++;
            } else {
                cart.push({ name: itemName, price: itemPrice, qty: 1 });
            }
            renderCart();
            updateAllCounters();
        });

        btnMinus.addEventListener('click', (e) => {
            e.stopPropagation();
            const cartIndex = cart.findIndex(i => i.name === itemName);
            if (cartIndex > -1) {
                cart[cartIndex].qty--;
                if (cart[cartIndex].qty <= 0) {
                    cart.splice(cartIndex, 1);
                }
                renderCart();
                updateAllCounters();
            }
        });

        item.addEventListener('click', () => {
            btnPlus.click();
            item.style.backgroundColor = "#fff";
            setTimeout(() => { item.style.backgroundColor = ""; }, 100);
        });
    });

    btnReset.addEventListener('click', () => {
        cart = [];
        renderCart();
        updateAllCounters();
    });

    btnVoid.addEventListener('click', () => {
        if(cart.length > 0) {
            cart.pop();
            renderCart();
            updateAllCounters();
        }
    });

    btnOpPlus.addEventListener('click', () => {
        if(cart.length > 0) {
            cart[cart.length - 1].qty++;
            renderCart();
            updateAllCounters();
        }
    });

    btnOpMinus.addEventListener('click', () => {
        if(cart.length > 0) {
            cart[cart.length - 1].qty--;
            if(cart[cart.length - 1].qty <= 0) cart.pop();
            renderCart();
            updateAllCounters();
        }
    });

    numButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            let valor = btn.innerText.trim();
            if (valor === ".00") {
                if (!inputCalculadora.includes(".")) inputCalculadora += ".00";
            } else {
                inputCalculadora += valor;
            }
            displayTotal.innerText = "$" + inputCalculadora;
        });
    });

    btnEqual.addEventListener('click', () => {
        if (inputCalculadora !== "") {
            const pago = parseFloat(inputCalculadora);
            const cambio = pago - totalGeneral;
            if (cambio >= 0) {
                displayTotal.innerText = "Cmb: $" + cambio.toFixed(2);
            } else {
                displayTotal.innerText = "Falta: $" + Math.abs(cambio).toFixed(2);
            }
            inputCalculadora = "";
        }
    });

    const adjustHeight = () => {
        let vh = window.innerHeight * 0.01;
        document.documentElement.style.setProperty('--vh', `${vh}px`);
    };
    window.addEventListener('resize', adjustHeight);
    adjustHeight();

    const btnKeyboard = document.getElementById('toggle-keyboard');
    const keyboardArea = document.querySelector('.buttons');
    const orderWindow = document.querySelector('.order-window');

    btnKeyboard.addEventListener('click', () => {
        const isHidden = keyboardArea.classList.toggle('hidden-keyboard');
        if (isHidden) {
            orderWindow.style.height = "90vh"; 
            btnKeyboard.style.backgroundColor = "#C5A059"; 
            btnKeyboard.style.color = "#1A1A1A";           
            btnKeyboard.querySelector('i').style.color = "#1A1A1A"; 
        } else {
            orderWindow.style.height = "40vh"; 
            btnKeyboard.style.backgroundColor = ""; 
            btnKeyboard.style.color = "";           
            btnKeyboard.querySelector('i').style.color = ""; 
        }
    });
    
    renderCart();
});