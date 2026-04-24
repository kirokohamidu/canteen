<?php
session_start();


if ($_SESSION['alogin'] != '') {
    $_SESSION['alogin'] = '';
}

error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'includes/db.php';
include 'includes/auth.php';

$error = '';
$conn = new mysqli($host, $user, $pass, $db);


if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare and execute query with MySQLi
    $sql = "SELECT * FROM users WHERE email=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if ($email === $row['email']) {
            $_SESSION['alogin'] = $row['email'];
            $_SESSION['name'] = $row['name'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['id'] = $row['id'];
            echo "<script type='text/javascript'> document.location = 'dashboard.php'; </script>";
        } else {
            echo "<script>alert('Invalid Details');</script>";
        }
    } else {
        echo "User not found";
    }
    // Close the statement
    $stmt->close();
}

$dsn->close();


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

                <input type="submit" name='submit' value="Login">
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