<?php
declare(strict_types=1);

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access this file directly");
}

/**
 * Register Telegram mode as early as possible (file-level).
 * This guarantees that NotificationSettingConfig / templates dropdown sees it.
 */
global $CFG_GLPI;
if (!isset($CFG_GLPI['notifications_modes']) || !is_array($CFG_GLPI['notifications_modes'])) {
   $CFG_GLPI['notifications_modes'] = [];
}
$CFG_GLPI['notifications_modes']['telegram'] = [
   'label' => __('Telegram', 'telegrambot'),
   'from'  => 'telegrambot',
];

function plugin_init_telegrambot(): void
{
   global $PLUGIN_HOOKS;

   $PLUGIN_HOOKS['csrf_compliant']['telegrambot'] = true;

   // Config page link in plugins list / setup
   $PLUGIN_HOOKS['config_page']['telegrambot'] = 'front/notificationwebsocketsetting.form.php';

   // Load classes (do not break GLPI if something is missing)
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
