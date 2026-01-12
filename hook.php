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

   // Config page (we expose settings UI from plugin menu)
   $PLUGIN_HOOKS['config_page']['telegrambot'] = 'front/notificationwebsocketsetting.form.php';

   // Required classes
   require_once __DIR__ . '/inc/bot.class.php';
   require_once __DIR__ . '/inc/fields.class.php';
   require_once __DIR__ . '/inc/cron.class.php';
   require_once __DIR__ . '/inc/notificationwebsocket.class.php';
   require_once __DIR__ . '/inc/notificationeventwebsocket.class.php';
   require_once __DIR__ . '/inc/notificationwebsocketsetting.class.php';

   if ((new Plugin())->isActivated('telegrambot')) {

      // Register Telegram as notification mode (standard templates)
      Notification_NotificationTemplate::registerMode(
         'telegram',
         __('Telegram', 'telegrambot'),
         'telegrambot'
      );

      // Cron
      $PLUGIN_HOOKS['cron']['telegrambot'] = [
         'cronMessagelistener' => [
            'function'   => 'plugin_telegrambot_cronMessagelistener',
            'frequency'  => MINUTE_TIMESTAMP,
            'mode'       => CronTask::MODE_EXTERNAL
         ],
         // lowercase wrapper
         'cronmessagelistener' => [
            'function'   => 'plugin_telegrambot_cronmessagelistener',
            'frequency'  => MINUTE_TIMESTAMP,
            'mode'       => CronTask::MODE_EXTERNAL
         ],
      ];
   }
}
