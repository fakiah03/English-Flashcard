<?php
// user/vocabulary.php
require_once '../config/database.php';
require_once '../includes/auth_check.php';

$user_id = $_SESSION['user_id'];

// Handle delete
if (isset($_GET['delete'])) {
    $del_id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM vocabulary WHERE id = ? AND user_id = ?");
    $stmt->execute([$del_id, $user_id]);
    header("Location: vocabulary.php");
    exit();
}

// Handle search
$search = $_GET['search'] ?? '';
if ($search) {
    $stmt = $pdo->prepare("SELECT * FROM vocabulary WHERE user_id = ? AND word LIKE ? ORDER BY word ASC");
    $stmt->execute([$user_id, "%$search%"]);
} else {
    $stmt = $pdo->prepare("SELECT * FROM vocabulary WHERE user_id = ? ORDER BY id DESC");
    $stmt->execute([$user_id]);
}
$words = $stmt->fetchAll();

include '../includes/header.php';
?>

<div class="d-flex">
    <?php include '../includes/sidebar.php'; ?>
    
    <div id="content" class="w-100">
        <nav class="top-navbar d-flex justify-content-between align-items-center">
            <h4 class="mb-0">My Vocabulary</h4>
            <div class="d-flex">
                <form class="d-flex me-3" method="GET">
                    <input class="form-control me-2" type="search" name="search" placeholder="Search word..." value="<?= htmlspecialchars($search) ?>">
                    <button class="btn btn-outline-primary" type="submit">Search</button>
                </form>
                <i class="bi bi-moon theme-toggle align-self-center me-3" id="theme-toggle"></i>
            </div>
        </nav>
        
        <div class="container-fluid">
            <div class="glass-card">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Word</th>
                                <th>Meaning</th>
                                <th>Category</th>
                                <th>Level</th>
                                <th>Forgot Count</th>
                                <th>Next Review</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($words) > 0): ?>
                                <?php foreach ($words as $w): ?>
                                <tr>
                                    <td class="fw-bold fs-5 text-primary">
                                        <?= htmlspecialchars($w['word']) ?>
                                        <?php if ($w['mastered']): ?>
                                            <i class="bi bi-patch-check-fill text-success" title="Mastered"></i>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($w['meaning']) ?></td>
                                    <td><span class="badge bg-secondary"><?= htmlspecialchars($w['category']) ?></span></td>
                                    <td>Lv. <?= $w['review_level'] ?></td>
                                    <td class="text-danger fw-bold"><?= $w['forgot_count'] > 0 ? $w['forgot_count'] : '-' ?></td>
                                    <td>
                                        <?php
                                            $next_date = strtotime($w['next_review_date']);
                                            if ($next_date <= time() && !$w['mastered']) {
                                                echo "<span class='text-danger fw-bold'>Due Now</span>";
                                            } else {
                                                echo date('M d, Y', $next_date);
                                            }
                                        ?>
                                    </td>
                                    <td>
                                        <!-- Edit and Delete actions -->
                                        <button class="btn btn-sm btn-info text-white"><i class="bi bi-pencil"></i></button>
                                        <a href="vocabulary.php?delete=<?= $w['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this word?')"><i class="bi bi-trash"></i></a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4">No vocabulary found. <a href="add_word.php">Add a new word</a></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
    </div>
</div>

<?php include '../includes/footer.php'; ?>
