<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

include 'includes/auth.php';
// include '/includes/db.php';
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

<body style="background: linear-gradient(rgba(15, 23, 42, 0.35), rgba(15, 23, 42, 0.35)), url('https://media.licdn.com/dms/image/v2/C4E1BAQGjEPqbhhDBfg/company-background_10000/company-background_10000/0/1646121135777/uganda_martyrs_university_umu_cover?e=2147483647&v=beta&t=0l2MTcSi5xmhQjj1Pm5IPhsgEMZ_0c9vX29yvmLA-Eg">
    <div class="container">
        <header>
            <div>
                <h1>UMU Online Canteen </h1>
                <p>Login to access the canteen system</p>
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