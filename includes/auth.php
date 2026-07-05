<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/functions.php';

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['flash'] = ['tipe' => 'error', 'pesan' => 'Silakan masuk terlebih dahulu.'];
        redirect('/perpus-mini/api/login.php');
    }
}

function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        $_SESSION['flash'] = ['tipe' => 'error', 'pesan' => 'Akses ditolak: hanya untuk admin.'];
        redirect('/perpus-mini/api/dashboard.php');
    }
}

function getUserById($id) {
    $pdo = getDb();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function getUserByEmail($email) {
    $pdo = getDb();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    return $stmt->fetch();
}

function createUser($nama, $email, $password) {
    $pdo = getDb();
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (nama, email, password) VALUES (?, ?, ?)");
    return $stmt->execute([$nama, $email, $hash]);
}
?>