DROP TABLE IF EXISTS `t_profil_histori_direktur`;

CREATE TABLE `t_profil_histori_direktur` (
    `id` MEDIUMINT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(150) DEFAULT NULL,
    `awal_menjabat` DATETIME DEFAULT NULL,
    `akhir_menjabat` DATETIME DEFAULT NULL,
    `file` VARCHAR(150) DEFAULT NULL,
    `slug` VARCHAR(150) DEFAULT NULL,
    `description` VARCHAR(255) DEFAULT NULL,
    `channel` VARCHAR(255) DEFAULT NULL,
    `created_by` INT(11) DEFAULT NULL,
    `updated_by` INT(11) DEFAULT NULL,
    `created_at` DATETIME DEFAULT NULL,
    `updated_at` DATETIME DEFAULT NULL,
    `deleted_at` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
