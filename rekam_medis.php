<?php
require_once 'koneksi.php';

// ============================================================
//  RIWAYAT PEMERIKSAAN - menampilkan data dengan SQL JOIN
//  agar nama pasien, dokter, dan obat tampil (bukan angka ID)
// ============================================================
$cari = trim($_GET['q'] ?? '');

$sql = "SELECT rm.*, p.nama_pasien, p.no_rm, d.nama_dokter, o.nama_obat
        FROM rekam_medis rm
        JOIN pasien p ON p.id_pasien = rm.id_pasien
        JOIN dokter d ON d.id_dokter = rm.id_dokter
        JOIN obat   o ON o.id_obat   = rm.id_obat";

if ($cari !== '') {
    $sql .= " WHERE p.nama_pasien LIKE ? OR d.nama_dokter LIKE ? OR o.nama_obat LIKE ? OR rm.diagnosa LIKE ?";
    $sql .= " ORDER BY rm.tanggal_periksa DESC, rm.id_rekam DESC";
    $stmt = mysqli_prepare($koneksi, $sql);
    $kata = "%$cari%";
    mysqli_stmt_bind_param($stmt, 'ssss', $kata, $kata, $kata, $kata);
    mysqli_stmt_execute($stmt);
    $hasil = mysqli_stmt_get_result($stmt);
} else {
    $sql .= " ORDER BY rm.tanggal_periksa DESC, rm.id_rekam DESC";
    $hasil = mysqli_query($koneksi, $sql);
}

$judul    = 'Rekam Medis';
$subjudul = 'Riwayat pemeriksaan pasien (SQL JOIN)';
require_once 'includes/header.php';
?>

<div class="kartu">
    <div class="toolbar">
        <form class="form-cari" method="get" action="">
            <input type="text" name="q" placeholder="Cari pasien / dokter / obat / diagnosa..." value="<?= e($cari) ?>">
            <button class="btn btn-abu" type="submit">🔍 Cari</button>
            <?php if ($cari !== ''): ?>
                <a class="btn btn-abu" href="rekam_medis.php">Reset</a>
            <?php endif; ?>
        </form>
        <div class="aksi">
            <a class="btn btn-biru" href="rekam_cetak.php" target="_blank">🖨️ Cetak Laporan</a>
            <a class="btn btn-utama" href="rekam_tambah.php">➕ Tambah Rekam Medis</a>
        </div>
    </div>

    <div class="tabel-bungkus">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Pasien</th>
                    <th>Dokter</th>
                    <th>Keluhan</th>
                    <th>Diagnosa</th>
                    <th>Tindakan</th>
                    <th>Obat &amp; Resep</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (mysqli_num_rows($hasil) === 0): ?>
                <tr><td colspan="9" class="kosong">Belum ada catatan rekam medis<?= $cari !== '' ? ' yang cocok dengan pencarian' : '' ?>.</td></tr>
            <?php endif; ?>
            <?php $no = 1; while ($r = mysqli_fetch_assoc($hasil)): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= tgl_indo($r['tanggal_periksa']) ?></td>
                    <td><b><?= e($r['nama_pasien']) ?></b><br><small><?= e($r['no_rm']) ?></small></td>
                    <td><?= e($r['nama_dokter']) ?></td>
                    <td><?= e($r['keluhan']) ?></td>
                    <td><?= e($r['diagnosa']) ?></td>
                    <td><?= e($r['tindakan']) ?></td>
                    <td><b><?= e($r['nama_obat']) ?></b><br><small><?= e($r['resep']) ?></small></td>
                    <td>
                        <div class="aksi">
                            <a class="btn btn-kecil btn-detail" href="rekam_detail.php?id=<?= (int) $r['id_rekam'] ?>">👁️ Detail</a>
                            <a class="btn btn-kecil btn-edit" href="rekam_edit.php?id=<?= (int) $r['id_rekam'] ?>">✏️ Edit</a>
                            <a class="btn btn-kecil btn-hapus" href="rekam_hapus.php?id=<?= (int) $r['id_rekam'] ?>"
                               data-konfirmasi="Hapus catatan rekam medis ini?">🗑️ Hapus</a>
                        </div>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
