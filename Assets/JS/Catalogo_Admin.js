document.addEventListener('DOMContentLoaded', () => {
    const btnPerfil = document.getElementById('btn-mi-perfil');
    const modalPerfil = document.getElementById('modal-perfil');
    const btnCerrarPerfil = document.getElementById('close-modal');
    const modalAdmin = document.getElementById('modal-admin-prod');
    const btnCerrarAdmin = document.getElementById('close-modal-admin');

    if (btnPerfil && modalPerfil) {
        btnPerfil.onclick = (e) => {
            e.preventDefault();
            modalPerfil.style.display = 'flex';
        };
    }

    if (btnCerrarPerfil) {
        btnCerrarPerfil.onclick = () => {
            modalPerfil.style.display = 'none';
        };
    }

    if (btnCerrarAdmin) {
        btnCerrarAdmin.onclick = () => {
            modalAdmin.style.display = 'none';
        };
    }

    window.onclick = (e) => {
        if (e.target === modalPerfil) modalPerfil.style.display = 'none';
        if (e.target === modalAdmin) modalAdmin.style.display = 'none';
    };

    const formAdmin = document.getElementById('form-admin-producto');
    if (formAdmin) {
        formAdmin.onsubmit = (e) => {
            e.preventDefault();
            const esNuevo = document.getElementById('admin-p-id').value === '';
            if(esNuevo) {
                alert("¡Nuevo producto guardado exitosamente!");
            } else {
                alert("¡Datos actualizados correctamente!");
            }
            modalAdmin.style.display = 'none';
        };
    }
});

window.abrirModalCrear = function() {
    const modal = document.getElementById('modal-admin-prod');
    const form = document.getElementById('form-admin-producto');
    
    if(form) form.reset();
    
    document.getElementById('admin-modal-title').innerText = "NUEVO PRODUCTO";
    document.getElementById('admin-p-id').value = ''; 

    if(modal) modal.style.display = 'flex';
};

window.abrirModalEditar = function(boton) {
    const modal = document.getElementById('modal-admin-prod');
    const form = document.getElementById('form-admin-producto');
    
    if(form) form.reset();

    const id = boton.getAttribute('data-id');
    const name = boton.getAttribute('data-name');
    const compra = boton.getAttribute('data-compra');
    const venta = boton.getAttribute('data-venta');
    const barcode = boton.getAttribute('data-barcode');
    const stock = boton.getAttribute('data-stock');
    const img = boton.getAttribute('data-img');
    
    document.getElementById('admin-modal-title').innerText = "EDITAR PRODUCTO ID: " + id;
    
    document.getElementById('admin-p-id').value = id || '';
    document.getElementById('admin-p-nombre').value = name || '';
    document.getElementById('admin-p-compra').value = compra || '';
    document.getElementById('admin-p-venta').value = venta || '';
    document.getElementById('admin-p-barcode').value = barcode || '';
    document.getElementById('admin-p-stock').value = stock || '';
    document.getElementById('admin-p-img').value = img || '';

    if(modal) modal.style.display = 'flex';
};

window.eliminarProducto = function(id) {
    if (confirm("¿Estás seguro de eliminar el producto " + id + "?")) {
        alert("Producto " + id + " eliminado.");
    }
};