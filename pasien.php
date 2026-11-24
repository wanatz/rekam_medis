<?php
require_once 'koneksi.php';

// Pencarian sederhana berdasarkan nama / nomor RM
$cari = trim($_GET['q'] ?? '');

if ($cari !== '') {
    $sql  = "SELECT * FROM pasien WHERE nama_pasien LIKE ? OR no_rm LIKE ? ORDER BY id_pasien DESC";
    $stmt = mysqli_prepare($koneksi, $sql);
    $kata = "%$cari%";
    mysqli_stmt_bind_param($stmt, 'ss', $kata, $kata);
    mysqli_stmt_execute($stmt);
    $hasil = mysqli_stmt_get_result($stmt);
} else {
    $hasil = mysqli_query($koneksi, "SELECT * FROM pasien ORDER BY id_pasien DESC");
}

$judul    = 'Data Pasien';
$subjudul = 'Kelola data pasien yang terdaftar';
require_once 'includes/header.php';
?>

<div class="kartu">
    <div class="toolbar">
        <form class="form-cari" method="get" action="">
            <input type="text" name="q" placeholder="Cari nama pasien / No. RM..." value="<?= e($cari) ?>">
            <button class="btn btn-abu" type="submit">🔍 Cari</button>
            <?php if ($cari !== ''): ?>
                <a class="btn btn-abu" href="pasien.php">Reset</a>
            <?php endif; ?>
        </form>
        <a class="btn btn-utama" href="pasien_tambah.php">➕ Tambah Pasien</a>
    </div>

    <div class="tabel-bungkus">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>No. RM</th>
                    <th>Nama Pasien</th>
                    <th>Jenis Kelamin</th>
                    <th>Tgl Lahir / Umur</th>
                    <th>Alamat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (mysqli_num_rows($hasil) === 0): ?>
                <tr><td colspan="7" class="kosong">Belum ada data pasien<?= $cari !== '' ? ' yang cocok dengan pencarian' : '' ?>.</td></tr>
            <?php endif; ?>
            <?php $no = 1; while ($p = mysqli_fetch_assoc($hasil)): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><b><?= e($p['no_rm']) ?></b></td>
                    <td><?= e($p['nama_pasien']) ?></td>
                    <td>
                        <?php if ($p['jenis_kelamin'] === 'L'): ?>
                            <span class="badge badge-l">Laki-laki</span>
                        <?php else: ?>
                            <span class="badge badge-p">Perempuan</span>
                        <?php endif; ?>
                    </td>
                    <td><?= tgl_indo($p['tanggal_lahir']) ?><br><small><?= hitung_umur($p['tanggal_lahir']) ?></small></td>
                    <td><?= e($p['alamat']) ?></td>
                    <td>
                        <div class="aksi">
                            <a class="btn btn-kecil btn-detail" href="pasien_detail.php?id=<?= (int) $p['id_pasien'] ?>">👁️ Detail</a>
                            <a class="btn btn-kecil btn-edit" href="pasien_edit.php?id=<?= (int) $p['id_pasien'] ?>">✏️ Edit</a>
                            <a class="btn btn-kecil btn-hapus" href="pasien_hapus.php?id=<?= (int) $p['id_pasien'] ?>"
                               data-konfirmasi="Hapus pasien <?= e($p['nama_pasien']) ?>? Data yang sudah punya rekam medis tidak dapat dihapus.">🗑️ Hapus</a>
                        </div>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
