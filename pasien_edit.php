<?php
require_once 'koneksi.php';

$id = (int) ($_GET['id'] ?? 0);
$stmt = mysqli_prepare($koneksi, "SELECT * FROM pasien WHERE id_pasien = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$pasien = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$pasien) {
    set_flash('gagal', 'Data pasien tidak ditemukan.');
    header('Location: pasien.php');
    exit;
}

$error = [];
$data  = $pasien;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['no_rm']         = trim($_POST['no_rm'] ?? '');
    $data['nama_pasien']   = trim($_POST['nama_pasien'] ?? '');
    $data['jenis_kelamin'] = $_POST['jenis_kelamin'] ?? '';
    $data['tanggal_lahir'] = $_POST['tanggal_lahir'] ?? '';
    $data['alamat']        = trim($_POST['alamat'] ?? '');

    if ($data['no_rm'] === '')         $error[] = 'Nomor rekam medis wajib diisi.';
    if ($data['nama_pasien'] === '')   $error[] = 'Nama pasien wajib diisi.';
    if (!in_array($data['jenis_kelamin'], ['L', 'P'], true)) $error[] = 'Jenis kelamin wajib dipilih.';
    if ($data['tanggal_lahir'] === '') $error[] = 'Tanggal lahir wajib diisi.';
    elseif ($data['tanggal_lahir'] > date('Y-m-d')) $error[] = 'Tanggal lahir tidak boleh melebihi hari ini.';

    // Nomor RM unik, kecuali milik pasien ini sendiri
    if ($data['no_rm'] !== '') {
        $cek = mysqli_prepare($koneksi, "SELECT id_pasien FROM pasien WHERE no_rm = ? AND id_pasien <> ?");
        mysqli_stmt_bind_param($cek, 'si', $data['no_rm'], $id);
        mysqli_stmt_execute($cek);
        if (mysqli_num_rows(mysqli_stmt_get_result($cek)) > 0) {
            $error[] = 'Nomor RM "' . $data['no_rm'] . '" sudah dipakai pasien lain.';
        }
    }

    if (!$error) {
        $upd = mysqli_prepare($koneksi,
            "UPDATE pasien SET no_rm = ?, nama_pasien = ?, jenis_kelamin = ?,
                    tanggal_lahir = ?, alamat = ? WHERE id_pasien = ?");
        mysqli_stmt_bind_param($upd, 'sssssi',
            $data['no_rm'], $data['nama_pasien'], $data['jenis_kelamin'],
            $data['tanggal_lahir'], $data['alamat'], $id);
        if (mysqli_stmt_execute($upd)) {
            set_flash('sukses', 'Data pasien berhasil diperbarui.');
            header('Location: pasien.php');
            exit;
        }
        $error[] = 'Gagal memperbarui data: ' . mysqli_error($koneksi);
    }
}

$judul    = 'Edit Pasien';
$subjudul = 'Perbarui data pasien: ' . $pasien['nama_pasien'];
require_once 'includes/header.php';
?>

<div class="kartu">
    <div class="kartu-judul">Form Edit Pasien</div>

    <?php if ($error): ?>
        <div class="error-box"><b>Periksa lagi:</b><ul>
            <?php foreach ($error as $e1): ?><li><?= e($e1) ?></li><?php endforeach; ?>
        </ul></div>
    <?php endif; ?>

    <form method="post" action="">
        <div class="form-grid">
            <div class="form-grup">
                <label>No. Rekam Medis <span class="wajib">*</span></label>
                <input type="text" name="no_rm" maxlength="20" value="<?= e($data['no_rm']) ?>" required>
            </div>
            <div class="form-grup">
                <label>Nama Pasien <span class="wajib">*</span></label>
                <input type="text" name="nama_pasien" maxlength="100" value="<?= e($data['nama_pasien']) ?>" required>
            </div>
            <div class="form-grup">
                <label>Jenis Kelamin <span class="wajib">*</span></label>
                <select name="jenis_kelamin" required>
                    <option value="L" <?= $data['jenis_kelamin'] === 'L' ? 'selected' : '' ?>>Laki-laki</option>
                    <option value="P" <?= $data['jenis_kelamin'] === 'P' ? 'selected' : '' ?>>Perempuan</option>
                </select>
            </div>
            <div class="form-grup">
                <label>Tanggal Lahir <span class="wajib">*</span></label>
                <input type="date" name="tanggal_lahir" max="<?= date('Y-m-d') ?>" value="<?= e($data['tanggal_lahir']) ?>" required>
            </div>
            <div class="form-grup penuh">
                <label>Alamat</label>
                <textarea name="alamat"><?= e($data['alamat']) ?></textarea>
            </div>
        </div>
        <div class="form-aksi">
            <button class="btn btn-utama" type="submit">💾 Simpan Perubahan</button>
            <a class="btn btn-abu" href="pasien.php">Batal</a>
        </div>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>
