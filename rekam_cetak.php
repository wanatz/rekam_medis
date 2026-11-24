<?php
require_once 'koneksi.php';

// ============================================================
//  LAPORAN RIWAYAT PEMERIKSAAN (siap cetak / simpan PDF)
//  Data digabung dengan SQL JOIN: pasien + dokter + obat.
// ============================================================
$hasil = mysqli_query($koneksi,
    "SELECT rm.*, p.nama_pasien, p.no_rm, d.nama_dokter, o.nama_obat
     FROM rekam_medis rm
     JOIN pasien p ON p.id_pasien = rm.id_pasien
     JOIN dokter d ON d.id_dokter = rm.id_dokter
     JOIN obat   o ON o.id_obat   = rm.id_obat
     ORDER BY rm.tanggal_periksa ASC, rm.id_rekam ASC");

$total = mysqli_num_rows($hasil);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Riwayat Pemeriksaan</title>
    <style>
        body { font-family: "Segoe UI", Arial, sans-serif; color: #1f2937; margin: 32px; }
        .kop { text-align: center; border-bottom: 3px double #0e7c6b; padding-bottom: 12px; margin-bottom: 20px; }
        .kop h1 { margin: 0; font-size: 22px; color: #0a5c50; }
        .kop p { margin: 4px 0 0; color: #6b7280; font-size: 13px; }
        table { width: 100%; border-collapse: collapse; font-size: 12.5px; }
        th, td { border: 1px solid #cbd5e1; padding: 7px 8px; text-align: left; vertical-align: top; }
        th { background: #e6f4f1; }
        .ttd { margin-top: 36px; text-align: right; font-size: 13px; }
        .no-print { margin-bottom: 16px; }
        @media print { .no-print { display: none; } body { margin: 12px; } }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()">🖨️ Cetak / Simpan PDF</button>
        <a href="rekam_medis.php">← Kembali</a>
    </div>

    <div class="kop">
        <h1>✚ MediCare — Klinik Sehat Sentosa</h1>
        <p>Laporan Riwayat Pemeriksaan Pasien &bull; Dicetak <?= tgl_indo(date('Y-m-d')) ?> &bull; Total <?= $total ?> catatan</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Pasien (No. RM)</th>
                <th>Dokter</th>
                <th>Keluhan</th>
                <th>Diagnosa</th>
                <th>Tindakan</th>
                <th>Obat &amp; Resep</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($total === 0): ?>
            <tr><td colspan="8" style="text-align:center">Belum ada catatan pemeriksaan.</td></tr>
        <?php endif; ?>
        <?php $no = 1; while ($r = mysqli_fetch_assoc($hasil)): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= tgl_indo($r['tanggal_periksa']) ?></td>
                <td><?= e($r['nama_pasien']) ?> (<?= e($r['no_rm']) ?>)</td>
                <td><?= e($r['nama_dokter']) ?></td>
                <td><?= e($r['keluhan']) ?></td>
                <td><?= e($r['diagnosa']) ?></td>
                <td><?= e($r['tindakan']) ?></td>
                <td><?= e($r['nama_obat']) ?> — <?= e($r['resep']) ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <div class="ttd">
        Tuban, <?= tgl_indo(date('Y-m-d')) ?><br>
        Petugas Rekam Medis<br><br><br>
        ( ____________________ )
    </div>

    <script>window.addEventListener('load', function () { setTimeout(function () { window.print(); }, 350); });</script>
</body>
</html>
