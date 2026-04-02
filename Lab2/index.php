<?php
$servername = "192.168.56.11";
$username = "testuser";
$password = "testpass";
$dbname = "webdb";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("DB ERROR: " . $conn->connect_error);
}

// Logga besök
$timestamp = date("Y-m-d H:i:s");
$server_name = gethostname();
$ip = $_SERVER['REMOTE_ADDR'];

$conn->query("INSERT INTO visits (timestamp, server_name, ip) VALUES ('$timestamp', '$server_name', '$ip')");

// Hämta senaste 5 besök
$result = $conn->query("SELECT * FROM visits ORDER BY id DESC LIMIT 5");

$visits = [];
while($row = $result->fetch_assoc()) {
    $visits[] = $row;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="sv">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Server Dashboard</title>

<style>
:root {
    --sage: #9caf88;
    --sage-dark: #7e946d;
    --bg: #f4f7f3;
    --card: rgba(255,255,255,0.6);
    --text: #1d1d1f;
    --subtle: #6e6e73;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
}

body {
    background: linear-gradient(180deg, #eef3ec, #f8faf7);
    color: var(--text);
}

nav {
    display: flex;
    justify-content: space-between;
    padding: 20px 40px;
    backdrop-filter: blur(20px);
    background: rgba(255,255,255,0.5);
    border-bottom: 1px solid rgba(0,0,0,0.05);
}

.container {
    max-width: 1200px;
    margin: auto;
    padding: 60px 30px;
}

.grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 25px;
}

/* FOOTER */
footer {
    margin-top: 60px;
    padding: 30px;
    text-align: center;
    color: var(--subtle);
    font-size: 0.9rem;
}

.card {
    background: var(--card);
    backdrop-filter: blur(25px);
    border-radius: 20px;
    padding: 25px;
    border: 1px solid rgba(255,255,255,0.4);
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
}

.terminal {
    background: #1f1f1f;
    color: #d1ffd6;
    padding: 15px;
    border-radius: 15px;
    font-family: monospace;
    margin-top: 10px;
}
</style>
</head>

<body>

<nav>
    <h1>🌿Server</h1>
    <span id="clock"></span>
</nav>

<div class="container">
    <h2>Overview</h2>

    <div class="grid">

        <div class="card">
            <h3>Web Server</h3>
            <p>Running smoothly</p>
        </div>

        <div class="card">
            <h3>System</h3>
            <p id="system"></p>
        </div>

        <div class="card">
            <h3>Uptime</h3>
            <p id="uptime"></p>
        </div>

        <!-- NY CARD -->
        <div class="card">
            <h3>Recent Visits</h3>
            <div class="terminal">
                <?php foreach ($visits as $visit): ?>
                    <p>
                        <?php echo $visit['timestamp']; ?> 
                        | <?php echo $visit['server_name']; ?>
                            <?php echo $visit['ip']; ?>
                    </p>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
</div>

<footer>
    Designed with a calm, natural interface · 2026
</footer>


<script>
function updateClock() {
    document.getElementById("clock").innerText =
        new Date().toLocaleTimeString();
}
setInterval(updateClock, 1000);

document.getElementById("system").innerText = navigator.platform;

let sec = 0;
setInterval(() => {
    sec++;
    document.getElementById("uptime").innerText = sec + " seconds";
}, 1000);
// BUTTON
function notify() {
    alert("Server is running perfectly 🌿");
}
</script>

</body>
</html>