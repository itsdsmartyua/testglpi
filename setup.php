<?php
declare(strict_types=1);

if (!defined('GLPI_ROOT')) {
   die('Sorry. You cannot access this file directly.');
}

define('PLUGIN_TELEGRAMBOT_VERSION', '0.1.0');
define('PLUGIN_TELEGRAMBOT_MIN_GLPI', '11.0.0');
define('PLUGIN_TELEGRAMBOT_MAX_GLPI', '11.99.99');

function plugin_version_telegrambot(): array
{
   return [
      'name'           => 'TelegramBot',
      'version'        => PLUGIN_TELEGRAMBOT_VERSION,
      'author'         => 'itsdsmartyua',
      'license'        => 'GPL-2.0-or-later',
      'homepage'       => 'https://github.com/itsdsmartyua/testglpi',
      'requirements'   => [
         'glpi' => [
            'min' => PLUGIN_TELEGRAMBOT_MIN_GLPI,
            'max' => PLUGIN_TELEGRAMBOT_MAX_GLPI,
         ],
         'php' => [
            'min' => '8.2',
         ],
      ],
   ];
}

function plugin_init_telegrambot(): void
{
   global $PLUGIN_HOOKS, $CFG_GLPI;

   $PLUGIN_HOOKS['csrf_compliant']['telegrambot'] = true;
   $PLUGIN_HOOKS['config_page']['telegrambot'] = 'front/notificationwebsocketsetting.form.php';

   $PLUGIN_HOOKS['cron']['telegrambot'] = [
      'messagelistener' => [
         'frequency' => 1,
         'comment'   => 'Telegram client bot polling (if enabled)',
      ],
   ];

   // Ensure mode exists even if Notification_NotificationTemplate is not loaded yet.
   if (!isset($CFG_GLPI['notifications_modes']) || !is_array($CFG_GLPI['notifications_modes'])) {
      $CFG_GLPI['notifications_modes'] = [];
   }
   $CFG_GLPI['notifications_modes']['telegram'] = [
      'label' => 'Telegram',
      'from'  => 'telegrambot',
   ];

   if (class_exists('Notification_NotificationTemplate', false)
      && method_exists('Notification_NotificationTemplate', 'registerMode')) {
      Notification_NotificationTemplate::registerMode('telegram', __('Telegram', 'telegrambot'), 'telegrambot');
   }

   // Try to register settings class (API differs by GLPI minor versions)
   $settingClass = 'PluginTelegrambotNotificationWebsocketSetting';

   if (!class_exists('NotificationSetting') && class_exists('Glpi\\Notification\\NotificationSetting')) {
      class_alias('Glpi\\Notification\\NotificationSetting', 'NotificationSetting');
   }

   $cfgClassCandidates = ['NotificationSettingConfig', 'Glpi\\Notification\\NotificationSettingConfig'];
   foreach ($cfgClassCandidates as $cfgClass) {
      if (!class_exists($cfgClass, false)) {
         continue;
      }
      foreach (['register', 'registerMode', 'registerSetting', 'registerConfig'] as $m) {
         if (method_exists($cfgClass, $m)) {
            $cfgClass::$m('telegram', $settingClass);
            break 2;
         }
      }
   }
}

function plugin_telegrambot_check_prerequisites(): bool
{
   if (version_compare(GLPI_VERSION, PLUGIN_TELEGRAMBOT_MIN_GLPI, 'lt')
      || version_compare(GLPI_VERSION, PLUGIN_TELEGRAMBOT_MAX_GLPI, 'gt')) {
      echo "This plugin requires GLPI >= " . PLUGIN_TELEGRAMBOT_MIN_GLPI . " and < " . PLUGIN_TELEGRAMBOT_MAX_GLPI;
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

   require_once __DIR__ . '/inc/notificationwebsocketsetting.class.php';
   PluginTelegrambotNotificationWebsocketSetting::ensureOneRow();

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
