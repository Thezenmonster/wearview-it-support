<?php
// Start session and check if user is logged in
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'staff') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Report IT Issue - WearView</title>
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
            background-color: #007bff;
            color: white;
            padding: 20px;
            text-align: center;
        }
        
        /* Container for form */
        .container {
            max-width: 600px;
            margin: 30px auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        /* Form elements */
        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }
        
        input[type="text"],
        input[type="email"],
        textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        
        textarea {
            height: 100px;
            resize: vertical;
        }
        
        /* Submit button */
        button {
            background-color: #007bff;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
        }
        
        button:hover {
            background-color: #0056b3;
        }
        
        /* Success message */
        .success {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        /* Error message */
        .error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
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
        <h1>WearView IT Support System</h1>
        <a href="logout.php" class="logout">Logout</a>
    </div>
    
    <div class="container">
        <h2>Report IT Issue</h2>
        
        <?php
        // Database connection
        $conn = new mysqli("localhost", "root", "", "wearview_db");
        
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        // Process form submission
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Prepare statement for security
            $stmt = $conn->prepare("INSERT INTO it_requests (staff_name, email, location, description) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $_POST['staff_name'], $_POST['email'], $_POST['location'], $_POST['description']);
            
            if ($stmt->execute()) {
                echo '<div class="success">IT issue reported successfully!</div>';
            } else {
                echo '<div class="error">Error submitting report. Please try again.</div>';
            }
            $stmt->close();
        }
        ?>
        
        <!-- Report form -->
        <form method="POST" action="" onsubmit="return validateForm()" novalidate>
            <label for="staff_name">Name:</label>
            <input type="text" id="staff_name" name="staff_name">
            
            <label for="email">Email:</label>
            <input type="email" id="email" name="email">
            
            <label for="location">Location:</label>
            <input type="text" id="location" name="location">
            
            <label for="description">Description of Issue:</label>
            <textarea id="description" name="description"></textarea>
            
            <button type="submit">Submit Report</button>
        </form>
    </div>

    <!-- Client-side validation -->
    <script>
    function validateForm() {
        // Get form elements
        var staffName = document.getElementById('staff_name').value.trim();
        var email = document.getElementById('email').value.trim();
        var location = document.getElementById('location').value.trim();
        var description = document.getElementById('description').value.trim();
        
        // Validate name (minimum 2 characters)
        if (staffName.length < 2) {
            alert("Please enter a valid name (at least 2 characters)");
            return false;
        }
        
        // Validate email format
        var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(email)) {
            alert("Please enter a valid email address");
            return false;
        }
        
        // Validate location (minimum 3 characters)
        if (location.length < 3) {
            alert("Please enter a valid location (at least 3 characters)");
            return false;
        }
        
        // Validate description (minimum 10 characters)
        if (description.length < 10) {
            alert("Please provide a detailed description (at least 10 characters)");
            return false;
        }
        
        // All validation passed
        return true;
    }
    </script>
</body>
</html>