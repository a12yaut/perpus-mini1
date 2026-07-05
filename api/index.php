<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Beranda - Perpustakaan Mini';
$bukuTerbaru = getAllBooks();
$bukuTerbaru = array_slice($bukuTerbaru, 0, 4);

require __DIR__ . '/../includes/header.php';
?>

<div class="bg-indigo-700 text-white rounded-xl p-8 mb-8">
    <h1 class="text-3xl font-bold mb-2">Selamat Datang di Perpustakaan Mini</h1>
    <p class="text-indigo-100 mb-4">Pinjam buku favoritmu dengan mudah. Maksimal <?= MAKS_PINJAM ?> buku per peminjaman.</p>
    <a href="/perpus-mini/api/katalog.php" class="inline-block bg-amber-400 text-indigo-900 font-semibold px-5 py-2 rounded hover:bg-amber-300">Lihat Katalog</a>
</div>

<h2 class="text-xl font-semibold mb-4">Buku Terbaru</h2>
<div class="grid grid-cols-2 md:grid-cols-4 gap-4">
    <?php foreach ($bukuTerbaru as $buku): ?>
        <a href="/perpus-mini/api/book_detail.php?id=<?= $buku['id'] ?>" class="bg-white rounded-lg shadow p-4 hover:shadow-md transition">
            <?php if ($buku['cover'] && file_exists(__DIR__ . '/../uploads/' . $buku['cover'])): ?>
                <img src="/perpus-mini/uploads/<?= $buku['cover'] ?>" 
                     alt="<?= htmlspecialchars($buku['judul']) ?>"
                     class="w-full aspect-[3/4] object-cover rounded mb-3">
            <?php else: ?>
                <div class="w-full aspect-[3/4] bg-indigo-100 text-indigo-700 flex items-center justify-center rounded font-bold text-2xl mb-3">
                    <?= getInisialBuku($buku['judul']) ?>
                </div>
            <?php endif; ?>
            <p class="font-medium text-sm line-clamp-2"><?= htmlspecialchars($buku['judul']) ?></p>
            <p class="text-xs text-gray-500"><?= htmlspecialchars($buku['penulis']) ?></p>
        </a>
    <?php endforeach; ?>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>