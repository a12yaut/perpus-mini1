<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['kembalikan'])) {
    $loanId = (int)$_POST['kembalikan'];
    if (returnLoan($loanId)) {
        $_SESSION['flash'] = ['tipe' => 'sukses', 'pesan' => 'Pengembalian berhasil diproses.'];
    } else {
        $_SESSION['flash'] = ['tipe' => 'error', 'pesan' => 'Gagal memproses pengembalian.'];
    }
    redirect('/perpus-mini/api/admin/loans.php');
}

$loans = getAllLoans();

$pageTitle = 'Kelola Peminjaman - Admin';
require __DIR__ . '/../../includes/header.php';
?>

<h1 class="text-2xl font-bold mb-6">Kelola Peminjaman</h1>

<div class="flex gap-3 mb-8 text-sm flex-wrap">
    <a href="/perpus-mini/api/admin/dashboard.php" class="px-4 py-2 rounded bg-white shadow hover:bg-gray-50">Statistik</a>
    <a href="/perpus-mini/api/admin/books.php" class="px-4 py-2 rounded bg-white shadow hover:bg-gray-50">Kelola Buku</a>
    <a href="/perpus-mini/api/admin/members.php" class="px-4 py-2 rounded bg-white shadow hover:bg-gray-50">Kelola Anggota</a>
    <a href="/perpus-mini/api/admin/loans.php" class="px-4 py-2 rounded bg-indigo-700 text-white">Kelola Peminjaman</a>
</div>

<div class="bg-white rounded-lg shadow overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-100 text-left">
            <tr>
                <th class="p-3">Anggota</th>
                <th class="p-3">Buku</th>
                <th class="p-3">Tanggal Pinjam</th>
                <th class="p-3">Jatuh Tempo</th>
                <th class="p-3">Status</th>
                <th class="p-3">Denda</th>
                <th class="p-3">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            <?php foreach ($loans as $l): 
                $telat = $l['status'] === 'dipinjam' && strtotime($l['tanggal_jatuh_tempo']) < strtotime(date('Y-m-d'));
            ?>
                <tr>
                    <td class="p-3"><?= htmlspecialchars($l['user_nama']) ?></td>
                    <td class="p-3"><?= htmlspecialchars($l['judul']) ?></td>
                    <td class="p-3"><?= formatTanggal($l['tanggal_pinjam']) ?></td>
                    <td class="p-3 <?= $telat ? 'text-red-500 font-medium' : '' ?>">
                        <?= formatTanggal($l['tanggal_jatuh_tempo']) ?>
                        <?= $telat ? '⚠️' : '' ?>
                    </td>
                    <td class="p-3">
                        <span class="px-2 py-1 rounded text-xs <?= $l['status'] === 'dipinjam' ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700' ?>">
                            <?= $l['status'] === 'dipinjam' ? 'Dipinjam' : 'Dikembalikan' ?>
                        </span>
                    </td>
                    <td class="p-3 <?= $l['denda'] > 0 ? 'text-red-500' : '' ?>">
                        <?= $l['denda'] > 0 ? formatRupiah($l['denda']) : '-' ?>
                    </td>
                    <td class="p-3">
                        <?php if ($l['status'] === 'dipinjam'): ?>
                            <form method="POST">
                                <button type="submit" name="kembalikan" value="<?= $l['id'] ?>" 
                                    class="bg-indigo-700 text-white text-xs px-3 py-1 rounded hover:bg-indigo-800">
                                    Kembalikan
                                </button>
                            </form>
                        <?php else: ?>
                            <span class="text-gray-400 text-xs">Selesai</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>