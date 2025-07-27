<?php
// Start session and check if user is admin
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "wearview_db");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle status update
if (isset($_POST['update_status'])) {
    $id = $_POST['request_id'];
    $stmt = $conn->prepare("UPDATE it_requests SET status = 'complete' WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// Get filter from URL
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

// Modify SQL based on filter
if ($filter == 'incomplete') {
    $sql = "SELECT * FROM it_requests WHERE status = 'incomplete' ORDER BY id DESC";
} elseif ($filter == 'complete') {
    $sql = "SELECT * FROM it_requests WHERE status = 'complete' ORDER BY id DESC";
} else {
    $sql = "SELECT * FROM it_requests ORDER BY id DESC";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - WearView IT Support</title>
    <style>
        /* Page layout */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
        }
        
        /* Header styling */
        .header {
            background-color: #343a40;
            color: white;
            padding: 20px;
            text-align: center;
        }
        
        /* Container */
        .container {
            max-width: 1200px;
            margin: 30px auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        /* Statistics boxes */
        .stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            border: 1px solid #dee2e6;
        }
        
        .stat-number {
            font-size: 36px;
            font-weight: bold;
            color: #007bff;
        }
        
        /* Filter buttons */
        .filter-buttons {
            margin: 20px 0;
            text-align: center;
        }
        
        .filter-btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 0 5px;
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        
        .filter-btn:hover {
            background-color: #5a6268;
        }
        
        .filter-btn.active {
            background-color: #007bff;
        }
        
        /* Table styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background-color: #007bff;
            color: white;
        }
        
        tr:hover {
            background-color: #f5f5f5;
        }
        
        /* Status badges */
        .status-incomplete {
            background-color: #ffc107;
            color: #000;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
        }
        
        .status-complete {
            background-color: #28a745;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
        }
        
        /* Complete button */
        .complete-btn {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 5px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .complete-btn:hover {
            background-color: #218838;
        }
        
        /* Logout link */
        .logout {
            float: right;
            color: white;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Admin Dashboard - WearView IT Support</h1>
        <a href="logout.php" class="logout">Logout</a>
    </div>
    
    <div class="container">
        <?php
        // Calculate statistics (always show all stats regardless of filter)
        $stats_result = $conn->query("SELECT * FROM it_requests");
        $total_requests = $stats_result->num_rows;
        $incomplete = 0;
        $complete = 0;
        
        while($row = $stats_result->fetch_assoc()) {
            if($row['status'] == 'incomplete') {
                $incomplete++;
            } else {
                $complete++;
            }
        }
        ?>
        
        <!-- Statistics Display -->
        <div class="stats">
            <div class="stat-box">
                <div class="stat-number"><?php echo $total_requests; ?></div>
                <div>Total Requests</div>
            </div>
            <div class="stat-box">
                <div class="stat-number"><?php echo $incomplete; ?></div>
                <div>Incomplete</div>
            </div>
            <div class="stat-box">
                <div class="stat-number"><?php echo $complete; ?></div>
                <div>Complete</div>
            </div>
        </div>
        
        <!-- Filter Buttons -->
        <div class="filter-buttons">
            <a href="?filter=all" class="filter-btn <?php echo $filter == 'all' ? 'active' : ''; ?>">All Jobs</a>
            <a href="?filter=incomplete" class="filter-btn <?php echo $filter == 'incomplete' ? 'active' : ''; ?>">Incomplete Jobs</a>
            <a href="?filter=complete" class="filter-btn <?php echo $filter == 'complete' ? 'active' : ''; ?>">Complete Jobs</a>
        </div>
        
        <h2>
            <?php 
            if ($filter == 'incomplete') echo "Incomplete IT Requests";
            elseif ($filter == 'complete') echo "Complete IT Requests";
            else echo "All IT Requests";
            ?>
        </h2>
        
        <!-- Requests Table -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Staff Name</th>
                    <th>Email</th>
                    <th>Location</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["id"] . "</td>";
                        echo "<td>" . htmlspecialchars($row["staff_name"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["email"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["location"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["description"]) . "</td>";
                        echo "<td>";
                        if($row["status"] == "incomplete") {
                            echo '<span class="status-incomplete">Incomplete</span>';
                        } else {
                            echo '<span class="status-complete">Complete</span>';
                        }
                        echo "</td>";
                        echo "<td>";
                        if($row["status"] == "incomplete") {
                            echo '<form method="POST" style="display:inline;">
                                    <input type="hidden" name="request_id" value="' . $row["id"] . '">
                                    <button type="submit" name="update_status" class="complete-btn">Mark Complete</button>
                                  </form>';
                        } else {
                            echo "—";
                        }
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' style='text-align:center;'>No " . ($filter != 'all' ? $filter : '') . " IT requests found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
$conn->close();
?>