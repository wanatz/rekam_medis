<?php
require_once 'koneksi.php';

$error = [];
$data  = ['nama_dokter' => '', 'spesialisasi' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['nama_dokter']  = trim($_POST['nama_dokter'] ?? '');
    $data['spesialisasi'] = trim($_POST['spesialisasi'] ?? '');

    if ($data['nama_dokter'] === '')  $error[] = 'Nama dokter wajib diisi.';
    if ($data['spesialisasi'] === '') $error[] = 'Spesialisasi wajib diisi.';

    if (!$error) {
        $stmt = mysqli_prepare($koneksi, "INSERT INTO dokter (nama_dokter, spesialisasi) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, 'ss', $data['nama_dokter'], $data['spesialisasi']);
        if (mysqli_stmt_execute($stmt)) {
            set_flash('sukses', 'Data dokter berhasil ditambahkan.');
            header('Location: dokter.php');
            exit;
        }
        $error[] = 'Gagal menyimpan data: ' . mysqli_error($koneksi);
    }
}

$judul    = 'Tambah Dokter';
$subjudul = 'Isi data dokter baru';
require_once 'includes/header.php';
?>

<div class="kartu">
    <div class="kartu-judul">Form Tambah Dokter</div>

    <?php if ($error): ?>
        <div class="error-box"><b>Periksa lagi:</b><ul>
            <?php foreach ($error as $e1): ?><li><?= e($e1) ?></li><?php endforeach; ?>
        </ul></div>
    <?php endif; ?>

    <form method="post" action="">
        <div class="form-grid">
            <div class="form-grup">
                <label>Nama Dokter <span class="wajib">*</span></label>
                <input type="text" name="nama_dokter" maxlength="100" placeholder="cth: dr. Sari Wulandari" value="<?= e($data['nama_dokter']) ?>" required>
            </div>
            <div class="form-grup">
                <label>Spesialisasi <span class="wajib">*</span></label>
                <input type="text" name="spesialisasi" maxlength="50" placeholder="cth: Umum, Anak, Penyakit Dalam" value="<?= e($data['spesialisasi']) ?>" required>
            </div>
        </div>
        <div class="form-aksi">
            <button class="btn btn-utama" type="submit">💾 Simpan</button>
            <a class="btn btn-abu" href="dokter.php">Batal</a>
        </div>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>
