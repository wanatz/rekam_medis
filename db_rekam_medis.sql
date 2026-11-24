-- ============================================================
--  Database Aplikasi Rekam Medis Sederhana
--  Uji Kompetensi Pemrograman Web - XII RPL - SMK Negeri 3 Tuban
--  Cara pakai: buka phpMyAdmin -> Import -> pilih file ini -> Go
--  Semua data contoh adalah data FIKTIF untuk pembelajaran.
-- ============================================================

CREATE DATABASE IF NOT EXISTS db_rekam_medis
    CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

USE db_rekam_medis;

-- ------------------------------------------------------------
--  Tabel pasien (tabel master)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS pasien (
    id_pasien     INT          NOT NULL AUTO_INCREMENT,
    no_rm         VARCHAR(20)  NOT NULL,
    nama_pasien   VARCHAR(100) NOT NULL,
    jenis_kelamin ENUM('L','P') NOT NULL,
    tanggal_lahir DATE         NOT NULL,
    alamat        TEXT,
    PRIMARY KEY (id_pasien),
    UNIQUE KEY uq_pasien_no_rm (no_rm)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
--  Tabel dokter (tabel master)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS dokter (
    id_dokter    INT          NOT NULL AUTO_INCREMENT,
    nama_dokter  VARCHAR(100) NOT NULL,
    spesialisasi VARCHAR(50)  NOT NULL,
    PRIMARY KEY (id_dokter)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
--  Tabel obat (tabel master)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS obat (
    id_obat   INT          NOT NULL AUTO_INCREMENT,
    nama_obat VARCHAR(100) NOT NULL,
    satuan    VARCHAR(30)  NOT NULL,
    stok      INT          NOT NULL DEFAULT 0,
    PRIMARY KEY (id_obat)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
--  Tabel rekam_medis (tabel transaksi)
--  Menghubungkan pasien, dokter, dan obat melalui Foreign Key.
--  ON DELETE RESTRICT: data induk yang sudah dipakai di sini
--  tidak dapat dihapus (menjaga integritas data).
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS rekam_medis (
    id_rekam        INT          NOT NULL AUTO_INCREMENT,
    id_pasien       INT          NOT NULL,
    id_dokter       INT          NOT NULL,
    id_obat         INT          NOT NULL,
    tanggal_periksa DATE         NOT NULL,
    keluhan         TEXT,
    diagnosa        TEXT,
    tindakan        TEXT,
    resep           VARCHAR(100),
    PRIMARY KEY (id_rekam),
    CONSTRAINT fk_rekam_pasien
        FOREIGN KEY (id_pasien) REFERENCES pasien (id_pasien)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_rekam_dokter
        FOREIGN KEY (id_dokter) REFERENCES dokter (id_dokter)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_rekam_obat
        FOREIGN KEY (id_obat) REFERENCES obat (id_obat)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ============================================================
--  DATA UJI (fiktif) - minimal sesuai LKPD terpenuhi:
--  5 pasien, 3 dokter, 5 obat, 4 rekam medis
-- ============================================================

INSERT INTO pasien (id_pasien, no_rm, nama_pasien, jenis_kelamin, tanggal_lahir, alamat) VALUES
(1, 'RM-001', 'Andi Pratama',    'L', '2000-05-14', 'Jl. Merdeka No. 10, Tuban'),
(2, 'RM-002', 'Siti Rahmawati',  'P', '1998-09-02', 'Jl. Kenanga No. 25, Tuban'),
(3, 'RM-003', 'Budi Santoso',    'L', '1985-01-30', 'Jl. Melati No. 7, Tuban'),
(4, 'RM-004', 'Dewi Lestari',    'P', '2010-12-19', 'Jl. Mawar No. 3, Tuban'),
(5, 'RM-005', 'Rina Anggraini',  'P', '1995-07-08', 'Jl. Flamboyan No. 18, Tuban');

INSERT INTO dokter (id_dokter, nama_dokter, spesialisasi) VALUES
(1, 'dr. Sari Wulandari',  'Umum'),
(2, 'dr. Agus Hartono',    'Penyakit Dalam'),
(3, 'dr. Maya Kusuma',     'Anak');

INSERT INTO obat (id_obat, nama_obat, satuan, stok) VALUES
(1, 'Paracetamol 500 mg', 'Tablet', 120),
(2, 'Amoxicillin 500 mg', 'Kapsul', 80),
(3, 'OBH Combi 100 ml',   'Sirup',  8),
(4, 'Cetirizine 10 mg',   'Tablet', 45),
(5, 'Betadine 30 ml',     'Tetes',  0);

INSERT INTO rekam_medis
    (id_pasien, id_dokter, id_obat, tanggal_periksa, keluhan, diagnosa, tindakan, resep) VALUES
(1, 1, 1, '2026-11-03', 'Demam dan sakit kepala sejak 2 hari', 'Demam / febris',
 'Pemeriksaan suhu tubuh (38,5°C), anamnesis, dan pemberian obat penurun panas', '3 x sehari sesudah makan'),
(2, 2, 2, '2026-11-04', 'Batuk berdahak dan tenggorokan sakit', 'Infeksi saluran pernapasan atas (ISPA)',
 'Pemeriksaan fisik tenggorokan dan paru, pemberian antibiotik', '3 x sehari, dihabiskan'),
(4, 3, 3, '2026-11-06', 'Batuk kering dan pilek', 'Common cold (flu biasa)',
 'Pemeriksaan umum anak, pemberian obat batuk sirup', '3 x 1 sendok teh sehari'),
(3, 2, 4, '2026-11-08', 'Gatal-gatal dan bentol di kulit setelah makan seafood', 'Alergi makanan',
 'Pemeriksaan kulit, pemberian antihistamin, edukasi pantang makanan pemicu', '1 x sehari sebelum tidur');
