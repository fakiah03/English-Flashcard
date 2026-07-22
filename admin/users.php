<?php
// admin/users.php
require_once '../config/database.php';
require_once '../includes/auth_check.php';

// Verify admin
if ($_SESSION['role'] !== 'admin') {
    die("Unauthorized access. Admin only.");
}

$stmt = $pdo->query("
    SELECT u.id, u.username, u.email, u.role, u.created_at, 
           COUNT(v.id) as vocab_count
    FROM users u
    LEFT JOIN vocabulary v ON u.id = v.user_id
    GROUP BY u.id
    ORDER BY u.id DESC
");
$users = $stmt->fetchAll();

include '../includes/header.php';
?>

<div class="d-flex">
    <?php include '../includes/sidebar.php'; ?>
    
    <div id="content" class="w-100">
        <nav class="top-navbar">
            <h4 class="mb-0">Manage Users</h4>
            <i class="bi bi-moon theme-toggle" id="theme-toggle"></i>
        </nav>
        
        <div class="container-fluid">
            <div class="glass-card">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Vocabulary Count</th>
                                <th>Joined Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($users as $u): ?>
                            <tr>
                                <td><?= $u['id'] ?></td>
                                <td><?= htmlspecialchars($u['username']) ?></td>
                                <td><?= htmlspecialchars($u['email']) ?></td>
                                <td>
                                    <?php if ($u['role'] == 'admin'): ?>
                                        <span class="badge bg-danger">Admin</span>
                                    <?php else: ?>
                                        <span class="badge bg-primary">User</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $u['vocab_count'] ?></td>
                                <td><?= date('M d, Y', strtotime($u['created_at'])) ?></td>
                                <td>
                                    <button class="btn btn-sm btn-info text-white" <?= $u['id'] == $_SESSION['user_id'] ? 'disabled' : '' ?>><i class="bi bi-pencil"></i></button>
                                    <button class="btn btn-sm btn-danger" <?= $u['id'] == $_SESSION['user_id'] ? 'disabled' : '' ?>><i class="bi bi-trash"></i></button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
    </div>
</div>

<?php include '../includes/footer.php'; ?>
