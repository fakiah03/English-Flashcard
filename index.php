<?php
// index.php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: user/dashboard.php");
} else {
    header("Location: auth/login.php");
}
exit();
?>
