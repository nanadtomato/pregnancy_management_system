<?php
session_start();
require_once "../config.php";
require_once "../classes/User.php";

// Check if the user is logged in and has the correct role (Doctor)
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 2) {
    header("Location: ../login.php");
    exit();
}

$userFirstName = $_SESSION['name'];
global $conn;

// Get total patients
$totalPatientsQuery = $conn->query("SELECT COUNT(*) AS total FROM patients");
$totalPatients = $totalPatientsQuery->fetch_assoc()['total'];

// // Get total appointments
// $totalAppointmentsQuery = $conn->query("SELECT COUNT(*) AS total FROM appointments");
// $totalAppointments = $totalAppointmentsQuery->fetch_assoc()['total'];

// // Fetch the most recent appointment status
// $recentAppointmentsQuery = $conn->query("
//     SELECT p.full_name, a.status, a.appointment_date 
//     FROM appointments a 
//     JOIN patients p ON a.patient_id = p.id 
//     ORDER BY a.appointment_date DESC
//     LIMIT 5
// ");

// $appointments = [];
// while ($row = $recentAppointmentsQuery->fetch_assoc()) {
//     $appointments[] = $row;
// }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/mainStyles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .card-custom {
            transition: transform 0.3s ease-in-out;
        }

        .card-custom:hover {
            transform: scale(1.05);
        }

        .card-header {
            font-size: 1.2rem;
        }

        .card-body h2 {
            font-size: 2.5rem;
            font-weight: bold;
        }

        .table-striped tbody tr:nth-child(odd) {
            background-color: #f9f9f9;
        }

        .table-striped tbody tr:nth-child(even) {
            background-color: #ffffff;
        }

        .badge-success {
            background-color: #28a745 !important;
        }

        .badge-warning {
            background-color: #ffc107 !important;
        }

        .badge-danger {
            background-color: #dc3545 !important;
        }

        .progress-bar {
            transition: width 1s ease-in-out;
        }
        .text-pink {
    color:rgb(196, 22, 80); /* You can use any shade of pink here */
}

.card-body i {
    transition: transform 0.3s ease-in-out;
}

.card-body i:hover {
    transform: scale(1.1); /* Slight zoom on hover */
}
    </style>
    <title>Doctor Dashboard</title>
</head>
<body>
<?php include('../includes/navbar.php'); ?>

<div class="main-content">
    <main>
        <h2 class="text-center mb-4">Welcome, Dr. <?php echo htmlspecialchars($userFirstName); ?></h2>
        
        <!-- Stats Cards with Hover Animation -->
        <div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm card-custom" style="height: 150px; background-color: #f8f9fa;">
            <div class="card-body text-center">
                <i class="fas fa-users fa-3x text-pink mb-3"></i>
                <h5 class="card-title text-muted">Total Patients</h5>
                <h2 class="text-dark"><?php echo $totalPatients; ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm card-custom" style="height: 150px; background-color: #f8f9fa;">
            <div class="card-body text-center">
                <i class="fas fa-calendar-check fa-3x text-pink mb-3"></i>
                <h5 class="card-title text-muted">Total Appointments</h5>
                <h2 class="text-dark"><?php echo $totalAppointments; ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm card-custom" style="height: 150px; background-color: #f8f9fa;">
            <div class="card-body text-center">
                <i class="fas fa-calendar-day fa-3x text-pink mb-3"></i>
                <h5 class="card-title text-muted">Appointments Today</h5>
                <h2 class="text-dark">5</h2> <!-- Replace with dynamic data -->
            </div>
        </div>
    </div>
</div>


        <!-- Appointment Status Chart -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-danger-subtle text-dark">Appointment Status Overview</div>
            <div class="card-body">
                <canvas id="appointmentsChart"></canvas>
            </div>
        </div>

        <!-- Recent Appointment Status Table with Sorting -->
        <div class="card shadow-sm">
            <div class="card-header bg-danger-subtle text-dark">Recent Appointment Status</div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0" id="appointmentsTable">
                    <thead>
                        <tr>
                            <th>Patient Name</th>
                            <th>Status</th>
                            <th>Appointment Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($appointments as $appointment): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($appointment['full_name']); ?></td>
                                <td>
                                    <span class="badge <?php echo getStatusClass($appointment['status']); ?>">
                                        <?php echo htmlspecialchars($appointment['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($appointment['appointment_date']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<script>
    // Chart.js for Appointment Status Overview
    var ctx = document.getElementById('appointmentsChart').getContext('2d');
    var appointmentsChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Completed', 'Pending', 'Cancelled'], // Adjust as per your data
            datasets: [{
                label: 'Appointments Status',
                data: [15, 10, 5], // Replace with dynamic data
                backgroundColor: ['#28a745', '#ffc107', '#dc3545'],
                borderColor: ['#28a745', '#ffc107', '#dc3545'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            animation: {
                duration: 1000,
                easing: 'easeInOutQuad',
                onComplete: function() {
                    var chartInstance = this.chart;
                    var ctx = chartInstance.ctx;
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'bottom';
                    this.data.datasets.forEach(function(dataset, i) {
                        var meta = chartInstance.getDatasetMeta(i);
                        meta.data.forEach(function(bar, index) {
                            var data = dataset.data[index];
                            ctx.fillText(data, bar.x, bar.y - 5);
                        });
                    });
                }
            }
        }
    });

    // Table Sorting Function
    document.querySelectorAll('#appointmentsTable th').forEach((header, index) => {
        header.addEventListener('click', () => {
            sortTable(index);
        });
    });

    function sortTable(columnIndex) {
        let rows = Array.from(document.querySelectorAll('#appointmentsTable tbody tr'));
        let sortedRows = rows.sort((rowA, rowB) => {
            let cellA = rowA.cells[columnIndex].innerText;
            let cellB = rowB.cells[columnIndex].innerText;
            return cellA > cellB ? 1 : cellA < cellB ? -1 : 0;
        });
        let tbody = document.querySelector('#appointmentsTable tbody');
        tbody.innerHTML = '';
        sortedRows.forEach(row => tbody.appendChild(row));
    }
</script>

<?php include('../includes/scripts.php'); ?>
</body>
</html>

<?php
// Helper function to get status badge class based on status
function getStatusClass($status) {
    switch ($status) {
        case 'Completed':
            return 'bg-success';
        case 'Pending':
            return 'bg-warning';
        case 'Cancelled':
            return 'bg-danger';
        default:
            return 'bg-secondary';
    }
}
?>
