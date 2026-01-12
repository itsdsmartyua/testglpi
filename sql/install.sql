-- Minimal config table for TelegramBot plugin
CREATE TABLE IF NOT EXISTS `glpi_plugin_telegrambot_configs` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(191) NOT NULL,
  `value` LONGTEXT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed defaults
INSERT IGNORE INTO `glpi_plugin_telegrambot_configs` (`name`, `value`) VALUES
('bot_token', ''),
('default_chat_id', ''),
('debug', '0');
