window.procesarPDFBackend = function(carrito, subtotal, descuento, total, recibido, cambio, vendedor, fecha, accion) {
    const datosVenta = { carrito, subtotal, descuento, total, recibido, cambio, vendedor, fecha, accion };
    fetch('../empleado/Generar_Ticket.php',{
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(datosVenta)
    })
    .then(response => {
        if (!response.ok) throw new Error("Error al generar PDF");
        return response.blob();
    })
    .then(blob => {
        const url = URL.createObjectURL(blob);
        if (accion === 'D') {
            const a = document.createElement('a');
            a.href = url;
            a.download = `Ticket_Liquour_${new Date().getTime()}.pdf`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        } else {
            window.open(url, '_blank');
        }
        setTimeout(() => URL.revokeObjectURL(url), 10000);
    })
    .catch(error => {
        console.error(error);
        alert("Ocurrió un error al generar el ticket PDF.");
    });
};

window.manejarImpresion = function(idVenta) {
    if (window.datosVentasHistoricas && window.datosVentasHistoricas[idVenta]) {
        const v = window.datosVentasHistoricas[idVenta];
        window.imprimirTicketDirecto(v.carrito, v.subtotal, v.descuento, v.total, v.total, 0, v.vendedor, v.fecha);
    } else {
        console.error("No se encontraron los datos para la venta: " + idVenta);
    }
};

window.manejarDescarga = function(idVenta) {
    if (window.datosVentasHistoricas && window.datosVentasHistoricas[idVenta]) {
        const v = window.datosVentasHistoricas[idVenta];
        window.descargarTicketDirecto(v.carrito, v.subtotal, v.descuento, v.total, v.total, 0, v.vendedor, v.fecha);
    } else {
        console.error("No se encontraron los datos para la venta: " + idVenta);
    }
};

window.imprimirTicketDirecto = function(carrito, subtotal, descuento, total, recibido, cambio, vendedor, fecha = null) {
    let f = fecha ? fecha : new Date().toLocaleString('es-ES');
    window.procesarPDFBackend(carrito, subtotal, descuento, total, recibido, cambio, vendedor, f, 'I');
};

window.descargarTicketDirecto = function(carrito, subtotal, descuento, total, recibido, cambio, vendedor, fecha = null) {
    let f = fecha ? fecha : new Date().toLocaleString('es-ES');
    window.procesarPDFBackend(carrito, subtotal, descuento, total, recibido, cambio, vendedor, f, 'D');
};

window.imprimirTicket = function(carritoStr, subtotal, descuento, total, recibido, cambio, vendedor = null) {
    try {
        const carrito = JSON.parse(decodeURIComponent(carritoStr));
        const vendedor_decodificado = vendedor ? decodeURIComponent(vendedor) : (window.vendedorActual || "Usuario");
        window.imprimirTicketDirecto(carrito, subtotal, descuento, total, recibido, cambio, vendedor_decodificado);
    } catch(e) {
        console.error(e);
    }
};

window.descargarTicket = function(carritoStr, subtotal, descuento, total, recibido, cambio, vendedor = null) {
    try {
        const carrito = JSON.parse(decodeURIComponent(carritoStr));
        const vendedor_decodificado = vendedor ? decodeURIComponent(vendedor) : (window.vendedorActual || "Usuario");
        window.descargarTicketDirecto(carrito, subtotal, descuento, total, recibido, cambio, vendedor_decodificado);
    } catch(e) {
        console.error(e);
    }
};

