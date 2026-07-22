<?php
// includes/sidebar.php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<nav id="sidebar">
    <div class="sidebar-header text-center mb-4">
        <h4 class="text-primary fw-bold">FlashCard AI</h4>
    </div>
    
    <ul class="list-unstyled components">
        <li>
            <a href="dashboard.php" class="nav-link <?= $currentPage == 'dashboard.php' ? 'active' : '' ?>">
                <i class="bi bi-house-door"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="vocabulary.php" class="nav-link <?= $currentPage == 'vocabulary.php' ? 'active' : '' ?>">
                <i class="bi bi-journal-text"></i> Vocabulary
            </a>
        </li>
        <li>
            <a href="add_word.php" class="nav-link <?= $currentPage == 'add_word.php' ? 'active' : '' ?>">
                <i class="bi bi-plus-circle"></i> Add Word
            </a>
        </li>
        <li>
            <a href="flashcards.php" class="nav-link <?= $currentPage == 'flashcards.php' ? 'active' : '' ?>">
                <i class="bi bi-card-heading"></i> Flashcards
                <?php
                // Count due today (simplified query, would normally use $pdo)
                ?>
                <span class="badge bg-danger rounded-pill float-end due-badge" style="display:none;"></span>
            </a>
        </li>
        <li>
            <a href="quiz.php" class="nav-link <?= $currentPage == 'quiz.php' ? 'active' : '' ?>">
                <i class="bi bi-controller"></i> Quiz
            </a>
        </li>
        <li>
            <a href="statistics.php" class="nav-link <?= $currentPage == 'statistics.php' ? 'active' : '' ?>">
                <i class="bi bi-bar-chart"></i> Statistics
            </a>
        </li>
        <?php if ($_SESSION['role'] === 'admin'): ?>
        <li>
            <hr class="dropdown-divider">
        </li>
        <li>
            <a href="../admin/dashboard.php" class="nav-link">
                <i class="bi bi-shield-lock"></i> Admin Panel
            </a>
        </li>
        <?php endif; ?>
    </ul>
</nav>
