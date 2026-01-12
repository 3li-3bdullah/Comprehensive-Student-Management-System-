<?php
require_once 'header.php';

$error = '';
$success = '';

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
        // Check if email already exists using prepared statement
        $check_stmt = $conn->prepare("SELECT id FROM students WHERE email = ?");
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $error = 'A student with this email already exists.';
        } else {
            // Insert using prepared statement to prevent SQL injection
            $stmt = $conn->prepare("INSERT INTO students (name, email, phone, course, address) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $name, $email, $phone, $course, $address);

            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Student '$name' has been added successfully!";
                redirect('index.php');
            } else {
                $error = 'Failed to add student. Please try again.';
            }
            $stmt->close();
        }
        $check_stmt->close();
    }
}
?>

<div class="container">
    <div class="page-header">
        <h1 class="page-title">Add New Student</h1>
        <p class="page-subtitle">Fill in the details to register a new student</p>
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
                       value="<?php echo h($_POST['name'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="email">Email Address *</label>
                <input type="email" id="email" name="email" class="form-control" 
                       placeholder="student@example.com"
                       value="<?php echo h($_POST['email'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" class="form-control" 
                       placeholder="+1 (555) 123-4567"
                       value="<?php echo h($_POST['phone'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label class="form-label" for="course">Course / Program *</label>
                <input type="text" id="course" name="course" class="form-control" 
                       placeholder="e.g., Computer Science, Business Administration"
                       value="<?php echo h($_POST['course'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="address">Address</label>
                <textarea id="address" name="address" class="form-control" rows="3" 
                          placeholder="Enter student's address"><?php echo h($_POST['address'] ?? ''); ?></textarea>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                <button type="submit" class="btn btn-primary">Add Student</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once 'footer.php'; ?>
