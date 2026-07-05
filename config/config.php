<?php
session_start();

define('MAKS_PINJAM', 3);
define('LAMA_PINJAM_HARI', 7);
define('DENDA_PER_HARI', 2000);

function getInisialBuku($judul) {
    $kata = explode(' ', $judul);
    $inisial = '';
    foreach ($kata as $k) {
        $inisial .= strtoupper(substr($k, 0, 1));
    }
    return substr($inisial, 0, 3);
}

function formatTanggal($tanggal) {
    return date('d/m/Y', strtotime($tanggal));
}

function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

function redirect($url) {
    header('Location: ' . $url);
    exit;
}
?>