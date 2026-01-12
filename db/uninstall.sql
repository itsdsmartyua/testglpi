DROP TABLE IF EXISTS `glpi_plugin_telegrambot_configs`;
DELETE FROM `glpi_configs` WHERE `context`='core' AND `name`='notifications_telegram';
