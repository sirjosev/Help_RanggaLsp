CREATE TABLE `skema` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) NOT NULL,
  `kode` varchar(50) NOT NULL,
  `jenis` varchar(100) NOT NULL,
  `harga` decimal(10,2) NOT NULL,
  `unit_kompetensi` int(11) NOT NULL,
  `masa_berlaku` int(11) DEFAULT NULL,
  `ringkasan` text DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode` (`kode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `skema` (`id`, `nama`, `kode`, `jenis`, `harga`, `unit_kompetensi`, `masa_berlaku`, `ringkasan`, `gambar`, `created_at`, `updated_at`) VALUES
(1, 'Junior Web Developer', 'JWD-001', 'Okupasi', 500000.00, 10, 3, 'Skema ini untuk calon programmer web junior.', 'dksassets/img/skema_684ae725eaa7c.png', '2025-07-25 01:30:00', '2025-07-25 01:30:00'),
(2, 'Digital Marketing', 'DM-001', 'Klaster', 750000.00, 8, 3, 'Skema ini mencakup berbagai aspek pemasaran digital.', 'dksassets/img/skema_684ae72a02aa0.png', '2025-07-25 01:30:00', '2025-07-25 01:30:00'),
(3, 'Data Scientist', 'DS-001', 'Okupasi', 1000000.00, 12, 3, 'Skema ini untuk para ilmuwan data yang ingin mendapatkan sertifikasi.', 'dksassets/img/skema_684ae81a3f8ed.png', '2025-07-25 01:30:00', '2025-07-25 01:30:00');

CREATE TABLE `unit_kompetensi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `skema_id` int(11) NOT NULL,
  `no_urut` int(11) NOT NULL,
  `kode_unit` varchar(100) NOT NULL,
  `judul_unit` varchar(255) NOT NULL,
  `standar_kompetensi` varchar(255) DEFAULT NULL,
  `lampiran_file` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `skema_id` (`skema_id`),
  CONSTRAINT `unit_kompetensi_ibfk_1` FOREIGN KEY (`skema_id`) REFERENCES `skema` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `persyaratan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `skema_id` int(11) NOT NULL,
  `deskripsi` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `skema_id` (`skema_id`),
  CONSTRAINT `persyaratan_ibfk_1` FOREIGN KEY (`skema_id`) REFERENCES `skema` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `dokumen_persyaratan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `skema_id` int(11) NOT NULL,
  `nama_dokumen` varchar(255) NOT NULL,
  `wajib` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `skema_id` (`skema_id`),
  CONSTRAINT `dokumen_persyaratan_ibfk_1` FOREIGN KEY (`skema_id`) REFERENCES `skema` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `metode_asesmen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `skema_id` int(11) NOT NULL,
  `jenis_peserta` varchar(100) NOT NULL,
  `metode` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `skema_id` (`skema_id`),
  CONSTRAINT `metode_asesmen_ibfk_1` FOREIGN KEY (`skema_id`) REFERENCES `skema` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `pemeliharaan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `skema_id` int(11) NOT NULL,
  `deskripsi` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `skema_id` (`skema_id`),
  CONSTRAINT `pemeliharaan_ibfk_1` FOREIGN KEY (`skema_id`) REFERENCES `skema` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `skema_metode_pengujian` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `skema_id` int(11) NOT NULL,
  `metode_pengujian` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `skema_id` (`skema_id`),
  CONSTRAINT `skema_metode_pengujian_ibfk_1` FOREIGN KEY (`skema_id`) REFERENCES `skema` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
