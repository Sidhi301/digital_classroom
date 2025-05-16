<?php
session_start();
include 'db.php'; // Ensure database connection is included

// Check if the user is logged in and has the 'teacher' role
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['role'] !== 'teacher') {
    header("Location: login.php"); // Redirect to login if not logged in as a teacher
    exit();
}

$username = $_SESSION['username'];

// Handle assignment upload
$uploadMessage = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['upload'])) {
    if (!empty($_POST['assignment_name']) && !empty($_POST['due_date']) && !empty($_FILES["file"]["name"])) {
        $assignment_name = $_POST['assignment_name'];
        $due_date = $_POST['due_date'];
        $filePath = "uploads/" . basename($_FILES["file"]["name"]);

        if (move_uploaded_file($_FILES["file"]["tmp_name"], $filePath)) {
            $insertSQL = "INSERT INTO assignment (title, due_date, file_path) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($con, $insertSQL);
            mysqli_stmt_bind_param($stmt, 'sss', $assignment_name, $due_date, $filePath);
            if (mysqli_stmt_execute($stmt)) {
                $uploadMessage = "Assignment uploaded successfully.";
            } else {
                $uploadMessage = "Error saving to database.";
            }
        } else {
            $uploadMessage = "Error uploading file.";
        }
    } else {
        $uploadMessage = "All fields are required.";
    }
}

// Fetch all assignments
$assignments = [];
$sql = "SELECT * FROM assignment";
$result = mysqli_query($con, $sql);
if ($result) {
    $assignments = mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Fetch submitted assignments with student details
$submittedAssignments = [];
$submissionSQL = "SELECT s.*, f.username, a.title AS assignment_name, s.submitted_at 
                  FROM submissions s 
                  JOIN form1 f ON s.student_id = f.id 
                  JOIN assignment a ON s.assignment_id = a.id 
                  ORDER BY s.submitted_at DESC";
$submissionResult = mysqli_query($con, $submissionSQL);
if ($submissionResult) {
    $submittedAssignments = mysqli_fetch_all($submissionResult, MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Teacher Dashboard</title>
    <link rel="stylesheet" href="teacher_dashboard.css">
    <script>
        function showSection(sectionId) {
            document.getElementById('assignments').style.display = 'none';
            document.getElementById('submitted').style.display = 'none';
            document.getElementById('upload').style.display = 'none';
            document.getElementById(sectionId).style.display = 'block';
        }
    </script>
</head>
<body>

<header>
    <div class="logo">Teacher Dashboard</div>
    <div class="profile">
        <span>Welcome, <?php echo htmlspecialchars($username); ?></span>
        <button onclick="location.href='logout.php'">Logout</button>
    </div>
</header>

<nav class="sidebar">
    <ul>
        <li><button onclick="showSection('assignments')">All Assignments</button></li>
        <li><button onclick="showSection('submitted')">Submitted Assignments</button></li>
        <li><button onclick="showSection('upload')">Upload Assignment</button></li>
    </ul>
</nav>

<main>
    <section id="assignments" style="display: block;">
        <h2>All Assignments</h2>
        <table border="1">
            <tr>
                <th>Assignment Name</th>
                <th>Due Date</th>
                <th>File</th>
            </tr>
            <?php foreach ($assignments as $assignment): ?>
            <tr>
                <td><?php echo isset($assignment['title']) ? htmlspecialchars($assignment['title']) : 'N/A'; ?></td>
                <td><?php echo isset($assignment['due_date']) ? htmlspecialchars($assignment['due_date']) : 'N/A'; ?></td>
                <td><a href="<?php echo isset($assignment['file_path']) ? htmlspecialchars($assignment['file_path']) : '#'; ?>" target="_blank">View File</a></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </section>

    <section id="submitted" style="display: none;">
        <h2>Submitted Assignments</h2>
        <table border="1">
            <tr>
                <th>Assignment Name</th>
                <th>Student Name</th>
                <th>Submission Date</th>
                <th>File</th>
            </tr>
            <?php foreach ($submittedAssignments as $row): ?>
            <tr>
                <td><?php echo isset($row['assignment_name']) ? htmlspecialchars($row['assignment_name']) : 'N/A'; ?></td>
                <td><?php echo isset($row['username']) ? htmlspecialchars($row['username']) : 'N/A'; ?></td>
                <td><?php echo isset($row['submitted_at']) ? htmlspecialchars($row['submitted_at']) : 'N/A'; ?></td>
                <td><a href="<?php echo isset($row['file_path']) ? htmlspecialchars($row['file_path']) : '#'; ?>" target="_blank">View File</a></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </section>

    <section id="upload" style="display: none;">
        <h2>Upload Assignment</h2>
        <?php if ($uploadMessage): ?>
            <p style="color: green;"><?php echo $uploadMessage; ?></p>
        <?php endif; ?>
        <form method="post" enctype="multipart/form-data">
            <label>Assignment Name:</label>
            <input type="text" name="assignment_name" required>
            <label>Due Date:</label>
            <input type="date" name="due_date" required>
            <label>Upload File:</label>
            <input type="file" name="file" required>
            <button type="submit" name="upload">Upload</button>
        </form>
    </section>
</main>

</body>
</html>
            