<?php
// includes/functions.php

/**
 * Sanitize user input to prevent XSS.
 */
function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

/**
 * Calculate the next review date based on difficulty selection.
 * Spaced Repetition Logic: 1, 3, 7, 14, 30, 60 days
 */
function calculate_next_review($current_level, $difficulty) {
    // difficulty: 'easy', 'medium', 'hard', 'again'
    
    if ($difficulty === 'again') {
        $new_level = 0; // Reset to beginning
    } elseif ($difficulty === 'hard') {
        $new_level = max(0, $current_level - 1);
    } elseif ($difficulty === 'medium') {
        $new_level = $current_level;
    } else {
        // easy
        $new_level = $current_level + 1;
    }
    
    // Max level 5 (which corresponds to 60 days)
    $new_level = min(5, $new_level);
    
    $days_to_add = 0;
    switch ($new_level) {
        case 0: $days_to_add = 1; break;
        case 1: $days_to_add = 3; break;
        case 2: $days_to_add = 7; break;
        case 3: $days_to_add = 14; break;
        case 4: $days_to_add = 30; break;
        case 5: $days_to_add = 60; break;
    }
    
    $next_date = date('Y-m-d H:i:s', strtotime("+$days_to_add days"));
    
    return [
        'level' => $new_level,
        'next_date' => $next_date,
        'mastered' => ($new_level == 5) ? 1 : 0
    ];
}

/**
 * Update learning streak for the user.
 */
function update_streak($pdo, $user_id) {
    $today = date('Y-m-d');
    $stmt = $pdo->prepare("SELECT * FROM learning_streak WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $streak = $stmt->fetch();
    
    if ($streak) {
        if ($streak['last_studied_date'] == $today) {
            // Already studied today, do nothing
            return;
        }
        
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        if ($streak['last_studied_date'] == $yesterday) {
            // Studied yesterday, increment streak
            $new_streak = $streak['current_streak'] + 1;
            $max_streak = max($streak['max_streak'], $new_streak);
            
            $update = $pdo->prepare("UPDATE learning_streak SET current_streak = ?, max_streak = ?, last_studied_date = ? WHERE user_id = ?");
            $update->execute([$new_streak, $max_streak, $today, $user_id]);
        } else {
            // Missed a day, reset streak
            $update = $pdo->prepare("UPDATE learning_streak SET current_streak = 1, last_studied_date = ? WHERE user_id = ?");
            $update->execute([$today, $user_id]);
        }
    } else {
        // First time studying
        $insert = $pdo->prepare("INSERT INTO learning_streak (user_id, current_streak, max_streak, last_studied_date) VALUES (?, 1, 1, ?)");
        $insert->execute([$user_id, $today]);
    }
}
?>
