<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

$keyword = trim($_GET['q'] ?? '');
$kategori = trim($_GET['kategori'] ?? '');

$buku = getAllBooks($keyword, $kategori);
$daftarKategori = getAllKategori();

$pageTitle = 'Katalog Buku - Perpustakaan Mini';
require __DIR__ . '/../includes/header.php';
?>

<h1 class="text-2xl font-bold mb-4">Katalog Buku</h1>

<form method="GET" class="flex flex-col md:flex-row gap-3 mb-6">
    <input type="text" name="q" placeholder="Cari judul atau penulis..." value="<?= htmlspecialchars($keyword) ?>"
        class="flex-1 border rounded px-3 py-2">
    <select name="kategori" class="border rounded px-3 py-2">
        <option value="">Semua Kategori</option>
        <?php foreach ($daftarKategori as $k): ?>
            <option value="<?= htmlspecialchars($k) ?>" <?= $kategori === $k ? 'selected' : '' ?>><?= htmlspecialchars($k) ?></option>
        <?php endforeach; ?>
    </select>
    <button type="submit" class="bg-indigo-700 text-white px-5 py-2 rounded hover:bg-indigo-800">Cari</button>
</form>

<?php if (empty($buku)): ?>
    <p class="text-gray-500">Tidak ada buku yang ditemukan.</p>
<?php else: ?>
<div class="grid grid-cols-2 md:grid-cols-4 gap-4">
    <?php foreach ($buku as $b): ?>
        <a href="/perpus-mini/api/book_detail.php?id=<?= $b['id'] ?>" class="bg-white rounded-lg shadow p-4 hover:shadow-md transition flex flex-col">
            <?php if ($b['cover'] && file_exists(__DIR__ . '/../uploads/' . $b['cover'])): ?>
                <img src="/perpus-mini/uploads/<?= $b['cover'] ?>" 
                     alt="<?= htmlspecialchars($b['judul']) ?>"
                     class="w-full aspect-[3/4] object-cover rounded mb-3">
            <?php else: ?>
                <div class="w-full aspect-[3/4] bg-indigo-100 text-indigo-700 flex items-center justify-center rounded font-bold text-2xl mb-3">
                    <?= getInisialBuku($b['judul']) ?>
                </div>
            <?php endif; ?>
            <p class="font-medium text-sm line-clamp-2"><?= htmlspecialchars($b['judul']) ?></p>
            <p class="text-xs text-gray-500 mb-1"><?= htmlspecialchars($b['penulis']) ?></p>
            <span class="text-xs mt-auto <?= $b['stok'] > 0 ? 'text-green-600' : 'text-red-500' ?>">
                <?= $b['stok'] > 0 ? 'Tersedia (' . $b['stok'] . ')' : 'Stok Habis' ?>
            </span>
        </a>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php require __DIR__ . '/../includes/footer.php'; ?>