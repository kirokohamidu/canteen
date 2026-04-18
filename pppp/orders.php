<?php
require_once 'includes/auth.php';
requireLogin();
$user = currentUser();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user['role'] === 'student') {
    $menuItemId = intval($_POST['menu_item_id'] ?? 0);
    $quantity = max(1, intval($_POST['quantity'] ?? 1));
    if ($menuItemId) {
        $stmt = $pdo->prepare('SELECT * FROM menu_items WHERE id = ?');
        $stmt->execute([$menuItemId]);
        $item = $stmt->fetch();
        if ($item) {
            $total = $item['price'] * $quantity;
            $stmt = $pdo->prepare('INSERT INTO orders (user_id, menu_item_id, quantity, total_price) VALUES (?, ?, ?, ?)');
            $stmt->execute([$user['id'], $menuItemId, $quantity, $total]);
            flash('success', 'Order placed successfully.');
            redirect('orders.php');
        }
    }
}

$success = flash('success');
if ($user['role'] === 'student') {
    $stmt = $pdo->prepare('SELECT o.*, m.name AS item_name FROM orders o JOIN menu_items m ON o.menu_item_id = m.id WHERE o.user_id = ? ORDER BY o.id DESC');
    $stmt->execute([$user['id']]);
    $orders = $stmt->fetchAll();
} else {
    $stmt = $pdo->query('SELECT o.*, u.name AS student_name, m.name AS item_name FROM orders o JOIN users u ON o.user_id = u.id JOIN menu_items m ON o.menu_item_id = m.id ORDER BY o.id DESC');
    $orders = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Orders - canteen</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="orders-page">
<div class="container">
    <header>
        <div><h1>Orders</h1></div>
        <?php include 'includes/navigation.php'; ?>
    </header>

    <?php if ($success): ?>
        <div class="alert"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <?php if ($user['role'] === 'student'): ?>
        <p>When you place an order from the menu page, it will appear here.</p>
    <?php else: ?>
        <p>All student orders are shown below. Canteen managers and admins can review orders here.</p>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <?php if ($user['role'] !== 'student'): ?><th>Student</th><?php endif; ?>
                <th>Menu Item</th>
                <th>Quantity</th>
                <th>Total Price</th>
                <th>Placed At</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($orders as $order): ?>
            <tr>
                <td><?php echo $order['id']; ?></td>
                <?php if ($user['role'] !== 'student'): ?><td><?php echo htmlspecialchars($order['student_name']); ?></td><?php endif; ?>
                <td><?php echo htmlspecialchars($order['item_name']); ?></td>
                <td><?php echo $order['quantity']; ?></td>
                <td><?php echo number_format($order['total_price'], 2); ?></td>
                <td><?php echo $order['created_at']; ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
