-- Not used directly in v0.1.3 (schema is applied in PHP to handle upgrades safely).
-- Kept as reference.
CREATE TABLE IF NOT EXISTS `glpi_plugin_telegrambot_configs` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(191) NOT NULL,
  `value` LONGTEXT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
