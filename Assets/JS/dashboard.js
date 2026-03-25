// CONFIGURACIÓN GLOBAL
Chart.defaults.color = '#4A4A4A';
Chart.defaults.font.family = 'Montserrat';
Chart.defaults.font.size = 10;

const gold   = '#C5A059';
const oxford = '#4A4A4A';

/* Line chart */
new Chart(document.getElementById('lineChart'), {
    type: 'line',
    data: {
        labels: ['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'],
        datasets: [
            {
                label: 'Este mes',
                data: [420, 580, 510, 690, 750, 870, 920],
                borderColor: gold,
                backgroundColor: 'rgba(197,160,89,.08)',
                borderWidth: 2,
                pointBackgroundColor: gold,
                pointRadius: 3.5,
                tension: 0.42,
                fill: true
            },
            {
                label: 'Mes pasado',
                data: [350, 490, 430, 600, 620, 710, 780],
                borderColor: oxford,
                backgroundColor: 'transparent',
                borderWidth: 1.5,
                borderDash: [4, 4],
                pointBackgroundColor: oxford,
                pointRadius: 2,
                tension: 0.42
            }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { color: 'rgba(255,255,255,.03)' }, ticks: { color: oxford } },
            y: { grid: { color: 'rgba(255,255,255,.03)' }, ticks: { color: oxford, callback: v => '$'+v } }
        }
    }
});

/* Bar chart */
new Chart(document.getElementById('barChart'), {
    type: 'bar',
    data: {
        labels: ['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'],
        datasets: [{
            data: [180, 240, 210, 290, 320, 410, 380],
            backgroundColor: 'rgba(197,160,89,.22)',
            borderColor: gold,
            borderWidth: 1,
            borderRadius: 4
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { display: false }, ticks: { color: oxford } },
            y: { grid: { color: 'rgba(255,255,255,.03)' }, ticks: { color: oxford } }
        }
    }
});