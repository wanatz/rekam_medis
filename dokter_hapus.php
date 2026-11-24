<?php
require_once 'koneksi.php';

$id = (int) ($_GET['id'] ?? 0);

$stmt = mysqli_prepare($koneksi, "DELETE FROM dokter WHERE id_dokter = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);

if (mysqli_stmt_execute($stmt)) {
    set_flash('sukses', 'Data dokter berhasil dihapus.');
} else {
    // Gagal biasanya karena dokter sudah tercatat pada rekam medis (FK RESTRICT)
    set_flash('gagal', 'Data dokter tidak dapat dihapus karena sudah tercatat pada rekam medis.');
}

header('Location: dokter.php');
exit;
