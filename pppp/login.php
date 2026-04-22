<?php
require_once 'includes/auth.php';
if (isLoggedIn()) {
    redirect('dashboard.php');
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($email === '' || $password === '') {
        $error = 'Please enter your email and password.';
    } elseif (loginUser($email, $password)) {
        redirect('dashboard.php');
    } else {
        $error = 'Invalid email or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - canteen</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body style="background: linear-gradient(rgba(15, 23, 42, 0.35), rgba(15, 23, 42, 0.35)), url('https://elearning.umu.ac.ug/pluginfile.php?file=%2F1%2Ftheme_academi%2Fslide1image%2F1776174832%2FUganda%20Martyrs%20University%20%20%281%29.webp');">
<div class="login-main-container">
    <div class="login-container container">
        <header>
            <h1>Login</h1>
            <nav><a href="index.php">Home</a></nav>
        </header>

        <?php if ($error): ?>
            <div class="alert"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="post" action="login.php">
            <label>Email</label>
            <input type="email" name="email" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <input type="submit" value="Login">
        </form>
    </div>

    <div class="info-container container">
        <footer class="login-footer">
            <p>Welcome to the Uganda Martyrs University ecanteen platform. We are delighted to have you join our vibrant online community of welfare. As a faith-based institution founded on the legacy, we are committed to providing a holistic and enriching canteen experience. We wish you every success in your academic journey.</p>
            <p><a href="#">Read More »</a></p>
            
            <div class="quick-links">
                <h4>Quick Links</h4>
                <a href="#">Apply Now</a>
                <a href="#">E-canteen</a>
                <a href="#">HelpDesk</a>
            </div>
            
            <div class="contact">
                <h4>The Uganda Martyrs University ecanteen</h4>
                <p>Contact Us</p>
                <p>P.O. Box 5498, Kampala, Uganda</p>
                <p>Phone: +256-742-635-511</p>
                <p>Email: ecanteen@umu.ac.ug</p>
            </div>
        </footer>
    </div>
</div>
</body>
</html>
