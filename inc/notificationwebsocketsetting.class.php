<?php
declare(strict_types=1);

require_once __DIR__ . '/bot.class.php';

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

   // MUST be NON-static (GLPI 11)
   public function getEnableLabel(): string
   {
      return __('Telegram notifications', 'telegrambot');
   }

   // MUST be NON-static (GLPI 11)
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

      self::renderRow('notification_bot_token', __('Notification bot token', 'telegrambot'), (string)($cfg['notification_bot_token'] ?? ''), true);
      self::renderRow('client_bot_token', __('Client bot token', 'telegrambot'), (string)($cfg['client_bot_token'] ?? ''), true);

      echo "<hr><h4>" . htmlescape(__('Fields plugin mapping (Users/Groups)', 'telegrambot')) . "</h4>";

      self::renderRow('user_chat_field', 'user_chat_field', (string)($cfg['user_chat_field'] ?? 'telegram_chat_id'));
      self::renderRow('user_topic_field', 'user_topic_field (optional)', (string)($cfg['user_topic_field'] ?? 'telegram_topic_id'));
      self::renderRow('group_chat_field', 'group_chat_field', (string)($cfg['group_chat_field'] ?? 'telegram_chat_id'));
      self::renderRow('group_topic_field', 'group_topic_field (optional)', (string)($cfg['group_topic_field'] ?? 'telegram_topic_id'));

      echo "<hr><h4>" . htmlescape(__('Client bot fields (optional)', 'telegrambot')) . "</h4>";

      self::renderRow('client_user_chat_field', 'client_user_chat_field', (string)($cfg['client_user_chat_field'] ?? ''));
      self::renderRow('client_user_topic_field', 'client_user_topic_field', (string)($cfg['client_user_topic_field'] ?? ''));
      self::renderRow('client_group_chat_field', 'client_group_chat_field', (string)($cfg['client_group_chat_field'] ?? ''));
      self::renderRow('client_group_topic_field', 'client_group_topic_field', (string)($cfg['client_group_topic_field'] ?? ''));

      echo "</div><div class='card-footer'>";
      echo "<button class='btn btn-primary' type='submit' name='update' value='1'>" . htmlescape(__('Save')) . "</button>";
      echo "</div></div>";

      echo "</form>";
      return true;
   }

   public function postForm(array $post): void
   {
      Session::checkRight('config', UPDATE);
      Session::checkCSRF($post);

      $input = [];
      foreach ([
         'notification_bot_token','client_bot_token',
         'user_chat_field','user_topic_field',
         'group_chat_field','group_topic_field',
         'client_user_chat_field','client_user_topic_field',
         'client_group_chat_field','client_group_topic_field',
      ] as $k) {
         if (array_key_exists($k, $post)) {
            $input[$k] = (string)$post[$k];
         }
      }
      PluginTelegrambotBot::saveConfig($input);
   }

   private static function renderRow(string $name, string $label, string $value, bool $secret = false): void
   {
      $type = $secret ? 'password' : 'text';
      echo "<div class='row mb-3'>";
      echo "<label class='col-sm-4 col-form-label' for='" . htmlescape($name) . "'>" . htmlescape($label) . "</label>";
      echo "<div class='col-sm-8'>";
      echo "<input class='form-control' type='{$type}' name='" . htmlescape($name) . "' id='" . htmlescape($name) . "' value='" . htmlescape($value) . "'>";
      echo "</div></div>";
   }

   public static function ensureOneRow(): void
   {
      global $DB;

      $table = 'glpi_plugin_telegrambot_configs';
      if (!$DB->tableExists($table)) {
         return;
      }

      $it = $DB->request(['FROM' => $table, 'LIMIT' => 1]);
      if (count($it) === 0) {
         $DB->insert($table, [
            'notification_bot_token'   => '',
            'client_bot_token'         => '',
            'user_chat_field'          => 'telegram_chat_id',
            'user_topic_field'         => 'telegram_topic_id',
            'group_chat_field'         => 'telegram_chat_id',
            'group_topic_field'        => 'telegram_topic_id',
            'client_user_chat_field'   => '',
            'client_user_topic_field'  => '',
            'client_group_chat_field'  => '',
            'client_group_topic_field' => '',
            'client_last_update_id'    => 0,
            'updated_at'               => null,
         ]);
      }
   }
}
