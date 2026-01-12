<?php
declare(strict_types=1);

require_once __DIR__ . '/bot.class.php';

if (!class_exists('NotificationSetting') && class_exists('Glpi\\Notification\\NotificationSetting')) {
   class_alias('Glpi\\Notification\\NotificationSetting', 'NotificationSetting');
}

class PluginTelegrambotNotificationWebsocketSetting extends NotificationSetting
{
   public static function getTypeName($nb = 0): string
   {
      return 'Telegram';
   }

   public static function getEnableLabel(): string
   {
      return __('Telegram notifications', 'telegrambot');
   }

   public static function ensureOneRow(): void
   {
      global $DB;
      $table = 'glpi_plugin_telegrambot_configs';
      if (!$DB->tableExists($table)) {
         return;
      }
      $res = $DB->query("SELECT id FROM `$table` ORDER BY id ASC LIMIT 1");
      if (!$res || $DB->numrows($res) === 0) {
         $DB->query("INSERT INTO `$table` (`notification_bot_token`,`client_bot_token`,`updated_at`) VALUES ('','','" . $DB->escape(date('Y-m-d H:i:s')) . "')");
      }
   }

   public static function showFormConfig($options = []): bool
   {
      global $CFG_GLPI;

      self::ensureOneRow();
      $cfg = PluginTelegrambotBot::getConfig();

      $action = $CFG_GLPI['root_doc'] . '/plugins/telegrambot/front/notificationwebsocketsetting.form.php';

      echo "<form method='post' action='" . htmlescape($action) . "'>";
      echo Html::hidden('_glpi_csrf_token', ['value' => Session::getNewCSRFToken()]);

      echo "<div class='card'>";
      echo "<div class='card-header'><h3>Telegram channel settings</h3></div>";
      echo "<div class='card-body'>";

      self::renderTextRow('notification_bot_token', __('Notification bot token', 'telegrambot'), (string)($cfg['notification_bot_token'] ?? ''), true);
      self::renderTextRow('client_bot_token', __('Client bot token', 'telegrambot'), (string)($cfg['client_bot_token'] ?? ''), true);

      echo "<h4 class='mt-3'>Fields plugin mapping (Users/Groups)</h4>";
      self::renderTextRow('user_chat_field', 'user_chat_field', (string)($cfg['user_chat_field'] ?? 'telegram_chat_id'));
      self::renderTextRow('group_chat_field', 'group_chat_field', (string)($cfg['group_chat_field'] ?? 'telegram_chat_id'));
      self::renderTextRow('group_topic_field', 'group_topic_field (optional)', (string)($cfg['group_topic_field'] ?? 'telegram_topic_id'));

      echo "<div class='mt-3'>";
      echo "<button class='btn btn-primary' type='submit' name='update' value='1'>" . __('Save') . "</button>";
      echo "</div>";

      echo "</div></div>";
      echo "</form>";

      return true;
   }

   private static function renderTextRow(string $name, string $label, string $value, bool $password = false): void
   {
      $type = $password ? 'password' : 'text';
      echo "<div class='form-group row mb-2'>";
      echo "<label class='col-sm-3 col-form-label'>" . htmlescape($label) . "</label>";
      echo "<div class='col-sm-9'>";
      echo "<input class='form-control' type='$type' name='" . htmlescape($name) . "' value='" . htmlescape($value) . "'>";
      echo "</div>";
      echo "</div>";
   }

   public function postForm(array $post): void
   {
      Session::checkCSRF($post);
      Session::checkRight('config', UPDATE);

      $input = [];
      foreach ([
         'notification_bot_token','client_bot_token',
         'user_chat_field','user_topic_field',
         'group_chat_field','group_topic_field',
         'client_user_chat_field','client_user_topic_field',
         'client_group_chat_field','client_group_topic_field'
      ] as $k) {
         if (isset($post[$k])) {
            $input[$k] = (string)$post[$k];
         }
      }
      PluginTelegrambotBot::saveConfig($input);
   }
}
