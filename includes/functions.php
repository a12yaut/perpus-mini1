<?php
require_once __DIR__ . '/../config/database.php';

// ============================================
// FUNGSI UPLOAD COVER BUKU
// ============================================
function uploadCover($file, $oldFile = null) {
    // Hapus file lama jika ada
    if ($oldFile && file_exists(__DIR__ . '/../uploads/' . $oldFile)) {
        unlink(__DIR__ . '/../uploads/' . $oldFile);
    }
    
    // Jika tidak ada file yang diupload
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return $oldFile; // Kembalikan file lama
    }
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    
    // Validasi ekstensi
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) {
        return null;
    }
    
    // Validasi ukuran (max 2MB)
    if ($file['size'] > 2 * 1024 * 1024) {
        return null;
    }
    
    // Buat nama file unik
    $filename = time() . '_' . uniqid() . '.' . $ext;
    $target = __DIR__ . '/../uploads/' . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $target)) {
        return $filename;
    }
    
    return null;
}

function deleteCover($filename) {
    if ($filename && file_exists(__DIR__ . '/../uploads/' . $filename)) {
        return unlink(__DIR__ . '/../uploads/' . $filename);
    }
    return true;
}

function getCoverUrl($filename) {
    if ($filename && file_exists(__DIR__ . '/../uploads/' . $filename)) {
        return '/perpus-mini/uploads/' . $filename;
    }
    return null;
}

// ============================================
// FUNGSI CRUD BUKU
// ============================================

function getAllBooks($keyword = '', $kategori = '') {
    $pdo = getDb();
    $sql = "SELECT * FROM books WHERE 1=1";
    $params = [];
    
    if ($keyword) {
        $sql .= " AND (judul LIKE ? OR penulis LIKE ?)";
        $params[] = "%$keyword%";
        $params[] = "%$keyword%";
    }
    if ($kategori) {
        $sql .= " AND kategori = ?";
        $params[] = $kategori;
    }
    
    $sql .= " ORDER BY id DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getBookById($id) {
    $pdo = getDb();
    $stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function createBook($data) {
    $pdo = getDb();
    $stmt = $pdo->prepare("INSERT INTO books (judul, penulis, kategori, tahun_terbit, sinopsis, cover, stok) VALUES (?, ?, ?, ?, ?, ?, ?)");
    return $stmt->execute([
        $data['judul'],
        $data['penulis'],
        $data['kategori'],
        $data['tahun_terbit'],
        $data['sinopsis'],
        $data['cover'] ?? null,
        $data['stok']
    ]);
}

function updateBook($id, $data) {
    $pdo = getDb();
    $stmt = $pdo->prepare("UPDATE books SET judul=?, penulis=?, kategori=?, tahun_terbit=?, sinopsis=?, cover=?, stok=? WHERE id=?");
    return $stmt->execute([
        $data['judul'],
        $data['penulis'],
        $data['kategori'],
        $data['tahun_terbit'],
        $data['sinopsis'],
        $data['cover'] ?? null,
        $data['stok'],
        $id
    ]);
}

function deleteBook($id) {
    $pdo = getDb();
    // Ambil nama file cover dulu
    $stmt = $pdo->prepare("SELECT cover FROM books WHERE id = ?");
    $stmt->execute([$id]);
    $book = $stmt->fetch();
    if ($book && $book['cover']) {
        deleteCover($book['cover']);
    }
    
    $stmt = $pdo->prepare("DELETE FROM books WHERE id = ?");
    return $stmt->execute([$id]);
}

function getAllKategori() {
    $pdo = getDb();
    $stmt = $pdo->query("SELECT DISTINCT kategori FROM books ORDER BY kategori");
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function getCart() {
    return $_SESSION['cart'] ?? [];
}

function addToCart($bookId) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    if (in_array($bookId, $_SESSION['cart'])) {
        return ['sukses' => false, 'pesan' => 'Buku sudah ada di daftar pinjam.'];
    }
    
    if (count($_SESSION['cart']) >= MAKS_PINJAM) {
        return ['sukses' => false, 'pesan' => 'Maksimal ' . MAKS_PINJAM . ' buku dalam satu peminjaman.'];
    }
    
    $_SESSION['cart'][] = $bookId;
    return ['sukses' => true, 'pesan' => 'Buku berhasil ditambahkan ke daftar pinjam.'];
}

function removeFromCart($bookId) {
    if (isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array_filter($_SESSION['cart'], function($id) use ($bookId) {
            return $id != $bookId;
        });
        $_SESSION['cart'] = array_values($_SESSION['cart']);
    }
}

function clearCart() {
    unset($_SESSION['cart']);
}

function getActiveLoanCount($userId) {
    $pdo = getDb();
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM loans WHERE user_id = ? AND status = 'dipinjam'");
    $stmt->execute([$userId]);
    return (int)$stmt->fetchColumn();
}

function createLoan($userId, $bookId) {
    $pdo = getDb();
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("SELECT stok FROM books WHERE id = ? FOR UPDATE");
        $stmt->execute([$bookId]);
        $stok = $stmt->fetchColumn();
        if ($stok <= 0) {
            $pdo->rollBack();
            return false;
        }
        
        $stmt = $pdo->prepare("UPDATE books SET stok = stok - 1 WHERE id = ?");
        $stmt->execute([$bookId]);
        
        $tanggalPinjam = date('Y-m-d');
        $tanggalJatuhTempo = date('Y-m-d', strtotime("+".LAMA_PINJAM_HARI." days"));
        $stmt = $pdo->prepare("INSERT INTO loans (user_id, book_id, tanggal_pinjam, tanggal_jatuh_tempo, status) VALUES (?, ?, ?, ?, 'dipinjam')");
        $stmt->execute([$userId, $bookId, $tanggalPinjam, $tanggalJatuhTempo]);
        
        $pdo->commit();
        return true;
    } catch (Exception $e) {
        $pdo->rollBack();
        return false;
    }
}

