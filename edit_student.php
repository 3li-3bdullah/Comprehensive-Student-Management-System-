<?php
require_once 'header.php';

$error = '';
$student = null;

// Get student ID from URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    $_SESSION['success_message'] = 'Invalid student ID.';
    redirect('index.php');
}

// Fetch student data using prepared statement
$stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['success_message'] = 'Student not found.';
    redirect('index.php');
}

$student = $result->fetch_assoc();
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $course = trim($_POST['course'] ?? '');
    $address = trim($_POST['address'] ?? '');

    // Validation
    if (empty($name) || empty($email) || empty($course)) {
        $error = 'Please fill in all required fields (Name, Email, Course).';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        // Check if email already exists for another student
        $check_stmt = $conn->prepare("SELECT id FROM students WHERE email = ? AND id != ?");
        $check_stmt->bind_param("si", $email, $id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $error = 'Another student with this email already exists.';
        } else {
            // Update using prepared statement
            $update_stmt = $conn->prepare("UPDATE students SET name = ?, email = ?, phone = ?, course = ?, address = ? WHERE id = ?");
            $update_stmt->bind_param("sssssi", $name, $email, $phone, $course, $address, $id);

            if ($update_stmt->execute()) {
                $_SESSION['success_message'] = "Student '$name' has been updated successfully!";
                redirect('index.php');
            } else {
                $error = 'Failed to update student. Please try again.';
            }
            $update_stmt->close();
        }
        $check_stmt->close();
    }

    // Update student array with posted values for form re-display
    $student['name'] = $name;
    $student['email'] = $email;
    $student['phone'] = $phone;
    $student['course'] = $course;
    $student['address'] = $address;
}
?>

<div class="container">
    <div class="page-header">
        <h1 class="page-title">Edit Student</h1>
        <p class="page-subtitle">Update the student's information below</p>
    </div>

    <div class="card" style="max-width: 700px;">
        <?php if ($error): ?>
            <div class="alert alert-danger">✕ <?php echo h($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label class="form-label" for="name">Full Name *</label>
                <input type="text" id="name" name="name" class="form-control" 
                       placeholder="Enter student's full name"
                       value="<?php echo h($student['name']); ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="email">Email Address *</label>
                <input type="email" id="email" name="email" class="form-control" 
                       placeholder="student@example.com"
                       value="<?php echo h($student['email']); ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" class="form-control" 
                       placeholder="+1 (555) 123-4567"
                       value="<?php echo h($student['phone']); ?>">
            </div>

            <div class="form-group">
                <label class="form-label" for="course">Course / Program *</label>
                <input type="text" id="course" name="course" class="form-control" 
                       placeholder="e.g., Computer Science, Business Administration"
                       value="<?php echo h($student['course']); ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="address">Address</label>
                <textarea id="address" name="address" class="form-control" rows="3" 
                          placeholder="Enter student's address"><?php echo h($student['address']); ?></textarea>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                <button type="submit" class="btn btn-primary">Update Student</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once 'footer.php'; ?>
