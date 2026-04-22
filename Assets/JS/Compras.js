function imprimirReporteCompras() {
    const busqueda = document.getElementById('searchInput').value;
    const proveedor = document.getElementById('provFilter').value;
    
    // Ruta corregida según tu prueba de ruta.php
    const url = `/LIQUOUR/Controller/ReporteController.php?reporte=compras&busqueda=${encodeURIComponent(busqueda)}&vendedor=${encodeURIComponent(proveedor)}`;
    
    window.open(url, '_blank');
}

function render() {
    const ordersElement = document.getElementById('ordersData');
    const chartElement = document.getElementById('chartData');
    
    if (!ordersElement || !chartElement) return;

    const ORDERS = JSON.parse(ordersElement.textContent);
    const CHART_DATA = JSON.parse(chartElement.textContent);
    const ROWS = 7;
    let page = 1, search = '', statusF = '', provF = '';
    const BADGE = { Recibido: 'badge-ok', Pendiente: 'badge-pending', Cancelado: 'badge-cancel' };

    const filtered = () => {
        return ORDERS.filter(r => {
            const q = search.toLowerCase();
            const match = !q || Object.values(r).some(v => String(v).toLowerCase().includes(q));
            return match && (!statusF || r.estado === statusF) && (!provF || r.proveedor === provF);
        });
    };

    const draw = () => {
        const rows = filtered();
        const pages = Math.max(1, Math.ceil(rows.length / ROWS));
        const slice = rows.slice((page - 1) * ROWS, page * ROWS);
        const tbody = document.getElementById('tableBody');
        
        tbody.innerHTML = slice.map(r => `
            <tr>
                <td class="td-id">${r.orden}</td>
                <td class="td-date">${r.fecha}</td>
                <td>${r.proveedor}</td>
                <td style="color:var(--cream);">${r.producto}</td>
                <td style="text-align:center">${r.qty}</td>
                <td>${r.precio}</td>
                <td class="td-price">${r.total}</td>
                <td><span class="badge ${BADGE[r.estado] || 'badge-ok'}">${r.estado}</span></td>
            </tr>`).join('');
    };

    document.getElementById('searchInput').addEventListener('input', e => { search = e.target.value; draw(); });
    document.getElementById('statusFilter').addEventListener('change', e => { statusF = e.target.value; draw(); });
    document.getElementById('provFilter').addEventListener('change', e => { provF = e.target.value; draw(); });
    draw();
}

document.addEventListener('DOMContentLoaded', render);