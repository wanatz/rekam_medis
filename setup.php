<?php
// ============================================================
//  SETUP DATABASE OTOMATIS
//  Membuat database db_rekam_medis, keempat tabel, relasi FK,
//  dan data uji fiktif - cukup dengan membuka halaman ini.
//
//  Cara pakai:
//  1. Pastikan MySQL/MariaDB aktif & config.php sudah benar.
//  2. Buka setup.php di browser, klik "Buat Database Sekarang".
//  Selesai - aplikasi siap dipakai. Halaman ini aman dibuka
//  berulang kali; data yang sudah ada tidak akan tertimpa
//  kecuali tombol Reset yang dipilih.
// ============================================================

require_once __DIR__ . '/config.php';

// Mode error mysqli klasik (cek manual lewat return value),
// agar tidak berubah jadi exception seperti default PHP 8.1+.
mysqli_report(MYSQLI_REPORT_OFF);

$pesan  = '';
$gagal  = false;
$status = null; // ringkasan isi database bila sudah terpasang

$server = @mysqli_connect($db_host, $db_user, $db_pass);
if (!$server) {
    $gagal = true;
    $pesan = 'Tidak dapat terhubung ke server MySQL/MariaDB. Pastikan server database aktif '
           . 'dan user/password pada config.php benar. Detail: ' . mysqli_connect_error();
}

// Jalankan seluruh isi file .sql (CREATE DATABASE + tabel + data uji)
function jalankan_sql($server, $file_sql)
{
    $sql = file_get_contents($file_sql);
    if ($sql === false) return 'File db_rekam_medis.sql tidak ditemukan.';
    if (!mysqli_multi_query($server, $sql)) {
        return 'Gagal menjalankan SQL: ' . mysqli_error($server);
    }
    // multi_query wajib dibaca sampai habis agar tidak ada sisa hasil
    do {
        if ($hasil = mysqli_store_result($server)) {
            mysqli_free_result($hasil);
        }
        if (mysqli_errno($server)) {
            return 'Gagal menjalankan SQL: ' . mysqli_error($server);
        }
    } while (mysqli_next_result($server));
    return null; // null = berhasil
}

function ringkasan($server, $db_name)
{
    if (!@mysqli_select_db($server, $db_name)) return null;
    $ringkas = [];
    foreach (['pasien', 'dokter', 'obat', 'rekam_medis'] as $tabel) {
        $h = @mysqli_query($server, "SELECT COUNT(*) AS j FROM `$tabel`");
        if (!$h) return null;
        $ringkas[$tabel] = (int) mysqli_fetch_assoc($h)['j'];
    }
    return $ringkas;
}

if ($server && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = $_POST['aksi'] ?? '';
    if ($aksi === 'install') {
        if (ringkasan($server, $db_name) !== null) {
            $pesan = 'Database sudah terpasang sebelumnya, tidak ada yang diubah.';
        } else {
            $err = jalankan_sql($server, __DIR__ . '/db_rekam_medis.sql');
            if ($err === null) {
                $pesan = 'Berhasil! Database, tabel, relasi, dan data uji sudah dibuat.';
            } else {
                $gagal = true;
                $pesan = $err;
            }
        }
    } elseif ($aksi === 'reset') {
        // Reset = hapus database lalu buat ulang dari nol
        mysqli_query($server, "DROP DATABASE IF EXISTS `" . mysqli_real_escape_string($server, $db_name) . "`");
        $err = jalankan_sql($server, __DIR__ . '/db_rekam_medis.sql');
        if ($err === null) {
            $pesan = 'Reset berhasil! Database dibuat ulang dari awal beserta data uji.';
        } else {
            $gagal = true;
            $pesan = $err;
        }
    }
}

if ($server) {
    $status = ringkasan($server, $db_name);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Database | Rekam Medis</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .setup-bungkus { max-width: 640px; margin: 48px auto; padding: 0 16px; }
        .setup-logo { font-size: 40px; text-align: center; }
        .setup-judul { text-align: center; margin-bottom: 18px; }
    </style>
</head>
<body>
<div class="setup-bungkus">
    <div class="setup-logo">✚</div>
    <div class="setup-judul">
        <h1>Setup Aplikasi Rekam Medis</h1>
        <p class="topbar-sub">Membuat database <b><?= htmlspecialchars($db_name) ?></b> secara otomatis</p>
    </div>

    <?php if ($pesan !== ''): ?>
        <div class="alert alert-<?= $gagal ? 'gagal' : 'sukses' ?>"><?= $gagal ? '⚠️' : '✅' ?> <?= htmlspecialchars($pesan) ?></div>
    <?php endif; ?>

    <div class="kartu">
        <?php if ($status !== null): ?>
            <div class="kartu-judul">Database sudah terpasang ✅</div>
            <table>
                <tbody>
                    <tr><td>Data pasien</td><td><b><?= $status['pasien'] ?></b></td></tr>
                    <tr><td>Data dokter</td><td><b><?= $status['dokter'] ?></b></td></tr>
                    <tr><td>Data obat</td><td><b><?= $status['obat'] ?></b></td></tr>
                    <tr><td>Catatan rekam medis</td><td><b><?= $status['rekam_medis'] ?></b></td></tr>
                </tbody>
            </table>
            <div class="form-aksi">
                <a class="btn btn-utama" href="index.php">🚀 Buka Aplikasi</a>
                <form method="post" action="" onsubmit="return confirm('Reset akan MENGHAPUS semua data dan membuat ulang database dari awal. Lanjutkan?')">
                    <input type="hidden" name="aksi" value="reset">
                    <button class="btn btn-abu" type="submit">↺ Reset Database</button>
                </form>
            </div>
        <?php elseif (!$gagal): ?>
            <div class="kartu-judul">Database belum dibuat</div>
            <p style="color:var(--teks-redup);font-size:14.5px;line-height:1.7">
                Klik tombol di bawah: database <b><?= htmlspecialchars($db_name) ?></b> akan dibuat
                lengkap dengan 4 tabel (pasien, dokter, obat, rekam_medis), relasi foreign key,
                dan data uji fiktif. Cara ini sama hasilnya dengan import
                <b>db_rekam_medis.sql</b> lewat phpMyAdmin.
            </p>
            <form method="post" action="">
                <input type="hidden" name="aksi" value="install">
                <div class="form-aksi">
                    <button class="btn btn-utama" type="submit">🛠️ Buat Database Sekarang</button>
                </div>
            </form>
        <?php else: ?>
            <div class="kartu-judul">Periksa config.php</div>
            <p style="color:var(--teks-redup);font-size:14.5px;line-height:1.7">
                Koneksi ke server database gagal. Pastikan MySQL/MariaDB aktif, lalu sesuaikan
                <b>$db_host</b>, <b>$db_user</b>, dan <b>$db_pass</b> pada file <b>config.php</b>
                dengan server yang dipakai, kemudian muat ulang halaman ini.
            </p>
        <?php endif; ?>
    </div>

    <footer class="footer">Aplikasi Rekam Medis Sederhana — data contoh adalah data fiktif.</footer>
</div>
</body>
</html>
