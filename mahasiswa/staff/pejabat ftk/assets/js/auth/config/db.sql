-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versi server:                 8.0.30 - MySQL Community Server - GPL
-- OS Server:                    Win64
-- HeidiSQL Versi:               12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Membuang struktur basisdata untuk db_surat_fakultas
CREATE DATABASE IF NOT EXISTS `db_surat_fakultas` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `db_surat_fakultas`;

-- membuang struktur untuk table db_surat_fakultas.arsip_surat
CREATE TABLE IF NOT EXISTS `arsip_surat` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_surat_final` int NOT NULL,
  `file_surat` varchar(255) NOT NULL,
  `status` enum('arsip','diambil') DEFAULT 'arsip',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_surat_final` (`id_surat_final`),
  CONSTRAINT `arsip_surat_ibfk_1` FOREIGN KEY (`id_surat_final`) REFERENCES `surat_final` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table db_surat_fakultas.draft_surat
CREATE TABLE IF NOT EXISTS `draft_surat` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_mahasiswa` int NOT NULL,
  `template_id` int NOT NULL,
  `file_surat` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lampiran` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_mahasiswa` (`id_mahasiswa`),
  KEY `template_id` (`template_id`),
  CONSTRAINT `draft_surat_ibfk_1` FOREIGN KEY (`id_mahasiswa`) REFERENCES `users` (`id`),
  CONSTRAINT `draft_surat_ibfk_2` FOREIGN KEY (`template_id`) REFERENCES `templates` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table db_surat_fakultas.log_surat
CREATE TABLE IF NOT EXISTS `log_surat` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_surat` int NOT NULL,
  `status` enum('verifikasi','disposisi','ditandatangani','diarsipkan','ditolak') NOT NULL,
  `id_user` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_user` (`id_user`),
  KEY `log_surat_ibfk_1` (`id_surat`),
  CONSTRAINT `log_surat_ibfk_1` FOREIGN KEY (`id_surat`) REFERENCES `surat_masuk` (`id`) ON DELETE CASCADE,
  CONSTRAINT `log_surat_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=94 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table db_surat_fakultas.log_surat_dihapus
CREATE TABLE IF NOT EXISTS `log_surat_dihapus` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_mahasiswa` int DEFAULT NULL,
  `id_log_surat` int DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=91 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table db_surat_fakultas.surat_final
CREATE TABLE IF NOT EXISTS `surat_final` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_staff_tu` int NOT NULL,
  `id_pejabat_fakultas` int NOT NULL,
  `file_surat` varchar(255) NOT NULL,
  `tanda_tangan` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `id_surat` int NOT NULL,
  `is_deleted_staff` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_staff_tu` (`id_staff_tu`),
  KEY `id_pejabat_fakultas` (`id_pejabat_fakultas`),
  CONSTRAINT `surat_final_ibfk_1` FOREIGN KEY (`id_staff_tu`) REFERENCES `users` (`id`),
  CONSTRAINT `surat_final_ibfk_2` FOREIGN KEY (`id_pejabat_fakultas`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table db_surat_fakultas.surat_final_dihapus
CREATE TABLE IF NOT EXISTS `surat_final_dihapus` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_mahasiswa` int NOT NULL,
  `id_surat_final` int NOT NULL,
  `deleted_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unik_mahasiswa_surat` (`id_mahasiswa`,`id_surat_final`),
  KEY `id_surat_final` (`id_surat_final`),
  CONSTRAINT `surat_final_dihapus_ibfk_1` FOREIGN KEY (`id_surat_final`) REFERENCES `surat_final` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table db_surat_fakultas.surat_masuk
CREATE TABLE IF NOT EXISTS `surat_masuk` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_mahasiswa` int NOT NULL,
  `id_staff_tu` int NOT NULL,
  `id_pejabat_fakultas` int DEFAULT NULL,
  `template_id` int DEFAULT NULL,
  `file_surat` varchar(255) NOT NULL,
  `file_cap` varchar(255) DEFAULT NULL,
  `status` enum('disposisi','ditandatangani','ditolak','verifikasi','dikirim') NOT NULL,
  `nomor_surat` varchar(50) DEFAULT NULL,
  `cap_surat` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lampiran` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_mahasiswa` (`id_mahasiswa`),
  KEY `id_staff_tu` (`id_staff_tu`),
  KEY `id_pejabat_fakultas` (`id_pejabat_fakultas`),
  KEY `template_id` (`template_id`),
  CONSTRAINT `surat_masuk_ibfk_1` FOREIGN KEY (`id_mahasiswa`) REFERENCES `users` (`id`),
  CONSTRAINT `surat_masuk_ibfk_2` FOREIGN KEY (`id_staff_tu`) REFERENCES `users` (`id`),
  CONSTRAINT `surat_masuk_ibfk_3` FOREIGN KEY (`id_pejabat_fakultas`) REFERENCES `users` (`id`),
  CONSTRAINT `surat_masuk_ibfk_4` FOREIGN KEY (`template_id`) REFERENCES `templates` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table db_surat_fakultas.templates
CREATE TABLE IF NOT EXISTS `templates` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_template` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `syarat` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Pengeluaran data tidak dipilih.

-- membuang struktur untuk table db_surat_fakultas.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('mahasiswa','staff_tu','pejabat_fakultas') NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Pengeluaran data tidak dipilih.

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
