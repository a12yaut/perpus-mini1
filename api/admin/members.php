<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus'])) {
    $id = (int)$_POST['hapus'];
    if (deleteMember($id)) {
        $_SESSION['flash'] = ['tipe' => 'sukses', 'pesan' => 'Anggota berhasil dihapus.'];
    } else {
        $_SESSION['flash'] = ['tipe' => 'error', 'pesan' => 'Gagal menghapus anggota.'];
    }
    redirect('/perpus-mini/api/admin/members.php');
}

$members = getAllMembers();

$pageTitle = 'Kelola Anggota - Admin';
require __DIR__ . '/../../includes/header.php';
?>

<h1 class="text-2xl font-bold mb-6">Kelola Anggota</h1>

<div class="flex gap-3 mb-8 text-sm flex-wrap">
    <a href="/perpus-mini/api/admin/dashboard.php" class="px-4 py-2 rounded bg-white shadow hover:bg-gray-50">Statistik</a>
    <a href="/perpus-mini/api/admin/books.php" class="px-4 py-2 rounded bg-white shadow hover:bg-gray-50">Kelola Buku</a>
    <a href="/perpus-mini/api/admin/members.php" class="px-4 py-2 rounded bg-indigo-700 text-white">Kelola Anggota</a>
    <a href="/perpus-mini/api/admin/loans.php" class="px-4 py-2 rounded bg-white shadow hover:bg-gray-50">Kelola Peminjaman</a>
</div>

<div class="bg-white rounded-lg shadow overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-100 text-left">
            <tr>
                <th class="p-3">Nama</th>
                <th class="p-3">Email</th>
                <th class="p-3">Bergabung</th>
                <th class="p-3">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            <?php foreach ($members as $m): ?>
                <tr>
                    <td class="p-3"><?= htmlspecialchars($m['nama']) ?></td>
                    <td class="p-3"><?= htmlspecialchars($m['email']) ?></td>
                    <td class="p-3"><?= formatTanggal($m['created_at']) ?></td>
                    <td class="p-3">
                        <form method="POST" onsubmit="return confirm('Hapus anggota ini? Semua peminjamannya juga akan dihapus.')">
                            <button type="submit" name="hapus" value="<?= $m['id'] ?>" class="text-red-500 hover:underline">Hapus</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>