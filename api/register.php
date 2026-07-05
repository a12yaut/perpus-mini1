<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

if (isLoggedIn()) {
    redirect(isAdmin() ? '/perpus-mini/api/admin/dashboard.php' : '/perpus-mini/api/dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $konfirmasi = $_POST['konfirmasi'] ?? '';

    if ($nama === '' || $email === '' || $password === '') {
        $error = 'Semua kolom wajib diisi.';
    } elseif ($password !== $konfirmasi) {
        $error = 'Konfirmasi kata sandi tidak cocok.';
    } elseif (strlen($password) < 6) {
        $error = 'Kata sandi minimal 6 karakter.';
    } elseif (getUserByEmail($email)) {
        $error = 'Email sudah terdaftar.';
    } else {
        createUser($nama, $email, $password);
        $_SESSION['flash'] = ['tipe' => 'sukses', 'pesan' => 'Pendaftaran berhasil, silakan masuk.'];
        redirect('/perpus-mini/api/login.php');
    }
}

$pageTitle = 'Daftar - Perpustakaan Mini';
require __DIR__ . '/../includes/header.php';
?>

<div class="max-w-sm mx-auto bg-white rounded-lg shadow p-6 mt-8">
    <h1 class="text-xl font-bold mb-4 text-center">Daftar Akun</h1>

    <?php if ($error): ?>
        <div class="mb-3 px-3 py-2 bg-red-100 text-red-700 rounded text-sm"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" class="space-y-3">
        <div>
            <label class="text-sm font-medium">Nama Lengkap</label>
            <input type="text" name="nama" required class="w-full border rounded px-3 py-2 mt-1" value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>">
        </div>
        <div>
            <label class="text-sm font-medium">Email</label>
            <input type="email" name="email" required class="w-full border rounded px-3 py-2 mt-1" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>
        <div>
            <label class="text-sm font-medium">Kata Sandi</label>
            <input type="password" name="password" required class="w-full border rounded px-3 py-2 mt-1">
        </div>
        <div>
            <label class="text-sm font-medium">Konfirmasi Kata Sandi</label>
            <input type="password" name="konfirmasi" required class="w-full border rounded px-3 py-2 mt-1">
        </div>
        <button type="submit" class="w-full bg-indigo-700 text-white py-2 rounded font-medium hover:bg-indigo-800">Daftar</button>
    </form>

    <p class="text-sm text-center mt-4 text-gray-600">
        Sudah punya akun? <a href="/perpus-mini/api/login.php" class="text-indigo-700 font-medium hover:underline">Masuk di sini</a>
    </p>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>