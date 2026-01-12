<?php
declare(strict_types=1);

use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\Request;

require_once __DIR__ . '/../vendor/autoload.php';

class PluginTelegrambotBot
{
   public const BOT_NOTIFICATION = 'notification';
   public const BOT_CLIENT       = 'client';

   public static function getConfig(): array
   {
      global $DB;

      $table = 'glpi_plugin_telegrambot_configs';
      if (!$DB->tableExists($table)) {
         return [];
      }

      $res = $DB->query("SELECT * FROM `$table` ORDER BY id ASC LIMIT 1");
      if ($res && $DB->numrows($res) > 0) {
         return $DB->fetchAssoc($res);
      }
      return [];
   }

   public static function saveConfig(array $input): bool
   {
      global $DB;

      $table = 'glpi_plugin_telegrambot_configs';
      if (!$DB->tableExists($table)) {
         return false;
      }

      $cfg = self::getConfig();
      $id  = (int)($cfg['id'] ?? 0);
      if ($id <= 0) {
         $DB->query("INSERT INTO `$table` (`notification_bot_token`,`client_bot_token`,`updated_at`) VALUES ('','','" . $DB->escape(date('Y-m-d H:i:s')) . "')");
         $cfg = self::getConfig();
         $id  = (int)($cfg['id'] ?? 0);
      }

      $fields = [
         'notification_bot_token','client_bot_token',
         'user_chat_field','user_topic_field',
         'group_chat_field','group_topic_field',
         'client_user_chat_field','client_user_topic_field',
         'client_group_chat_field','client_group_topic_field'
      ];

      $sets = [];
      foreach ($fields as $f) {
         if (array_key_exists($f, $input)) {
            $sets[] = "`$f`='" . $DB->escape((string)$input[$f]) . "'";
         }
      }

      $sets[] = "`updated_at`='" . $DB->escape(date('Y-m-d H:i:s')) . "'";
      $sql = "UPDATE `$table` SET " . implode(',', $sets) . " WHERE `id`=" . (int)$id;
      return (bool)$DB->query($sql);
   }

   public static function updateClientLastUpdateId(int $update_id): void
   {
      global $DB;

      $cfg = self::getConfig();
      $id  = (int)($cfg['id'] ?? 0);
      if ($id <= 0) {
         return;
      }
      $DB->query("UPDATE `glpi_plugin_telegrambot_configs` SET `client_last_update_id`=" . (int)$update_id
         . ", `updated_at`='" . $DB->escape(date('Y-m-d H:i:s')) . "' WHERE `id`=" . (int)$id);
   }

   public static function getTelegram(string $type): ?Telegram
   {
      $cfg = self::getConfig();
      if (!$cfg) {
         return null;
      }

      $token = '';
      if ($type === self::BOT_NOTIFICATION) {
         $token = (string)($cfg['notification_bot_token'] ?? '');
      } elseif ($type === self::BOT_CLIENT) {
         $token = (string)($cfg['client_bot_token'] ?? '');
      }

      $token = trim($token);
      if ($token === '') {
         return null;
      }

      return new Telegram($token, '');
   }

   public static function sendMessage(string $text, string $chat_id, ?string $topic_id = null): bool
   {
      $telegram = self::getTelegram(self::BOT_NOTIFICATION);
      if (!$telegram) {
         return false;
      }

      Request::initialize($telegram);

      $data = [
         'chat_id' => $chat_id,
         'text'    => $text,
         'parse_mode' => 'HTML',
      ];

      $topic_id = trim((string)$topic_id);
      if ($topic_id !== '') {
         $data['message_thread_id'] = (int)$topic_id;
      }

      $res = Request::sendMessage($data);
      return $res->isOk();
   }
}
