<?php
// admin/dashboard.php
require_once '../config/database.php';
require_once '../includes/auth_check.php';

// Verify admin
if ($_SESSION['role'] !== 'admin') {
    die("Unauthorized access. Admin only.");
}

$stats = [];
$stats['users'] = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$stats['words'] = $pdo->query("SELECT COUNT(*) FROM vocabulary")->fetchColumn();
$stats['forgotten'] = $pdo->query("SELECT SUM(forgot_count) FROM vocabulary")->fetchColumn() ?: 0;
$stats['quizzes'] = $pdo->query("SELECT COUNT(*) FROM quiz_history")->fetchColumn();

// Recent Users
$recent_users = $pdo->query("SELECT username, email, created_at FROM users ORDER BY id DESC LIMIT 5")->fetchAll();

include '../includes/header.php';
?>

<div class="d-flex">
    <?php include '../includes/sidebar.php'; ?>
    
    <div id="content" class="w-100">
        <nav class="top-navbar">
            <h4 class="mb-0">Admin Dashboard</h4>
            <div class="d-flex">
                <i class="bi bi-moon theme-toggle me-3" id="theme-toggle"></i>
                <a href="../auth/logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
            </div>
        </nav>
        
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="glass-card text-center text-primary h-100 border-start border-4 border-primary">
                    <h6>Total Users</h6>
                    <h2><?= $stats['users'] ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="glass-card text-center text-success h-100 border-start border-4 border-success">
                    <h6>Total Vocabulary</h6>
                    <h2><?= $stats['words'] ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="glass-card text-center text-danger h-100 border-start border-4 border-danger">
                    <h6>Total Forgets</h6>
                    <h2><?= $stats['forgotten'] ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="glass-card text-center text-warning h-100 border-start border-4 border-warning">
                    <h6>Quizzes Taken</h6>
                    <h2><?= $stats['quizzes'] ?></h2>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-8">
                <div class="glass-card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="mb-0">Recently Registered Users</h5>
                        <a href="users.php" class="btn btn-sm btn-primary">View All</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Joined Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($recent_users as $u): ?>
                                <tr>
                                    <td><?= htmlspecialchars($u['username']) ?></td>
                                    <td><?= htmlspecialchars($u['email']) ?></td>
                                    <td><?= date('M d, Y', strtotime($u['created_at'])) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>

<?php include '../includes/footer.php'; ?>
