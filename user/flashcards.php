<?php
// user/flashcards.php
require_once '../config/database.php';
require_once '../includes/auth_check.php';
require_once '../includes/functions.php';

$user_id = $_SESSION['user_id'];

// Get words due today
$stmt = $pdo->prepare("SELECT * FROM vocabulary WHERE user_id = ? AND DATE(next_review_date) <= CURDATE() AND mastered = 0 ORDER BY RAND() LIMIT 1");
$stmt->execute([$user_id]);
$current_word = $stmt->fetch();

include '../includes/header.php';
?>

<div class="d-flex">
    <?php include '../includes/sidebar.php'; ?>
    
    <div id="content" class="w-100">
        <nav class="top-navbar d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Flashcards</h4>
            <i class="bi bi-moon theme-toggle" id="theme-toggle"></i>
        </nav>
        
        <div class="container-fluid d-flex justify-content-center align-items-center" style="min-height: 70vh;">
            <?php if ($current_word): ?>
                <div class="glass-card text-center" style="width: 100%; max-width: 600px;">
                    <div class="flashcard" id="flashcard" onclick="flipCard()">
                        <div class="flashcard-inner">
                            <h2 id="word-text"><?= htmlspecialchars($current_word['word']) ?></h2>
                            <?php if ($current_word['pronunciation']): ?>
                            <div class="flashcard-pronunciation text-muted">[ <?= htmlspecialchars($current_word['pronunciation']) ?> ]</div>
                            <?php endif; ?>
                            
                            <div class="flashcard-meaning text-primary fw-bold mt-4" style="font-size: 1.5rem;">
                                <?= nl2br(htmlspecialchars($current_word['meaning'])) ?>
                            </div>
                            
                            <?php if ($current_word['example']): ?>
                            <div class="flashcard-example text-muted mt-3 fst-italic">
                                "<?= nl2br(htmlspecialchars($current_word['example'])) ?>"
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div id="action-buttons" class="mt-4" style="display: none;">
                        <h6 class="text-muted mb-3">How was it?</h6>
                        <div class="d-flex justify-content-center gap-2">
                            <form method="POST" action="process_review.php">
                                <input type="hidden" name="vocab_id" value="<?= $current_word['id'] ?>">
                                <input type="hidden" name="current_level" value="<?= $current_word['review_level'] ?>">
                                <button type="submit" name="difficulty" value="again" class="btn btn-danger">Again <br><small>1 Day</small></button>
                                <button type="submit" name="difficulty" value="hard" class="btn btn-warning">Hard <br><small>Decrease</small></button>
                                <button type="submit" name="difficulty" value="medium" class="btn btn-info text-white">Good <br><small>Keep</small></button>
                                <button type="submit" name="difficulty" value="easy" class="btn btn-success">Easy <br><small>Increase</small></button>
                            </form>
                        </div>
                    </div>
                    
                    <div class="mt-3 text-muted">
                        <small>Click the card to reveal the answer.</small>
                    </div>
                </div>
            <?php else: ?>
                <div class="glass-card text-center p-5">
                    <h1 class="text-success mb-3"><i class="bi bi-emoji-smile"></i></h1>
                    <h3 class="fw-bold">All caught up!</h3>
                    <p class="text-muted">You have no more words to review today.</p>
                    <a href="dashboard.php" class="btn btn-primary mt-3">Back to Dashboard</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function flipCard() {
    document.querySelector('.flashcard-meaning').style.display = 'block';
    if(document.querySelector('.flashcard-example')) {
        document.querySelector('.flashcard-example').style.display = 'block';
    }
    if(document.querySelector('.flashcard-pronunciation')) {
        document.querySelector('.flashcard-pronunciation').style.display = 'block';
    }
    document.getElementById('action-buttons').style.display = 'block';
}
</script>

<?php include '../includes/footer.php'; ?>
