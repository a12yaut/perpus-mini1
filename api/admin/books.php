<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

requireAdmin();

$aksi = $_GET['aksi'] ?? 'daftar';
$id = (int)($_GET['id'] ?? 0);

// Simpan (tambah/edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan'])) {
    // Upload cover
    $coverFile = $_FILES['cover'] ?? null;
    $editId = (int)($_POST['id'] ?? 0);
    
    if ($editId > 0) {
        // Ambil cover lama
        $oldBook = getBookById($editId);
        $oldCover = $oldBook['cover'] ?? null;
        $coverName = uploadCover($coverFile, $oldCover);
    } else {
        $coverName = uploadCover($coverFile);
    }
    
    $data = [
        'judul' => trim($_POST['judul']),
        'penulis' => trim($_POST['penulis']),
        'kategori' => trim($_POST['kategori']),
        'tahun_terbit' => (int)$_POST['tahun_terbit'],
        'sinopsis' => trim($_POST['sinopsis']),
        'cover' => $coverName,
        'stok' => (int)$_POST['stok'],
    ];

    if ($editId > 0) {
        updateBook($editId, $data);
        $_SESSION['flash'] = ['tipe' => 'sukses', 'pesan' => 'Buku berhasil diperbarui.'];
    } else {
        createBook($data);
        $_SESSION['flash'] = ['tipe' => 'sukses', 'pesan' => 'Buku baru berhasil ditambahkan.'];
    }
    redirect('/perpus-mini/api/admin/books.php');
}

// Hapus
if ($aksi === 'hapus' && $id > 0) {
    deleteBook($id);
    $_SESSION['flash'] = ['tipe' => 'sukses', 'pesan' => 'Buku berhasil dihapus.'];
    redirect('/perpus-mini/api/admin/books.php');
}

$bukuEdit = ($aksi === 'edit' && $id > 0) ? getBookById($id) : null;
$semuaBuku = getAllBooks();

$pageTitle = 'Kelola Buku - Admin';
require __DIR__ . '/../../includes/header.php';
?>

<h1 class="text-2xl font-bold mb-6">Kelola Buku</h1>

<div class="flex gap-3 mb-8 text-sm flex-wrap">
    <a href="/perpus-mini/api/admin/dashboard.php" class="px-4 py-2 rounded bg-white shadow hover:bg-gray-50">Statistik</a>
    <a href="/perpus-mini/api/admin/books.php" class="px-4 py-2 rounded bg-indigo-700 text-white">Kelola Buku</a>
    <a href="/perpus-mini/api/admin/members.php" class="px-4 py-2 rounded bg-white shadow hover:bg-gray-50">Kelola Anggota</a>
    <a href="/perpus-mini/api/admin/loans.php" class="px-4 py-2 rounded bg-white shadow hover:bg-gray-50">Kelola Peminjaman</a>
</div>

