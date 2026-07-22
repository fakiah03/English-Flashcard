<?php
// auth/forgot_password.php
session_start();
require_once '../config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    
    if (empty($email)) {
        $error = "Please enter your email address.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user) {
            // Simulated Email Functionality
            // In a real scenario, generate a token, save to DB, and send an email with the link.
            // Here, we just pretend it was sent successfully.
            $success = "A password reset link has been sent to your email (simulated).";
        } else {
            $error = "No account found with that email address.";
        }
    }
}
include '../includes/header.php';
?>
<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="glass-card animate-fade-in" style="width: 100%; max-width: 400px;">
        <div class="text-center mb-4">
            <h2 class="text-primary fw-bold">Reset Password</h2>
            <p>Enter your email to receive a reset link</p>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="mb-4">
                <label for="email" class="form-label">Email address</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            
            <button type="submit" class="btn btn-primary w-100 mb-3">Send Reset Link</button>
            <div class="text-center">
                Remember your password? <a href="login.php" class="text-primary text-decoration-none fw-bold">Login</a>
            </div>
        </form>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
