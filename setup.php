<?php
declare(strict_types=1);

/**
 * GLPI plugin setup file
 * Must NOT contain plugin_init_telegrambot() (it belongs to hook.php)
 */

function plugin_version_telegrambot(): array
{
   return [
      'name'           => 'TelegramBot',
      'version'        => '0.0.0',
      'author'         => 'itsdsmartyua',
      'license'        => 'GPLv3+',
      'homepage'       => '',
      'requirements'   => [
         'glpi' => [
            'min' => '11.0.0',
            'max' => '11.9.9',
         ],
         'php' => [
            'min' => '8.1.0',
         ],
      ],
   ];
}

function plugin_telegrambot_check_prerequisites(): bool
{
   return true;
}

function plugin_telegrambot_check_config(): bool
{
   return true;
}

function plugin_telegrambot_install(): bool
{
   global $DB;

   // DB install (if file exists)
   $install = __DIR__ . '/db/install.sql';
   if (is_readable($install)) {
      $DB->runFile($install);
   }

   // Ensure notifications_telegram flag exists (avoid breaking UI)
   // WARNING: duplicate warning is OK (unicity), doesn't break anything
   $sql = "INSERT IGNORE INTO `glpi_configs` (`context`, `name`, `value`)
           VALUES ('core', 'notifications_telegram', '1')";
   $DB->doQuery($sql);

   return true;
}

function plugin_telegrambot_uninstall(): bool
{
   global $DB;

   $uninstall = __DIR__ . '/db/uninstall.sql';
   if (is_readable($uninstall)) {
      $DB->runFile($uninstall);
   }

   return true;
}
