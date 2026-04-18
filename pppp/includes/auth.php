<?php
session_start();
require_once __DIR__ . '/../db.php';

function redirect($path) {
    header('Location: ' . $path);
    exit;
}

function isLoggedIn() {
    return isset($_SESSION['user']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        redirect('login.php');
    }
}

function currentUser() {
    return $_SESSION['user'] ?? null;
}

function getUserByEmail($email) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    return $stmt->fetch();
}

function loginUser($email, $password) {
    $user = getUserByEmail($email);
    if ($user && $password === $user['password']) {
        unset($user['password']);
        $_SESSION['user'] = $user;
        return true;
    }
    return false;
}

function logoutUser() {
    session_unset();
    session_destroy();
}

function flash($key, $message = null) {
    if ($message === null) {
        if (isset($_SESSION['flash'][$key])) {
            $msg = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
            return $msg;
        }
        return null;
    }
    $_SESSION['flash'][$key] = $message;
}

