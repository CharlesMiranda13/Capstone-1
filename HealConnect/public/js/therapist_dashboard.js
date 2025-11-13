document.addEventListener("DOMContentLoaded", () => {
    const { usergrowth, appointmentTypeData } = window.dashboardData;

    // MONTHLY APPOINTMENTS CHART
    const ctxUser = document.getElementById("monthlyChart");
    if (ctxUser) {
        new Chart(ctxUser, {
            type: "line",
            data: {
                labels: usergrowth.labels,
                datasets: [{
                    label: "Appointments",
                    data: usergrowth.values,
                    borderColor: "rgba(54, 162, 235, 1)",
                    backgroundColor: "rgba(54, 162, 235, 0.2)",
                    borderWidth: 2,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { 
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            precision: 0,
                            callback: function(value){ return Math.floor(value); }
                        }
                    }
                }
            }
        });
    }

    // APPOINTMENT TYPE CHART
    const ctxAppt = document.getElementById("appointmentChart");
    if (ctxAppt) {
        new Chart(ctxAppt, {
            type: "doughnut",
            data: {
                labels: appointmentTypeData.labels,
                datasets: [{
                    data: appointmentTypeData.values,
                    backgroundColor: ["#4CAF50", "#FF9800", "#03A9F4"]
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: "bottom" } }
            }
        });
    }
});
