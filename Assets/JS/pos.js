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

window.generarTicketHTML = function(carrito, subtotal, descuento, total, recibido, cambio, vendedor = "Usuario", fechaTicket = null) {
    let fecha = fechaTicket ? fechaTicket : new Date().toLocaleString('es-ES');
    let itemsHTML = '';
    
    carrito.forEach(item => {
        let sub = item.qty * item.price;
        itemsHTML += `
            <tr>
                <td>${item.qty}x</td>
                <td>${item.name}</td>
                <td class="t-right">$${sub.toFixed(2)}</td>
            </tr>
        `;
    });

    let descuentoRow = '';
    if (descuento > 0) {
        descuentoRow = `<div class="row discount"><span>Descuento:</span><span>-$${descuento.toFixed(2)}</span></div>`;
    }

    return `
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <title>Ticket de Venta - LIQUOUR</title>
            <style>
                body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #e5e5e5; margin: 0; padding: 40px; display: flex; justify-content: center; }
                .ticket-container { background: #fff; width: 100%; max-width: 380px; padding: 30px; box-shadow: 0 15px 25px rgba(0,0,0,0.15); border-radius: 8px; }
                .t-header { text-align: center; border-bottom: 2px dashed #1a1a1a; padding-bottom: 20px; margin-bottom: 20px; }
                .t-header h1 { font-size: 32px; margin: 0; color: #1a1a1a; letter-spacing: 3px; text-transform: uppercase; }
                .t-header p { margin: 8px 0 0; color: #4a4a4a; font-size: 14px; font-weight: bold;}
                .t-vendedor { font-size: 13px; color: #666; margin-top: 5px; font-style: italic; }
                .t-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 14px; color: #1a1a1a; }
                .t-table th { text-align: left; border-bottom: 1px solid #1a1a1a; padding-bottom: 8px; text-transform: uppercase; font-size: 12px; }
                .t-table td { padding: 10px 0; border-bottom: 1px solid #f0f0f0; }
                .t-right { text-align: right; }
                .t-totals { font-size: 15px; line-height: 1.8; border-bottom: 2px dashed #1a1a1a; padding-bottom: 20px; margin-bottom: 20px; color: #1a1a1a; }
                .t-totals .row { display: flex; justify-content: space-between; }
                .t-totals .row.discount { color: #e74c3c; }
                .t-totals .row.grand-total { font-size: 22px; font-weight: bold; margin-top: 10px; color: #1a1a1a; }
                .t-footer { text-align: center; font-size: 13px; color: #4a4a4a; font-style: italic; }
            </style>
        </head>
        <body>
            <div class="ticket-container">
                <div class="t-header">
                    <h1>LIQUOUR</h1>
                    <p>COMPROBANTE DE COMPRA</p>
                    <p style="font-weight: normal; font-size: 12px;">${fecha}</p>
                    <div class="t-vendedor">Atendido por: ${vendedor}</div>
                </div>
                <table class="t-table">
                    <thead><tr><th>Cant.</th><th>Descripción</th><th class="t-right">Importe</th></tr></thead>
                    <tbody>${itemsHTML}</tbody>
                </table>
                <div class="t-totals">
                    <div class="row"><span>Subtotal:</span><span>$${subtotal.toFixed(2)}</span></div>
                    ${descuentoRow}
                    <div class="row grand-total"><span>TOTAL:</span><span>$${total.toFixed(2)}</span></div>
                </div>
                <div class="t-totals" style="border: none; padding-bottom: 0;">
                    <div class="row"><span>Efectivo:</span><span>$${recibido.toFixed(2)}</span></div>
                    <div class="row"><span>Cambio:</span><span>$${cambio.toFixed(2)}</span></div>
                </div>
                <div class="t-footer">
                    <p>¡Gracias por su compra!</p>
                </div>
            </div>
            <script>
                window.onload = function() { window.print(); };
            </script>
        </body>
        </html>
    `;
};

window.imprimirTicketDirecto = function(carrito, subtotal, descuento, total, recibido, cambio, vendedor, fecha = null) {
    try {
        const ticketWindow = window.open('', '_blank');
        ticketWindow.document.write(window.generarTicketHTML(carrito, subtotal, descuento, total, recibido, cambio, vendedor, fecha));
        ticketWindow.document.close();
    } catch(e) {
        console.error(e);
    }
};

