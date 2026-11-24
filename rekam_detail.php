<?php
require_once 'koneksi.php';

$id = (int) ($_GET['id'] ?? 0);

// Detail satu catatan pemeriksaan dengan SQL JOIN
$stmt = mysqli_prepare($koneksi,
    "SELECT rm.*, p.no_rm, p.nama_pasien, p.jenis_kelamin, p.tanggal_lahir, p.alamat,
            d.nama_dokter, d.spesialisasi, o.nama_obat, o.satuan
     FROM rekam_medis rm
     JOIN pasien p ON p.id_pasien = rm.id_pasien
     JOIN dokter d ON d.id_dokter = rm.id_dokter
     JOIN obat   o ON o.id_obat   = rm.id_obat
     WHERE rm.id_rekam = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$r = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$r) {
    set_flash('gagal', 'Catatan rekam medis tidak ditemukan.');
    header('Location: rekam_medis.php');
    exit;
}

$judul    = 'Detail Rekam Medis';
$subjudul = 'Pemeriksaan ' . $r['nama_pasien'] . ' — ' . tgl_indo($r['tanggal_periksa']);
require_once 'includes/header.php';
?>

<div class="kartu">
    <div class="kartu-judul">Data Pasien &amp; Pemeriksa</div>
    <div class="detail-grid">
        <div class="detail-item"><div class="dt-label">No. Rekam Medis</div><div class="dt-nilai"><?= e($r['no_rm']) ?></div></div>
        <div class="detail-item"><div class="dt-label">Tanggal Periksa</div><div class="dt-nilai"><?= tgl_indo($r['tanggal_periksa']) ?></div></div>
        <div class="detail-item"><div class="dt-label">Nama Pasien</div><div class="dt-nilai"><?= e($r['nama_pasien']) ?></div></div>
        <div class="detail-item"><div class="dt-label">Dokter Pemeriksa</div><div class="dt-nilai"><?= e($r['nama_dokter']) ?> (<?= e($r['spesialisasi']) ?>)</div></div>
        <div class="detail-item"><div class="dt-label">Jenis Kelamin</div><div class="dt-nilai"><?= $r['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan' ?></div></div>
        <div class="detail-item"><div class="dt-label">Umur Pasien</div><div class="dt-nilai"><?= hitung_umur($r['tanggal_lahir']) ?></div></div>
        <div class="detail-item"><div class="dt-label">Alamat</div><div class="dt-nilai"><?= e($r['alamat']) ?></div></div>
        <div class="detail-item"><div class="dt-label">Obat</div><div class="dt-nilai"><?= e($r['nama_obat']) ?> (<?= e($r['satuan']) ?>)</div></div>
    </div>
</div>

<div class="kartu">
    <div class="kartu-judul">Hasil Pemeriksaan</div>
    <div class="detail-item"><div class="dt-label">Keluhan</div><div class="dt-nilai detail-teks"><?= e($r['keluhan']) ?></div></div>
    <div class="detail-item"><div class="dt-label">Diagnosa</div><div class="dt-nilai detail-teks"><?= e($r['diagnosa']) ?></div></div>
    <div class="detail-item"><div class="dt-label">Tindakan</div><div class="dt-nilai detail-teks"><?= e($r['tindakan']) ?></div></div>
    <div class="detail-item"><div class="dt-label">Resep / Aturan Pakai</div><div class="dt-nilai detail-teks"><?= e($r['resep']) ?></div></div>

    <div class="form-aksi">
        <a class="btn btn-edit" href="rekam_edit.php?id=<?= (int) $r['id_rekam'] ?>">✏️ Edit</a>
        <a class="btn btn-abu" href="rekam_medis.php">← Kembali ke Riwayat</a>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
