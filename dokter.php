<?php
require_once 'koneksi.php';

$cari = trim($_GET['q'] ?? '');

if ($cari !== '') {
    $sql  = "SELECT * FROM dokter WHERE nama_dokter LIKE ? OR spesialisasi LIKE ? ORDER BY id_dokter DESC";
    $stmt = mysqli_prepare($koneksi, $sql);
    $kata = "%$cari%";
    mysqli_stmt_bind_param($stmt, 'ss', $kata, $kata);
    mysqli_stmt_execute($stmt);
    $hasil = mysqli_stmt_get_result($stmt);
} else {
    $hasil = mysqli_query($koneksi, "SELECT * FROM dokter ORDER BY id_dokter DESC");
}

$judul    = 'Data Dokter';
$subjudul = 'Kelola data dokter dan spesialisasinya';
require_once 'includes/header.php';
?>

<div class="kartu">
    <div class="toolbar">
        <form class="form-cari" method="get" action="">
            <input type="text" name="q" placeholder="Cari nama dokter / spesialisasi..." value="<?= e($cari) ?>">
            <button class="btn btn-abu" type="submit">🔍 Cari</button>
            <?php if ($cari !== ''): ?>
                <a class="btn btn-abu" href="dokter.php">Reset</a>
            <?php endif; ?>
        </form>
        <a class="btn btn-utama" href="dokter_tambah.php">➕ Tambah Dokter</a>
    </div>

    <div class="tabel-bungkus">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Dokter</th>
                    <th>Spesialisasi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (mysqli_num_rows($hasil) === 0): ?>
                <tr><td colspan="4" class="kosong">Belum ada data dokter<?= $cari !== '' ? ' yang cocok dengan pencarian' : '' ?>.</td></tr>
            <?php endif; ?>
            <?php $no = 1; while ($d = mysqli_fetch_assoc($hasil)): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><b><?= e($d['nama_dokter']) ?></b></td>
                    <td><?= e($d['spesialisasi']) ?></td>
                    <td>
                        <div class="aksi">
                            <a class="btn btn-kecil btn-edit" href="dokter_edit.php?id=<?= (int) $d['id_dokter'] ?>">✏️ Edit</a>
                            <a class="btn btn-kecil btn-hapus" href="dokter_hapus.php?id=<?= (int) $d['id_dokter'] ?>"
                               data-konfirmasi="Hapus dokter <?= e($d['nama_dokter']) ?>? Dokter yang sudah menangani pemeriksaan tidak dapat dihapus.">🗑️ Hapus</a>
                        </div>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
