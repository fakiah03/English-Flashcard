<?php
// user/process_review.php
require_once '../config/database.php';
require_once '../includes/auth_check.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['vocab_id'])) {
    $vocab_id = $_POST['vocab_id'];
    $current_level = (int)$_POST['current_level'];
    $difficulty = $_POST['difficulty'];
    $user_id = $_SESSION['user_id'];
    
    // Verify ownership
    $stmt = $pdo->prepare("SELECT id FROM vocabulary WHERE id = ? AND user_id = ?");
    $stmt->execute([$vocab_id, $user_id]);
    if ($stmt->fetch()) {
        $review_result = calculate_next_review($current_level, $difficulty);
        
        $update = $pdo->prepare("UPDATE vocabulary SET review_level = ?, next_review_date = ?, mastered = ?, difficulty = ? WHERE id = ?");
        $update->execute([
            $review_result['level'],
            $review_result['next_date'],
            $review_result['mastered'],
            $difficulty,
            $vocab_id
        ]);
        
        // Update Streak
        update_streak($pdo, $user_id);
    }
    
    // Redirect to next flashcard
    header("Location: flashcards.php");
    exit();
}
header("Location: dashboard.php");
exit();
?>
