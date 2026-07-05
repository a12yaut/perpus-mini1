<?php
// reset_password.php
require_once 'config/database.php';
require_once 'includes/functions.php';

$email = 'admin@perpus.com';
$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);

$pdo = getDb();
$stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
$stmt->execute([$hash, $email]);

echo "✅ Password untuk $email sudah direset menjadi: $password<br>";
echo "<a href='/perpus-mini/api/login.php'>Login sekarang</a>";
?>