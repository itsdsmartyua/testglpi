<?php
declare(strict_types=1);

use Plugin;

/**
 * Init hooks
 */
function plugin_init_telegrambot(): void
{
   global $PLUGIN_HOOKS;

   $PLUGIN_HOOKS['csrf_compliant']['telegrambot'] = true;
   $PLUGIN_HOOKS['config_page']['telegrambot']    = 'front/config.form.php';

   $plugin = new Plugin();
   if ($plugin->isActivated('telegrambot')) {
      // Register notification mode (required)
      Notification_NotificationTemplate::registerMode(
         'telegrambot',                          // MODE (custom)
         __('Telegram', 'telegrambot'),           // Label
         'telegrambot'                            // Plugin name
      );
   }
}

/**
 * Install
 */
function plugin_telegrambot_install(): bool
{
   // Enable flag for this notification mode in core configuration
   Config::setConfigurationValues('core', [
      'notifications_telegrambot' => 0
   ]);

   // Plugin configuration defaults
   Config::setConfigurationValues('plugin:telegrambot', [
      'bot_token_out'        => '',
      'parse_mode'           => 'HTML',

      // Fields plugin mapping (you will create these fields)
      'fields_container'     => 'telegram',
      'field_chat_id_notify' => 'tg_chat_id_notify',
      'field_enabled'        => 'tg_enabled',
      'field_out_enabled'    => 'tg_bot_out_enabled',

      'debug'                => 0
   ]);

   return true;
}

/**
 * Uninstall
 */
function plugin_telegrambot_uninstall(): bool
{
   $config = new Config();

   $config->deleteConfigurationValues('core', [
      'notifications_telegrambot'
   ]);

   $config->deleteConfigurationValues('plugin:telegrambot', [
      'bot_token_out',
      'parse_mode',
      'fields_container',
      'field_chat_id_notify',
      'field_enabled',
      'field_out_enabled',
      'debug'
   ]);

   return true;
}

/**
 * Update (called when plugin is "Для обновления")
 */
function plugin_telegrambot_update($current_version = null): bool
{
   // Ensure core flag exists
   Config::setConfigurationValues('core', [
      'notifications_telegrambot' => (int)(Config::getConfigurationValue('core', 'notifications_telegrambot') ?? 0),
   ]);

   // Ensure plugin defaults exist (do not overwrite existing values)
   $defaults = [
      'bot_token_out'        => '',
      'parse_mode'           => 'HTML',
      'fields_container'     => 'telegram',
      'field_chat_id_notify' => 'tg_chat_id_notify',
      'field_enabled'        => 'tg_enabled',
      'field_out_enabled'    => 'tg_bot_out_enabled',
      'debug'                => 0
   ];

   foreach ($defaults as $k => $v) {
      $cur = Config::getConfigurationValue('plugin:telegrambot', $k);
      if ($cur === null) {
         Config::setConfigurationValues('plugin:telegrambot', [$k => $v]);
      }
   }

   return true;
}
