<?php
// user/add_word.php
require_once '../config/database.php';
require_once '../includes/auth_check.php';

$user_id = $_SESSION['user_id'];
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $word = trim($_POST['word']);
    $meaning = trim($_POST['meaning']);
    $example = trim($_POST['example']);
    $pronunciation = trim($_POST['pronunciation']);
    $category = trim($_POST['category']);
    
    // DUPLICATE DETECTION LOGIC
    $stmt = $pdo->prepare("SELECT id, forgot_count FROM vocabulary WHERE user_id = ? AND LOWER(word) = LOWER(?)");
    $stmt->execute([$user_id, $word]);
    $existing = $stmt->fetch();
    
    if ($existing) {
        // Word exists! Do not insert. Update forgot_count instead.
        $new_forgot_count = $existing['forgot_count'] + 1;
        
        $update = $pdo->prepare("UPDATE vocabulary SET forgot_count = ?, last_forgotten = CURRENT_TIMESTAMP WHERE id = ?");
        $update->execute([$new_forgot_count, $existing['id']]);
        
        // Log to history
        $log = $pdo->prepare("INSERT INTO vocabulary_history (vocab_id, user_id, event_type) VALUES (?, ?, 'forgotten')");
        $log->execute([$existing['id'], $user_id]);
        
        // Prepare notification for SweetAlert2
        $messageType = 'duplicate';
        $message = "You have forgotten this word $new_forgot_count times. Last forgotten: today.";
    } else {
        // Insert new word
        $insert = $pdo->prepare("INSERT INTO vocabulary (user_id, word, meaning, example, pronunciation, category) VALUES (?, ?, ?, ?, ?, ?)");
        if ($insert->execute([$user_id, $word, $meaning, $example, $pronunciation, $category])) {
            $messageType = 'success';
            $message = "Vocabulary added successfully!";
        } else {
            $messageType = 'error';
            $message = "Failed to add vocabulary.";
        }
    }
}

include '../includes/header.php';
?>

<div class="d-flex">
    <?php include '../includes/sidebar.php'; ?>
    
    <div id="content" class="w-100">
        <nav class="top-navbar">
            <h4 class="mb-0">Add New Word</h4>
            <i class="bi bi-moon theme-toggle" id="theme-toggle"></i>
        </nav>
        
        <div class="container-fluid">
            <div class="glass-card mx-auto" style="max-width: 700px;">
                
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="word" class="form-label fw-bold">English Word</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="word" name="word" required>
                            <button class="btn btn-info text-white" type="button" id="btn-generate-ai"><i class="bi bi-magic"></i> Auto Generate details (AI)</button>
                        </div>
                        <small class="text-muted">Enter a word and click Auto Generate to fetch meaning & pronunciation from a free Dictionary API.</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="meaning" class="form-label fw-bold">Meaning</label>
                        <textarea class="form-control" id="meaning" name="meaning" rows="2" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="example" class="form-label fw-bold">Example Sentence</label>
                        <textarea class="form-control" id="example" name="example" rows="2"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="pronunciation" class="form-label fw-bold">Pronunciation (IPA)</label>
                            <input type="text" class="form-control" id="pronunciation" name="pronunciation">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="category" class="form-label fw-bold">Category</label>
                            <input type="text" class="form-control" id="category" name="category" value="General">
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 mt-3"><i class="bi bi-save"></i> Save Vocabulary</button>
                </form>
            </div>
        </div>
        
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<?php if ($messageType == 'duplicate'): ?>
<script>
    Swal.fire({
        title: 'Word Already Exists!',
        html: `You already added this word before.<br><br><b>Word:</b> <?= htmlspecialchars($word) ?><br><br><?= $message ?>`,
        icon: 'warning',
        confirmButtonText: 'I will remember it'
    });
</script>
<?php elseif ($messageType == 'success'): ?>
<script>
    Swal.fire('Success!', '<?= $message ?>', 'success');
</script>
<?php elseif ($messageType == 'error'): ?>
<script>
    Swal.fire('Error!', '<?= $message ?>', 'error');
</script>
<?php endif; ?>
