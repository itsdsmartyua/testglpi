<?php
declare(strict_types=1);

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access this file directly");
}

function plugin_init_telegrambot(): void
{
   global $PLUGIN_HOOKS, $CFG_GLPI;

   $PLUGIN_HOOKS['csrf_compliant']['telegrambot'] = true;

   // config page
   $PLUGIN_HOOKS['config_page']['telegrambot'] = 'front/notificationwebsocketsetting.form.php';

   // do not break UI if plugin files are missing
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

   // ---- Register notification MODE safely (no core require) ----
   if (!isset($CFG_GLPI['notifications_modes']) || !is_array($CFG_GLPI['notifications_modes'])) {
      $CFG_GLPI['notifications_modes'] = [];
   }

   // Add / override our mode
   $CFG_GLPI['notifications_modes']['telegram'] = [
      'label' => __('Telegram', 'telegrambot'),
      'from'  => 'telegrambot',
   ];

   // Ensure it is enabled by default (admin can still disable in notifications settings)
   if (!isset($CFG_GLPI['notifications_telegram'])) {
      $CFG_GLPI['notifications_telegram'] = 1;
   }
}
