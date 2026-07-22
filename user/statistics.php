<?php
// user/statistics.php
require_once '../config/database.php';
require_once '../includes/auth_check.php';

$user_id = $_SESSION['user_id'];

// Get difficult words (highest forgot count)
$stmt = $pdo->prepare("SELECT word, forgot_count FROM vocabulary WHERE user_id = ? AND forgot_count > 0 ORDER BY forgot_count DESC LIMIT 10");
$stmt->execute([$user_id]);
$difficult_words = $stmt->fetchAll();
$diff_labels = json_encode(array_column($difficult_words, 'word'));
$diff_data = json_encode(array_column($difficult_words, 'forgot_count'));

// Mastered vs Learning
$stmt = $pdo->prepare("SELECT mastered, COUNT(*) as count FROM vocabulary WHERE user_id = ? GROUP BY mastered");
$stmt->execute([$user_id]);
$mastered_stats = $stmt->fetchAll();
$mastered_count = 0;
$learning_count = 0;
foreach($mastered_stats as $stat) {
    if ($stat['mastered'] == 1) $mastered_count = $stat['count'];
    else $learning_count = $stat['count'];
}

include '../includes/header.php';
?>

<div class="d-flex">
    <?php include '../includes/sidebar.php'; ?>
    
    <div id="content" class="w-100">
        <nav class="top-navbar">
            <h4 class="mb-0">Learning Statistics</h4>
            <i class="bi bi-moon theme-toggle" id="theme-toggle"></i>
        </nav>
        
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="glass-card text-center h-100">
                    <h5>Most Forgotten Words</h5>
                    <?php if (count($difficult_words) > 0): ?>
                        <canvas id="forgotChart"></canvas>
                    <?php else: ?>
                        <p class="text-muted mt-5">No forgotten words yet. Great job!</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="col-md-6 mb-4">
                <div class="glass-card text-center h-100 d-flex flex-column align-items-center">
                    <h5>Progress Overview</h5>
                    <div style="max-width: 300px; width: 100%;">
                        <canvas id="progressChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Colors
    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    const textColor = isDark ? '#e0e0e0' : '#333';
    
    <?php if (count($difficult_words) > 0): ?>
    const ctx1 = document.getElementById('forgotChart').getContext('2d');
    new Chart(ctx1, {
        type: 'bar',
        data: {
            labels: <?= $diff_labels ?>,
            datasets: [{
                label: 'Times Forgotten',
                data: <?= $diff_data ?>,
                backgroundColor: 'rgba(244, 67, 54, 0.6)',
                borderColor: 'rgba(244, 67, 54, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { color: textColor }
                },
                x: {
                    ticks: { color: textColor }
                }
            },
            plugins: {
                legend: { labels: { color: textColor } }
            }
        }
    });
    <?php endif; ?>
    
    const ctx2 = document.getElementById('progressChart').getContext('2d');
    new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: ['Mastered', 'Learning'],
            datasets: [{
                data: [<?= $mastered_count ?>, <?= $learning_count ?>],
                backgroundColor: [
                    'rgba(76, 175, 80, 0.8)',
                    'rgba(106, 27, 154, 0.8)'
                ],
                borderWidth: 0
            }]
        },
        options: {
            plugins: {
                legend: { labels: { color: textColor } }
            }
        }
    });
});
</script>
