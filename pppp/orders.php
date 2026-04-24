<?php
require_once 'includes/auth.php';
// requireLogin();
// $user = currentUser();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $orderId = intval($_POST['order_id'] ?? 0);
    $status = $_POST['status'] ?? '';
    if (($_SESSION['role'] === 'canteen_manager' || $_SESSION['role'] === 'system_admin') && $orderId && in_array($status, ['pending', 'completed', 'cancelled'])) {
        $stmt = $dsn->prepare('UPDATE orders SET status = ? WHERE id = ?');
        $updated = $stmt->execute([$status, $orderId]);
        if ($updated) {
            flash('success', "Order status updated to '" . ucfirst($status) . "'.");
        } else {
            flash('error', 'Could not update order.');
        }
        redirect('orders.php');
    }

    if ($_SESSION['role'] === 'student') {
        $menuItemId = intval($_POST['menu_item_id'] ?? 0);
        $quantity = max(1, intval($_POST['quantity'] ?? 1));
        if ($menuItemId) {
            $stmt = $dsn->prepare('SELECT * FROM menu_items WHERE id = ?');
            $stmt->execute([$menuItemId]);
            $item = mysqli_fetch_all($stmt->get_result(), MYSQLI_ASSOC)[0] ?? null;

            if ($item) {
                $total = $item['price'] * $quantity;
                $stmt = $dsn->prepare('INSERT INTO orders (user_id, menu_item_id, quantity, total_price, status) VALUES (?, ?, ?, ?, "pending")');
                $stmt->execute([$_SESSION['id'], $menuItemId, $quantity, $total]);
                flash('success', 'Order placed successfully.');
                redirect('orders.php');
            }
        }
    }
}

$success = flash('success');
if ($_SESSION['role'] === 'student') {
    $stmt = $dsn->prepare('SELECT o.*, COALESCE(o.status, "pending") AS status, m.name AS item_name FROM orders o JOIN menu_items m ON o.menu_item_id = m.id WHERE o.user_id = ? ORDER BY o.id DESC');
    $stmt->execute([$_SESSION['id']]);
    $orders = mysqli_fetch_all($stmt->get_result(), MYSQLI_ASSOC);
} else {
    $stmt = $dsn->prepare('
    SELECT o.*, 
           COALESCE(o.status, "pending") AS status, 
           u.name AS student_name, 
           m.name AS item_name 
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    JOIN menu_items m ON o.menu_item_id = m.id 
    ORDER BY o.id DESC
');

    $stmt->execute();
    $orders = mysqli_fetch_all($stmt->get_result(), MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Orders - canteen</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script>
        function updateStatus(orderId, status) {
            if (confirm('Set order status to "' + status + '"?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                <input type="hidden" name="action" value="update_status">
                <input type="hidden" name="order_id" value="${orderId}">
                <input type="hidden" name="status" value="${status}">
            `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</head>

<body class="orders-page">
    <?php include 'includes/navigation.php'; ?>
    <div class="container">
        <header>
            <div>
                <h1>Orders</h1>
            </div>
        </header>

        <?php if ($success): ?>
            <div class="alert"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <?php if ($_SESSION['role'] === 'student'): ?>
            <p>When you place an order from the menu page, it will appear here.</p>
        <?php else: ?>
            <p>All student orders are shown below. Canteen managers and admins can review orders here.</p>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <?php if ($_SESSION['role'] !== 'student'): ?><th>Student</th><?php endif; ?>
                    <th>Menu Item</th>
                    <th>Quantity</th>
                    <th>Total Price</th>
                    <th>Status</th>
                    <?php if ($_SESSION['role'] === 'canteen_manager' || $_SESSION['role'] === 'system_admin'): ?>
                        <th>Actions</th>
                    <?php endif; ?>
                    <th>Placed At</th>
                </tr>
            </thead>
            <tbody>


                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?php echo $order['id']; ?></td>
                        <?php if ($_SESSION['role'] !== 'student'): ?><td><?php echo htmlspecialchars($order['student_name']); ?></td><?php endif; ?>
                        <td><?php echo htmlspecialchars($order['item_name']); ?></td>
                        <td><?php echo $order['quantity']; ?></td>
                        <td><?php echo number_format($order['total_price'], 2); ?></td>
                        <td><span class="status <?php echo strtolower($order['status']); ?>"><?php echo ucfirst($order['status']); ?></span></td>
                        <?php if ($_SESSION['role'] === 'canteen_manager' || $_SESSION['role'] === 'system_admin'): ?>
                            <td class="order-actions">
                                <select onchange="updateStatus(<?php echo $order['id']; ?>, this.value)">
                                    <option value="pending" <?php echo ($order['status'] === 'pending') ? 'selected' : ''; ?>>Pending</option>
                                    <option value="completed" <?php echo ($order['status'] === 'completed') ? 'selected' : ''; ?>>Completed</option>
                                    <option value="cancelled" <?php echo ($order['status'] === 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </td>
                        <?php endif; ?>
                        <td><?php echo $order['created_at']; ?></td>
                    </tr>
                <?php endforeach; ?>

            </tbody>
        </table>
    </div>
</body>

</html>