<div class="bg-white rounded-lg shadow p-5 mb-8">
    <h2 class="font-semibold mb-4"><?= $bukuEdit ? 'Edit Buku' : 'Tambah Buku Baru' ?></h2>
    <form method="POST" enctype="multipart/form-data" class="grid md:grid-cols-2 gap-3">
        <input type="hidden" name="id" value="<?= $bukuEdit['id'] ?? '' ?>">
        <div>
            <label class="text-sm font-medium">Judul</label>
            <input type="text" name="judul" required class="w-full border rounded px-3 py-2 mt-1" value="<?= htmlspecialchars($bukuEdit['judul'] ?? '') ?>">
        </div>
        <div>
            <label class="text-sm font-medium">Penulis</label>
            <input type="text" name="penulis" required class="w-full border rounded px-3 py-2 mt-1" value="<?= htmlspecialchars($bukuEdit['penulis'] ?? '') ?>">
        </div>
        <div>
            <label class="text-sm font-medium">Kategori</label>
            <input type="text" name="kategori" required class="w-full border rounded px-3 py-2 mt-1" value="<?= htmlspecialchars($bukuEdit['kategori'] ?? '') ?>">
        </div>
        <div>
            <label class="text-sm font-medium">Tahun Terbit</label>
            <input type="number" name="tahun_terbit" required min="1900" max="<?= date('Y') ?>" class="w-full border rounded px-3 py-2 mt-1" value="<?= htmlspecialchars($bukuEdit['tahun_terbit'] ?? date('Y')) ?>">
        </div>
        <div>
            <label class="text-sm font-medium">Stok</label>
            <input type="number" name="stok" required min="0" class="w-full border rounded px-3 py-2 mt-1" value="<?= htmlspecialchars($bukuEdit['stok'] ?? 1) ?>">
        </div>
        <div class="md:col-span-2">
            <label class="text-sm font-medium">Sinopsis</label>
            <textarea name="sinopsis" rows="3" class="w-full border rounded px-3 py-2 mt-1"><?= htmlspecialchars($bukuEdit['sinopsis'] ?? '') ?></textarea>
        </div>
        <div class="md:col-span-2">
            <label class="text-sm font-medium">Cover Buku</label>
            <input type="file" name="cover" accept="image/*" class="w-full border rounded px-3 py-2 mt-1">
            <?php if ($bukuEdit && $bukuEdit['cover'] && file_exists(__DIR__ . '/../../uploads/' . $bukuEdit['cover'])): ?>
                <div class="mt-2">
                    <img src="/perpus-mini/uploads/<?= $bukuEdit['cover'] ?>" 
                         alt="Cover" class="w-32 h-auto rounded shadow">
                    <p class="text-xs text-gray-500 mt-1">Cover saat ini</p>
                </div>
            <?php endif; ?>
            <p class="text-xs text-gray-400 mt-1">Format: JPG, PNG, GIF, WEBP. Maks 2MB</p>
        </div>
        <div class="md:col-span-2 flex gap-2">
            <button type="submit" name="simpan" class="bg-indigo-700 text-white px-5 py-2 rounded hover:bg-indigo-800">
                <?= $bukuEdit ? 'Simpan Perubahan' : 'Tambah Buku' ?>
            </button>
            <?php if ($bukuEdit): ?>
                <a href="/perpus-mini/api/admin/books.php" class="px-5 py-2 rounded border hover:bg-gray-50">Batal</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="bg-white rounded-lg shadow overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-100 text-left">
            <tr>
                <th class="p-3">Cover</th>
                <th class="p-3">Judul</th>
                <th class="p-3">Penulis</th>
                <th class="p-3">Kategori</th>
                <th class="p-3">Stok</th>
                <th class="p-3">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            <?php foreach ($semuaBuku as $b): ?>
                <tr>
                    <td class="p-3">
                        <?php if ($b['cover'] && file_exists(__DIR__ . '/../../uploads/' . $b['cover'])): ?>
                            <img src="/perpus-mini/uploads/<?= $b['cover'] ?>" 
                                 alt="Cover" class="w-12 h-16 object-cover rounded">
                        <?php else: ?>
                            <div class="w-12 h-16 bg-indigo-100 text-indigo-700 flex items-center justify-center rounded text-xs">
                                No Cover
                            </div>
                        <?php endif; ?>
                    </td>
                    <td class="p-3"><?= htmlspecialchars($b['judul']) ?></td>
                    <td class="p-3"><?= htmlspecialchars($b['penulis']) ?></td>
                    <td class="p-3"><?= htmlspecialchars($b['kategori']) ?></td>
                    <td class="p-3"><?= $b['stok'] ?></td>
                    <td class="p-3 flex gap-3">
                        <a href="/perpus-mini/api/admin/books.php?aksi=edit&id=<?= $b['id'] ?>" class="text-indigo-700 hover:underline">Edit</a>
                        <a href="/perpus-mini/api/admin/books.php?aksi=hapus&id=<?= $b['id'] ?>"
                           onclick="return confirm('Hapus buku ini?')" class="text-red-500 hover:underline">Hapus</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>