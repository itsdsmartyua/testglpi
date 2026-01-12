<?php
declare(strict_types=1);

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access this file directly");
}

function plugin_init_telegrambot(): void
{
   global $PLUGIN_HOOKS, $CFG_GLPI;

   $PLUGIN_HOOKS['csrf_compliant']['telegrambot'] = true;
   $PLUGIN_HOOKS['config_page']['telegrambot']    = 'front/notificationwebsocketsetting.form.php';

   // Stop here if plugin is not active
   if (!(new Plugin())->isActivated('telegrambot')) {
      return;
   }

   /**
    * 1) Register notification MODE (must happen even if plugin classes are broken/missing).
    *    This is what makes "Telegram" appear as a mode in templates.
    */
   if (class_exists('Notification_NotificationTemplate')
       && method_exists('Notification_NotificationTemplate', 'registerMode')) {

      Notification_NotificationTemplate::registerMode(
         'telegram',
         __('Telegram', 'telegrambot'),
         'telegrambot'
      );
   } else {
      // Fallback (should not be needed, but safe)
      if (!isset($CFG_GLPI['notifications_modes']) || !is_array($CFG_GLPI['notifications_modes'])) {
         $CFG_GLPI['notifications_modes'] = [];
      }
      $CFG_GLPI['notifications_modes']['telegram'] = [
         'label' => __('Telegram', 'telegrambot'),
         'from'  => 'telegrambot',
      ];
   }

   /**
    * 2) Ensure mode is enabled.
    *    UI hides modes that are not enabled via $CFG_GLPI['notifications_<mode>'].
    *    We persist it once in DB (core config), so it survives cache/session.
    */
   if (!isset($CFG_GLPI['notifications_telegram'])) {
      $CFG_GLPI['notifications_telegram'] = 1;

      // Persist only once per session, and only if user can update config
      if (Session::getLoginUserID()
          && Session::haveRight('config', UPDATE)
          && empty($_SESSION['plugin_telegrambot_notif_bootstrap_done'])) {

         $_SESSION['plugin_telegrambot_notif_bootstrap_done'] = 1;

         if (class_exists('Config') && method_exists('Config', 'setConfigurationValues')) {
            Config::setConfigurationValues('core', [
               'notifications_telegram' => 1
            ]);
         }
      }
   }

   /**
    * 3) Now load plugin classes (optional for mode listing).
    *    Do NOT break GLPI UI if any file is missing.
    */
   $optional = [
      __DIR__ . '/inc/bot.class.php',
      __DIR__ . '/inc/fields.class.php',
      __DIR__ . '/inc/notificationwebsocket.class.php',
      __DIR__ . '/inc/notificationeventwebsocket.class.php',
      __DIR__ . '/inc/notificationwebsocketsetting.class.php',
      __DIR__ . '/inc/cron.class.php',
   ];
   foreach ($optional as $f) {
      if (is_file($f)) {
         require_once $f;
      }
   }
}