function returnLoan($loanId) {
    $pdo = getDb();
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("SELECT * FROM loans WHERE id = ? AND status = 'dipinjam' FOR UPDATE");
        $stmt->execute([$loanId]);
        $loan = $stmt->fetch();
        if (!$loan) {
            $pdo->rollBack();
            return false;
        }
        
        $tanggalKembali = date('Y-m-d');
        $denda = 0;
        if (strtotime($tanggalKembali) > strtotime($loan['tanggal_jatuh_tempo'])) {
            $selisih = (strtotime($tanggalKembali) - strtotime($loan['tanggal_jatuh_tempo'])) / (60 * 60 * 24);
            $denda = ceil($selisih) * DENDA_PER_HARI;
        }
        
        $stmt = $pdo->prepare("UPDATE loans SET tanggal_kembali = ?, denda = ?, status = 'dikembalikan' WHERE id = ?");
        $stmt->execute([$tanggalKembali, $denda, $loanId]);
        
        $stmt = $pdo->prepare("UPDATE books SET stok = stok + 1 WHERE id = ?");
        $stmt->execute([$loan['book_id']]);
        
        $pdo->commit();
        return true;
    } catch (Exception $e) {
        $pdo->rollBack();
        return false;
    }
}

function getLoansByUser($userId) {
    $pdo = getDb();
    $stmt = $pdo->prepare("
        SELECT l.*, b.judul, b.penulis 
        FROM loans l 
        JOIN books b ON l.book_id = b.id 
        WHERE l.user_id = ? 
        ORDER BY l.created_at DESC
    ");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

function getAllLoans() {
    $pdo = getDb();
    $stmt = $pdo->query("
        SELECT l.*, u.nama as user_nama, u.email, b.judul, b.penulis 
        FROM loans l 
        JOIN users u ON l.user_id = u.id 
        JOIN books b ON l.book_id = b.id 
        ORDER BY l.created_at DESC
    ");
    return $stmt->fetchAll();
}

function getStatistik() {
    $pdo = getDb();
    $stats = [];
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM books");
    $stats['total_buku'] = (int)$stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'anggota'");
    $stats['total_anggota'] = (int)$stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM loans WHERE status = 'dipinjam'");
    $stats['peminjaman_aktif'] = (int)$stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT SUM(denda) FROM loans");
    $stats['total_denda'] = (int)$stmt->fetchColumn() ?: 0;
    
    $stmt = $pdo->query("
        SELECT b.judul, COUNT(l.id) as total_pinjam 
        FROM books b 
        JOIN loans l ON b.id = l.book_id 
        GROUP BY b.id 
        ORDER BY total_pinjam DESC 
        LIMIT 5
    ");
    $stats['buku_terpopuler'] = $stmt->fetchAll();
    
    $stmt = $pdo->query("
        SELECT DATE_FORMAT(tanggal_pinjam, '%Y-%m') as bulan, COUNT(*) as total 
        FROM loans 
        GROUP BY bulan 
        ORDER BY bulan DESC 
        LIMIT 6
    ");
    $stats['pinjam_per_bulan'] = $stmt->fetchAll();
    
    return $stats;
}

function getAllMembers() {
    $pdo = getDb();
    $stmt = $pdo->query("SELECT * FROM users WHERE role = 'anggota' ORDER BY created_at DESC");
    return $stmt->fetchAll();
}

function deleteMember($id) {
    $pdo = getDb();
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'anggota'");
    return $stmt->execute([$id]);
}
?>