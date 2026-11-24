<?php
require_once 'koneksi.php';

$error = [];
$data  = ['no_rm' => '', 'nama_pasien' => '', 'jenis_kelamin' => '', 'tanggal_lahir' => '', 'alamat' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['no_rm']         = trim($_POST['no_rm'] ?? '');
    $data['nama_pasien']   = trim($_POST['nama_pasien'] ?? '');
    $data['jenis_kelamin'] = $_POST['jenis_kelamin'] ?? '';
    $data['tanggal_lahir'] = $_POST['tanggal_lahir'] ?? '';
    $data['alamat']        = trim($_POST['alamat'] ?? '');

    // Validasi input
    if ($data['no_rm'] === '')         $error[] = 'Nomor rekam medis wajib diisi.';
    if ($data['nama_pasien'] === '')   $error[] = 'Nama pasien wajib diisi.';
    if (!in_array($data['jenis_kelamin'], ['L', 'P'], true)) $error[] = 'Jenis kelamin wajib dipilih.';
    if ($data['tanggal_lahir'] === '') $error[] = 'Tanggal lahir wajib diisi.';
    elseif ($data['tanggal_lahir'] > date('Y-m-d')) $error[] = 'Tanggal lahir tidak boleh melebihi hari ini.';

    // Nomor RM harus unik
    if ($data['no_rm'] !== '') {
        $cek = mysqli_prepare($koneksi, "SELECT id_pasien FROM pasien WHERE no_rm = ?");
        mysqli_stmt_bind_param($cek, 's', $data['no_rm']);
        mysqli_stmt_execute($cek);
        if (mysqli_num_rows(mysqli_stmt_get_result($cek)) > 0) {
            $error[] = 'Nomor RM "' . $data['no_rm'] . '" sudah dipakai pasien lain.';
        }
    }

    if (!$error) {
        $stmt = mysqli_prepare($koneksi,
            "INSERT INTO pasien (no_rm, nama_pasien, jenis_kelamin, tanggal_lahir, alamat)
             VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, 'sssss',
            $data['no_rm'], $data['nama_pasien'], $data['jenis_kelamin'],
            $data['tanggal_lahir'], $data['alamat']);
        if (mysqli_stmt_execute($stmt)) {
            set_flash('sukses', 'Data pasien berhasil ditambahkan.');
            header('Location: pasien.php');
            exit;
        }
        $error[] = 'Gagal menyimpan data: ' . mysqli_error($koneksi);
    }
}

$judul    = 'Tambah Pasien';
$subjudul = 'Isi data pasien baru dengan lengkap';
require_once 'includes/header.php';
?>

<div class="kartu">
    <div class="kartu-judul">Form Tambah Pasien</div>

    <?php if ($error): ?>
        <div class="error-box"><b>Periksa lagi:</b><ul>
            <?php foreach ($error as $e1): ?><li><?= e($e1) ?></li><?php endforeach; ?>
        </ul></div>
    <?php endif; ?>

    <form method="post" action="">
        <div class="form-grid">
            <div class="form-grup">
                <label>No. Rekam Medis <span class="wajib">*</span></label>
                <input type="text" name="no_rm" maxlength="20" placeholder="cth: RM-001" value="<?= e($data['no_rm']) ?>" required>
                <span class="petunjuk">Harus unik, tidak boleh sama dengan pasien lain.</span>
            </div>
            <div class="form-grup">
                <label>Nama Pasien <span class="wajib">*</span></label>
                <input type="text" name="nama_pasien" maxlength="100" placeholder="Nama lengkap pasien" value="<?= e($data['nama_pasien']) ?>" required>
            </div>
            <div class="form-grup">
                <label>Jenis Kelamin <span class="wajib">*</span></label>
                <select name="jenis_kelamin" required>
                    <option value="">-- Pilih --</option>
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
                <textarea name="alamat" placeholder="Alamat lengkap pasien"><?= e($data['alamat']) ?></textarea>
            </div>
        </div>
        <div class="form-aksi">
            <button class="btn btn-utama" type="submit">💾 Simpan</button>
            <a class="btn btn-abu" href="pasien.php">Batal</a>
        </div>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>
