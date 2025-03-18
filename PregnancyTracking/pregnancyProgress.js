// Chart initialization
const ctx = document.getElementById('kickChart').getContext('2d');
const kickChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: [],
        datasets: [{
            label: 'Kick Count',
            data: [],
            borderColor: '#4e73df',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top'
            },
        }
    }
});

// Manual log submission
document.getElementById('manualLogForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch('../PregnancyTracking/save_kick_log.php', {
        method: 'POST',
        body: formData,
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert(data.message);
            } else {
                alert(data.message);
            }
        })
        .catch(error => console.error('Error:', error));
});

// Update chart when date is selected
document.getElementById('logDate').addEventListener('change', function () {
    const selectedDate = this.value;

    fetch(`../PregnancyTracking/get_kick_logs.php?date=${selectedDate}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const logs = data.data;
                const labels = logs.map(log => log.start_time);
                const kickCounts = logs.map(log => log.kick_count);

                kickChart.data.labels = labels;
                kickChart.data.datasets[0].data = kickCounts;
                kickChart.update();
            } else {
                alert('Failed to retrieve logs.');
            }
        })
        .catch(error => console.error('Error:', error));
});

// Start/Stop button functionality for tracking kicks
document.getElementById('startStopBtn').addEventListener('click', function () {
    const kickCountDisplay = document.getElementById('kickCount');
    const timeLeftDisplay = document.getElementById('timeLeft');
    let kickCount = 0;
    let timer;
    let timeLeft = 12 * 60 * 60; // 12 hours in seconds

    // Start tracking when button is clicked
    if (this.textContent === 'Start') {
        this.textContent = 'Stop'; // Change button text to Stop
        kickCount = 0; // Reset kick count
        kickCountDisplay.textContent = kickCount;
        timeLeftDisplay.textContent = formatTime(timeLeft);

        // Start timer countdown
        timer = setInterval(() => {
            if (timeLeft <= 0 || kickCount >= 10) {
                clearInterval(timer); // Stop timer when time is up or 10 kicks are reached
                this.textContent = 'Start'; // Change button back to Start
                alert('Tracking has stopped.');
            } else {
                timeLeft--;
                timeLeftDisplay.textContent = formatTime(timeLeft);
            }
        }, 1000);
    } else {
        // Stop tracking when button is clicked again
        clearInterval(timer);
        this.textContent = 'Start';
    }

    // Function to format time in HH:MM:SS
    function formatTime(seconds) {
        const hours = Math.floor(seconds / 3600);
        const minutes = Math.floor((seconds % 3600) / 60);
        const secs = seconds % 60;
        return `${pad(hours)}:${pad(minutes)}:${pad(secs)}`;
    }

    // Function to pad time units
    function pad(num) {
        return num < 10 ? '0' + num : num;
    }

    // Listen for kick count event (You need to implement kick counting separately)
    document.addEventListener('kickDetected', function () {
        kickCount++;
        kickCountDisplay.textContent = kickCount;

        // Update chart data
        const currentTime = new Date().toLocaleTimeString();
        kickChart.data.labels.push(currentTime);
        kickChart.data.datasets[0].data.push(kickCount);
        kickChart.update();

        if (kickCount >= 10) {
            clearInterval(timer); // Stop the timer once 10 kicks are counted
            alert('Tracking has stopped after 10 kicks.');
        }
    });
});


// Example of triggering the kickDetected event
function simulateKick() {
    const event = new Event('kickDetected');
    document.dispatchEvent(event);
}
// Handle manual log submission
document.getElementById('manualLogForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    fetch('../backend/save_kick_log.php', {
        method: 'POST',
        body: formData,
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
    })
    .catch(error => console.error('Error:', error));
});
// You can call simulateKick() whenever a kick is detected (e.g., by clicking a button or via other logic)
