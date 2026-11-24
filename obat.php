<?php
require_once 'koneksi.php';

$cari = trim($_GET['q'] ?? '');

if ($cari !== '') {
    $sql  = "SELECT * FROM obat WHERE nama_obat LIKE ? OR satuan LIKE ? ORDER BY id_obat DESC";
    $stmt = mysqli_prepare($koneksi, $sql);
    $kata = "%$cari%";
    mysqli_stmt_bind_param($stmt, 'ss', $kata, $kata);
    mysqli_stmt_execute($stmt);
    $hasil = mysqli_stmt_get_result($stmt);
} else {
    $hasil = mysqli_query($koneksi, "SELECT * FROM obat ORDER BY id_obat DESC");
}

$judul    = 'Data Obat';
$subjudul = 'Kelola data obat dan stoknya';
require_once 'includes/header.php';
?>

<div class="kartu">
    <div class="toolbar">
        <form class="form-cari" method="get" action="">
            <input type="text" name="q" placeholder="Cari nama obat / satuan..." value="<?= e($cari) ?>">
            <button class="btn btn-abu" type="submit">🔍 Cari</button>
            <?php if ($cari !== ''): ?>
                <a class="btn btn-abu" href="obat.php">Reset</a>
            <?php endif; ?>
        </form>
        <a class="btn btn-utama" href="obat_tambah.php">➕ Tambah Obat</a>
    </div>

    <div class="tabel-bungkus">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Obat</th>
                    <th>Satuan</th>
                    <th>Stok</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (mysqli_num_rows($hasil) === 0): ?>
                <tr><td colspan="6" class="kosong">Belum ada data obat<?= $cari !== '' ? ' yang cocok dengan pencarian' : '' ?>.</td></tr>
            <?php endif; ?>
            <?php $no = 1; while ($o = mysqli_fetch_assoc($hasil)): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><b><?= e($o['nama_obat']) ?></b></td>
                    <td><?= e($o['satuan']) ?></td>
                    <td><?= (int) $o['stok'] ?></td>
                    <td>
                        <?php if ((int) $o['stok'] <= 0): ?>
                            <span class="badge badge-stok-habis">Habis</span>
                        <?php elseif ((int) $o['stok'] <= 10): ?>
                            <span class="badge badge-stok-tipis">Menipis</span>
                        <?php else: ?>
                            <span class="badge badge-stok-aman">Tersedia</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="aksi">
                            <a class="btn btn-kecil btn-edit" href="obat_edit.php?id=<?= (int) $o['id_obat'] ?>">✏️ Edit</a>
                            <a class="btn btn-kecil btn-hapus" href="obat_hapus.php?id=<?= (int) $o['id_obat'] ?>"
                               data-konfirmasi="Hapus obat <?= e($o['nama_obat']) ?>? Obat yang sudah tercatat pada rekam medis tidak dapat dihapus.">🗑️ Hapus</a>
                        </div>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
