
        /* Charts same as yours (unchanged logic) */

      new Chart(chartMain, {
    type: 'line',
    data: {
        labels: ['1','2','3','4','5','6','7','8','9','10'],
        datasets: [
            {
                label: 'ألمستخدمين',
                data: [52, 48, 45, 43, 47, 38, 41, 46, 59, 33],
                borderColor: '#14b8a6',
                backgroundColor: 'rgba(20,184,166,0.15)',
                fill: true,
                tension: 0.4,
                pointRadius: 0
            },
            {
                label: 'الطلبات',
                data: [40, 15, 18, 16, 17, 6, 8, 9, 20, 15],
                borderColor: '#0984e3',
                backgroundColor: 'rgba(9,132,227,0.15)',
                fill: true,
                tension: 0.4,
                pointRadius: 0
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            x: {
                display: true,
                title: {
                    display: true,
                    text: 'الأيام',
                    font: {
                        size: 14,
                        weight: 'bold'
                    }
                }
            },
            y: {
                display: true,
                beginAtZero: true
            }
        }
    }
});

        new Chart(chartSmall1, { type: 'line', data: { labels: Array(20).fill(""), datasets: [{ data: [40, 45, 35, 50, 60, 55, 65, 62, 70, 68, 72, 75, 70, 78, 80, 72, 76, 68, 74, 70], borderColor: '#14b8a6', backgroundColor: 'rgba(20,184,166,0.15)', fill: true, tension: 0.4, pointRadius: 0 }] }, options: { plugins: { legend: { display: false } }, scales: { x: { display: false }, y: { display: false } }, responsive: true, maintainAspectRatio: false } });

        new Chart(chartSmall2, { type: 'bar', data: { labels: Array(20).fill(""), datasets: [{ data: [12, 18, 15, 20, 22, 19, 17, 16, 14, 10, 8, 6, 5, 4, 6, 5, 7, 6, 5, 6], backgroundColor: '#2f80ed', borderRadius: 4 }] }, options: { plugins: { legend: { display: false } }, scales: { x: { display: false }, y: { display: false } }, responsive: true, maintainAspectRatio: false } });

        new Chart(chartSessions, {
            type: 'bar', data: {
                labels: Array(8).fill(""), datasets: [
                    { data: [2, 3, 6, 10, 18, 16, 14, 9], backgroundColor: '#6c5ce7' },
                    { data: [3, 4, 8, 15, 20, 18, 15, 12], backgroundColor: '#9b92aa' }
                ]
            }, options: { plugins: { legend: { display: false } }, scales: { x: { display: false }, y: { display: false } }, responsive: true, maintainAspectRatio: false }
        });

        new Chart(donutChart, { type: 'doughnut', data: { datasets: [{ data: [25, 20, 30, 15], backgroundColor: ['#6c5ce7', '#3b82f6', '#14b8a6', '#0ea5e9'], borderWidth: 0 }] }, options: { plugins: { legend: { display: false } }, cutout: '65%', responsive: true, maintainAspectRatio: false } });

        new Chart(circle1, { type: 'doughnut', data: { datasets: [{ data: [26.8, 73.2], backgroundColor: ['#3b82f6', '#e5e7eb'], borderWidth: 0 }] }, options: { plugins: { legend: { display: false } }, cutout: '70%', responsive: true, maintainAspectRatio: true } });

        new Chart(circle2, { type: 'doughnut', data: { datasets: [{ data: [60, 40], backgroundColor: ['#14b8a6', '#e5e7eb'], borderWidth: 0 }] }, options: { plugins: { legend: { display: false } }, cutout: '70%', responsive: true, maintainAspectRatio: true } });
