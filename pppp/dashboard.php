<?php

// error_reporting(E_ALL);
// ini_set('display_errors', 1);

require_once 'includes/auth.php';
// requireLogin();
$user = currentUser();

if (!isset($_SESSION['alogin']) || strlen($_SESSION['alogin']) == "") {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dashboard - canteen</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body class="dashboard-page">
    <?php include 'includes/navigation.php'; ?>
    <div class="container">
        <header>
            <div>
                <h1>Dashboard</h1>
                <p>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?></p>
                <small><?php echo htmlspecialchars($_SESSION['alogin']); ?></small>
                </h6>
            </div>
        </header>

        <div class="hero-slider">
            <div class="slide slide-1" aria-label="Fresh food spread"></div>
            <div class="slide slide-2" aria-label="Refreshing drink selection"></div>
            <div class="slide slide-3" aria-label="Tasty canteen meal"></div>
        </div>

        <div class="dashboard-actions">
            <?php if ($_SESSION['role'] === 'system_admin'): ?>
                <div class="action-group action-left">
                    <a class="button" href="users.php">Manage Users</a>
                </div>
                <div class="action-group action-center">
                    <a class="button" href="menu.php">View Menu</a>
                </div>
                <div class="action-group action-right">
                    <a class="button" href="orders.php">View Orders</a>
                </div>
            <?php elseif ($_SESSION['role'] === 'canteen_manager'): ?>
                <div class="action-group action-left">
                    <a class="button" href="menu.php">Manage Menu Items</a>
                </div>
                <div class="action-group action-center"></div>
                <div class="action-group action-right">
                    <a class="button" href="orders.php">View Orders</a>
                </div>
            <?php else: ?>
                <div class="action-group action-left">
                    <a class="button" href="menu.php">Browse Menu</a>
                </div>
                <div class="action-group action-center"></div>
                <div class="action-group action-right">
                    <a class="button" href="orders.php">Place Order</a>
                </div>
            <?php endif; ?>
        </div>

        <div class="footer">
            <p>Canteen Online System</p>
        </div>
    </div>
</body>

</html>