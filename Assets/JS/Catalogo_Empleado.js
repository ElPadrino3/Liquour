function mostrarResultadoFiltro(total) {
    const label = document.getElementById('resultado-filtro');
    if (!label) return;

    if (total === 0) {
        label.innerText = 'No se encontraron productos';
    } else if (total === 1) {
        label.innerText = 'Se encontró 1 producto';
    } else {
        label.innerText = `Se encontraron ${total} productos`;
    }
}

function aplicarFiltros() {
    const inputBusqueda = document.getElementById('search-input');
    const radioSeleccionado = document.querySelector('input[name="categoria"]:checked');
    const productos = document.querySelectorAll('.item-producto');

    const textoBusqueda = inputBusqueda ? inputBusqueda.value.trim().toLowerCase() : '';
    const categoriaSeleccionada = radioSeleccionado ? radioSeleccionado.value.toLowerCase() : 'todos';

    let visibles = 0;

    productos.forEach(producto => {
        const nombre = producto.querySelector('.nombre-producto')?.textContent.toLowerCase() || '';
        const codigo = producto.querySelector('.codigo-producto')?.textContent.toLowerCase() || '';
        const categoria = (producto.dataset.categoria || '').toLowerCase();

        const coincideBusqueda =
            textoBusqueda === '' ||
            nombre.includes(textoBusqueda) ||
            codigo.includes(textoBusqueda);

        const coincideCategoria =
            categoriaSeleccionada === 'todos' ||
            categoria === categoriaSeleccionada;

        if (coincideBusqueda && coincideCategoria) {
            producto.style.display = 'flex';
            visibles++;
        } else {
            producto.style.display = 'none';
        }
    });

    mostrarResultadoFiltro(visibles);
}

document.addEventListener('DOMContentLoaded', () => {
    const inputBusqueda = document.getElementById('search-input');
    const radios = document.querySelectorAll('input[name="categoria"]');

    if (inputBusqueda) {
        inputBusqueda.addEventListener('input', aplicarFiltros);
    }

    radios.forEach(radio => {
        radio.addEventListener('change', aplicarFiltros);
    });

    aplicarFiltros();
});