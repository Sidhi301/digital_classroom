<?php
session_start(); // Start the session

// Include the database connection
include 'db.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the username, password, and role from the form
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role']; // Get the role selected from the form

    // Validate the login credentials
    if (!empty($username) && !empty($password) && !empty($role)) {
        // Escape the username to prevent SQL Injection
        $username = mysqli_real_escape_string($con, $username);

        // Query to fetch the user data based on the entered username and role
        $query = "SELECT * FROM form1 WHERE username='$username' AND role='$role'";  // Filter by role
        $result = mysqli_query($con, $query);

        // Check if the user exists in the database
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);

            // Check if the user data has a hashed password
            if (isset($row['password']) && !empty($row['password'])) {
                // Verify the hashed password using password_verify()
                if (password_verify($password, $row['password'])) {
                    // Password is correct, store user data in the session
                    // After successful login
                    $_SESSION['logged_in'] = true;
                    $_SESSION['user_id'] = $row['id']; // Store the user ID
                    $_SESSION['username'] = $row['username'];
                    $_SESSION['role'] = $row['role']; // Role (student, teacher) stored in session
                    

                    // Redirect to the dashboard based on the role
                    if ($row['role'] == 'teacher') {
                        header("Location: teacher_dashboard.php"); // Redirect to teacher dashboard
                    } else {
                        header("Location: student_dashboard.php"); // Redirect to student dashboard
                    }
                    exit();
                } else {
                    // If password does not match
                    echo "<script type='text/javascript'>alert('Invalid credentials. Please try again.');</script>";
                }
            } else {
                echo "<script type='text/javascript'>alert('No password found in the database.');</script>";
            }
        } else {
            // If user does not exist or role does not match
            echo "<script type='text/javascript'>alert('Invalid credentials. Please try again.');</script>";
        }
    } else {
        // If either username, password, or role is empty
        echo "<script type='text/javascript'>alert('Please fill in all fields.');</script>";
    }
}
?>

<!-- HTML Form for Login -->
<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>

<body>
    <div class="login-container" id="login-form">
        <h2>Digital Classroom Login</h2>
        <form id="loginForm" action="login.php" method="POST" onsubmit="return validateLoginForm()">
            <!-- Username -->
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required placeholder="Enter your username">

            <!-- Password -->
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required placeholder="Enter your password">

            <!-- Role Selection -->
            <div class="role-selection">
                <label>
                    <input type="radio" name="role" value="student" required>
                    Student
                </label>
                <label>
                    <input type="radio" name="role" value="teacher" required>
                    Teacher
                </label>
            </div>

            <!-- Login Button -->
            <button type="submit" class="login-btn">Login</button>
        </form>

        <div class="toggle-link">
            <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
        </div>
    </div>

    <script>
        // Function to validate login form
        function validateLoginForm() {
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            const role = document.querySelector('input[name="role"]:checked');  // Get selected radio button value

            if (username === "" || password === "" || !role) {
                alert("Please fill in all fields.");
                return false;
            }
            return true;
        }
    </script>
</body>

</html>