window.descargarTicketDirecto = function(carrito, subtotal, descuento, total, recibido, cambio, vendedor, fecha = null) {
    try {
        const htmlContent = window.generarTicketHTML(carrito, subtotal, descuento, total, recibido, cambio, vendedor, fecha);
        
        const blob = new Blob([htmlContent], { type: 'text/html' });
        const url = URL.createObjectURL(blob);
        
        const a = document.createElement('a');
        a.href = url;
        a.download = `Ticket_Liquour_${new Date().getTime()}.html`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    } catch(e) {
        console.error(e);
    }
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
        const toast = document.createElement('div');
        const bg = tipo === 'exito' ? 'var(--dorado-mate)' : (tipo === 'error' ? '#e74c3c' : 'var(--gris-oxford)');
        const color = tipo === 'exito' ? 'var(--negro-carbon)' : 'var(--white)';
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
                if (prodName.includes(searchTerm)) {
                    prod.style.display = 'flex';
                } else {
                    prod.style.display = 'none';
                }
            });
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
            itemRow.style.cssText = "display: flex; justify-content: space-between; padding: 10px; border-bottom: 1px solid #ccc; font-size: 0.85rem; font-weight: bold; cursor: pointer; color: #161616; align-items: center;";
            
            let tagDescuento = porcentajeDescuentoItem > 0 ? `<span style="color:#e74c3c; font-size:0.7rem; display:block;">(-${porcentajeDescuentoItem}%)</span>` : "";

            itemRow.innerHTML = `
                <span style="width: 15%;">${item.qty}</span>
                <span style="flex: 1;">${item.name} ${tagDescuento}</span>
                <span style="width: 20%;">$${item.price.toFixed(2)}</span>
                <span style="width: 25%; text-align: right; padding-right: 15px;">$${subtotalConDescuento.toFixed(2)}</span>
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
            if(stockMaximo === 0) {
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

            document.getElementById('det-img').src = imgSrc;
            document.getElementById('det-nombre').innerText = itemName;
            document.getElementById('det-cat').innerText = cat;
            document.getElementById('det-cod').innerText = cod;
            document.getElementById('det-stock').innerText = stockMaximo;
            document.getElementById('det-precio').innerText = itemPriceText;

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
            
            const descTableBody = document.getElementById('desc-table-body');
            descTableBody.innerHTML = '';
            
            cart.forEach((item, index) => {
                const descPorcentajeAnterior = descuentosIndividuales[item.name] || 0;
                
                const tr = document.createElement('tr');
                tr.style.borderBottom = "1px solid var(--gris-oxford)";
                tr.innerHTML = `
                    <td style="padding: 10px;">${item.qty}x</td>
                    <td style="padding: 10px;">${item.name}</td>
                    <td style="padding: 10px;">$${(item.price * item.qty).toFixed(2)}</td>
                    <td style="padding: 10px; display: flex; align-items: center; gap: 5px;">
                        <input type="number" class="desc-input-indiv" data-itemname="${item.name}" value="${descPorcentajeAnterior}" step="1" min="0" max="100" style="width: 60px; padding: 5px; border-radius: 4px; border: 1px solid var(--dorado-mate); background: var(--negro-carbon); color: var(--dorado-mate); text-align: center;"> %
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
            document.getElementById('pago-total-mostrar').innerText = "$" + window.totalGeneral.toFixed(2);
            document.getElementById('efectivo-recibido').value = "";
            document.getElementById('pago-cambio').innerText = "$0.00";
            document.getElementById('pago-cambio').style.color = "var(--negro-carbon)";
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
            spanCambio.style.color = "#161616";
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
            
            const successAnim = document.createElement('div');
            successAnim.innerHTML = `
                <div style="background: rgba(0,0,0,0.85); position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 9999999; display: flex; justify-content: center; align-items: center; flex-direction: column; color: var(--dorado-mate); backdrop-filter: blur(5px);">
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
                        stockPill.innerText = `STOCK ${newStock}`;
                        
                        const btnPlus = prod.querySelector('.btn-plus');
                        if(btnPlus) btnPlus.setAttribute('data-stock', newStock);

                        const tIcon = prod.querySelector('.toggle-icon');
                        const qtyS = prod.querySelector('.qty-counter');
                        if(tIcon) {
                            tIcon.classList.replace('fa-toggle-on', 'fa-toggle-off');
                            tIcon.classList.remove('active-gold');
                        }
                        if(qtyS) qtyS.innerText = "0";
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
});