<?php
declare(strict_types=1);

function plugin_init_telegrambot(): void
{
   global $PLUGIN_HOOKS;

   $PLUGIN_HOOKS['csrf_compliant']['telegrambot'] = true;

   // link from plugins page to our config form
   $PLUGIN_HOOKS['config_page']['telegrambot'] = 'front/notificationtelegrambotsetting.form.php';

   // Ensure GLPI can load our legacy classes (inc/)
   require_once __DIR__ . '/inc/notificationtelegrambot.class.php';
   require_once __DIR__ . '/inc/notificationtelegrambotsetting.class.php';

   // Register notification mode for Templates/Notifications UI
   $plugin = new Plugin();
   if ($plugin->isActivated('telegrambot')) {
      Notification_NotificationTemplate::registerMode(
         'telegram',
         __('Telegram', 'telegrambot'),
         'telegrambot'
      );
   }
}

function plugin_telegrambot_install(): bool
{
   Config::setConfigurationValues('core', [
      'notifications_telegrambot' => 0,
   ]);

   Config::setConfigurationValues('plugin:telegrambot', [
      'bot_token_out'     => '',
      'parse_mode'        => 'HTML',

      // Fields(User) mapping
      'fields_container'  => 'telegram',
      'field_chat_id'     => 'tg_chat_id_notify',
      'field_enabled'     => 'tg_enabled',
      'field_out_enabled' => 'tg_bot_out_enabled',

      'debug'             => 0,
   ]);

   return true;
}

function plugin_telegrambot_update($current_version = null): bool
{
   if (Config::getConfigurationValue('core', 'notifications_telegrambot') === null) {
      Config::setConfigurationValues('core', ['notifications_telegrambot' => 0]);
   }

   $defaults = [
      'bot_token_out'     => '',
      'parse_mode'        => 'HTML',
      'fields_container'  => 'telegram',
      'field_chat_id'     => 'tg_chat_id_notify',
      'field_enabled'     => 'tg_enabled',
      'field_out_enabled' => 'tg_bot_out_enabled',
      'debug'             => 0,
   ];

   foreach ($defaults as $k => $v) {
      if (Config::getConfigurationValue('plugin:telegrambot', $k) === null) {
         Config::setConfigurationValues('plugin:telegrambot', [$k => $v]);
      }
   }

   return true;
}

function plugin_telegrambot_uninstall(): bool
{
   $config = new Config();

   $config->deleteConfigurationValues('core', [
      'notifications_telegrambot',
   ]);

   $config->deleteConfigurationValues('plugin:telegrambot', [
      'bot_token_out',
      'parse_mode',
      'fields_container',
      'field_chat_id',
      'field_enabled',
      'field_out_enabled',
      'debug',
   ]);

   return true;
}
