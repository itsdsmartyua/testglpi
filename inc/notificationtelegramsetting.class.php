<?php
declare(strict_types=1);

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access this file directly");
}

/**
 * Notification settings handler for "telegram" notification mode.
 * This class MUST extend NotificationSetting to be compatible with GLPI core
 * notification settings page (/front/setup.notification.php).
 */
class PluginTelegrambotNotificationTelegramSetting extends NotificationSetting
{
   /**
    * Label displayed in global notification settings page.
    */
   public function getEnableLabel(): string
   {
      return __('Telegram', 'telegrambot');
   }

   /**
    * Show configuration block inside global notification settings page.
    *
    * GLPI calls this from NotificationSettingConfig.
    */
   public function showFormConfig(array $options = []): bool
   {
      global $CFG_GLPI;

      // Link to plugin dedicated settings page
      $url = $CFG_GLPI['root_doc'] . '/plugins/telegrambot/front/notificationwebsocketsetting.form.php';

      echo "<div class='alert alert-info'>";
      echo htmlescape(__('Telegram mode is configured in the plugin settings page.', 'telegrambot')) . "<br>";
      echo "<a class='btn btn-primary mt-2' href='" . htmlescape($url) . "'>";
      echo htmlescape(__('Open Telegram settings', 'telegrambot'));
      echo "</a>";
      echo "</div>";

      return true;
   }
}
