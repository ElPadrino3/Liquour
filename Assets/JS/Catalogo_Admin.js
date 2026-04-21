document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('search-inventory');
    const filterSelect = document.getElementById('filter-category');

    function filterTable() {
        const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
        const categoryTerm = filterSelect ? filterSelect.value : 'Todos';
        const rows = document.querySelectorAll('#inventory-body .table-row');
        
        rows.forEach(row => {
            const info = row.querySelector('.col-info').innerText.toLowerCase();
            const brand = row.querySelector('.col-brand').innerText.trim();
            
            const matchSearch = info.includes(searchTerm);
            const matchCategory = (categoryTerm === 'Todos' || brand === categoryTerm);
            
            if (matchSearch && matchCategory) {
                row.style.display = 'grid';
            } else {
                row.style.display = 'none';
            }
        });
    }

    if(searchInput) searchInput.addEventListener('input', filterTable);
    if(filterSelect) filterSelect.addEventListener('change', filterTable);
});

function cerrarModal(idModal) {
    const modal = document.getElementById(idModal);
    if(modal) modal.style.display = 'none';
}

function abrirModalAdd() {
    document.getElementById('modal-add').style.display = 'flex';
}

function abrirModalView(producto) {
    document.getElementById('view_img').src = producto.imagen ? producto.imagen : 'https://images.pexels.com/photos/11271794/pexels-photo-11271794.jpeg';
    document.getElementById('view_nombre').innerText = producto.nombre;
    document.getElementById('view_sku').innerText = 'SKU: ' + (producto.codigo_barras || 'N/A');
    document.getElementById('view_cat').innerText = producto.nombre_categoria || 'N/A';
    document.getElementById('view_stock').innerText = producto.stock;
    document.getElementById('view_costo').innerText = '$' + parseFloat(producto.precio_compra).toFixed(2);
    document.getElementById('view_precio').innerText = '$' + parseFloat(producto.precio_venta).toFixed(2);
    
    document.getElementById('modal-view').style.display = 'flex';
}

function abrirModalEdit(producto) {
    document.getElementById('edit_id').value = producto.id_producto;
    document.getElementById('edit_nombre').value = producto.nombre;
    document.getElementById('edit_codigo').value = producto.codigo_barras;
    document.getElementById('edit_categoria').value = producto.id_categoria;
    document.getElementById('edit_costo').value = producto.precio_compra;
    document.getElementById('edit_precio').value = producto.precio_venta;
    document.getElementById('edit_stock').value = producto.stock;
    document.getElementById('edit_imagen_actual').value = producto.imagen;
    
    const imgPreview = document.getElementById('edit_preview');
    imgPreview.src = producto.imagen ? producto.imagen : '';
    imgPreview.style.display = producto.imagen ? 'inline-block' : 'none';
    
    detectarProveedor('edit');
    document.getElementById('modal-edit').style.display = 'flex';
}

function abrirModalDelete(id, nombre) {
    document.getElementById('delete_id').value = id;
    document.getElementById('delete_nombre').innerText = nombre;
    document.getElementById('modal-delete').style.display = 'flex';
}

function previewImage(event, previewId) {
    const input = event.target;
    const preview = document.getElementById(previewId);
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'inline-block';
        }
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.src = '';
        preview.style.display = 'none';
    }
}

function detectarProveedor(tipo) {
    const catSelect = document.getElementById(tipo + '_categoria');
    const provSelect = document.getElementById(tipo + '_proveedor');
    
    if(catSelect && provSelect) {
        const idCat = catSelect.value;
        const options = provSelect.options;
        
        if (idCat !== "") {
            if(options.length > 1) {
                provSelect.selectedIndex = 1;
            }
        } else {
            provSelect.selectedIndex = 0;
        }
    }
}