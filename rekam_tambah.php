<?php
require_once 'koneksi.php';

// Data untuk dropdown diambil dari tabel master (relasi)
$daftar_pasien = mysqli_query($koneksi, "SELECT id_pasien, no_rm, nama_pasien FROM pasien ORDER BY nama_pasien ASC");
$daftar_dokter = mysqli_query($koneksi, "SELECT id_dokter, nama_dokter, spesialisasi FROM dokter ORDER BY nama_dokter ASC");
$daftar_obat   = mysqli_query($koneksi, "SELECT id_obat, nama_obat, satuan, stok FROM obat ORDER BY nama_obat ASC");

$error = [];
$data  = ['id_pasien' => '', 'id_dokter' => '', 'id_obat' => '', 'tanggal_periksa' => date('Y-m-d'),
          'keluhan' => '', 'diagnosa' => '', 'tindakan' => '', 'resep' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach (array_keys($data) as $k) {
        $data[$k] = trim($_POST[$k] ?? '');
    }

    // Validasi: FK wajib dipilih dari dropdown (nilainya pasti ada di tabel induk)
    if ($data['id_pasien'] === '' || !ctype_digit($data['id_pasien'])) $error[] = 'Pasien wajib dipilih.';
    if ($data['id_dokter'] === '' || !ctype_digit($data['id_dokter'])) $error[] = 'Dokter wajib dipilih.';
    if ($data['id_obat'] === ''   || !ctype_digit($data['id_obat']))   $error[] = 'Obat wajib dipilih.';
    if ($data['tanggal_periksa'] === '') $error[] = 'Tanggal periksa wajib diisi.';
    elseif ($data['tanggal_periksa'] > date('Y-m-d')) $error[] = 'Tanggal periksa tidak boleh melebihi hari ini.';
    if ($data['keluhan'] === '')  $error[] = 'Keluhan wajib diisi.';
    if ($data['diagnosa'] === '') $error[] = 'Diagnosa wajib diisi.';

    if (!$error) {
        $stmt = mysqli_prepare($koneksi,
            "INSERT INTO rekam_medis
                (id_pasien, id_dokter, id_obat, tanggal_periksa, keluhan, diagnosa, tindakan, resep)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, 'iiisssss',
            $data['id_pasien'], $data['id_dokter'], $data['id_obat'], $data['tanggal_periksa'],
            $data['keluhan'], $data['diagnosa'], $data['tindakan'], $data['resep']);
        if (mysqli_stmt_execute($stmt)) {
            set_flash('sukses', 'Catatan rekam medis berhasil ditambahkan.');
            header('Location: rekam_medis.php');
            exit;
        }
        $error[] = 'Gagal menyimpan data. Pastikan pasien, dokter, dan obat yang dipilih masih tersedia. (' . mysqli_error($koneksi) . ')';
    }
}

$judul    = 'Tambah Rekam Medis';
$subjudul = 'Catat pemeriksaan baru — pasien, dokter, dan obat dipilih dari database';
require_once 'includes/header.php';
?>

<div class="kartu">
    <div class="kartu-judul">Form Tambah Rekam Medis</div>

    <?php if ($error): ?>
        <div class="error-box"><b>Periksa lagi:</b><ul>
            <?php foreach ($error as $e1): ?><li><?= e($e1) ?></li><?php endforeach; ?>
        </ul></div>
    <?php endif; ?>

    <form method="post" action="">
        <div class="form-grid">
            <div class="form-grup">
                <label>Pasien <span class="wajib">*</span></label>
                <select name="id_pasien" required>
                    <option value="">-- Pilih pasien --</option>
                    <?php while ($p = mysqli_fetch_assoc($daftar_pasien)): ?>
                        <option value="<?= (int) $p['id_pasien'] ?>" <?= (string) $data['id_pasien'] === (string) $p['id_pasien'] ? 'selected' : '' ?>>
                            <?= e($p['no_rm']) ?> — <?= e($p['nama_pasien']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-grup">
                <label>Dokter <span class="wajib">*</span></label>
                <select name="id_dokter" required>
                    <option value="">-- Pilih dokter --</option>
                    <?php while ($d = mysqli_fetch_assoc($daftar_dokter)): ?>
                        <option value="<?= (int) $d['id_dokter'] ?>" <?= (string) $data['id_dokter'] === (string) $d['id_dokter'] ? 'selected' : '' ?>>
                            <?= e($d['nama_dokter']) ?> (<?= e($d['spesialisasi']) ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-grup">
                <label>Obat <span class="wajib">*</span></label>
                <select name="id_obat" required>
                    <option value="">-- Pilih obat --</option>
                    <?php while ($o = mysqli_fetch_assoc($daftar_obat)): ?>
                        <option value="<?= (int) $o['id_obat'] ?>" <?= (string) $data['id_obat'] === (string) $o['id_obat'] ? 'selected' : '' ?>>
                            <?= e($o['nama_obat']) ?> — <?= e($o['satuan']) ?> (stok: <?= (int) $o['stok'] ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-grup">
                <label>Tanggal Periksa <span class="wajib">*</span></label>
                <input type="date" name="tanggal_periksa" max="<?= date('Y-m-d') ?>" value="<?= e($data['tanggal_periksa']) ?>" required>
            </div>
            <div class="form-grup penuh">
                <label>Keluhan <span class="wajib">*</span></label>
                <textarea name="keluhan" placeholder="Keluhan yang disampaikan pasien" required><?= e($data['keluhan']) ?></textarea>
            </div>
            <div class="form-grup penuh">
                <label>Diagnosa <span class="wajib">*</span></label>
                <textarea name="diagnosa" placeholder="Hasil diagnosa dokter" required><?= e($data['diagnosa']) ?></textarea>
            </div>
            <div class="form-grup penuh">
                <label>Tindakan</label>
                <textarea name="tindakan" placeholder="Tindakan pemeriksaan yang dilakukan"><?= e($data['tindakan']) ?></textarea>
            </div>
            <div class="form-grup penuh">
                <label>Resep / Aturan Pakai</label>
                <input type="text" name="resep" maxlength="100" placeholder="cth: 3 x sehari sesudah makan" value="<?= e($data['resep']) ?>">
            </div>
        </div>
        <div class="form-aksi">
            <button class="btn btn-utama" type="submit">💾 Simpan</button>
            <a class="btn btn-abu" href="rekam_medis.php">Batal</a>
        </div>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>
