document.addEventListener('DOMContentLoaded', () => {
    const modalCompra = document.getElementById('modal-comprar-producto');

    window.onclick = (e) => {
        if (e.target === modalCompra) {
            cerrarModalCompra();
        }
    };
});

window.abrirModalCompra = function(id, nombre, precio) {
    const modal = document.getElementById('modal-comprar-producto');
    
    document.getElementById('compra-id-producto').value = id;
    document.getElementById('compra-nombre').value = nombre;
    document.getElementById('compra-precio').value = precio;
    document.getElementById('compra-cantidad').value = 1;
    
    calcularTotal();
    
    if(modal) modal.style.display = 'flex';
};

window.cerrarModalCompra = function() {
    const modal = document.getElementById('modal-comprar-producto');
    if(modal) modal.style.display = 'none';
};

window.calcularTotal = function() {
    const cantidad = parseFloat(document.getElementById('compra-cantidad').value) || 0;
    const precio = parseFloat(document.getElementById('compra-precio').value) || 0;
    const total = cantidad * precio;
    
    document.getElementById('compra-total').value = total.toFixed(2);
};