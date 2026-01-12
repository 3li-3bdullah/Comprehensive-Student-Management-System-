<?php
require_once 'header.php';

// Get search and sort parameters
$search = trim($_GET['search'] ?? '');
$sort_by = $_GET['sort'] ?? 'id';
$sort_order = $_GET['order'] ?? 'DESC';

// Validate sort parameters
$allowed_sort = ['id', 'name', 'email', 'course', 'created_at'];
$allowed_order = ['ASC', 'DESC'];

if (!in_array($sort_by, $allowed_sort)) {
    $sort_by = 'id';
}
if (!in_array($sort_order, $allowed_order)) {
    $sort_order = 'DESC';
}

// Build query with search
$query = "SELECT * FROM students";
$params = [];
$types = "";

if (!empty($search)) {
    $query .= " WHERE name LIKE ? OR email LIKE ? OR course LIKE ?";
    $search_param = "%$search%";
    $params = [$search_param, $search_param, $search_param];
    $types = "sss";
}

$query .= " ORDER BY $sort_by $sort_order";

// Execute query using prepared statement
$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Get total count
$total_students = $result->num_rows;

// Get statistics
$stats_query = $conn->query("SELECT COUNT(*) as total FROM students");
$total_all = $stats_query->fetch_assoc()['total'];

// Function to generate sort link
function sortLink($column, $label, $current_sort, $current_order)
{
    $new_order = ($current_sort === $column && $current_order === 'ASC') ? 'DESC' : 'ASC';
    $arrow = '';
    if ($current_sort === $column) {
        $arrow = $current_order === 'ASC' ? ' ↑' : ' ↓';
    }
    $search = isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '';
    return "<a href=\"?sort=$column&order=$new_order$search\">$label$arrow</a>";
}

// Check for success message
$success_message = $_SESSION['success_message'] ?? '';
unset($_SESSION['success_message']);
?>

<div class="container">
    <div class="page-header">
        <h1 class="page-title">Student Dashboard</h1>
        <p class="page-subtitle">Manage and track all your student records</p>
    </div>

    <?php if ($success_message): ?>
        <div class="alert alert-success">
            ✓ <?php echo h($success_message); ?>
        </div>
    <?php endif; ?>

    <!-- Statistics -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value"><?php echo $total_all; ?></div>
            <div class="stat-label">Total Students</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?php echo $total_students; ?></div>
            <div class="stat-label">Showing Results</div>
        </div>
    </div>

    <!-- Search and Actions -->
    <div class="card">
        <form method="GET" class="search-box">
            <input type="text" name="search" class="form-control search-input"
                placeholder="Search by name, email, or course..."
                value="<?php echo h($search); ?>">
            <button type="submit" class="btn btn-primary">Search</button>
            <?php if (!empty($search)): ?>
                <a href="index.php" class="btn btn-secondary">Clear</a>
            <?php endif; ?>
            <a href="add_student.php" class="btn btn-primary">+ Add Student</a>
        </form>

        <?php if ($total_students > 0): ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th><?php echo sortLink('id', 'ID', $sort_by, $sort_order); ?></th>
                            <th><?php echo sortLink('name', 'Name', $sort_by, $sort_order); ?></th>
                            <th><?php echo sortLink('email', 'Email', $sort_by, $sort_order); ?></th>
                            <th><?php echo sortLink('course', 'Course', $sort_by, $sort_order); ?></th>
                            <th><?php echo sortLink('created_at', 'Added', $sort_by, $sort_order); ?></th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><span class="badge badge-success">#<?php echo h($row['id']); ?></span></td>
                                <td><strong><?php echo h($row['name']); ?></strong></td>
                                <td><?php echo h($row['email']); ?></td>
                                <td><span class="badge badge-warning"><?php echo h($row['course']); ?></span></td>
                                <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                                <td>
                                    <div class="actions">
                                        <a href="edit_student.php?id=<?php echo $row['id']; ?>" class="btn btn-secondary btn-sm">Edit</a>
                                        <a href="delete_student.php?id=<?php echo $row['id']; ?>"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('Are you sure you want to delete this student?');">Delete</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <h3>No students found</h3>
                <p>
                    <?php if (!empty($search)): ?>
                        No students match your search criteria. Try a different search term.
                    <?php else: ?>
                        Get started by adding your first student.
                    <?php endif; ?>
                </p>
                <br>
                <a href="add_student.php" class="btn btn-primary">+ Add First Student</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$stmt->close();
require_once 'footer.php';
?>