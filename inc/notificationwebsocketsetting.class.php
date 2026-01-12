<?php
declare(strict_types=1);

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access this file directly");
}

/**
 * GLPI notification setting handler for "telegram" mode.
 * MUST extend NotificationSetting, otherwise core notification config page breaks.
 */
class PluginTelegrambotNotificationTelegramSetting extends NotificationSetting
{
   /**
    * Label shown near enable/disable switch on /front/setup.notification.php
    */
   public function getEnableLabel(): string
   {
      return __('Telegram notifications', 'telegrambot');
   }

   /**
    * Additional configuration form displayed in global notification settings.
    * We keep it simple: a link to the plugin settings page.
    */
   public function showFormConfig(array $options = []): bool
   {
      global $CFG_GLPI;

      echo "<div class='alert alert-info'>";
      echo "<i class='ti ti-brand-telegram'></i> ";
      echo __('Telegram settings are managed in the plugin page.', 'telegrambot');
      echo "</div>";

      $url = $CFG_GLPI['root_doc'] . '/plugins/telegrambot/front/notificationwebsocketsetting.form.php';

      echo "<div class='mb-3'>";
      echo "<a class='btn btn-primary' href='" . Html::cleanInputText($url) . "'>";
      echo __('Open Telegram plugin settings', 'telegrambot');
      echo "</a>";
      echo "</div>";

      return true;
   }
}
