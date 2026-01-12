<?php
require_once 'config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

// Get student ID from URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    $_SESSION['success_message'] = 'Invalid student ID.';
    redirect('index.php');
}

// First, get the student name for the success message
$stmt = $conn->prepare("SELECT name FROM students WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['success_message'] = 'Student not found.';
    redirect('index.php');
}

$student = $result->fetch_assoc();
$student_name = $student['name'];
$stmt->close();

// Delete student using prepared statement
$delete_stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
$delete_stmt->bind_param("i", $id);

if ($delete_stmt->execute()) {
    $_SESSION['success_message'] = "Student '$student_name' has been deleted successfully.";
} else {
    $_SESSION['success_message'] = 'Failed to delete student.';
}

$delete_stmt->close();

// Redirect back to index
redirect('index.php');
?>
