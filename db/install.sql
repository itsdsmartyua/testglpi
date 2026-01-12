CREATE TABLE IF NOT EXISTS `glpi_plugin_telegrambot_configs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `notification_bot_token` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_bot_token` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_chat_field` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'telegram_chat_id',
  `user_topic_field` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'telegram_topic_id',
  `group_chat_field` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'telegram_chat_id',
  `group_topic_field` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'telegram_topic_id',
  `client_user_chat_field` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `client_user_topic_field` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `client_group_chat_field` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `client_group_topic_field` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `client_last_update_id` bigint unsigned NOT NULL DEFAULT 0,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `glpi_configs` (`context`, `name`, `value`)
VALUES ('core', 'notifications_telegram', '1');
