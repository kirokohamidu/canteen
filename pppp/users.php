<?php
require_once 'includes/auth.php';
requireLogin();
$user = currentUser();
if ($user['role'] !== 'system_admin') {
    redirect('dashboard.php');
}

$search = trim($_GET['search'] ?? '');
$params = [];
$query = 'SELECT * FROM users';
if ($search !== '') {
    $query .= ' WHERE name LIKE ? OR email LIKE ?';
    $params = ["%$search%", "%$search%"];    
}
$query .= ' ORDER BY id DESC';
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$users = $stmt->fetchAll();

$message = '';
$editUser = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = $_POST['role'] ?? 'student';
    $password = $_POST['password'] ?? '';

    if ($action === 'create') {
        if ($name && $email && $password) {
            $stmt = $pdo->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)');
            $stmt->execute([$name, $email, $password, $role]);
            $message = 'User created successfully.';
        } else {
            $message = 'Please fill in all user fields.';
        }
    }

    if ($action === 'update') {
        $id = intval($_POST['id'] ?? 0);
        if ($id && $name && $email) {
            if ($password !== '') {
                $stmt = $pdo->prepare('UPDATE users SET name = ?, email = ?, password = ?, role = ? WHERE id = ?');
                $stmt->execute([$name, $email, $password, $role, $id]);
            } else {
                $stmt = $pdo->prepare('UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?');
                $stmt->execute([$name, $email, $role, $id]);
            }
            $message = 'User updated successfully.';
        }
    }

    if ($action === 'delete') {
        $id = intval($_POST['id'] ?? 0);
        if ($id) {
            $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
            $stmt->execute([$id]);
            $message = 'User deleted successfully.';
        }
    }
    redirect('users.php');
}

if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$id]);
    $editUser = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users - canteen</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="users-page">
<div class="container">
    <header>
        <div><h1>Manage Users</h1></div>
        <?php include 'includes/navigation.php'; ?>
    </header>

    <form method="get" action="users.php">
        <input type="text" name="search" placeholder="Search users by name or email" value="<?php echo htmlspecialchars($search); ?>">
        <input type="submit" value="Search">
    </form>

    <h2><?php echo $editUser ? 'Edit User' : 'Add User'; ?></h2>
    <form method="post" action="users.php">
        <input type="hidden" name="action" value="<?php echo $editUser ? 'update' : 'create'; ?>">
        <?php if ($editUser): ?>
            <input type="hidden" name="id" value="<?php echo $editUser['id']; ?>">
        <?php endif; ?>
        <label>Name</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($editUser['name'] ?? ''); ?>" required>

        <label>Email</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($editUser['email'] ?? ''); ?>" required>

        <label>Password <?php echo $editUser ? '(leave blank to keep current)' : ''; ?></label>
        <input type="password" name="password" <?php echo $editUser ? '' : 'required'; ?> >

        <label>Role</label>
        <select name="role">
            <option value="student" <?php echo (isset($editUser['role']) && $editUser['role'] === 'student') ? 'selected' : ''; ?>>Student</option>
            <option value="canteen_manager" <?php echo (isset($editUser['role']) && $editUser['role'] === 'canteen_manager') ? 'selected' : ''; ?>>Canteen Manager</option>
            <option value="system_admin" <?php echo (isset($editUser['role']) && $editUser['role'] === 'system_admin') ? 'selected' : ''; ?>>System Administrator</option>
        </select>

        <input type="submit" value="<?php echo $editUser ? 'Update User' : 'Create User'; ?>">
    </form>

    <h2>User Records</h2>
    <table>
        <thead>
            <tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Actions</th></tr>
        </thead>
        <tbody>
        <?php foreach ($users as $userRow): ?>
            <tr>
                <td><?php echo $userRow['id']; ?></td>
                <td><?php echo htmlspecialchars($userRow['name']); ?></td>
                <td><?php echo htmlspecialchars($userRow['email']); ?></td>
                <td><?php echo htmlspecialchars($userRow['role']); ?></td>
                <td class="actions">
                    <a href="users.php?edit=<?php echo $userRow['id']; ?>">Edit</a>
                    <form method="post" action="users.php" onsubmit="return confirm('Delete this user?');">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?php echo $userRow['id']; ?>">
                        <button type="submit">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
