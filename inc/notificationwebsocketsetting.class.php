<?php
declare(strict_types=1);

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access this file directly");
}

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
      return (bool) Session::haveRight('config', READ);
   }

   public static function canUpdate(): bool
   {
      return (bool) Session::haveRight('config', UPDATE);
   }

   public function getEnableLabel(): string
   {
      return __('Enable Telegram notifications', 'telegrambot');
   }

   public function showFormConfig($options = []): bool
   {
      return $this->showForm(1, is_array($options) ? $options : []);
   }

   public function showForm($ID, array $options = []): bool
   {
      if (!self::canView()) {
         return false;
      }

      global $CFG_GLPI;

      $cfg    = PluginTelegrambotBot::getConfig();
      $action = $CFG_GLPI['root_doc'] . '/plugins/telegrambot/front/notificationwebsocketsetting.form.php';

      echo "<form method='post' action='" . Html::cleanInputText($action) . "'>";
      echo "<div class='center spaced'>";

      echo "<table class='tab_cadre_fixe'>";
      echo "<tr><th colspan='2'>" . __('Telegram channel settings', 'telegrambot') . "</th></tr>";

      echo "<tr class='tab_bg_1'><td width='35%'>" . __('Notification bot token', 'telegrambot') . "</td>";
      echo "<td><input type='password' name='notification_bot_token' style='width:100%;' value='" . Html::cleanInputText((string)($cfg['notification_bot_token'] ?? '')) . "'></td></tr>";

      echo "<tr class='tab_bg_1'><td>" . __('Client bot token', 'telegrambot') . "</td>";
      echo "<td><input type='password' name='client_bot_token' style='width:100%;' value='" . Html::cleanInputText((string)($cfg['client_bot_token'] ?? '')) . "'></td></tr>";

      echo "<tr><th colspan='2'>" . __('Fields plugin mapping (Users/Groups)', 'telegrambot') . "</th></tr>";

      echo "<tr class='tab_bg_1'><td>" . __('user_chat_field', 'telegrambot') . "</td>";
      echo "<td><input type='text' name='user_chat_field' style='width:100%;' value='" . Html::cleanInputText((string)($cfg['user_chat_field'] ?? 'telegram_chat_id')) . "'></td></tr>";

      echo "<tr class='tab_bg_1'><td>" . __('group_chat_field', 'telegrambot') . "</td>";
      echo "<td><input type='text' name='group_chat_field' style='width:100%;' value='" . Html::cleanInputText((string)($cfg['group_chat_field'] ?? 'telegram_chat_id')) . "'></td></tr>";

      echo "<tr class='tab_bg_1'><td>" . __('group_topic_field (optional)', 'telegrambot') . "</td>";
      echo "<td><input type='text' name='group_topic_field' style='width:100%;' value='" . Html::cleanInputText((string)($cfg['group_topic_field'] ?? 'telegram_topic_id')) . "'></td></tr>";

      echo "</table>";

      echo "<div class='center'>";
      // GLPI 11 CSRF (listener will validate it)
      echo "<input type='hidden' name='_glpi_csrf_token' value='" . Html::cleanInputText(Session::getNewCSRFToken()) . "'>";
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

      // IMPORTANT: do NOT call Session::checkCSRF() here.
      // GLPI 11 checks CSRF before controller execution (CheckCsrfListener).

      $data = [
         'notification_bot_token' => $post['notification_bot_token'] ?? '',
         'client_bot_token'       => $post['client_bot_token'] ?? '',
         'user_chat_field'        => $post['user_chat_field'] ?? 'telegram_chat_id',
         'group_chat_field'       => $post['group_chat_field'] ?? 'telegram_chat_id',
         'group_topic_field'      => $post['group_topic_field'] ?? 'telegram_topic_id',
      ];

      PluginTelegrambotBot::updateConfig($data);
      return true;
   }
}