document.addEventListener('DOMContentLoaded', () => {
    function mostrarNotificacion(mensaje, tipo = 'info') {
        const doradoMate = getComputedStyle(document.documentElement).getPropertyValue('--dorado-mate').trim() || '#C5A059';
        const bg = tipo === 'exito' ? doradoMate : (tipo === 'error' ? '#e74c3c' : '#4A4A4A');
        const color = tipo === 'exito' ? '#1A1A1A' : '#FFFFFF';
        const icon = tipo === 'exito' ? '<i class="fas fa-check-circle"></i> ' : (tipo === 'error' ? '<i class="fas fa-exclamation-circle"></i> ' : '<i class="fas fa-info-circle"></i> ');
        toast.innerHTML = icon + mensaje;
        toast.style.cssText = `position:fixed; top:20px; right:20px; background:${bg}; color:${color}; padding:15px 25px; border-radius:8px; font-weight:bold; z-index:999999; box-shadow:0 5px 15px rgba(0,0,0,0.5); transform:translateX(150%); transition:0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); display:flex; align-items:center; gap:10px; font-size:1rem;`;
        document.body.appendChild(toast);
        setTimeout(() => toast.style.transform = 'translateX(0)', 50);
        setTimeout(() => {
            toast.style.transform = 'translateX(150%)';
            setTimeout(() => toast.remove(), 400);
        }, 3000);
    }

    function actualizarEstadoProducto(prod, nuevoStock) {
        const stockPill = prod.querySelector('.stock-pill');
        const btnPlus = prod.querySelector('.btn-plus');
        const btnMinus = prod.querySelector('.btn-minus');
        const toggleIcon = prod.querySelector('.toggle-icon');
        const qtySpan = prod.querySelector('.qty-counter');
        const productCard = prod;
        const doradoMate = getComputedStyle(document.documentElement).getPropertyValue('--dorado-mate').trim() || '#C5A059';
        
        stockPill.innerText = `STOCK ${nuevoStock}`;
        if (btnPlus) btnPlus.setAttribute('data-stock', nuevoStock);
        
        if (nuevoStock <= 0) {
            productCard.classList.add('sin-stock');
            stockPill.classList.add('critico');
            stockPill.classList.remove('bajo');
            if (toggleIcon) {
                toggleIcon.style.pointerEvents = 'none';
                toggleIcon.style.opacity = '0.4';
                toggleIcon.style.cursor = 'not-allowed';
                if (toggleIcon.classList.contains('fa-toggle-on')) {
                    toggleIcon.classList.replace('fa-toggle-on', 'fa-toggle-off');
                    toggleIcon.classList.remove('active-gold');
                }
            }
            if (qtySpan) qtySpan.innerText = "0";
            const itemName = prod.querySelector('.item').innerText;
            cart = cart.filter(i => i.name !== itemName);
            if (descuentosIndividuales[itemName]) delete descuentosIndividuales[itemName];
            renderCart();
        } else if (nuevoStock <= 5) {
            stockPill.classList.add('critico');
            stockPill.classList.remove('bajo');
            if (toggleIcon) {
                toggleIcon.style.pointerEvents = 'auto';
                toggleIcon.style.opacity = '1';
                toggleIcon.style.cursor = 'pointer';
            }
            if (productCard.classList.contains('sin-stock')) {
                productCard.classList.remove('sin-stock');
            }
        } else if (nuevoStock <= 15) {
            stockPill.classList.add('bajo');
            stockPill.classList.remove('critico');
            if (toggleIcon) {
                toggleIcon.style.pointerEvents = 'auto';
                toggleIcon.style.opacity = '1';
                toggleIcon.style.cursor = 'pointer';
            }
            if (productCard.classList.contains('sin-stock')) {
                productCard.classList.remove('sin-stock');
            }
        } else {
            stockPill.classList.remove('critico', 'bajo');
            if (toggleIcon) {
                toggleIcon.style.pointerEvents = 'auto';
                toggleIcon.style.opacity = '1';
                toggleIcon.style.cursor = 'pointer';
            }
            if (productCard.classList.contains('sin-stock')) {
                productCard.classList.remove('sin-stock');
            }
        }
    }

    const categorySelect = document.getElementById('category-select');
    const products = document.querySelectorAll('.product-card');

    if(categorySelect) {
        categorySelect.addEventListener('change', (e) => {
            const filterValue = e.target.value;
            products.forEach(prod => {
                const prodCat = prod.getAttribute('data-categoria');
                if (filterValue === 'Todos' || prodCat === filterValue) {
                    prod.style.display = 'flex'; 
                } else {
                    prod.style.display = 'none';
                }
            });
        });
    }

    const searchInput = document.getElementById('search-input');
    if(searchInput) {
        searchInput.addEventListener('input', (e) => {
            const searchTerm = e.target.value.toLowerCase();
            products.forEach(prod => {
                const prodName = prod.querySelector('.item').innerText.toLowerCase();
                const prodCode = prod.getAttribute('data-codigo').toLowerCase(); 
                if (prodName.includes(searchTerm) || prodCode.includes(searchTerm)) {
                    prod.style.display = 'flex';
                } else {
                    prod.style.display = 'none';
                }
            });
        });

        searchInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                const codigoEscrito = searchInput.value.trim();
                if (codigoEscrito.length > 0) {
                    procesarCodigoEscaneado(codigoEscrito); 
                    searchInput.value = ''; 
                    searchInput.dispatchEvent(new Event('input')); 
                }
            }
        });
    }

    const cartBody = document.getElementById('cart-body');
    const displayTotal = document.getElementById('display-total');
    const displayDiscount = document.getElementById('display-discount');
    const totProd = document.getElementById('tot-prod');
    const totDesc = document.getElementById('tot-desc');
    
    let cart = [];
    window.totalGeneral = 0;
    window.subtotalSinDescuento = 0;
    window.totalDescuentoAplicado = 0;
    let descuentoGlobalPorcentaje = 0; 
    let descuentosIndividuales = {}; 

    function renderCart() {
        const doradoMate = getComputedStyle(document.documentElement).getPropertyValue('--dorado-mate').trim() || '#C5A059';
        cartBody.innerHTML = '';
        let subtotalGeneral = 0;
        let totalItems = 0;
        let totalDescuentoCalculado = 0;

        cart.forEach((item, index) => {
            const subtotalOriginal = item.qty * item.price;
            let porcentajeDescuentoItem = descuentosIndividuales[item.name] || 0;
            let descuentoAplicadoAlItem = subtotalOriginal * (porcentajeDescuentoItem / 100);
            const subtotalConDescuento = subtotalOriginal - descuentoAplicadoAlItem;

            subtotalGeneral += subtotalOriginal;
            totalItems += item.qty;
            totalDescuentoCalculado += descuentoAplicadoAlItem;

            const itemRow = document.createElement('div');
            itemRow.style.cssText = `display: flex; justify-content: space-between; padding: 10px; border-bottom: 1px solid #ccc; font-size: 0.85rem; font-weight: bold; cursor: pointer; color: #161616; align-items: center;`;
            let tagDescuento = porcentajeDescuentoItem > 0 ? `<span style="color:#e74c3c; font-size:0.7rem; display:block;">(-${porcentajeDescuentoItem}%)</span>` : "";

            itemRow.innerHTML = `
                <span style="width: 15%;">${item.qty}</span>
                <span style="flex: 1;">${item.name} ${tagDescuento}</span>
                <span style="width: 20%; color: ${doradoMate}; font-weight: bold;">$${item.price.toFixed(2)}</span>
                <span style="width: 25%; text-align: right; padding-right: 15px; color: ${doradoMate};">$${subtotalConDescuento.toFixed(2)}</span>
                <i class="fas fa-trash-alt" style="color:#e74c3c;"></i>
            `;
            itemRow.addEventListener('click', () => {
                const nameRemoved = item.name;
                cart.splice(index, 1);
                if(descuentosIndividuales[nameRemoved]) delete descuentosIndividuales[nameRemoved];
                renderCart();
                products.forEach(prod => {
                    if(prod.querySelector('.item').innerText === nameRemoved) {
                        const tIcon = prod.querySelector('.toggle-icon');
                        const qtyS = prod.querySelector('.qty-counter');
                        tIcon.classList.replace('fa-toggle-on', 'fa-toggle-off');
                        tIcon.classList.remove('active-gold');
                        qtyS.innerText = "0";
                    }
                });
            });
            cartBody.appendChild(itemRow);
        });

        if(descuentoGlobalPorcentaje > 0) {
            let descGlobalMonto = subtotalGeneral * (descuentoGlobalPorcentaje / 100);
            totalDescuentoCalculado += descGlobalMonto;
        }

        window.subtotalSinDescuento = subtotalGeneral;
        window.totalDescuentoAplicado = totalDescuentoCalculado;
        window.totalGeneral = subtotalGeneral - totalDescuentoCalculado;
        if(window.totalGeneral < 0) window.totalGeneral = 0;

        if(totProd) totProd.innerText = totalItems;
        if(totDesc) totDesc.innerText = "$" + window.totalGeneral.toFixed(2);
        if(displayDiscount) displayDiscount.innerText = "-$" + totalDescuentoCalculado.toFixed(2);
        if(displayTotal) displayTotal.innerText = "$" + window.totalGeneral.toFixed(2);
        if(displayTotal) displayTotal.style.color = doradoMate;
        if(displayDiscount) displayDiscount.style.color = doradoMate;
    }

    products.forEach(item => {
        const btnPlus = item.querySelector('.btn-plus');
        const btnMinus = item.querySelector('.btn-minus');
        const toggleIcon = item.querySelector('.toggle-icon');
        const eyeIcon = item.querySelector('.eye-icon');
        const qtySpan = item.querySelector('.qty-counter');
        const stockPill = item.querySelector('.stock-pill');
        
        const itemName = item.querySelector('.item').innerText;
        const itemPriceText = item.querySelector('.product-price-pill').innerText;
        const itemPrice = parseFloat(itemPriceText.replace('$', '').trim());
        const idProducto = btnPlus.getAttribute('data-id');
        let stockMaximo = parseInt(stockPill.innerText.replace('STOCK', '').trim()) || 0;

        if (stockMaximo <= 0) {
            actualizarEstadoProducto(item, stockMaximo);
            if (toggleIcon) {
                toggleIcon.style.pointerEvents = 'none';
                toggleIcon.style.opacity = '0.4';
            }
        }

        btnMinus.addEventListener('click', () => {
            let actual = parseInt(qtySpan.innerText);
            if (actual > 0) {
                actual--;
                qtySpan.innerText = actual;
                if(toggleIcon.classList.contains('fa-toggle-on')) {
                    const cartItem = cart.find(i => i.name === itemName);
                    if(cartItem) {
                        cartItem.qty = actual;
                        if(actual === 0) {
                            cart = cart.filter(i => i.name !== itemName);
                            toggleIcon.classList.replace('fa-toggle-on', 'fa-toggle-off');
                            toggleIcon.classList.remove('active-gold');
                            if(descuentosIndividuales[itemName]) delete descuentosIndividuales[itemName];
                        }
                        renderCart();
                    }
                }
            }
        });

        btnPlus.addEventListener('click', () => {
            let actual = parseInt(qtySpan.innerText);
            if (actual < stockMaximo) {
                actual++;
                qtySpan.innerText = actual;
                if(toggleIcon.classList.contains('fa-toggle-on')) {
                    const cartItem = cart.find(i => i.name === itemName);
                    if(cartItem) {
                        cartItem.qty = actual;
                        renderCart();
                    }
                }
            } else {
                mostrarNotificacion(`Solo quedan ${stockMaximo} en stock.`, 'error');
            }
        });

        toggleIcon.addEventListener('click', () => {
            if(stockMaximo <= 0) {
                mostrarNotificacion("Producto agotado", 'error');
                return;
            }
            let actual = parseInt(qtySpan.innerText);
            if(toggleIcon.classList.contains('fa-toggle-off')) {
                if(actual === 0) {
                    actual = 1;
                    qtySpan.innerText = actual;
                }
                toggleIcon.classList.replace('fa-toggle-off', 'fa-toggle-on');
                toggleIcon.classList.add('active-gold');
                cart.push({ id: idProducto, name: itemName, price: itemPrice, qty: actual });
                renderCart();
            } else {
                toggleIcon.classList.replace('fa-toggle-on', 'fa-toggle-off');
                toggleIcon.classList.remove('active-gold');
                qtySpan.innerText = "0";
                cart = cart.filter(i => i.name !== itemName);
                if(descuentosIndividuales[itemName]) delete descuentosIndividuales[itemName];
                renderCart();
            }
        });

        eyeIcon.addEventListener('click', () => {
            const imgSrc = item.querySelector('.product-img').src;
            const cat = item.getAttribute('data-categoria');
            const cod = item.getAttribute('data-codigo');
            const doradoMate = getComputedStyle(document.documentElement).getPropertyValue('--dorado-mate').trim() || '#C5A059';
            document.getElementById('det-img').src = imgSrc;
            document.getElementById('det-nombre').innerText = itemName;
            document.getElementById('det-cat').innerText = cat;
            document.getElementById('det-cod').innerText = cod;
            document.getElementById('det-stock').innerText = stockMaximo;
            document.getElementById('det-precio').innerText = itemPriceText;
            document.getElementById('det-precio').style.color = doradoMate;
            document.getElementById('modal-detalles-producto').style.display = 'flex';
        });
    });

    document.getElementById('cerrar-modal-detalles')?.addEventListener('click', () => {
        document.getElementById('modal-detalles-producto').style.display = 'none';
    });

    const modalVentas = document.getElementById('modal-ultimas-ventas');
    document.getElementById('btn-abrir-ventas')?.addEventListener('click', () => {
        modalVentas.style.display = 'flex';
        document.getElementById('dot-ventas').style.display = 'none';
    });
    document.getElementById('cerrar-modal-ventas')?.addEventListener('click', () => { modalVentas.style.display = 'none'; });
    document.getElementById('btn-cerrar-ventas-sec')?.addEventListener('click', () => { modalVentas.style.display = 'none'; });

    const btnDescuento = document.getElementById('btn-descuento');
    const modalAvanzadoDesc = document.getElementById('modal-descuentos-avanzado');
    
    if (btnDescuento && modalAvanzadoDesc) {
        btnDescuento.addEventListener('click', () => {
            if (cart.length === 0) {
                mostrarNotificacion("Agrega productos al carrito primero.", 'info');
                return;
            }
            const doradoMate = getComputedStyle(document.documentElement).getPropertyValue('--dorado-mate').trim() || '#C5A059';
            const descTableBody = document.getElementById('desc-table-body');
            descTableBody.innerHTML = '';
            cart.forEach((item, index) => {
                const descPorcentajeAnterior = descuentosIndividuales[item.name] || 0;
                const tr = document.createElement('tr');
                tr.style.borderBottom = "1px solid #4A4A4A";
                tr.innerHTML = `
                    <td style="padding: 10px;">${item.qty}x</td>
                    <td style="padding: 10px;">${item.name}</td>
                    <td style="padding: 10px; color: ${doradoMate};">$${(item.price * item.qty).toFixed(2)}</td>
                    <td style="padding: 10px; display: flex; align-items: center; gap: 5px;">
                        <input type="number" class="desc-input-indiv" data-itemname="${item.name}" value="${descPorcentajeAnterior}" step="1" min="0" max="100" style="width: 60px; padding: 5px; border-radius: 4px; border: 1px solid ${doradoMate}; background: #1A1A1A; color: ${doradoMate}; text-align: center;"> %
                    </td>
                `;
                descTableBody.appendChild(tr);
            });
            document.getElementById('desc-global-input').value = descuentoGlobalPorcentaje;
            modalAvanzadoDesc.style.display = 'flex';
        });

        document.getElementById('cerrar-modal-desc-avanzado').addEventListener('click', () => {
            modalAvanzadoDesc.style.display = 'none';
        });

        document.getElementById('btn-aplicar-desc-avanzado').addEventListener('click', () => {
            const descGlobalVal = parseFloat(document.getElementById('desc-global-input').value) || 0;
            descuentoGlobalPorcentaje = descGlobalVal > 100 ? 100 : (descGlobalVal < 0 ? 0 : descGlobalVal);
            descuentosIndividuales = {};
            const inputsIndiv = document.querySelectorAll('.desc-input-indiv');
            inputsIndiv.forEach(input => {
                const nombre = input.getAttribute('data-itemname');
                let porcentaje = parseFloat(input.value) || 0;
                if(porcentaje > 0) {
                    if(porcentaje > 100) porcentaje = 100;
                    descuentosIndividuales[nombre] = porcentaje;
                }
            });
            renderCart();
            modalAvanzadoDesc.style.display = 'none';
            mostrarNotificacion("Descuentos aplicados correctamente.", 'exito');
        });
    }

    const btnPagar = document.getElementById('btn-pagar');
    if (btnPagar) {
        btnPagar.addEventListener('click', () => {
            if(window.totalGeneral <= 0) {
                mostrarNotificacion("El carrito está vacío.", 'info');
                return;
            }
            const doradoMate = getComputedStyle(document.documentElement).getPropertyValue('--dorado-mate').trim() || '#C5A059';
            document.getElementById('pago-total-mostrar').innerText = "$" + window.totalGeneral.toFixed(2);
            document.getElementById('pago-total-mostrar').style.color = doradoMate;
            document.getElementById('efectivo-recibido').value = "";
            document.getElementById('pago-cambio').innerText = "$0.00";
            document.getElementById('pago-cambio').style.color = "#1A1A1A";
            document.getElementById('modal-pago-efectivo').style.display = 'flex';
        });
    }

    const modalPago = document.getElementById('modal-pago-efectivo');
    const inputRecibido = document.getElementById('efectivo-recibido');
    const spanCambio = document.getElementById('pago-cambio');

    document.getElementById('cerrar-modal-pago')?.addEventListener('click', () => { modalPago.style.display = 'none'; });
    document.getElementById('btn-cancelar-pago')?.addEventListener('click', () => { modalPago.style.display = 'none'; });

    inputRecibido?.addEventListener('input', function() {
        const recibido = parseFloat(this.value) || 0;
        const cambio = recibido - window.totalGeneral;
        if (cambio >= 0) {
            spanCambio.innerText = "$" + cambio.toFixed(2);
            spanCambio.style.color = "#1A1A1A";
        } else {
            spanCambio.innerText = "Falta: $" + Math.abs(cambio).toFixed(2);
            spanCambio.style.color = "#e74c3c";
        }
    });

    document.getElementById('btn-finalizar-venta')?.addEventListener('click', () => {
        const recibido = parseFloat(inputRecibido.value) || 0;
        if (recibido < window.totalGeneral) { 
            mostrarNotificacion("El efectivo recibido es insuficiente.", 'error'); 
            return; 
        }
        const datosVenta = {
            total: window.totalGeneral,
            recibido: recibido,
            descuento_global: descuentoGlobalPorcentaje,
            carrito: cart 
        };
        const subtotalParaTicket = window.subtotalSinDescuento;
        const descuentoParaTicket = window.totalDescuentoAplicado;
        const totalParaTicket = window.totalGeneral;
        const cambioParaTicket = recibido - window.totalGeneral;

        fetch('procesar_venta.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(datosVenta)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                modalPago.style.display = 'none';
                const doradoMate = getComputedStyle(document.documentElement).getPropertyValue('--dorado-mate').trim() || '#C5A059';
                const successAnim = document.createElement('div');
                successAnim.innerHTML = `
                    <div style="background: rgba(0,0,0,0.85); position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 9999999; display: flex; justify-content: center; align-items: center; flex-direction: column; color: ${doradoMate}; backdrop-filter: blur(5px);">
                        <i class="fas fa-check-circle" style="font-size: 6rem; animation: popIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);"></i>
                        <h2 style="margin-top: 20px; font-size: 2.5rem; letter-spacing: 2px; text-transform: uppercase; animation: slideUp 0.5s ease-out;">¡Venta Exitosa!</h2>
                    </div>
                    <style>
                        @keyframes popIn { 0% { transform: scale(0) rotate(-45deg); opacity: 0; } 100% { transform: scale(1) rotate(0); opacity: 1; } }
                        @keyframes slideUp { 0% { transform: translateY(30px); opacity: 0; } 100% { transform: translateY(0); opacity: 1; } }
                    </style>
                `;
                document.body.appendChild(successAnim);
                setTimeout(() => {
                    successAnim.style.opacity = '0';
                    successAnim.style.transition = 'opacity 0.4s ease';
                    setTimeout(() => successAnim.remove(), 400);
                }, 2000);

                let vrItems = document.querySelectorAll('.vr-item h4');
                if(vrItems.length >= 2) {
                    let contadorVentas = parseInt(vrItems[0].innerText) || 0;
                    let acumuladoActual = parseFloat(vrItems[1].innerText.replace('$', '')) || 0;
                    contadorVentas++;
                    acumuladoActual += totalParaTicket;
                    vrItems[0].innerText = contadorVentas;
                    vrItems[1].innerText = "$" + acumuladoActual.toFixed(2);
                    vrItems[2].innerText = new Date().toLocaleDateString('es-ES');
                    const ventasLista = document.querySelector('.ventas-lista');
                    if(ventasLista) {
                        const vendedorStr = window.vendedorActual || "Usuario";
                        const nuevaVentaHTML = `
                            <div class="venta-item">
                                <div class="v-badge">#Nuevo</div>
                                <div class="v-info">
                                    <strong>VENTA #Nuevo</strong>
                                    <span><i class="far fa-calendar-alt"></i> ${new Date().toLocaleString('es-ES')}</span>
                                </div>
                                <div class="v-monto">$${totalParaTicket.toFixed(2)}</div>
                                <div class="v-acciones">
                                    <button class="btn-icon" onclick="window.imprimirTicketDirecto(${JSON.stringify(cart).replace(/"/g, '&quot;')}, ${subtotalParaTicket}, ${descuentoParaTicket}, ${totalParaTicket}, ${recibido}, ${cambioParaTicket}, '${vendedorStr}')"><i class="fas fa-print"></i></button>
                                    <button class="btn-icon text-red" onclick="window.descargarTicketDirecto(${JSON.stringify(cart).replace(/"/g, '&quot;')}, ${subtotalParaTicket}, ${descuentoParaTicket}, ${totalParaTicket}, ${recibido}, ${cambioParaTicket}, '${vendedorStr}')"><i class="fas fa-download"></i></button>
                                </div>
                            </div>
                        `;
                        ventasLista.insertAdjacentHTML('afterbegin', nuevaVentaHTML);
                    }
                }
                cart.forEach(cartItem => {
                    products.forEach(prod => {
                        if(prod.querySelector('.item').innerText === cartItem.name) {
                            const stockPill = prod.querySelector('.stock-pill');
                            let currentStock = parseInt(stockPill.innerText.replace('STOCK', '').trim());
                            let newStock = currentStock - cartItem.qty;
                            actualizarEstadoProducto(prod, newStock);
                        }
                    });
                });
                cart = [];
                descuentoGlobalPorcentaje = 0;
                descuentosIndividuales = {};
                renderCart();
                document.getElementById('dot-ventas').style.display = 'block';
            } else {
                mostrarNotificacion("Error en BD: " + data.error, 'error');
            }
        })
        .catch(error => {
            mostrarNotificacion("Error de conexión con el servidor.", 'error');
        });
    });

    let barcodeRaw = "";
    let barcodeTimeout;

    document.addEventListener('keypress', function(e) {
        if (e.target.id === 'search-input' || e.target.tagName === 'INPUT') return;
        if (e.key === 'Enter') {
            if (barcodeRaw.length > 3) { 
                procesarCodigoEscaneado(barcodeRaw);
            }
            barcodeRaw = ""; 
            e.preventDefault();
            return;
        }
        barcodeRaw += e.key;
        clearTimeout(barcodeTimeout);
        barcodeTimeout = setTimeout(() => {
            barcodeRaw = "";
        }, 50); 
    });

    function procesarCodigoEscaneado(codigoEscaner) {
        let productoEncontrado = false;
        products.forEach(prod => {
            const codigoProducto = prod.getAttribute('data-codigo');
            if (codigoProducto === codigoEscaner) {
                productoEncontrado = true;
                const stockPill = prod.querySelector('.stock-pill');
                let stockActual = parseInt(stockPill.innerText.replace('STOCK', '').trim()) || 0;
                if (stockActual <= 0) {
                    mostrarNotificacion("Producto agotado", 'error');
                    return;
                }
                const toggleIcon = prod.querySelector('.toggle-icon');
                const btnPlus = prod.querySelector('.btn-plus');
                if (toggleIcon && toggleIcon.classList.contains('fa-toggle-off')) {
                    toggleIcon.click();
                    mostrarNotificacion("Producto agregado: " + prod.querySelector('.item').innerText, "exito");
                } else if (btnPlus) {
                    btnPlus.click();
                    mostrarNotificacion("Cantidad aumentada", "info");
                }
            }
        });
        if (!productoEncontrado) {
            mostrarNotificacion("Código " + codigoEscaner + " no registrado en inventario.", "error");
        }
    }

   (function sincronizarColores() {
    const coloresGuardados = localStorage.getItem('liquour_colors');
    if (coloresGuardados) {
        try {
            const colores = JSON.parse(coloresGuardados);
            const dorado = colores['--color-dorado'] || '#C5A059';
            const fondo = colores['--bg-carbon'] || '#1A1A1A';
            const texto = colores['--text-blanco-crema'] || '#F5F5DC';
            const borde = colores['--border-fuerte'] || '#4A4A4A';
            
            document.documentElement.style.setProperty('--dorado-mate', dorado);
            document.documentElement.style.setProperty('--negro-carbon', fondo);
            document.documentElement.style.setProperty('--blanco-crema', texto);
            document.documentElement.style.setProperty('--gris-oxford', borde);
            
            document.querySelectorAll('.product-price-pill, .total-oro, .oro-text, .box-total h2, .box-desc h2, .v-monto, .v-badge, .btn-icon, .stock-pill, .search-box label, .category-box label, .nav-icons, .modal-pago-cabecera, .btn-cerrar-modal, .icono-dolar, .btn-pago-confirmar, .vr-item .oro-text').forEach(el => {
                if (el.style) {
                    if (el.classList.contains('btn-pago-confirmar')) {
                        el.style.backgroundColor = dorado;
                        el.style.color = fondo;
                    } else if (el.classList.contains('btn-pagar')) {
                        el.style.backgroundColor = dorado;
                    } else {
                        el.style.color = dorado;
                    }
                }
            });
            
            document.querySelectorAll('.btn-pagar').forEach(el => {
                el.style.backgroundColor = dorado;
                el.style.color = fondo;
            });
            
            document.querySelectorAll('.btn-descuento').forEach(el => {
                el.style.borderColor = dorado;
                el.style.color = dorado;
            });
            
            console.log('🎨 Colores sincronizados con POS:', { dorado, fondo, texto });
        } catch(e) {
            console.log('Error cargando colores:', e);
        }
    }
})();
});