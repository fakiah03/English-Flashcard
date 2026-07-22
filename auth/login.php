<?php
// auth/login.php
session_start();
require_once '../config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            // Redirect based on role
            if ($user['role'] === 'admin') {
                header("Location: ../admin/dashboard.php");
            } else {
                header("Location: ../user/dashboard.php");
            }
            exit();
        } else {
            $error = "Invalid email or password.";
        }
    }
}
include '../includes/header.php';
?>
<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="glass-card animate-fade-in" style="width: 100%; max-width: 400px;">
        <div class="text-center mb-4">
            <h2 class="text-primary fw-bold">FlashCard AI</h2>
            <p>Login to continue</p>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="d-flex justify-content-between mb-3">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="remember">
                    <label class="form-check-label" for="remember">Remember me</label>
                </div>
                <a href="forgot_password.php" class="text-primary text-decoration-none">Forgot password?</a>
            </div>
            <button type="submit" class="btn btn-primary w-100 mb-3">Login</button>
            <div class="text-center">
                Don't have an account? <a href="register.php" class="text-primary text-decoration-none fw-bold">Register</a>
            </div>
        </form>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
