<?php
declare(strict_types=1);

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access this file directly");
}

/**
 * Telegram notification channel settings
 * GLPI 11 compatibility:
 * - canView()/canUpdate() must be static (CommonGLPI)
 * - getName($options = []) signature
 * - showForm($ID, array $options = []) signature
 */
class PluginTelegrambotNotificationWebsocketSetting extends NotificationSetting
{
   public static function getTypeName($nb = 0): string
   {
      return __('Telegram', 'telegrambot');
   }

   public function getName($options = [])
   {
      return 'telegram';
   }

   public static function canView(): bool
   {
      return Session::haveRight('config', READ);
   }

   public static function canUpdate(): bool
   {
      return Session::haveRight('config', UPDATE);
   }

   public function showForm($ID, array $options = []): bool
   {
      if (!self::canView()) {
         return false;
      }

      $cfg = PluginTelegrambotBot::getConfig();

      echo "<form method='post' action='" . Html::getFormUrlWithID('notificationwebsocketsetting.form.php') . "'>";
      echo "<div class='center spaced'>";

      echo "<table class='tab_cadre_fixe'>";
      echo "<tr><th colspan='2'>" . __('Telegram channel settings', 'telegrambot') . "</th></tr>";

      echo "<tr class='tab_bg_1'><td width='35%'>" . __('Notification bot token', 'telegrambot') . "</td>";
      echo "<td><input type='password' name='notification_bot_token' style='width:100%;' value='" . Html::cleanInputText((string)($cfg['notification_bot_token'] ?? '')) . "'></td></tr>";

      echo "<tr class='tab_bg_1'><td>" . __('Client bot token', 'telegrambot') . "</td>";
      echo "<td><input type='password' name='client_bot_token' style='width:100%;' value='" . Html::cleanInputText((string)($cfg['client_bot_token'] ?? '')) . "'></td></tr>";

      echo "<tr><th colspan='2'>" . __('Fields plugin mapping (Users/Groups)', 'telegrambot') . "</th></tr>";

      echo "<tr class='tab_bg_1'><td>" . __('user_chat_field', 'telegrambot') . "</td>";
      echo "<td><input type='text' name='user_chat_field' style='width:100%;' value='" . Html::cleanInputText((string)($cfg['user_chat_field'] ?? '')) . "'></td></tr>";

      echo "<tr class='tab_bg_1'><td>" . __('user_topic_field (optional)', 'telegrambot') . "</td>";
      echo "<td><input type='text' name='user_topic_field' style='width:100%;' value='" . Html::cleanInputText((string)($cfg['user_topic_field'] ?? '')) . "'></td></tr>";

      echo "<tr class='tab_bg_1'><td>" . __('group_chat_field', 'telegrambot') . "</td>";
      echo "<td><input type='text' name='group_chat_field' style='width:100%;' value='" . Html::cleanInputText((string)($cfg['group_chat_field'] ?? '')) . "'></td></tr>";

      echo "<tr class='tab_bg_1'><td>" . __('group_topic_field (optional)', 'telegrambot') . "</td>";
      echo "<td><input type='text' name='group_topic_field' style='width:100%;' value='" . Html::cleanInputText((string)($cfg['group_topic_field'] ?? '')) . "'></td></tr>";

      echo "<tr><th colspan='2'>" . __('Optional client_* mapping (if different)', 'telegrambot') . "</th></tr>";

      echo "<tr class='tab_bg_1'><td>" . __('client_user_chat_field', 'telegrambot') . "</td>";
      echo "<td><input type='text' name='client_user_chat_field' style='width:100%;' value='" . Html::cleanInputText((string)($cfg['client_user_chat_field'] ?? '')) . "'></td></tr>";

      echo "<tr class='tab_bg_1'><td>" . __('client_user_topic_field', 'telegrambot') . "</td>";
      echo "<td><input type='text' name='client_user_topic_field' style='width:100%;' value='" . Html::cleanInputText((string)($cfg['client_user_topic_field'] ?? '')) . "'></td></tr>";

      echo "<tr class='tab_bg_1'><td>" . __('client_group_chat_field', 'telegrambot') . "</td>";
      echo "<td><input type='text' name='client_group_chat_field' style='width:100%;' value='" . Html::cleanInputText((string)($cfg['client_group_chat_field'] ?? '')) . "'></td></tr>";

      echo "<tr class='tab_bg_1'><td>" . __('client_group_topic_field', 'telegrambot') . "</td>";
      echo "<td><input type='text' name='client_group_topic_field' style='width:100%;' value='" . Html::cleanInputText((string)($cfg['client_group_topic_field'] ?? '')) . "'></td></tr>";

      echo "</table>";

      echo "<div class='center'>";
      echo "<input type='hidden' name='csrf_token' value='" . Session::getNewCSRFToken() . "'>";
      echo "<input type='submit' name='update' class='submit' value='" . __('Save') . "'>";
      echo "</div>";

      echo "</div>";
      Html::closeForm();

      return true;
   }

   public function postForm(array $post): bool
   {
      if (!self::canUpdate()) {
         return false;
      }
      if (!Session::checkCSRF($post)) {
         return false;
      }

      $data = [
         'notification_bot_token'   => $post['notification_bot_token'] ?? '',
         'client_bot_token'         => $post['client_bot_token'] ?? '',
         'user_chat_field'          => $post['user_chat_field'] ?? '',
         'user_topic_field'         => $post['user_topic_field'] ?? '',
         'group_chat_field'         => $post['group_chat_field'] ?? '',
         'group_topic_field'        => $post['group_topic_field'] ?? '',
         'client_user_chat_field'   => $post['client_user_chat_field'] ?? '',
         'client_user_topic_field'  => $post['client_user_topic_field'] ?? '',
         'client_group_chat_field'  => $post['client_group_chat_field'] ?? '',
         'client_group_topic_field' => $post['client_group_topic_field'] ?? '',
      ];

      PluginTelegrambotBot::updateConfig($data);
      return true;
   }
}
