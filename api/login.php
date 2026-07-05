<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

if (isLoggedIn()) {
    redirect(isAdmin() ? '/perpus-mini/api/admin/dashboard.php' : '/perpus-mini/api/dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $user = getUserByEmail($email);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nama'] = $user['nama'];
        $_SESSION['role'] = $user['role'];
        redirect($user['role'] === 'admin' ? '/perpus-mini/api/admin/dashboard.php' : '/perpus-mini/api/dashboard.php');
    } else {
        $error = 'Email atau kata sandi salah.';
    }
}

$pageTitle = 'Masuk - Perpustakaan Mini';
require __DIR__ . '/../includes/header.php';
?>

<div class="max-w-sm mx-auto bg-white rounded-lg shadow p-6 mt-8">
    <h1 class="text-xl font-bold mb-4 text-center">Masuk</h1>

    <?php if ($error): ?>
        <div class="mb-3 px-3 py-2 bg-red-100 text-red-700 rounded text-sm"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" class="space-y-3">
        <div>
            <label class="text-sm font-medium">Email</label>
            <input type="email" name="email" required class="w-full border rounded px-3 py-2 mt-1" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>
        <div>
            <label class="text-sm font-medium">Kata Sandi</label>
            <input type="password" name="password" required class="w-full border rounded px-3 py-2 mt-1">
        </div>
        <button type="submit" class="w-full bg-indigo-700 text-white py-2 rounded font-medium hover:bg-indigo-800">Masuk</button>
    </form>

    <p class="text-sm text-center mt-4 text-gray-600">
        Belum punya akun? <a href="/perpus-mini/api/register.php" class="text-indigo-700 font-medium hover:underline">Daftar di sini</a>
    </p>
    <p class="text-xs text-center mt-2 text-gray-400">Admin: admin@perpus.com / password: admin123</p>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>