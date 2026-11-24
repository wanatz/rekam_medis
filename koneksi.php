<?php
// ============================================================
//  KONEKSI DATABASE - Aplikasi Rekam Medis Sederhana
//  Database : db_rekam_medis (MySQL / MariaDB)
//  Pengaturan server ada di file config.php
// ============================================================

require_once __DIR__ . '/config.php';

// Mode error mysqli klasik (cek manual lewat return value).
// Wajib dipanggil sebelum koneksi: sejak PHP 8.1 mysqli melempar
// exception secara default bila baris ini tidak ada.
mysqli_report(MYSQLI_REPORT_OFF);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$koneksi = @mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$koneksi) {
    $belum_dibuat = mysqli_connect_errno() === 1049; // 1049 = database belum ada
    $saran = $belum_dibuat
        ? "<p>Database <b>" . htmlspecialchars($db_name) . "</b> belum dibuat. " .
          "Buka halaman <a href='setup.php'><b>setup.php</b></a> untuk membuatnya otomatis, " .
          "atau import file <b>db_rekam_medis.sql</b> lewat phpMyAdmin.</p>"
        : "<p>Pastikan MySQL/MariaDB sudah berjalan, lalu periksa user &amp; password " .
          "di file <b>config.php</b>.</p>";
    die(
        "<div style='font-family:sans-serif;max-width:640px;margin:60px auto;padding:24px;" .
        "border:1px solid #f3b4b4;background:#fff5f5;border-radius:12px;color:#7a1f1f'>" .
        "<h2 style='margin-top:0'>Koneksi database gagal</h2>" . $saran .
        "<p style='color:#a33'>Detail: " . htmlspecialchars(mysqli_connect_error()) . "</p></div>"
    );
}

mysqli_set_charset($koneksi, "utf8mb4");

// ------------------------------------------------------------
//  Helper kecil yang dipakai di semua halaman
// ------------------------------------------------------------

// Escape output agar aman dari XSS
function e($nilai)
{
    return htmlspecialchars((string) $nilai, ENT_QUOTES, 'UTF-8');
}

// Simpan pesan flash (berhasil/gagal) untuk ditampilkan sekali
function set_flash($tipe, $pesan)
{
    $_SESSION['flash'] = ['tipe' => $tipe, 'pesan' => $pesan];
}

// Ambil & tampilkan pesan flash (dipanggil di header)
function tampil_flash()
{
    if (!empty($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        $ikon = $f['tipe'] === 'sukses' ? '✅' : '⚠️';
        echo '<div class="alert alert-' . e($f['tipe']) . '">' . $ikon . ' ' . e($f['pesan']) . '</div>';
    }
}

// Format tanggal Indonesia: 2026-01-05 -> 05 Januari 2026
function tgl_indo($tanggal)
{
    if (!$tanggal) return '-';
    $bulan = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
              'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $pecah = explode('-', $tanggal);
    if (count($pecah) !== 3) return $tanggal;
    return (int) $pecah[2] . ' ' . $bulan[(int) $pecah[1]] . ' ' . $pecah[0];
}

// Hitung umur dari tanggal lahir
function hitung_umur($tanggal_lahir)
{
    if (!$tanggal_lahir) return '-';
    $lahir = new DateTime($tanggal_lahir);
    $umur  = $lahir->diff(new DateTime('today'))->y;
    return $umur . ' th';
}
