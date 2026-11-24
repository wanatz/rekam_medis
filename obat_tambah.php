<?php
require_once 'koneksi.php';

$error = [];
$data  = ['nama_obat' => '', 'satuan' => '', 'stok' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['nama_obat'] = trim($_POST['nama_obat'] ?? '');
    $data['satuan']    = trim($_POST['satuan'] ?? '');
    $data['stok']      = trim($_POST['stok'] ?? '');

    if ($data['nama_obat'] === '') $error[] = 'Nama obat wajib diisi.';
    if ($data['satuan'] === '')    $error[] = 'Satuan wajib dipilih.';
    if ($data['stok'] === '' || !is_numeric($data['stok'])) $error[] = 'Stok wajib diisi dengan angka.';
    elseif ((int) $data['stok'] < 0) $error[] = 'Stok tidak boleh negatif.';

    if (!$error) {
        $stok = (int) $data['stok'];
        $stmt = mysqli_prepare($koneksi, "INSERT INTO obat (nama_obat, satuan, stok) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, 'ssi', $data['nama_obat'], $data['satuan'], $stok);
        if (mysqli_stmt_execute($stmt)) {
            set_flash('sukses', 'Data obat berhasil ditambahkan.');
            header('Location: obat.php');
            exit;
        }
        $error[] = 'Gagal menyimpan data: ' . mysqli_error($koneksi);
    }
}

$judul    = 'Tambah Obat';
$subjudul = 'Isi data obat baru';
require_once 'includes/header.php';
?>

<div class="kartu">
    <div class="kartu-judul">Form Tambah Obat</div>

    <?php if ($error): ?>
        <div class="error-box"><b>Periksa lagi:</b><ul>
            <?php foreach ($error as $e1): ?><li><?= e($e1) ?></li><?php endforeach; ?>
        </ul></div>
    <?php endif; ?>

    <form method="post" action="">
        <div class="form-grid">
            <div class="form-grup">
                <label>Nama Obat <span class="wajib">*</span></label>
                <input type="text" name="nama_obat" maxlength="100" placeholder="cth: Paracetamol 500 mg" value="<?= e($data['nama_obat']) ?>" required>
            </div>
            <div class="form-grup">
                <label>Satuan <span class="wajib">*</span></label>
                <select name="satuan" required>
                    <option value="">-- Pilih satuan --</option>
                    <?php foreach (['Tablet', 'Kapsul', 'Sirup', 'Salep', 'Tetes', 'Injeksi'] as $s): ?>
                        <option value="<?= $s ?>" <?= $data['satuan'] === $s ? 'selected' : '' ?>><?= $s ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-grup">
                <label>Stok <span class="wajib">*</span></label>
                <input type="number" name="stok" min="0" step="1" placeholder="cth: 100" value="<?= e($data['stok']) ?>" required>
                <span class="petunjuk">Stok tidak boleh kurang dari 0.</span>
            </div>
        </div>
        <div class="form-aksi">
            <button class="btn btn-utama" type="submit">💾 Simpan</button>
            <a class="btn btn-abu" href="obat.php">Batal</a>
        </div>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>
