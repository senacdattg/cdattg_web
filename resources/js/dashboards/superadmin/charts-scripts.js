// Gráfico de Asistencia Semanal
const asistenciaChart = new Chart(document.getElementById('asistenciaChartNew'), {
    type: 'line',
    data: {
        labels: ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'],
        datasets: [{
            label: 'Asistencias',
            data: [220, 230, 210, 225, 215],
            borderColor: '#4fc3f7',
            tension: 0.3,
            fill: true,
            backgroundColor: 'rgba(79, 195, 247, 0.1)'
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top'
            }
        }
    }
});

// Gráfico de Uso de Ambientes
const ambientesChart = new Chart(document.getElementById('ambientesChart'), {
    type: 'bar',
    data: {
        labels: ['Mañana', 'Tarde', 'Noche'],
        datasets: [{
            label: 'Ambientes Ocupados',
            data: [12, 8, 5],
            backgroundColor: '#26a69a'
        }, {
            label: 'Ambientes Disponibles',
            data: [3, 7, 10],
            backgroundColor: '#ffa726'
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top'
            }
        },
        scales: {
            y: {
                stacked: true,
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Número de Ambientes'
                }
            },
            x: {
                stacked: true
            }
        }
    }
});

// Gráfico de Distribución por Programa
const distribucionChart = new Chart(document.getElementById('distribucionChart'), {
    type: 'doughnut',
    data: {
        labels: ['ADSO', 'Multimedia', 'Contabilidad', 'Otros'],
        datasets: [{
            data: [45, 25, 20, 10],
            backgroundColor: [
                '#4fc3f7',
                '#673ab7',
                '#26a69a',
                '#ffa726'
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});