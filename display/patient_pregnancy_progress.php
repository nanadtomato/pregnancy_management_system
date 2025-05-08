<?php
session_start();
require_once "../config.php";

// Ensure user is patient
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    header("Location: ../login.php");
    exit();
}

$patient_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pregnancy Progress Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../css/mainStyles.css?v=<?= time() ?>">

    <style>
        
        .bg-pink { background-color: #f78da7; }
        .btn-pink { background-color: #f78da7; color: white; }

        .btn-pink {
    background-color: #f78da7;
    color: white;
    border: none;
    border-radius: 50px;
    padding: 10px 20px;
    font-weight: bold;
    transition: background-color 0.3s ease;
}
.btn-pink:hover {
    background-color: #f55d83;
}

    </style>
</head>

<!DOCTYPE html>
<html lang="en">
<!-- <?php include('../includes/header.php'); ?> -->
<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<link rel="stylesheet" href="../css/mainStyles.css">
    <title>Nurse Dashboard</title>
</head>
<body>
 <div class="main-content">
 <main>
 <div class="container py-4">

    <h2 class="text-center mb-5">Pregnancy Progress Tracker </h2>

    <!-- Kick Tracking Section -->
    <div class="card mb-4">
        <div class="card-header bg-danger-subtle text-dark">Track Baby Kick</div>
        <div class="card-body text-center">
            <div class="mb-2">
                <button id="trackKickBtn" type="button" class="btn-pink">Track Kick</button>
            </div>
            <p class="mt-2">Time Left: <span id="timer">--:--:--</span></p>
            <div id="kickCountDisplay">Total Kicks Today: <span id="kickCount">0</span>/10</div>
            <div id="kickStatus" class="mt-3 text-success"></div>
        </div>
    </div>

    <!-- Graph Summary Section -->
    <div class="card mb-4">
    <div class="card-header bg-danger-subtle text-dark">Kick Summary </div>
        
    <div class="row mb-3">
    <div class="col-md-4">
        <label>Filter Range</label>
        <select id="filterRange" class="form-select">
            <option value="today">Today</option>
            <option value="7">Last 7 Days</option>
            <option value="30">Last 1 Month</option>
            <option value="90">Last 3 Months</option>
            <option value="180">Last 6 Months</option>
            <option value="270">Last 9 Months</option>
            <option value="custom">Custom Range</option>
        </select>
    </div>
    <div class="col-md-4">
        <label>From</label>
        <input type="date" id="startDate" class="form-control" disabled>
    </div>
    <div class="col-md-4">
        <label>To</label>
        <input type="date" id="endDate" class="form-control" disabled>
    </div>
</div>

        <div class="card-body">
            <canvas id="kickChart"></canvas>
        </div>
    </div>

    <!-- Manual Entry/Edit Section -->
    <div class="card">
        
        <div class="card-header bg-danger-subtle text-dark">Manual Entry</div>
        <div class="card-body">
            <form id="manualForm">
                <div class="row mb-2">
                    <div class="col">
                        <label>Date</label>
                        <input type="date" name="log_date" class="form-control" required>
                    </div>
                    <div class="col">
                        <label>Start Time</label>
                        <input type="time" name="start_time" class="form-control" required>
                    </div>
                    <div class="col">
                        <label>End Time</label>
                        <input type="time" name="end_time" class="form-control" required>
                    </div>
                    <div class="col">
                        <label>Kick Count</label>
                        <input type="number" name="kick_count" class="form-control" min="1" max="10" required>
                    </div>
                </div>
                <button type="submit" class="btn-pink">Submit Log</button>
            </form>
            <div id="manualStatus" class="mt-2"></div>
        </div>
    </div>
</div>

  </main>
</div>



<?php include('../includes/navbar.php'); ?>
<?php include('../includes/scripts.php'); ?>
<script>
let countdown = 43200; // 12 hours in seconds
let timerInterval;

function formatTime(seconds) {
    const hrs = String(Math.floor(seconds / 3600)).padStart(2, '0');
    const mins = String(Math.floor((seconds % 3600) / 60)).padStart(2, '0');
    const secs = String(seconds % 60).padStart(2, '0');
    return `${hrs}:${mins}:${secs}`;
}

function startTimer() {
    clearInterval(timerInterval);
    timerInterval = setInterval(() => {
        if (countdown > 0) {
            countdown--;
            document.getElementById("timer").textContent = formatTime(countdown);
        }
    }, 1000);
}

// Load current kick count
function loadKickCount() {
    fetch('patient_track_kick.php?action=count')
        .then(res => res.json())
        .then(data => {
            document.getElementById("kickCount").textContent = data.kick_count;
            if (data.kick_count >= 10) {
                document.getElementById("trackKickBtn").disabled = true;
                document.getElementById("kickStatus").textContent = "10 kicks recorded. Tracking completed for today.";
            } else {
                startTimer();
            }
        });
}

// Track Kick button logic
document.getElementById("trackKickBtn").addEventListener("click", () => {
    fetch('patient_track_kick.php?action=track')
        .then(res => res.json())
        .then(data => {
            document.getElementById("kickStatus").textContent = data.message;
            loadKickCount();
            loadChart();
        });
});

// Manual log form submit
const manualForm = document.getElementById("manualForm");
manualForm.addEventListener("submit", function(e) {
    e.preventDefault();
    const formData = new FormData(manualForm);
    fetch("patient_track_kick.php?action=manual", {
        method: "POST",
        body: formData
    }).then(res => res.json())
    .then(data => {
        document.getElementById("manualStatus").textContent = data.message;
        loadKickCount();
        loadChart();
    });
});

// Load chart data
function loadChart() {
    fetch('patient_get_kick_data.php')
        .then(res => res.json())
        .then(data => {
            const dates = data.map(d => d.log_date);
            const kicks = data.map(d => d.kick_count);

            const ctx = document.getElementById('kickChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: dates,
                    datasets: [{
                        label: 'Kick Count',
                        data: kicks,
                        fill: false,
                        borderColor: '#f78da7',
                        tension: 0.1
                    }]
                }
            });
        });
}

