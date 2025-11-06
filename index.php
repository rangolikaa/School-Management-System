<?php
/**
 * Dashboard Page
 * Main dashboard with statistics and overview
 */

$pageTitle = 'Dashboard';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/session_handler.php';
require_once __DIR__ . '/includes/header.php';

// Require login
requireLogin();

// Get current session ID
$currentSessionId = getCurrentSessionId();

// Display messages
if (isset($_SESSION['success_message'])) {
    echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success_message']) . '</div>';
    unset($_SESSION['success_message']);
}

if (isset($_SESSION['error_message'])) {
    echo '<div class="alert alert-error">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
    unset($_SESSION['error_message']);
}

if (isset($_GET['error'])) {
    if ($_GET['error'] === 'access_denied') {
        echo '<div class="alert alert-error">Access denied. Admin privileges required.</div>';
    }
}

// Get statistics for current session
$conn = getDBConnection();

// Count students in current session
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM students WHERE session_id = ?");
$stmt->bind_param("i", $currentSessionId);
$stmt->execute();
$result = $stmt->get_result();
$studentCount = $result->fetch_assoc()['count'];
$stmt->close();

// Count classes in current session
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM classes WHERE session_id = ?");
$stmt->bind_param("i", $currentSessionId);
$stmt->execute();
$result = $stmt->get_result();
$classCount = $result->fetch_assoc()['count'];
$stmt->close();

// Count teachers
$result = $conn->query("SELECT COUNT(*) as count FROM teachers");
$teacherCount = $result->fetch_assoc()['count'];

// Get current session info
$currentSession = getCurrentSession();
closeDBConnection($conn);
?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Dashboard Overview</h2>
    </div>
    
    <?php if ($currentSession): ?>
        <div style="margin-bottom: 1.5rem; padding: 1rem; background-color: var(--bg-color); border-radius: 0.375rem;">
            <p><strong>Current Session:</strong> <?php echo htmlspecialchars($currentSession['session_start'] . '-' . $currentSession['session_end']); ?></p>
        </div>
    <?php endif; ?>
    
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value"><?php echo $studentCount; ?></div>
            <div class="stat-label">Total Students</div>
            <a href="students/index.php" class="btn btn-primary btn-small" style="margin-top: 0.5rem; display: inline-block;">View All</a>
        </div>
        
        <div class="stat-card">
            <div class="stat-value"><?php echo $classCount; ?></div>
            <div class="stat-label">Classes</div>
            <a href="classes/index.php" class="btn btn-primary btn-small" style="margin-top: 0.5rem; display: inline-block;">View All</a>
        </div>
        
        <div class="stat-card">
            <div class="stat-value"><?php echo $teacherCount; ?></div>
            <div class="stat-label">Teachers</div>
            <a href="teachers/index.php" class="btn btn-primary btn-small" style="margin-top: 0.5rem; display: inline-block;">View All</a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Quick Actions</h2>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
        <a href="students/add.php" class="btn btn-success">Add Student</a>
        <a href="classes/add.php" class="btn btn-success">Add Class</a>
        <a href="sections/add.php" class="btn btn-success">Add Section</a>
        <a href="teachers/add.php" class="btn btn-success">Add Teacher</a>
        <?php if (isAdmin()): ?>
            <a href="sessions/add.php" class="btn btn-success">Add Session</a>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
