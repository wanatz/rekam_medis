<?php
require_once 'koneksi.php';

$id = (int) ($_GET['id'] ?? 0);

$stmt = mysqli_prepare($koneksi, "SELECT * FROM pasien WHERE id_pasien = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$p = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$p) {
    set_flash('gagal', 'Data pasien tidak ditemukan.');
    header('Location: pasien.php');
    exit;
}

// Seluruh riwayat pemeriksaan pasien ini (SQL JOIN)
$stmt = mysqli_prepare($koneksi,
    "SELECT rm.*, d.nama_dokter, o.nama_obat
     FROM rekam_medis rm
     JOIN dokter d ON d.id_dokter = rm.id_dokter
     JOIN obat   o ON o.id_obat   = rm.id_obat
     WHERE rm.id_pasien = ?
     ORDER BY rm.tanggal_periksa DESC, rm.id_rekam DESC");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$riwayat = mysqli_stmt_get_result($stmt);

$judul    = 'Detail Pasien';
$subjudul = $p['nama_pasien'] . ' — ' . $p['no_rm'];
require_once 'includes/header.php';
?>

<div class="kartu">
    <div class="kartu-judul">Profil Pasien</div>
    <div class="detail-grid">
        <div class="detail-item"><div class="dt-label">No. Rekam Medis</div><div class="dt-nilai"><?= e($p['no_rm']) ?></div></div>
        <div class="detail-item"><div class="dt-label">Nama Pasien</div><div class="dt-nilai"><?= e($p['nama_pasien']) ?></div></div>
        <div class="detail-item"><div class="dt-label">Jenis Kelamin</div><div class="dt-nilai"><?= $p['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan' ?></div></div>
        <div class="detail-item"><div class="dt-label">Tanggal Lahir / Umur</div><div class="dt-nilai"><?= tgl_indo($p['tanggal_lahir']) ?> (<?= hitung_umur($p['tanggal_lahir']) ?>)</div></div>
        <div class="detail-item"><div class="dt-label">Alamat</div><div class="dt-nilai"><?= e($p['alamat']) ?></div></div>
        <div class="detail-item"><div class="dt-label">Total Pemeriksaan</div><div class="dt-nilai"><?= mysqli_num_rows($riwayat) ?> kali</div></div>
    </div>
    <div class="form-aksi">
        <a class="btn btn-edit" href="pasien_edit.php?id=<?= (int) $p['id_pasien'] ?>">✏️ Edit Pasien</a>
        <a class="btn btn-abu" href="pasien.php">← Kembali ke Data Pasien</a>
    </div>
</div>

<div class="kartu">
    <div class="kartu-judul">Riwayat Pemeriksaan Pasien Ini
        <small>Diambil dari tabel rekam_medis dengan SQL JOIN ke dokter dan obat.</small>
    </div>
    <div class="tabel-bungkus">
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Dokter</th>
                    <th>Keluhan</th>
                    <th>Diagnosa</th>
                    <th>Obat &amp; Resep</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (mysqli_num_rows($riwayat) === 0): ?>
                <tr><td colspan="6" class="kosong">Pasien ini belum pernah tercatat melakukan pemeriksaan.</td></tr>
            <?php endif; ?>
            <?php while ($r = mysqli_fetch_assoc($riwayat)): ?>
                <tr>
                    <td><?= tgl_indo($r['tanggal_periksa']) ?></td>
                    <td><?= e($r['nama_dokter']) ?></td>
                    <td><?= e($r['keluhan']) ?></td>
                    <td><?= e($r['diagnosa']) ?></td>
                    <td><b><?= e($r['nama_obat']) ?></b><br><small><?= e($r['resep']) ?></small></td>
                    <td><a class="btn btn-kecil btn-detail" href="rekam_detail.php?id=<?= (int) $r['id_rekam'] ?>">👁️ Detail</a></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
