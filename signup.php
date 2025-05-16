<?php

// Start session
session_start();

// Include database connection
include("db.php");

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Get user input
    $username = $_POST['username'];
    $password = $_POST['password'];
    $gmail = $_POST['email'];
    $role = $_POST['role'];  // Get the role value from the form

    // Validate input data
    if (!empty($username) && !empty($password) && !empty($gmail) && filter_var($gmail, FILTER_VALIDATE_EMAIL) && !empty($role)) {
        
        // Check if password is strong enough
        if (strlen($password) >= 5) {
            
            // Hash the password before storing in the database
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            // Sanitize the email, username, and role to prevent SQL injection
            $username = mysqli_real_escape_string($con, $username);
            $gmail = mysqli_real_escape_string($con, $gmail);
            $role = mysqli_real_escape_string($con, $role);

            // Prepare the query to insert the user data
            $query = "INSERT INTO form1 (username, password, gmail, role) VALUES ('$username', '$hashedPassword', '$gmail', '$role')";

            // Execute the query
            if (mysqli_query($con, $query)) {
                // Redirect to login page after successful registration
                header("Location: login.php");
                exit(); // Stop further script execution
            } else {
                echo "<script type='text/javascript'>alert('Error: " . mysqli_error($con) . "')</script>";
            }
        } else {
            echo "<script type='text/javascript'>alert('Password must be at least 5 characters long.')</script>";
        }
    } else {
        echo "<script type='text/javascript'>alert('Please enter valid information.')</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
</head>

<body>

    <div class="login-container" id="signup-form">
        <h2>Digital Classroom Sign Up</h2>
        <form id="signupForm" action="signup.php" method="POST" onsubmit="return validateSignupForm()">

            <!-- Username -->
            <label for="signupUsername">Username</label>
            <input type="text" id="signupUsername" name="username" required placeholder="Enter your username">

            <!-- Password -->
            <label for="signupPassword">Password</label>
            <input type="password" id="signupPassword" name="password" required placeholder="Enter your password">

            <!-- Email -->
            <label for="signupEmail">Email</label>
            <input type="email" id="signupEmail" name="email" required placeholder="Enter your email-id">

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

            <!-- Sign Up Button -->
            <button type="submit" class="login-btn">Sign Up</button>
        </form>

        <div class="toggle-link">
            <p>Already have an account? <a href="login.php">Login</a></p>
        </div>
    </div>

    <script>
        // Function to validate signup form
        function validateSignupForm() {
            const username = document.getElementById('signupUsername').value;
            const password = document.getElementById('signupPassword').value;
            const email = document.getElementById('signupEmail').value;
            const role = document.querySelector('input[name="role"]:checked'); // Get selected role

            if (username === "" || password === "" || email === "" || !role) {
                alert("Please fill in all fields.");
                return false;
            }

            if (password.length < 6) {
                alert("Password must be at least 6 characters.");
                return false;
            }

            // Validate email format
            const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
            if (!emailPattern.test(email)) {
                alert("Please enter a valid email address.");
                return false;
            }

            return true;
        }
    </script>

</body>

</html>

