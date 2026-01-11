<?php
declare(strict_types=1);

/**
 * Init hooks (MUST be here, not in setup.php)
 */
function plugin_init_telegrambot(): void
{
   global $PLUGIN_HOOKS;

   $PLUGIN_HOOKS['csrf_compliant']['telegrambot'] = true;

   // Our config page (NotificationSetting form)
   $PLUGIN_HOOKS['config_page']['telegrambot'] = 'front/notificationtelegrambotsetting.form.php';

   // Register notification mode (Telegram)
   $plugin = new \Plugin();
   if ($plugin->isActivated('telegrambot')) {
      \Notification_NotificationTemplate::registerMode(
         'telegrambot',
         __('Telegram', 'telegrambot'),
         'telegrambot'
      );
   }
}

/**
 * Install
 */
function plugin_telegrambot_install(): bool
{
   \Config::setConfigurationValues('core', [
      'notifications_telegrambot' => 0,
   ]);

   \Config::setConfigurationValues('plugin:telegrambot', [
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

/**
 * Update (GLPI shows "Для обновления" until this exists)
 */
function plugin_telegrambot_update($current_version = null): bool
{
   if (\Config::getConfigurationValue('core', 'notifications_telegrambot') === null) {
      \Config::setConfigurationValues('core', ['notifications_telegrambot' => 0]);
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
      if (\Config::getConfigurationValue('plugin:telegrambot', $k) === null) {
         \Config::setConfigurationValues('plugin:telegrambot', [$k => $v]);
      }
   }

   return true;
}

/**
 * Uninstall
 */
function plugin_telegrambot_uninstall(): bool
{
   $config = new \Config();

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
