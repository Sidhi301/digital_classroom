<?php
session_start();
require 'db.php';         // For login 
require 'assignment_db.php'; // For assignment database
require 'submission_db.php'; // For submissions database

if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['user_id'];

// Ensure database connections exist
if (!isset($assignmentPDO) || !isset($pdo)) {
    die("Error: Database connections not established.");
}

// Fetch assignments
try {
    $stmt = $assignmentPDO->query("SELECT * FROM assignment ORDER BY due_date DESC");
    $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching assignments: " . $e->getMessage());
}

// Handle assignment submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['assignment_file'])) {
    $assignment_id = $_POST['assignment_id'];
    $file_name = $_FILES['assignment_file']['name'];
    $file_tmp = $_FILES['assignment_file']['tmp_name'];
    $upload_dir = 'uploads/';
    $file_path = $upload_dir . $file_name;
    $submitted_at = date('Y-m-d H:i:s'); // Current timestamp
    $status = "Pending"; // Default status

    if (move_uploaded_file($file_tmp, $file_path)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO submissions (student_id, assignment_id, file_path, submitted_at, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$student_id, $assignment_id, $file_path, $submitted_at, $status]);
            echo "<script>alert('Assignment submitted successfully!');</script>";
        } catch (PDOException $e) {
            die("Error submitting assignment: " . $e->getMessage());
        }
    } else {
        echo "<script>alert('File upload failed.');</script>";
    }
}

// Fetch submissions
try {
    $stmt = $pdo->prepare("SELECT * FROM submissions WHERE student_id = ?");
    $stmt->execute([$student_id]);
    $submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching submissions: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="student_dashboard.css">
</head>
<body>
    <header>
        <div class="logo">Student Dashboard</div>
        <div class="profile">
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <button onclick="window.location.href='logout.php'">Logout</button>
        </div>
    </header>
    <nav class="sidebar">
        <ul>
            <li><button onclick="showSection('dashboard')">Dashboard</button></li>
            <li><button onclick="showSection('assignments')">Assignments</button></li>
            <li><button onclick="showSection('submissions')">My Submissions</button></li>
        </ul>
    </nav>
    <main class="content-wrapper">
        <section id="dashboard" class="content-section active">
            <h2 class="dashboard-header">Dashboard</h2>
            <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?> to your student dashboard. View and submit assignments here.</p>
        </section>
        
        <section id="assignments" class="content-section">
            <h2 class="section-title">Available Assignments</h2>
            <ul class="assignment-list">
                <?php foreach ($assignments as $row) { ?>
                    <li><strong><?php echo htmlspecialchars($row['title']); ?></strong> - Due: <?php echo htmlspecialchars($row['due_date']); ?></li>
                    <form action="" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="assignment_id" value="<?php echo $row['id']; ?>">
                        <input type="file" name="assignment_file" required>
                        <button type="submit">Submit</button>
                    </form>
                <?php } ?>
            </ul>
        </section>
        
        <section id="submissions" class="content-section">
            <h2 class="section-title">My Submissions</h2>
            <ul class="grade-list">
                <?php foreach ($submissions as $sub) { ?>
                    <li><strong><?php echo htmlspecialchars(basename($sub['file_path'])); ?></strong> - Submitted on <?php echo htmlspecialchars($sub['submitted_at']); ?> </li>
                <?php } ?>
            </ul>
        </section>
    </main>
    <script>
        function showSection(sectionId) {
            document.querySelectorAll('.content-section').forEach(sec => sec.classList.remove('active'));
            document.getElementById(sectionId).classList.add('active');
        }
    </script>
</body>
</html>