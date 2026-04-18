<?php
require_once 'includes/auth.php';
if (isLoggedIn()) {
    redirect('dashboard.php');
}
$error = flash('error');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Canteen System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="container">
    <header>
        <div>
            <img src="https://umu.ac.ug/wp-content/uploads/2023/01/umu-logo.png" alt="Uganda Martyrs University Logo" class="logo">
            <h1>Canteen Online System</h1>
            <p>Login to access the canteen system for students, managers and administrators.</p>
        </div>
    </header>

    <?php if ($error): ?>
        <div class="alert"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="login-section">
        <a class="button" href="login.php">Login</a>
    </div>
</div>
</body>
</html>
