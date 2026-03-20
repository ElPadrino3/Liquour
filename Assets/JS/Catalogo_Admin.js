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
});

function abrirModalNuevo() {
    const modal = document.getElementById('modal-admin-prod');
    const form = document.getElementById('form-admin-producto');
    document.getElementById('admin-modal-title').innerText = "NUEVO PRODUCTO";
    form.reset();
    modal.style.display = 'flex';
}

function abrirModalAdmin(modo, id) {
    const modal = document.getElementById('modal-admin-prod');
    document.getElementById('admin-modal-title').innerText = "EDITAR PRODUCTO ID: " + id;
    modal.style.display = 'flex';
}

function eliminarProducto(id) {
    if (confirm("¿Eliminar producto " + id + "?")) {
        alert("Producto eliminado visualmente");
    }
}