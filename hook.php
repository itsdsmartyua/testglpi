<?php
declare(strict_types=1);

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access this file directly");
}

function plugin_init_telegrambot(): void
{
   global $PLUGIN_HOOKS;

   $PLUGIN_HOOKS['csrf_compliant']['telegrambot'] = true;
   $PLUGIN_HOOKS['config_page']['telegrambot']    = 'front/notificationwebsocketsetting.form.php';

   // Load plugin classes (do not crash GLPI if something missing)
   $need = [
      __DIR__ . '/inc/bot.class.php',
      __DIR__ . '/inc/fields.class.php',
      __DIR__ . '/inc/notificationwebsocket.class.php',
      __DIR__ . '/inc/notificationeventwebsocket.class.php',
      __DIR__ . '/inc/notificationwebsocketsetting.class.php',
      __DIR__ . '/inc/cron.class.php',
   ];
   foreach ($need as $f) {
      if (!is_file($f)) {
         return;
      }
      require_once $f;
   }

   if (!(new Plugin())->isActivated('telegrambot')) {
      return;
   }

   // GLPI 11 namespace alias (for your setting class)
   if (!class_exists('NotificationSetting') && class_exists('Glpi\\Notification\\NotificationSetting')) {
      class_alias('Glpi\\Notification\\NotificationSetting', 'NotificationSetting');
   }

   // 1) Register mode (so it can exist in modes list)
   if (class_exists('Notification_NotificationTemplate')
       && method_exists('Notification_NotificationTemplate', 'registerMode')) {

      Notification_NotificationTemplate::registerMode(
         'telegram',
         __('Telegram', 'telegrambot'),
         'telegrambot'
      );
   }

   // 2) Ensure mode is enabled in config (otherwise it won't appear in dropdown)
   if (class_exists('Config') && method_exists('Config', 'setConfigurationValues')) {
      // Do not force-disable/override admin choice, only ensure it exists once
      if (!isset($_SESSION['plugin_telegrambot_notifications_bootstrapped'])) {
         $_SESSION['plugin_telegrambot_notifications_bootstrapped'] = 1;
         Config::setConfigurationValues('core', [
            'notifications_telegram' => 1
         ]);
      }
   }
}
