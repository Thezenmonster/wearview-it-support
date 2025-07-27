<!DOCTYPE html>
<html>
<head>
    <title>WearView IT Support - Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-box {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            width: 300px;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .error {
            color: red;
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>WearView IT Support</h2>
        <h3>Staff Login</h3>
        
        <?php
        session_start();
        
        // Check if form was submitted
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $username = $_POST['username'];
            $password = $_POST['password'];
            
            // Check credentials - UPDATED TO MATCH ASSIGNMENT BRIEF
            if ($username == "staffmember" && $password == "letmein!123") {
                $_SESSION['user_type'] = 'staff';
                header("Location: report_issue.php");
                exit();
            } elseif ($username == "admin" && $password == "heretohelp!456") {
                $_SESSION['user_type'] = 'admin';
                header("Location: admin_dashboard.php");
                exit();
            } else {
                echo '<p class="error">Invalid username or password!</p>';
            }
        }
        ?>
        
        <form method="POST" action="">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>