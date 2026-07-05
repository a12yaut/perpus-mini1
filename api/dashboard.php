<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();
if (isAdmin()) redirect('/perpus-mini/api/admin/dashboard.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['kembalikan'])) {
    $loanId = (int)$_POST['kembalikan'];
    if (returnLoan($loanId)) {
        $_SESSION['flash'] = ['tipe' => 'sukses', 'pesan' => 'Buku berhasil dikembalikan.'];
    } else {
        $_SESSION['flash'] = ['tipe' => 'error', 'pesan' => 'Gagal memproses pengembalian.'];
    }
    redirect('/perpus-mini/api/dashboard.php');
}

$riwayat = getLoansByUser($_SESSION['user_id']);
$aktif = array_filter($riwayat, function($l) {
    return $l['status'] === 'dipinjam';
});
$selesai = array_filter($riwayat, function($l) {
    return $l['status'] === 'dikembalikan';
});
$totalDenda = array_sum(array_column($riwayat, 'denda'));

$pageTitle = 'Dashboard Saya - Perpustakaan Mini';
require __DIR__ . '/../includes/header.php';
?>

<h1 class="text-2xl font-bold mb-6">Dashboard Saya</h1>

<div class="grid grid-cols-3 gap-4 mb-8">
    <div class="bg-white rounded-lg shadow p-4 text-center">
        <p class="text-2xl font-bold text-indigo-700"><?= count($aktif) ?></p>
        <p class="text-xs text-gray-500">Sedang Dipinjam</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4 text-center">
        <p class="text-2xl font-bold text-indigo-700"><?= count($selesai) ?></p>
        <p class="text-xs text-gray-500">Riwayat Selesai</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4 text-center">
        <p class="text-2xl font-bold text-red-500"><?= formatRupiah($totalDenda) ?></p>
        <p class="text-xs text-gray-500">Total Denda</p>
    </div>
</div>

<h2 class="text-lg font-semibold mb-3">Buku Sedang Dipinjam</h2>
<?php if (empty($aktif)): ?>
    <p class="text-gray-500 mb-8">Belum ada buku yang dipinjam. <a href="/perpus-mini/api/katalog.php" class="text-indigo-700 hover:underline">Cari buku</a>.</p>
<?php else: ?>
<div class="bg-white rounded-lg shadow divide-y mb-8">
    <?php foreach ($aktif as $l):
        $telat = strtotime($l['tanggal_jatuh_tempo']) < strtotime(date('Y-m-d'));
    ?>
        <div class="p-4 flex items-center justify-between flex-wrap gap-2">
            <div>
                <p class="font-medium"><?= htmlspecialchars($l['judul']) ?></p>
                <p class="text-xs text-gray-500">
                    Dipinjam: <?= formatTanggal($l['tanggal_pinjam']) ?> &middot;
                    Jatuh tempo: <span class="<?= $telat ? 'text-red-500 font-medium' : '' ?>"><?= formatTanggal($l['tanggal_jatuh_tempo']) ?></span>
                    <?= $telat ? ' (Terlambat)' : '' ?>
                </p>
            </div>
            <form method="POST">
                <button type="submit" name="kembalikan" value="<?= $l['id'] ?>" class="bg-indigo-700 text-white text-sm px-4 py-1.5 rounded hover:bg-indigo-800">
                    Kembalikan
                </button>
            </form>
        </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<h2 class="text-lg font-semibold mb-3">Riwayat Peminjaman</h2>
<?php if (empty($selesai)): ?>
    <p class="text-gray-500">Belum ada riwayat peminjaman selesai.</p>
<?php else: ?>
<div class="bg-white rounded-lg shadow overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-100 text-left">
            <tr>
                <th class="p-3">Judul</th>
                <th class="p-3">Dipinjam</th>
                <th class="p-3">Dikembalikan</th>
                <th class="p-3">Denda</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            <?php foreach ($selesai as $l): ?>
                <tr>
                    <td class="p-3"><?= htmlspecialchars($l['judul']) ?></td>
                    <td class="p-3"><?= formatTanggal($l['tanggal_pinjam']) ?></td>
                    <td class="p-3"><?= formatTanggal($l['tanggal_kembali']) ?></td>
                    <td class="p-3 <?= $l['denda'] > 0 ? 'text-red-500' : '' ?>"><?= formatRupiah($l['denda']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php require __DIR__ . '/../includes/footer.php'; ?>