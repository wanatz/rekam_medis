<?php
require_once 'koneksi.php';

$id = (int) ($_GET['id'] ?? 0);

$stmt = mysqli_prepare($koneksi, "DELETE FROM rekam_medis WHERE id_rekam = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);

if (mysqli_stmt_execute($stmt)) {
    set_flash('sukses', 'Catatan rekam medis berhasil dihapus.');
} else {
    set_flash('gagal', 'Gagal menghapus catatan rekam medis: ' . mysqli_error($koneksi));
}

header('Location: rekam_medis.php');
exit;
