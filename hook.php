<?php
declare(strict_types=1);

function plugin_init_telegrambot(): void
{
   global $PLUGIN_HOOKS;

   $PLUGIN_HOOKS['csrf_compliant']['telegrambot'] = true;
   $PLUGIN_HOOKS['config_page']['telegrambot'] = 'front/notificationtelegrambotsetting.form.php';

   // legacy classes
   require_once __DIR__ . '/inc/notificationtelegrambot.class.php';
   require_once __DIR__ . '/inc/notificationtelegrambotsetting.class.php';

   if ((new Plugin())->isActivated('telegrambot')) {

      // 1) register notification "mode" so it appears in templates
      Notification_NotificationTemplate::registerMode(
         'telegram',
         __('Telegram', 'telegrambot'),
         'telegrambot'
      );

      // 2) register setting class so GLPI shows it in UI
      NotificationSettingConfig::register(
         'telegram',
         PluginTelegrambotNotificationTelegrambotSetting::class
      );
   }
}

function plugin_telegrambot_install(): bool
{
   // Enable/disable flag for this mode
   Config::setConfigurationValues('core', [
      'notifications_telegram' => 0,
   ]);

   // Plugin config
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
   // Ensure core flag exists
   if (Config::getConfigurationValue('core', 'notifications_telegram') === null) {
      Config::setConfigurationValues('core', ['notifications_telegram' => 0]);
   }

   // Ensure plugin defaults exist without overwriting existing values
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
      'notifications_telegram',
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
