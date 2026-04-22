<?php
require_once __DIR__ . '/auth.php';
$user = currentUser();

$navItems = [
    ['href' => 'dashboard.php', 'label' => 'Dashboard'],
    ['href' => 'menu.php', 'label' => 'Menu'],
    ['href' => 'orders.php', 'label' => 'Orders'],
];

if ($user && $user['role'] === 'system_admin') {
    $navItems[] = ['href' => 'users.php', 'label' => 'Manage Users'];
}
?>
<nav class="main-nav">
    <div class="nav-center">
        <form class="search-form" action="menu.php" method="get">
            <input type="text" name="search" placeholder="Search menu items" />
            <button type="submit">Search</button>
        </form>
    </div>
    <div class="nav-menu">
        <?php foreach ($navItems as $item): ?>
            <a href="<?php echo htmlspecialchars($item['href']); ?>"><?php echo htmlspecialchars($item['label']); ?></a>
        <?php endforeach; ?>
    </div>
    <div class="nav-right">
        <a href="logout.php">Logout</a>
    </div>
</nav>
