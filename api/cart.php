<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();
if (isAdmin()) redirect('/perpus-mini/api/admin/dashboard.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['hapus'])) {
        removeFromCart((int)$_POST['hapus']);
        redirect('/perpus-mini/api/cart.php');
    }
    if (isset($_POST['konfirmasi_pinjam'])) {
        $cart = getCart();
        if (empty($cart)) {
            $_SESSION['flash'] = ['tipe' => 'error', 'pesan' => 'Daftar pinjam masih kosong.'];
            redirect('/perpus-mini/api/cart.php');
        }
        $sisaKuota = MAKS_PINJAM - getActiveLoanCount($_SESSION['user_id']);
        if (count($cart) > $sisaKuota) {
            $_SESSION['flash'] = ['tipe' => 'error', 'pesan' => 'Kuota peminjaman aktifmu tidak cukup (maks ' . MAKS_PINJAM . ' buku sedang dipinjam).'];
            redirect('/perpus-mini/api/cart.php');
        }

        $gagal = [];
        foreach ($cart as $bookId) {
            $buku = getBookById($bookId);
            if (!$buku || $buku['stok'] <= 0 || !createLoan($_SESSION['user_id'], $bookId)) {
                $gagal[] = $buku['judul'] ?? "ID $bookId";
                continue;
            }
        }
        clearCart();

        if (empty($gagal)) {
            $_SESSION['flash'] = ['tipe' => 'sukses', 'pesan' => 'Peminjaman berhasil! Cek dashboard untuk detail tanggal kembali.'];
        } else {
            $_SESSION['flash'] = ['tipe' => 'error', 'pesan' => 'Sebagian buku gagal dipinjam (stok habis): ' . implode(', ', $gagal)];
        }
        redirect('/perpus-mini/api/dashboard.php');
    }
}

$cartIds = getCart();
$bukuCart = array_map('getBookById', $cartIds);
$sisaKuota = MAKS_PINJAM - getActiveLoanCount($_SESSION['user_id']);

$pageTitle = 'Daftar Pinjam - Perpustakaan Mini';
require __DIR__ . '/../includes/header.php';
?>

<h1 class="text-2xl font-bold mb-1">Daftar Pinjam</h1>
<p class="text-sm text-gray-500 mb-6"><?= count($cartIds) ?>/<?= MAKS_PINJAM ?> buku dipilih &middot; sisa kuota aktifmu: <?= max($sisaKuota, 0) ?></p>

<?php if (empty($bukuCart)): ?>
    <div class="bg-white rounded-lg shadow p-8 text-center text-gray-500">
        Daftar pinjam masih kosong. <a href="/perpus-mini/api/katalog.php" class="text-indigo-700 hover:underline">Cari buku di katalog</a>.
    </div>
<?php else: ?>
    <div class="bg-white rounded-lg shadow divide-y">
        <?php foreach ($bukuCart as $b): if (!$b) continue; ?>
            <div class="p-4 flex items-center justify-between">
                <div>
                    <p class="font-medium"><?= htmlspecialchars($b['judul']) ?></p>
                    <p class="text-xs text-gray-500"><?= htmlspecialchars($b['penulis']) ?></p>
                </div>
                <form method="POST">
                    <button type="submit" name="hapus" value="<?= $b['id'] ?>" class="text-red-500 text-sm hover:underline">Hapus</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>

    <form method="POST" class="mt-4">
        <button type="submit" name="konfirmasi_pinjam"
            class="bg-indigo-700 text-white px-5 py-2 rounded hover:bg-indigo-800">
            Konfirmasi Pinjam (<?= LAMA_PINJAM_HARI ?> hari)
        </button>
    </form>
<?php endif; ?>

<?php require __DIR__ . '/../includes/footer.php'; ?>