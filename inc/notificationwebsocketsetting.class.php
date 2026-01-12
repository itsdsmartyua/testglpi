<?php
declare(strict_types=1);

require_once __DIR__ . '/bot.class.php';

// GLPI 11 moved NotificationSetting into namespace
if (!class_exists('NotificationSetting') && class_exists('Glpi\\Notification\\NotificationSetting')) {
   class_alias('Glpi\\Notification\\NotificationSetting', 'NotificationSetting');
}

class PluginTelegrambotNotificationWebsocketSetting extends NotificationSetting
{
   public static $rightname = 'config';

   public static function getTypeName($nb = 0): string
   {
      return __('Telegram', 'telegrambot');
   }

   /**
    * IMPORTANT: must be NON static in GLPI 11
    */
   public function getEnableLabel(): string
   {
      return __('Telegram notifications', 'telegrambot');
   }

   /**
    * IMPORTANT: must be NON static in GLPI 11
    */
   public function showFormConfig($options = []): bool
   {
      global $CFG_GLPI;

      self::ensureOneRow();
      $cfg = PluginTelegrambotBot::getConfig();

      $action = $CFG_GLPI['root_doc'] . '/plugins/telegrambot/front/notificationwebsocketsetting.form.php';

      echo "<form method='post' action='" . htmlescape($action) . "'>";
      echo Html::hidden('_glpi_csrf_token', ['value' => Session::getNewCSRFToken()]);

      echo "<div class='card'>";
      echo "<div class='card-header'><h3>" . htmlescape(__('Telegram channel settings', 'telegrambot')) . "</h3></div>";
      echo "<div class='card-body'>";

      self::renderTextRow(
         'notification_bot_token',
         __('Notification bot token', 'telegrambot'),
         (string)($cfg['notification_bot_token'] ?? ''),
         true
      );

      self::renderTextRow(
         'client_bot_token',
         __('Client bot token', 'telegrambot'),
         (string)($cfg['client_bot_token'] ?? ''),
         true
      );

      echo "<hr>";
      echo "<h4>" . htmlescape(__('Fields plugin mapping (Users/Groups)', 'telegrambot')) . "</h4>";

      self::renderTextRow(
         'user_chat_field',
         'user_chat_field',
         (string)($cfg['user_chat_field'] ?? 'telegram_chat_id')
      );

      self::renderTextRow(
         'user_topic_field',
         'user_topic_field (optional)',
         (string)($cfg['user_topic_field'] ?? 'telegram_topic_id')
      );

      self::renderTextRow(
         'group_chat_field',
         'group_chat_field',
         (string)($cfg['group_chat_field'] ?? 'telegram_chat_id')
      );

      self::renderTextRow(
         'group_topic_field',
         'group_topic_field (optional)',
         (string)($cfg['group_topic_field'] ?? 'telegram_topic_id')
      );

      echo "<hr>";
      echo "<h4>" . htmlescape(__('Client bot fields (optional)', 'telegrambot')) . "</h4>";

      self::renderTextRow(
         'client_user_chat_field',
         'client_user_chat_field',
         (string)($cfg['client_user_chat_field'] ?? '')
      );

      self::renderTextRow(
         'client_user_topic_field',
         'client_user_topic_field',
         (string)($cfg['client_user_topic_field'] ?? '')
      );

      self::renderTextRow(
         'client_group_chat_field',
         'client_group_chat_field',
         (string)($cfg['client_group_chat_field'] ?? '')
      );

      self::renderTextRow(
         'client_group_topic_field',
         'client_group_topic_field',
         (string)($cfg['client_group_topic_field'] ?? '')
      );

      echo "</div>";
      echo "<div class='card-footer'>";
      echo "<button class='btn btn-primary' type='submit' name='update' value='1'>"
         . htmlescape(__('Save')) . "</button>";
      echo "</div>";
      echo "</div>";

      echo "</form>";

      return true;
   }

   /**
    * Handles POST from our form
    */
   public function postForm(array $post): void
   {
      // must be config right
      Session::checkRight('config', UPDATE);
      Session::checkCSRF($post);

      self::ensureOneRow();

      $input = [];
      foreach ([
         'notification_bot_token', 'client_bot_token',
         'user_chat_field', 'user_topic_field',
         'group_chat_field', 'group_topic_field',
         'client_user_chat_field', 'client_user_topic_field',
         'client_group_chat_field', 'client_group_topic_field',
      ] as $k) {
         if (array_key_exists($k, $post)) {
            $input[$k] = (string)$post[$k];
         }
      }

      PluginTelegrambotBot::saveConfig($input);
   }

   private static function renderTextRow(string $name, string $label, string $value, bool $is_secret = false): void
   {
      $type = $is_secret ? 'password' : 'text';

      echo "<div class='row mb-3'>";
      echo "<label class='col-sm-4 col-form-label' for='" . htmlescape($name) . "'>"
         . htmlescape($label) . "</label>";
      echo "<div class='col-sm-8'>";
      echo "<input class='form-control' type='{$type}' name='" . htmlescape($name) . "' id='" . htmlescape($name) . "' value='"
         . htmlescape($value) . "'>";
      echo "</div>";
      echo "</div>";
   }

   private static function ensureOneRow(): void
   {
      global $DB;

      // table is created by install.sql; we just ensure there is at least one row
      $table = 'glpi_plugin_telegrambot_configs';
      $res = $DB->request([
         'FROM'   => $table,
         'LIMIT'  => 1
      ]);

      if (count($res) === 0) {
         $DB->insert($table, [
            'notification_bot_token'     => '',
            'client_bot_token'           => '',
            'user_chat_field'            => 'telegram_chat_id',
            'user_topic_field'           => 'telegram_topic_id',
            'group_chat_field'           => 'telegram_chat_id',
            'group_topic_field'          => 'telegram_topic_id',
            'client_user_chat_field'     => '',
            'client_user_topic_field'    => '',
            'client_group_chat_field'    => '',
            'client_group_topic_field'   => '',
            'client_last_update_id'      => 0,
            'updated_at'                 => null,
         ]);
      }
   }
}
