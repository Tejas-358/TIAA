<?php
$servername = "localhost";
$username = "root";
$password = "Tejas@358"; // No password for the root user
$dbname = "UserFinancialDB";

// Establish a MySQL connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user ID is provided in the URL
if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM financial_info WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    echo '<html lang="en">
    <head>
        <!-- Include Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f8f9fa;
                text-align: center;
                padding: 20px;
            }

            .dashboard-container {
                background-color: #ffffff;
                padding: 20px;
                border-radius: 10px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                margin: 20px auto;
                max-width: 600px;
            }

            h2 {
                color: #343a40;
            }

            .info-item {
                margin-bottom: 15px;
                color: #495057;
            }
        </style>
    </head>
    <body>';

    echo '<div class="dashboard-container">';
    echo '<h2>User Dashboard</h2>';

    // Display financial information
    echo '<div class="info-item">';
    if ($result->num_rows > 0) {
        // Move the while loop inside this condition
        while ($row = $result->fetch_assoc()) {
            echo "<strong>Bank Name:</strong> " . $row['bank_name'] . "<br>";
            echo "<strong>Amount Withdrawn:</strong> $" . $row['amount_withdrawn'] . "<br>";
            echo "<strong>Transaction Date:</strong> " . $row['transaction_date'] . "<br>";
        }

        // JavaScript for Chart.js
        echo '<canvas id="amountChart" width="400" height="200"></canvas>
        <script>
            var ctx = document.getElementById("amountChart").getContext("2d");
            var chart = new Chart(ctx, {
                type: "bar",
                data: {
                    labels: ["Bank Name"],
                    datasets: [{
                        label: "Amount Withdrawn",
                        data: [' . $row['amount_withdrawn'] . '],
                        backgroundColor: "#007bff"
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        </script>';
    } else {
        echo '<p>No financial information available for this user.</p>';
    }
    echo '</div>';

    echo '</div>';

    echo '</body></html>';

    $stmt->close();
} else {
    echo "User ID not provided.";
}

$conn->close();
?>
