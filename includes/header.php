<?php
// Template bagian atas: <head> + sidebar navigasi.
// Setiap halaman cukup mengisi variabel $judul sebelum include file ini.
$halaman_aktif = basename($_SERVER['PHP_SELF']);

function nav_aktif($file, $halaman_aktif)
{
    // Halaman tambah/edit ikut menyalakan menu induknya
    $peta = [
        'pasien.php'      => ['pasien.php', 'pasien_tambah.php', 'pasien_edit.php', 'pasien_detail.php'],
        'dokter.php'      => ['dokter.php', 'dokter_tambah.php', 'dokter_edit.php'],
        'obat.php'        => ['obat.php', 'obat_tambah.php', 'obat_edit.php'],
        'rekam_medis.php' => ['rekam_medis.php', 'rekam_tambah.php', 'rekam_edit.php', 'rekam_detail.php'],
    ];
    $grup = $peta[$file] ?? [$file];
    return in_array($halaman_aktif, $grup, true) ? ' class="aktif"' : '';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($judul ?? 'Dashboard') ?> | Rekam Medis</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>✚</text></svg>">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="layout">

    <aside class="sidebar">
        <div class="brand">
            <div class="brand-logo">✚</div>
            <div>
                <div class="brand-nama">MediCare</div>
                <div class="brand-sub">Rekam Medis Digital</div>
            </div>
        </div>

        <nav class="menu">
            <div class="menu-judul">Menu Utama</div>
            <a href="index.php"<?= nav_aktif('index.php', $halaman_aktif) ?>>🏠 <span>Dashboard</span></a>
            <a href="pasien.php"<?= nav_aktif('pasien.php', $halaman_aktif) ?>>🧑‍🤝‍🧑 <span>Data Pasien</span></a>
            <a href="dokter.php"<?= nav_aktif('dokter.php', $halaman_aktif) ?>>🩺 <span>Data Dokter</span></a>
            <a href="obat.php"<?= nav_aktif('obat.php', $halaman_aktif) ?>>💊 <span>Data Obat</span></a>

            <div class="menu-judul">Pemeriksaan</div>
            <a href="rekam_medis.php"<?= nav_aktif('rekam_medis.php', $halaman_aktif) ?>>📋 <span>Rekam Medis</span></a>
        </nav>

        <div class="sidebar-bawah">
            <div class="kartu-info">
                <b>Uji Kompetensi</b><br>
                Pemrograman Web &bull; XII RPL<br>
                SMK Negeri 3 Tuban
            </div>
        </div>
    </aside>

    <main class="konten">
        <header class="topbar">
            <div>
                <h1><?= e($judul ?? 'Dashboard') ?></h1>
                <p class="topbar-sub"><?= e($subjudul ?? 'Aplikasi Rekam Medis Sederhana') ?></p>
            </div>
            <div class="topbar-kanan">
                <span class="badge-tanggal">📅 <?= tgl_indo(date('Y-m-d')) ?></span>
            </div>
        </header>

        <?php tampil_flash(); ?>
