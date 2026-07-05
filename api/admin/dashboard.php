<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

requireAdmin();

$stat = getStatistik();
$maxBulan = max(array_column($stat['pinjam_per_bulan'], 'total') ?: [1]);

$pageTitle = 'Dashboard Admin - Perpustakaan Mini';
require __DIR__ . '/../../includes/header.php';
?>

<h1 class="text-2xl font-bold mb-6">Dashboard Admin</h1>

<div class="flex gap-3 mb-8 text-sm flex-wrap">
    <a href="/perpus-mini/api/admin/dashboard.php" class="px-4 py-2 rounded bg-indigo-700 text-white">Statistik</a>
    <a href="/perpus-mini/api/admin/books.php" class="px-4 py-2 rounded bg-white shadow hover:bg-gray-50">Kelola Buku</a>
    <a href="/perpus-mini/api/admin/members.php" class="px-4 py-2 rounded bg-white shadow hover:bg-gray-50">Kelola Anggota</a>
    <a href="/perpus-mini/api/admin/loans.php" class="px-4 py-2 rounded bg-white shadow hover:bg-gray-50">Kelola Peminjaman</a>
</div>

<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-lg shadow p-4 text-center">
        <p class="text-2xl font-bold text-indigo-700"><?= $stat['total_buku'] ?></p>
        <p class="text-xs text-gray-500">Total Buku</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4 text-center">
        <p class="text-2xl font-bold text-indigo-700"><?= $stat['total_anggota'] ?></p>
        <p class="text-xs text-gray-500">Total Anggota</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4 text-center">
        <p class="text-2xl font-bold text-indigo-700"><?= $stat['peminjaman_aktif'] ?></p>
        <p class="text-xs text-gray-500">Peminjaman Aktif</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4 text-center">
        <p class="text-2xl font-bold text-red-500"><?= formatRupiah($stat['total_denda']) ?></p>
        <p class="text-xs text-gray-500">Total Denda Terkumpul</p>
    </div>
</div>

<div class="grid md:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg shadow p-5">
        <h2 class="font-semibold mb-4">Buku Terpopuler</h2>
        <?php if (empty($stat['buku_terpopuler'])): ?>
            <p class="text-sm text-gray-500">Belum ada data peminjaman.</p>
        <?php else: ?>
            <ol class="space-y-2 text-sm">
                <?php foreach ($stat['buku_terpopuler'] as $i => $b): ?>
                    <li class="flex justify-between border-b pb-2">
                        <span><?= $i + 1 ?>. <?= htmlspecialchars($b['judul']) ?></span>
                        <span class="text-gray-500"><?= $b['total_pinjam'] ?>x dipinjam</span>
                    </li>
                <?php endforeach; ?>
            </ol>
        <?php endif; ?>
    </div>

    <div class="bg-white rounded-lg shadow p-5">
        <h2 class="font-semibold mb-4">Peminjaman per Bulan</h2>
        <?php if (empty($stat['pinjam_per_bulan'])): ?>
            <p class="text-sm text-gray-500">Belum ada data peminjaman.</p>
        <?php else: ?>
            <div class="space-y-2">
                <?php foreach (array_reverse($stat['pinjam_per_bulan']) as $p): ?>
                    <div class="flex items-center gap-2 text-sm">
                        <span class="w-16 text-gray-500"><?= htmlspecialchars($p['bulan']) ?></span>
                        <div class="flex-1 bg-gray-100 rounded h-4">
                            <div class="bg-indigo-600 h-4 rounded" style="width: <?= max(($p['total'] / $maxBulan) * 100, 5) ?>%"></div>
                        </div>
                        <span class="w-6 text-right"><?= $p['total'] ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>