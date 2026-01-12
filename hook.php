<?php
declare(strict_types=1);

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access this file directly");
}

// GLPI 11: NotificationSetting moved to Glpi\Notification\NotificationSetting
if (!class_exists('NotificationSetting') && class_exists('Glpi\\Notification\\NotificationSetting')) {
   class_alias('Glpi\\Notification\\NotificationSetting', 'NotificationSetting');
}

function plugin_init_telegrambot(): void
{
   global $PLUGIN_HOOKS, $CFG_GLPI;

   $PLUGIN_HOOKS['csrf_compliant']['telegrambot'] = true;

   // Always keep config page accessible
   $PLUGIN_HOOKS['config_page']['telegrambot'] = 'front/notificationwebsocketsetting.form.php';

   // Load plugin classes (defensive)
   $files = [
      __DIR__ . '/inc/bot.class.php',
      __DIR__ . '/inc/fields.class.php',
      __DIR__ . '/inc/cron.class.php',
      __DIR__ . '/inc/notificationwebsocket.class.php',
      __DIR__ . '/inc/notificationeventwebsocket.class.php',
      __DIR__ . '/inc/notificationwebsocketsetting.class.php',
   ];

   foreach ($files as $f) {
      if (is_file($f)) {
         require_once $f;
      } else {
         // don't crash GLPI UI if something is missing
         return;
      }
   }

   if (!(new Plugin())->isActivated('telegrambot')) {
      return;
   }

   /**
    * Register notification MODE so it appears in templates "Режим" dropdown.
    * GLPI 11 still has Notification_NotificationTemplate::registerMode(),
    * but it may not be autoloaded in plugin context => require file explicitly.
    */
   if (!class_exists('Notification_NotificationTemplate')) {
      $core = GLPI_ROOT . '/src/Notification_NotificationTemplate.php';
      if (is_file($core)) {
         require_once $core;
      }
   }

   if (class_exists('Notification_NotificationTemplate')
       && method_exists('Notification_NotificationTemplate', 'registerMode')) {

      Notification_NotificationTemplate::registerMode(
         'telegram',
         __('Telegram', 'telegrambot'),
         'telegrambot'
      );

      // Do not override admin choice, but ensure it is enabled by default in memory
      // (mode list hides disabled modes: $CFG_GLPI['notifications_'.$mode])
      if (!isset($CFG_GLPI['notifications_telegram'])) {
         $CFG_GLPI['notifications_telegram'] = 1;
      }
   }

   // IMPORTANT: do NOT register NotificationSettingConfig here (it broke your UI).
   // Config is managed via plugin config_page.
}
