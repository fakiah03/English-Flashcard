<?php
// user/dashboard.php
require_once '../config/database.php';
require_once '../includes/auth_check.php';
require_once '../includes/functions.php';

$user_id = $_SESSION['user_id'];

// Fetch stats
$stats = [
    'total' => 0,
    'today' => 0,
    'mastered' => 0,
    'forgotten_today' => 0,
    'due_today' => 0,
    'streak' => 0
];

$stmt = $pdo->prepare("SELECT COUNT(*) FROM vocabulary WHERE user_id = ?");
$stmt->execute([$user_id]);
$stats['total'] = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM vocabulary WHERE user_id = ? AND DATE(created_at) = CURDATE()");
$stmt->execute([$user_id]);
$stats['today'] = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM vocabulary WHERE user_id = ? AND mastered = 1");
$stmt->execute([$user_id]);
$stats['mastered'] = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM vocabulary WHERE user_id = ? AND DATE(last_forgotten) = CURDATE()");
$stmt->execute([$user_id]);
$stats['forgotten_today'] = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM vocabulary WHERE user_id = ? AND DATE(next_review_date) <= CURDATE() AND mastered = 0");
$stmt->execute([$user_id]);
$stats['due_today'] = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT current_streak FROM learning_streak WHERE user_id = ?");
$stmt->execute([$user_id]);
$streak = $stmt->fetchColumn();
$stats['streak'] = $streak ? $streak : 0;

// Fetch Recent Words
$stmt = $pdo->prepare("SELECT word, meaning, created_at FROM vocabulary WHERE user_id = ? ORDER BY id DESC LIMIT 5");
$stmt->execute([$user_id]);
$recent_words = $stmt->fetchAll();

include '../includes/header.php';
?>

<div class="d-flex">
    <?php include '../includes/sidebar.php'; ?>
    
    <div id="content" class="w-100">
        <nav class="top-navbar">
            <div>
                <h4 class="mb-0">Welcome back, <?= htmlspecialchars($_SESSION['username']) ?>!</h4>
            </div>
            <div>
                <i class="bi bi-moon theme-toggle me-3" id="theme-toggle"></i>
                <a href="../auth/logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
            </div>
        </nav>
        
        <div class="row mb-4">
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="glass-card text-center h-100 border-start border-4 border-primary">
                    <h5 class="text-muted">Total Words</h5>
                    <h2 class="text-primary fw-bold"><?= $stats['total'] ?></h2>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="glass-card text-center h-100 border-start border-4 border-success">
                    <h5 class="text-muted">Due Today</h5>
                    <h2 class="text-success fw-bold"><?= $stats['due_today'] ?></h2>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="glass-card text-center h-100 border-start border-4 border-warning">
                    <h5 class="text-muted">Learning Streak</h5>
                    <h2 class="text-warning fw-bold"><i class="bi bi-fire"></i> <?= $stats['streak'] ?> Days</h2>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="glass-card text-center h-100 border-start border-4 border-info">
                    <h5 class="text-muted">Mastered</h5>
                    <h2 class="text-info fw-bold"><?= $stats['mastered'] ?></h2>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-8">
                <div class="glass-card">
                    <h5 class="mb-4">Recently Added Words</h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Word</th>
                                    <th>Meaning</th>
                                    <th>Added</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(count($recent_words) > 0): ?>
                                    <?php foreach($recent_words as $w): ?>
                                    <tr>
                                        <td class="fw-bold"><?= htmlspecialchars($w['word']) ?></td>
                                        <td><?= htmlspecialchars($w['meaning']) ?></td>
                                        <td><?= date('M d, Y', strtotime($w['created_at'])) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="3" class="text-center">No words added yet.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="glass-card">
                    <h5 class="mb-4">Quick Actions</h5>
                    <a href="add_word.php" class="btn btn-primary w-100 mb-3"><i class="bi bi-plus-lg"></i> Add New Word</a>
                    <a href="flashcards.php" class="btn btn-success w-100 mb-3"><i class="bi bi-play-circle"></i> Review Flashcards</a>
                    <a href="quiz.php" class="btn btn-warning w-100"><i class="bi bi-controller"></i> Take a Quiz</a>
                </div>
                
                <div class="glass-card mt-3 border-danger border border-2">
                    <h6 class="text-danger fw-bold"><i class="bi bi-exclamation-triangle"></i> Forgotten Today</h6>
                    <p class="mb-0 fs-3 text-center text-danger fw-bold"><?= $stats['forgotten_today'] ?> words</p>
                </div>
            </div>
        </div>
        
    </div>
</div>

<?php include '../includes/footer.php'; ?>
