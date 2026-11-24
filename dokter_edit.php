<?php
require_once 'koneksi.php';

$id = (int) ($_GET['id'] ?? 0);
$stmt = mysqli_prepare($koneksi, "SELECT * FROM dokter WHERE id_dokter = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$dokter = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$dokter) {
    set_flash('gagal', 'Data dokter tidak ditemukan.');
    header('Location: dokter.php');
    exit;
}

$error = [];
$data  = $dokter;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['nama_dokter']  = trim($_POST['nama_dokter'] ?? '');
    $data['spesialisasi'] = trim($_POST['spesialisasi'] ?? '');

    if ($data['nama_dokter'] === '')  $error[] = 'Nama dokter wajib diisi.';
    if ($data['spesialisasi'] === '') $error[] = 'Spesialisasi wajib diisi.';

    if (!$error) {
        $upd = mysqli_prepare($koneksi, "UPDATE dokter SET nama_dokter = ?, spesialisasi = ? WHERE id_dokter = ?");
        mysqli_stmt_bind_param($upd, 'ssi', $data['nama_dokter'], $data['spesialisasi'], $id);
        if (mysqli_stmt_execute($upd)) {
            set_flash('sukses', 'Data dokter berhasil diperbarui.');
            header('Location: dokter.php');
            exit;
        }
        $error[] = 'Gagal memperbarui data: ' . mysqli_error($koneksi);
    }
}

$judul    = 'Edit Dokter';
$subjudul = 'Perbarui data dokter: ' . $dokter['nama_dokter'];
require_once 'includes/header.php';
?>

<div class="kartu">
    <div class="kartu-judul">Form Edit Dokter</div>

    <?php if ($error): ?>
        <div class="error-box"><b>Periksa lagi:</b><ul>
            <?php foreach ($error as $e1): ?><li><?= e($e1) ?></li><?php endforeach; ?>
        </ul></div>
    <?php endif; ?>

    <form method="post" action="">
        <div class="form-grid">
            <div class="form-grup">
                <label>Nama Dokter <span class="wajib">*</span></label>
                <input type="text" name="nama_dokter" maxlength="100" value="<?= e($data['nama_dokter']) ?>" required>
            </div>
            <div class="form-grup">
                <label>Spesialisasi <span class="wajib">*</span></label>
                <input type="text" name="spesialisasi" maxlength="50" value="<?= e($data['spesialisasi']) ?>" required>
            </div>
        </div>
        <div class="form-aksi">
            <button class="btn btn-utama" type="submit">💾 Simpan Perubahan</button>
            <a class="btn btn-abu" href="dokter.php">Batal</a>
        </div>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>
