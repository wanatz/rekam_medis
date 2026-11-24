<?php
require_once 'koneksi.php';

$id = (int) ($_GET['id'] ?? 0);
$stmt = mysqli_prepare($koneksi, "SELECT * FROM obat WHERE id_obat = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$obat = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$obat) {
    set_flash('gagal', 'Data obat tidak ditemukan.');
    header('Location: obat.php');
    exit;
}

$error = [];
$data  = $obat;

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
        $upd = mysqli_prepare($koneksi, "UPDATE obat SET nama_obat = ?, satuan = ?, stok = ? WHERE id_obat = ?");
        mysqli_stmt_bind_param($upd, 'ssii', $data['nama_obat'], $data['satuan'], $stok, $id);
        if (mysqli_stmt_execute($upd)) {
            set_flash('sukses', 'Data obat berhasil diperbarui.');
            header('Location: obat.php');
            exit;
        }
        $error[] = 'Gagal memperbarui data: ' . mysqli_error($koneksi);
    }
}

$judul    = 'Edit Obat';
$subjudul = 'Perbarui data obat: ' . $obat['nama_obat'];
require_once 'includes/header.php';
?>

<div class="kartu">
    <div class="kartu-judul">Form Edit Obat</div>

    <?php if ($error): ?>
        <div class="error-box"><b>Periksa lagi:</b><ul>
            <?php foreach ($error as $e1): ?><li><?= e($e1) ?></li><?php endforeach; ?>
        </ul></div>
    <?php endif; ?>

    <form method="post" action="">
        <div class="form-grid">
            <div class="form-grup">
                <label>Nama Obat <span class="wajib">*</span></label>
                <input type="text" name="nama_obat" maxlength="100" value="<?= e($data['nama_obat']) ?>" required>
            </div>
            <div class="form-grup">
                <label>Satuan <span class="wajib">*</span></label>
                <select name="satuan" required>
                    <?php foreach (['Tablet', 'Kapsul', 'Sirup', 'Salep', 'Tetes', 'Injeksi'] as $s): ?>
                        <option value="<?= $s ?>" <?= $data['satuan'] === $s ? 'selected' : '' ?>><?= $s ?></option>
                    <?php endforeach; ?>
                    <?php if (!in_array($data['satuan'], ['Tablet', 'Kapsul', 'Sirup', 'Salep', 'Tetes', 'Injeksi'], true) && $data['satuan'] !== ''): ?>
                        <option value="<?= e($data['satuan']) ?>" selected><?= e($data['satuan']) ?></option>
                    <?php endif; ?>
                </select>
            </div>
            <div class="form-grup">
                <label>Stok <span class="wajib">*</span></label>
                <input type="number" name="stok" min="0" step="1" value="<?= e($data['stok']) ?>" required>
            </div>
        </div>
        <div class="form-aksi">
            <button class="btn btn-utama" type="submit">💾 Simpan Perubahan</button>
            <a class="btn btn-abu" href="obat.php">Batal</a>
        </div>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>
