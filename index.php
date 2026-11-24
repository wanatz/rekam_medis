<?php
require_once 'koneksi.php';

// Dashboard: jumlah data dihitung dengan fungsi agregat SQL COUNT()
function hitung($koneksi, $tabel)
{
    $hasil = mysqli_query($koneksi, "SELECT COUNT(*) AS jumlah FROM $tabel");
    return (int) mysqli_fetch_assoc($hasil)['jumlah'];
}

$jml_pasien = hitung($koneksi, 'pasien');
$jml_dokter = hitung($koneksi, 'dokter');
$jml_obat   = hitung($koneksi, 'obat');
$jml_rekam  = hitung($koneksi, 'rekam_medis');

// 5 riwayat pemeriksaan terbaru (SQL JOIN 3 tabel master)
$terbaru = mysqli_query($koneksi,
    "SELECT rm.id_rekam, rm.tanggal_periksa, rm.diagnosa,
            p.nama_pasien, d.nama_dokter, o.nama_obat
     FROM rekam_medis rm
     JOIN pasien p ON p.id_pasien = rm.id_pasien
     JOIN dokter d ON d.id_dokter = rm.id_dokter
     JOIN obat   o ON o.id_obat   = rm.id_obat
     ORDER BY rm.tanggal_periksa DESC, rm.id_rekam DESC
     LIMIT 5");

// Obat yang stoknya menipis / habis
$stok_tipis = mysqli_query($koneksi,
    "SELECT * FROM obat WHERE stok <= 10 ORDER BY stok ASC LIMIT 5");

// Jumlah pemeriksaan per dokter (agregat COUNT + GROUP BY dengan JOIN)
$per_dokter = mysqli_query($koneksi,
    "SELECT d.nama_dokter, COUNT(rm.id_rekam) AS jumlah
     FROM dokter d
     LEFT JOIN rekam_medis rm ON rm.id_dokter = d.id_dokter
     GROUP BY d.id_dokter, d.nama_dokter
     ORDER BY jumlah DESC");
$grafik = [];
$terbanyak = 1;
while ($g = mysqli_fetch_assoc($per_dokter)) {
    $grafik[] = $g;
    if ((int) $g['jumlah'] > $terbanyak) $terbanyak = (int) $g['jumlah'];
}

$judul    = 'Dashboard';
$subjudul = 'Ringkasan data Aplikasi Rekam Medis';
require_once 'includes/header.php';
?>

<div class="grid-stat">
    <div class="kartu-stat">
        <div class="stat-ikon">🧑‍🤝‍🧑</div>
        <div>
            <div class="stat-angka"><?= $jml_pasien ?></div>
            <div class="stat-label">Total Pasien</div>
            <a class="stat-link" href="pasien.php">Lihat data →</a>
        </div>
    </div>
    <div class="kartu-stat biru">
        <div class="stat-ikon">🩺</div>
        <div>
            <div class="stat-angka"><?= $jml_dokter ?></div>
            <div class="stat-label">Total Dokter</div>
            <a class="stat-link" href="dokter.php">Lihat data →</a>
        </div>
    </div>
    <div class="kartu-stat ungu">
        <div class="stat-ikon">💊</div>
        <div>
            <div class="stat-angka"><?= $jml_obat ?></div>
            <div class="stat-label">Jenis Obat</div>
            <a class="stat-link" href="obat.php">Lihat data →</a>
        </div>
    </div>
    <div class="kartu-stat oranye">
        <div class="stat-ikon">📋</div>
        <div>
            <div class="stat-angka"><?= $jml_rekam ?></div>
            <div class="stat-label">Rekam Medis</div>
            <a class="stat-link" href="rekam_medis.php">Lihat data →</a>
        </div>
    </div>
</div>

<div class="kartu">
    <div class="kartu-judul">Riwayat Pemeriksaan Terbaru
        <small>5 pemeriksaan terakhir — nama pasien, dokter, dan obat ditampilkan dengan SQL JOIN.</small>
    </div>
    <div class="tabel-bungkus">
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Pasien</th>
                    <th>Dokter</th>
                    <th>Diagnosa</th>
                    <th>Obat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (mysqli_num_rows($terbaru) === 0): ?>
                <tr><td colspan="6" class="kosong">Belum ada catatan pemeriksaan. Tambahkan lewat menu Rekam Medis.</td></tr>
            <?php endif; ?>
            <?php while ($r = mysqli_fetch_assoc($terbaru)): ?>
                <tr>
                    <td><?= tgl_indo($r['tanggal_periksa']) ?></td>
                    <td><b><?= e($r['nama_pasien']) ?></b></td>
                    <td><?= e($r['nama_dokter']) ?></td>
                    <td><?= e($r['diagnosa']) ?></td>
                    <td><?= e($r['nama_obat']) ?></td>
                    <td><a class="btn btn-kecil btn-detail" href="rekam_detail.php?id=<?= (int) $r['id_rekam'] ?>">👁️ Detail</a></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="kartu">
    <div class="kartu-judul">Pemeriksaan per Dokter
        <small>Jumlah pemeriksaan yang ditangani setiap dokter (SQL COUNT + GROUP BY).</small>
    </div>
    <?php if (!$grafik): ?>
        <p class="kosong">Belum ada data dokter.</p>
    <?php endif; ?>
    <?php foreach ($grafik as $g): ?>
        <div class="grafik-baris">
            <div class="grafik-label"><?= e($g['nama_dokter']) ?></div>
            <div class="grafik-track">
                <div class="grafik-isi" style="width: <?= max(4, round(((int) $g['jumlah'] / $terbanyak) * 100)) ?>%"></div>
            </div>
            <div class="grafik-angka"><?= (int) $g['jumlah'] ?></div>
        </div>
    <?php endforeach; ?>
</div>

<div class="kartu">
    <div class="kartu-judul">Stok Obat Perlu Perhatian
        <small>Obat dengan stok 10 atau kurang, agar segera diisi ulang.</small>
    </div>
    <div class="tabel-bungkus">
        <table>
            <thead>
                <tr><th>Nama Obat</th><th>Satuan</th><th>Stok</th><th>Status</th></tr>
            </thead>
            <tbody>
            <?php if (mysqli_num_rows($stok_tipis) === 0): ?>
                <tr><td colspan="4" class="kosong">Semua stok obat masih aman. 👍</td></tr>
            <?php endif; ?>
            <?php while ($o = mysqli_fetch_assoc($stok_tipis)): ?>
                <tr>
                    <td><b><?= e($o['nama_obat']) ?></b></td>
                    <td><?= e($o['satuan']) ?></td>
                    <td><?= (int) $o['stok'] ?></td>
                    <td>
                        <?php if ((int) $o['stok'] <= 0): ?>
                            <span class="badge badge-stok-habis">Habis</span>
                        <?php else: ?>
                            <span class="badge badge-stok-tipis">Menipis</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
