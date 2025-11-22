document.addEventListener("DOMContentLoaded", () => {
    const { usergrowth, userRoleData} = window.dashboardData;

    // USER GROWTH CHART
    const ctxUser = document.getElementById("monthlyChart");
    if (ctxUser) {
        new Chart(ctxUser, {
            type: "line",
            data: {
                labels: usergrowth.labels,
                datasets: [{
                    label: "New Users",
                    data: usergrowth.values,
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
                    y: { 
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            precision: 0,
                            callback: function(value){
                                return Math.floor(value);
                            }
                        }
                    }
                }
            }
        });
    }

    // USER ROLES DISTRIBUTION CHART
    const ctxRoles = document.getElementById("userRolesChart");
    if (ctxRoles) {
        new Chart(ctxRoles, {
            type: "pie",
            data: {
                labels: ["Patients", "Therapists", "Clinics"],
                datasets: [{
                    data: [
                        userRoleData.patients,
                        userRoleData.therapists,
                        userRoleData.clinics,
                    ]
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
