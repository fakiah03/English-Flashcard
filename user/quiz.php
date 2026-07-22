<?php
// user/quiz.php
require_once '../config/database.php';
require_once '../includes/auth_check.php';
require_once '../includes/functions.php';

$user_id = $_SESSION['user_id'];
$message = '';
$messageType = '';

// Check if there are enough words
$stmt = $pdo->prepare("SELECT COUNT(*) FROM vocabulary WHERE user_id = ?");
$stmt->execute([$user_id]);
$word_count = $stmt->fetchColumn();

if ($word_count < 4) {
    $error_msg = "You need at least 4 words in your vocabulary to take a quiz. Please add more words.";
} else {
    // Generate Quiz
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['answer'])) {
        $correct_id = $_POST['correct_id'];
        $answer = $_POST['answer']; // The ID they chose
        
        if ($correct_id == $answer) {
            $messageType = 'success';
            $message = "Correct!";
            
            // Log score
            $pdo->prepare("INSERT INTO quiz_history (user_id, score, total, type) VALUES (?, 1, 1, 'multiple_choice')")->execute([$user_id]);
        } else {
            $messageType = 'error';
            $message = "Incorrect.";
            $pdo->prepare("INSERT INTO quiz_history (user_id, score, total, type) VALUES (?, 0, 1, 'multiple_choice')")->execute([$user_id]);
        }
        
        // Update Streak
        update_streak($pdo, $user_id);
    }
    
    // Pick a random target word
    $stmt = $pdo->prepare("SELECT id, word, meaning FROM vocabulary WHERE user_id = ? ORDER BY RAND() LIMIT 1");
    $stmt->execute([$user_id]);
    $question_word = $stmt->fetch();
    
    // Pick 3 random wrong meanings
    $stmt = $pdo->prepare("SELECT id, meaning FROM vocabulary WHERE user_id = ? AND id != ? ORDER BY RAND() LIMIT 3");
    $stmt->execute([$user_id, $question_word['id']]);
    $wrong_options = $stmt->fetchAll();
    
    $options = $wrong_options;
    $options[] = ['id' => $question_word['id'], 'meaning' => $question_word['meaning']];
    shuffle($options);
}

include '../includes/header.php';
?>

<div class="d-flex">
    <?php include '../includes/sidebar.php'; ?>
    
    <div id="content" class="w-100">
        <nav class="top-navbar d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Quiz Mode</h4>
            <i class="bi bi-moon theme-toggle" id="theme-toggle"></i>
        </nav>
        
        <div class="container-fluid d-flex justify-content-center align-items-center" style="min-height: 70vh;">
            <?php if (isset($error_msg)): ?>
                <div class="glass-card text-center p-5">
                    <h3 class="text-warning"><i class="bi bi-exclamation-triangle"></i> Oops!</h3>
                    <p><?= htmlspecialchars($error_msg) ?></p>
                    <a href="add_word.php" class="btn btn-primary mt-3">Add Words</a>
                </div>
            <?php else: ?>
                <div class="glass-card" style="width: 100%; max-width: 600px;">
                    
                    <?php if ($messageType == 'success'): ?>
                        <div class="alert alert-success text-center fw-bold"><i class="bi bi-check-circle"></i> <?= $message ?></div>
                    <?php elseif ($messageType == 'error'): ?>
                        <div class="alert alert-danger text-center fw-bold"><i class="bi bi-x-circle"></i> <?= $message ?></div>
                    <?php endif; ?>
                    
                    <div class="text-center mb-4">
                        <p class="text-muted mb-1">What is the meaning of:</p>
                        <h2 class="text-primary fw-bold" style="font-size: 3rem;"><?= htmlspecialchars($question_word['word']) ?></h2>
                    </div>
                    
                    <form method="POST" action="">
                        <input type="hidden" name="correct_id" value="<?= $question_word['id'] ?>">
                        <div class="d-grid gap-3">
                            <?php foreach($options as $index => $opt): ?>
                                <button type="submit" name="answer" value="<?= $opt['id'] ?>" class="btn btn-outline-primary text-start p-3 fs-5">
                                    <span class="fw-bold me-2"><?= chr(65 + $index) ?>.</span> <?= htmlspecialchars($opt['meaning']) ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </form>
                    
                    <div class="text-center mt-4">
                        <a href="quiz.php" class="btn btn-secondary btn-sm">Skip Question</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
