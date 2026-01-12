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
   global $PLUGIN_HOOKS;

   $PLUGIN_HOOKS['csrf_compliant']['telegrambot'] = true;

   // ALWAYS keep plugin config accessible from Plugins list
   $PLUGIN_HOOKS['config_page']['telegrambot'] = 'front/notificationwebsocketsetting.form.php';

   // Load classes defensively (if any missing -> do not break GLPI UI)
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
         // don't crash GLPI if file missing
         return;
      }
   }

   // If plugin is not activated - stop here
   if (!class_exists('Plugin') || !(new Plugin())->isActivated('telegrambot')) {
      return;
   }

   // Register Telegram notification mode (templates). Do it only if API exists.
   if (class_exists('Notification_NotificationTemplate')
       && method_exists('Notification_NotificationTemplate', 'registerMode')) {
      Notification_NotificationTemplate::registerMode(
         'telegram',
         __('Telegram', 'telegrambot'),
         'telegrambot'
      );
   }

   // IMPORTANT:
   // We DO NOT register settings inside Setup > Notifications here,
   // because your GLPI build doesn't have stable register API and it breaks UI.
   // Config remains available via plugin config_page.
}