loadKickCount();
loadChart();

document.getElementById("filterRange").addEventListener("change", () => {
    const selected = document.getElementById("filterRange").value;
    const custom = selected === "custom";
    document.getElementById("startDate").disabled = !custom;
    document.getElementById("endDate").disabled = !custom;
    if (!custom) {
        loadChart(); // Only load chart for non-custom
    }
});

document.getElementById("startDate").addEventListener("change", () => {
    const start = document.getElementById("startDate").value;
    const end = document.getElementById("endDate").value;
    if (start && end) loadChart();
});

document.getElementById("endDate").addEventListener("change", () => {
    const start = document.getElementById("startDate").value;
    const end = document.getElementById("endDate").value;
    if (start && end) loadChart();
});

if (!data || data.length === 0) {
    // Optional: display message like "No data available"
    return;
}


document.getElementById("startDate").addEventListener("change", loadChart);
document.getElementById("endDate").addEventListener("change", loadChart);

function loadChart() {
    const filterRange = document.getElementById("filterRange").value;
    const startDate = document.getElementById("startDate").value;
    const endDate = document.getElementById("endDate").value;

    const params = new URLSearchParams({
        range: filterRange,
        start: startDate,
        end: endDate
    });

    fetch('patient_get_kick_data.php?' + params.toString())
        .then(res => res.json())
        .then(data => {
            const dates = data.map(d => d.log_date);
            const kicks = data.map(d => d.kick_count);

            const ctx = document.getElementById('kickChart').getContext('2d');
            if (window.kickChartInstance) window.kickChartInstance.destroy();

            window.kickChartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: dates.reverse(),
                    datasets: [{
                        label: 'Kick Count',
                        data: kicks.reverse(),
                        fill: false,
                        borderColor: '#f78da7',
                        tension: 0.1
                    }]
                }
            });
        });
}

document.getElementById("trackKickBtn").addEventListener("click", () => {
    console.log("Track Kick clicked!");
    fetch('patient_track_kick.php?action=track')
        .then(res => res.json())
        .then(data => {
            console.log("Track Kick Response:", data); // <-- Add this
            document.getElementById("kickStatus").textContent = data.message;
            loadKickCount();
            loadChart();
        }).catch(err => console.error("Error in fetch:", err)); // and this
});

</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
