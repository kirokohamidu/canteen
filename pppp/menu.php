<?php
require_once 'includes/auth.php';
requireLogin();
$user = currentUser();

$search = trim($_GET['search'] ?? '');
$query = 'SELECT * FROM menu_items';
$params = [];
if ($search !== '') {
    $query .= ' WHERE name LIKE ? OR description LIKE ?';
    $params = ["%$search%", "%$search%"];    
}
$query .= ' ORDER BY id DESC';
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$items = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user['role'] === 'canteen_manager') {
    $action = $_POST['action'] ?? '';
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = floatval($_POST['price'] ?? 0);

    if ($action === 'create' && $name && $price > 0) {
        $stmt = $pdo->prepare('INSERT INTO menu_items (name, description, price) VALUES (?, ?, ?)');
        $stmt->execute([$name, $description, $price]);
        redirect('menu.php');
    }
    if ($action === 'update') {
        $id = intval($_POST['id'] ?? 0);
        if ($id && $name && $price > 0) {
            $stmt = $pdo->prepare('UPDATE menu_items SET name = ?, description = ?, price = ? WHERE id = ?');
            $stmt->execute([$name, $description, $price, $id]);
            redirect('menu.php');
        }
    }
    if ($action === 'delete') {
        $id = intval($_POST['id'] ?? 0);
        if ($id) {
            $stmt = $pdo->prepare('DELETE FROM menu_items WHERE id = ?');
            $stmt->execute([$id]);
            redirect('menu.php');
        }
    }
}

$editItem = null;
if (isset($_GET['edit']) && $user['role'] === 'canteen_manager') {
    $id = intval($_GET['edit']);
    $stmt = $pdo->prepare('SELECT * FROM menu_items WHERE id = ?');
    $stmt->execute([$id]);
    $editItem = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Menu - canteen</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="menu-page" style="background: linear-gradient(rgba(15, 23, 42, 0.35), rgba(15, 23, 42, 0.35)), url('https://elearning.umu.ac.ug/pluginfile.php?file=%2F1%2Ftheme_academi%2Fslide2image%2F1776174832%2FUganda%20Martyrs%20University%20%281%29%281%29%20%281%29.webp');">
    <?php include 'includes/navigation.php'; ?>
    <div class="container">
        <header>
            <div><h1>Menu</h1></div>
        </header>

        <?php if ($user['role'] === 'canteen_manager'): ?>
            <h2><?php echo $editItem ? 'Edit Menu Item' : 'Add Menu Item'; ?></h2>
            <form method="post" action="menu.php">
                <input type="hidden" name="action" value="<?php echo $editItem ? 'update' : 'create'; ?>">
                <?php if ($editItem): ?>
                    <input type="hidden" name="id" value="<?php echo $editItem['id']; ?>">
                <?php endif; ?>
                <label>Name</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($editItem['name'] ?? ''); ?>" required>

                <label>Description</label>
                <textarea name="description"><?php echo htmlspecialchars($editItem['description'] ?? ''); ?></textarea>

                <label>Price</label>
                <input type="text" name="price" value="<?php echo htmlspecialchars($editItem['price'] ?? ''); ?>" required>

                <input type="submit" value="<?php echo $editItem ? 'Update Item' : 'Add Item'; ?>">
            </form>
        <?php endif; ?>

        <h2>Menu Items</h2>
        <div class="menu-grid">
<?php 
$imageMap = [
    'Chicken Roll' => 'https://www.allrecipes.com/thmb/koFD08H_utDaUsNc578_YZ2peFg=/0x512/filters:no_upscale():max_bytes(150000):strip_icc()/8635-southern-fried-chicken-ddmfs_4x3-90736ab31a7a4bb59eb04e2380ccebe7.jpg',
    'Chapati' => 'https://divdishes.com/wp-content/uploads/2024/07/Chapati-Recipe-Thumbnail-scaled.jpg',
    'Soft Drink' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQspqLKMNzNcJV93mQY2XDIsxhT3NB6YwRHsQ&s',
    'sweet' => 'https://sweetz-united.de/cdn/shop/articles/Sweetz_United_Blog_Beste_Sussigkeiten_1d0919cc-c96b-476a-bfa7-9ba92ab9c098.jpg?v=1759324676&width=1600',
    'Juice' => 'https://www.crazyvegankitchen.com/wp-content/uploads/2023/06/mango-juice-recipe.jpg',
    'Mandazi' => 'https://i.ytimg.com/vi/DGjk_1pfigM/maxresdefault.jpg',
    'default' => 'https://images.unsplash.com/photo-1567620905732-2d1ec7ab7445?w=500&fit=crop' // food placeholder
];
?>
        <?php foreach ($items as $item): 
            $imageUrl = $imageMap[$item['name']] ?? $imageMap['default'];
        ?>
            <div class="menu-item-card">
                <img src="<?php echo htmlspecialchars($imageUrl); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="menu-image">
                <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                <p class="description"><?php echo htmlspecialchars($item['description']); ?></p>
                <p class="price">UGX <?php echo number_format($item['price'], 2); ?></p>
                <div class="actions">
                    <?php if ($user['role'] === 'canteen_manager'): ?>
                        <a href="menu.php?edit=<?php echo $item['id']; ?>" class="button">Edit</a>
                        <form method="post" action="menu.php" onsubmit="return confirm('Delete this menu item?');" style="display:inline;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                            <button type="submit" class="button">Delete</button>
                        </form>
                    <?php elseif ($user['role'] === 'student'): ?>
                        <form method="post" action="orders.php" style="display:inline;">
                            <input type="hidden" name="menu_item_id" value="<?php echo $item['id']; ?>">
                            <input type="number" name="quantity" min="1" value="1" style="width: 60px; margin-right: 8px;">
                            <button type="submit" class="button">Order</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>

        </div>
    </div>
</body>
</html>
