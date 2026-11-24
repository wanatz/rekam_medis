<?php
require_once 'koneksi.php';

$id = (int) ($_GET['id'] ?? 0);

$stmt = mysqli_prepare($koneksi, "DELETE FROM pasien WHERE id_pasien = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);

if (mysqli_stmt_execute($stmt)) {
    set_flash('sukses', 'Data pasien berhasil dihapus.');
} else {
    // Gagal biasanya karena pasien sudah memiliki catatan rekam medis (FK RESTRICT)
    set_flash('gagal', 'Data pasien tidak dapat dihapus karena sudah memiliki catatan rekam medis.');
}

header('Location: pasien.php');
exit;
