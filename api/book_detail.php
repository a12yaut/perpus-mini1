<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

$id = (int)($_GET['id'] ?? 0);
$buku = getBookById($id);

if (!$buku) {
    $_SESSION['flash'] = ['tipe' => 'error', 'pesan' => 'Buku tidak ditemukan.'];
    redirect('/perpus-mini/api/katalog.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_cart'])) {
    if (!isLoggedIn()) {
        redirect('/perpus-mini/api/login.php');
    }
    $hasil = addToCart($buku['id']);
    $_SESSION['flash'] = ['tipe' => $hasil['sukses'] ? 'sukses' : 'error', 'pesan' => $hasil['pesan']];
    redirect('/perpus-mini/api/book_detail.php?id=' . $buku['id']);
}

$pageTitle = htmlspecialchars($buku['judul']) . ' - Perpustakaan Mini';
require __DIR__ . '/../includes/header.php';

$diCart = isset($_SESSION['cart']) && in_array($buku['id'], $_SESSION['cart']);
?>

<a href="/perpus-mini/api/katalog.php" class="text-indigo-700 text-sm hover:underline">&larr; Kembali ke katalog</a>

<div class="bg-white rounded-lg shadow p-6 mt-4 flex flex-col md:flex-row gap-6">
    <?php if ($buku['cover'] && file_exists(__DIR__ . '/../uploads/' . $buku['cover'])): ?>
        <img src="/perpus-mini/uploads/<?= $buku['cover'] ?>" 
             alt="<?= htmlspecialchars($buku['judul']) ?>"
             class="w-full md:w-48 aspect-[3/4] object-cover rounded flex-shrink-0">
    <?php else: ?>
        <div class="w-full md:w-48 aspect-[3/4] bg-indigo-100 text-indigo-700 flex items-center justify-center rounded font-bold text-4xl flex-shrink-0">
            <?= getInisialBuku($buku['judul']) ?>
        </div>
    <?php endif; ?>
    <div class="flex-1">
        <h1 class="text-2xl font-bold"><?= htmlspecialchars($buku['judul']) ?></h1>
        <p class="text-gray-600 mb-1">oleh <?= htmlspecialchars($buku['penulis']) ?></p>
        <div class="flex gap-2 text-xs mb-3">
            <span class="bg-indigo-100 text-indigo-700 px-2 py-1 rounded"><?= htmlspecialchars($buku['kategori']) ?></span>
            <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded"><?= htmlspecialchars($buku['tahun_terbit']) ?></span>
        </div>
        <p class="text-gray-700 mb-4"><?= nl2br(htmlspecialchars($buku['sinopsis'])) ?></p>
        <p class="mb-4 text-sm <?= $buku['stok'] > 0 ? 'text-green-600' : 'text-red-500' ?>">
            <?= $buku['stok'] > 0 ? 'Stok tersedia: ' . $buku['stok'] : 'Stok sedang habis' ?>
        </p>

        <?php if (!isLoggedIn()): ?>
            <a href="/perpus-mini/api/login.php" class="inline-block bg-indigo-700 text-white px-5 py-2 rounded hover:bg-indigo-800">Masuk untuk Meminjam</a>
        <?php elseif (isAdmin()): ?>
            <p class="text-sm text-gray-400 italic">Akun admin tidak dapat meminjam buku.</p>
        <?php elseif ($buku['stok'] <= 0): ?>
            <button disabled class="bg-gray-300 text-gray-500 px-5 py-2 rounded cursor-not-allowed">Stok Habis</button>
        <?php elseif ($diCart): ?>
            <button disabled class="bg-gray-300 text-gray-500 px-5 py-2 rounded cursor-not-allowed">Sudah di Daftar Pinjam</button>
        <?php else: ?>
            <form method="POST">
                <button type="submit" name="tambah_cart" class="bg-amber-400 text-indigo-900 font-medium px-5 py-2 rounded hover:bg-amber-300">
                    + Tambah ke Daftar Pinjam
                </button>
            </form>
        <?php endif; ?>
   