-- Drop the table if it exists
DROP TABLE IF EXISTS `t_peraturan`;

-- Create the table
CREATE TABLE `t_peraturan` (
    `id` MEDIUMINT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(150) DEFAULT NULL,
    `category` VARCHAR(150) DEFAULT NULL,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
