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

   // Fallback config page (always works)
   $PLUGIN_HOOKS['config_page']['telegrambot'] = 'front/notificationwebsocketsetting.form.php';

   // Load classes
   require_once __DIR__ . '/inc/bot.class.php';
   require_once __DIR__ . '/inc/fields.class.php';
   require_once __DIR__ . '/inc/cron.class.php';
   require_once __DIR__ . '/inc/notificationwebsocket.class.php';
   require_once __DIR__ . '/inc/notificationeventwebsocket.class.php';
   require_once __DIR__ . '/inc/notificationwebsocketsetting.class.php';

   if (!(new Plugin())->isActivated('telegrambot')) {
      return;
   }

   // 1) Register Telegram as notification mode (standard templates)
   if (class_exists('Notification_NotificationTemplate')) {
      Notification_NotificationTemplate::registerMode(
         'telegram',
         __('Telegram', 'telegrambot'),
         'telegrambot'
      );
   }

   // 2) Register settings UI under Setup > Notifications (if GLPI provides API)
   // Different GLPI builds expose different registration helpers; try safely.

   // (A) Some versions provide NotificationSettingConfig::register($mode, $class)
   if (class_exists('NotificationSettingConfig') && method_exists('NotificationSettingConfig', 'register')) {
      NotificationSettingConfig::register('telegram', PluginTelegrambotNotificationWebsocketSetting::class);

   // (B) Some versions provide Glpi\Notification\NotificationSetting::register($mode, $class)
   } elseif (class_exists('Glpi\\Notification\\NotificationSetting') && method_exists('Glpi\\Notification\\NotificationSetting', 'register')) {
      \Glpi\Notification\NotificationSetting::register('telegram', PluginTelegrambotNotificationWebsocketSetting::class);

   // (C) Some versions provide NotificationSetting::register($mode, $class) via alias above
   } elseif (class_exists('NotificationSetting') && method_exists('NotificationSetting', 'register')) {
      NotificationSetting::register('telegram', PluginTelegrambotNotificationWebsocketSetting::class);
   }

   // 3) Cron tasks
   $PLUGIN_HOOKS['cron']['telegrambot'] = [
      'cronMessagelistener' => [
         'function'   => 'plugin_telegrambot_cronMessagelistener',
         'frequency'  => MINUTE_TIMESTAMP,
         'mode'       => CronTask::MODE_EXTERNAL
      ],
      // lowercase wrapper for GLPI quirks
      'cronmessagelistener' => [
         'function'   => 'plugin_telegrambot_cronmessagelistener',
         'frequency'  => MINUTE_TIMESTAMP,
         'mode'       => CronTask::MODE_EXTERNAL
      ],
   ];
}
