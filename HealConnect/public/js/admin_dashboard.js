document.addEventListener("DOMContentLoaded", () => {
    const { usergrowth, appointmentTypeData } = window.dashboardData;

    // USER GROWTH CHART
    const ctxUser = document.getElementById("userGrowthChart");
    if (ctxUser) {
        new Chart(ctxUser, {
            type: "line",
            data: {
                labels: usergrowth.labels,
                datasets: [{
                    label: "New Users",
                    data: userGrowthData.values,
                    borderColor: "rgba(54, 162, 235, 1)",
                    backgroundColor: "rgba(54, 162, 235, 0.2)",
                    borderWidth: 2,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }

    // APPOINTMENT TYPE CHART
    const ctxAppt = document.getElementById("appointmentTypeChart");
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
                plugins: {
                    legend: { position: "bottom" }
                }
            }
        });
    }
});
