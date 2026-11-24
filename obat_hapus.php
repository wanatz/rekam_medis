<?php
require_once 'koneksi.php';

$id = (int) ($_GET['id'] ?? 0);

$stmt = mysqli_prepare($koneksi, "DELETE FROM obat WHERE id_obat = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);

if (mysqli_stmt_execute($stmt)) {
    set_flash('sukses', 'Data obat berhasil dihapus.');
} else {
    // Gagal biasanya karena obat sudah tercatat pada rekam medis (FK RESTRICT)
    set_flash('gagal', 'Data obat tidak dapat dihapus karena sudah tercatat pada rekam medis.');
}

header('Location: obat.php');
exit;
