<?php
declare(strict_types=1);

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access this file directly");
}

define('PLUGIN_TELEGRAMBOT_VERSION', '11.0.4-1');
define('PLUGIN_TELEGRAMBOT_MIN_GLPI', '11.0.4');
define('PLUGIN_TELEGRAMBOT_MAX_GLPI', '11.99.99');

function plugin_version_telegrambot(): array
{
   return [
      'name'           => 'TelegramBot',
      'version'        => PLUGIN_TELEGRAMBOT_VERSION,
      'author'         => 'Your Team',
      'license'        => 'MIT',
      'homepage'       => '',
      'requirements'   => [
         'glpi' => [
            'min' => PLUGIN_TELEGRAMBOT_MIN_GLPI,
            'max' => PLUGIN_TELEGRAMBOT_MAX_GLPI,
         ],
         'php'  => [
            'min' => '8.2.0',
         ],
      ],
   ];
}

function plugin_telegrambot_check_prerequisites(): bool
{
   if (version_compare(GLPI_VERSION, PLUGIN_TELEGRAMBOT_MIN_GLPI, '<')) {
      echo "This plugin requires GLPI >= " . PLUGIN_TELEGRAMBOT_MIN_GLPI;
      return false;
   }
   if (version_compare(GLPI_VERSION, PLUGIN_TELEGRAMBOT_MAX_GLPI, '>')) {
      echo "This plugin is not compatible with GLPI > " . PLUGIN_TELEGRAMBOT_MAX_GLPI;
      return false;
   }
   return true;
}

function plugin_telegrambot_check_config(): bool
{
   return true;
}

function plugin_telegrambot_install(): bool
{
   global $DB;

   $sql = __DIR__ . '/db/install.sql';
   if (is_readable($sql)) {
      $DB->runFile($sql);
   }

   // create default config row if not exists
   require_once __DIR__ . '/inc/bot.class.php';
   PluginTelegrambotBot::ensureDefaultConfig();

   return true;
}

function plugin_telegrambot_uninstall(): bool
{
   global $DB;

   $sql = __DIR__ . '/db/uninstall.sql';
   if (is_readable($sql)) {
      $DB->runFile($sql);
   }
   return true;
}
