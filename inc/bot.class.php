<?php
declare(strict_types=1);

use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\Request;

class PluginTelegrambotBot
{
   public const TABLE_CONFIG = 'glpi_plugin_telegrambot_configs';

   public static function ensureDefaultConfig(): void
   {
      global $DB;

      if (!$DB->tableExists(self::TABLE_CONFIG)) {
         return;
      }

      $row = $DB->request([
         'FROM'  => self::TABLE_CONFIG,
         'LIMIT' => 1
      ])->current();

      if (!$row) {
         $DB->insert(self::TABLE_CONFIG, [
            'id' => 1,
            'notification_bot_token' => '',
            'client_bot_token'       => '',
            'user_chat_field'        => 'telegram_chat_id',
            'user_topic_field'       => 'telegram_topic_id',
            'group_chat_field'       => 'telegram_chat_id',
            'group_topic_field'      => 'telegram_topic_id',
            'client_user_chat_field' => '',
            'client_user_topic_field'=> '',
            'client_group_chat_field'=> '',
            'client_group_topic_field'=>'',
            'client_last_update_id'  => 0,
            'updated_at'             => date('Y-m-d H:i:s')
         ]);
      }
   }

   public static function getConfig(): array
   {
      global $DB;

      self::ensureDefaultConfig();

      $row = $DB->request([
         'FROM'  => self::TABLE_CONFIG,
         'LIMIT' => 1
      ])->current();

      if (!$row) {
         return [
            'notification_bot_token' => '',
            'client_bot_token'       => '',
            'user_chat_field'        => 'telegram_chat_id',
            'user_topic_field'       => 'telegram_topic_id',
            'group_chat_field'       => 'telegram_chat_id',
            'group_topic_field'      => 'telegram_topic_id',
            'client_user_chat_field' => '',
            'client_user_topic_field'=> '',
            'client_group_chat_field'=> '',
            'client_group_topic_field'=>'',
            'client_last_update_id'  => 0,
         ];
      }
      return $row;
   }

   public static function updateConfig(array $data): bool
   {
      global $DB;

      self::ensureDefaultConfig();

      $allowed = [
         'notification_bot_token',
         'client_bot_token',
         'user_chat_field',
         'user_topic_field',
         'group_chat_field',
         'group_topic_field',
         'client_user_chat_field',
         'client_user_topic_field',
         'client_group_chat_field',
         'client_group_topic_field',
         'client_last_update_id',
      ];

      $payload = [];
      foreach ($allowed as $k) {
         if (array_key_exists($k, $data)) {
            $payload[$k] = is_string($data[$k]) ? trim($data[$k]) : $data[$k];
         }
      }
      $payload['updated_at'] = date('Y-m-d H:i:s');

      if (count($payload) === 1) {
         return true;
      }

      return $DB->update(
         self::TABLE_CONFIG,
         $payload,
         ['id' => 1]
      );
   }

   /**
    * Send Telegram message for GLPI notifications bot (simple HTTP API).
    */
   public static function sendNotificationMessage(
      string $botToken,
      string|int $chatId,
      ?string $topicId,
      string $text,
      ?string $parseMode = 'HTML'
   ): bool {
      $botToken = trim($botToken);
      if ($botToken === '' || (string)$chatId === '') {
         return false;
      }

      $endpoint = "https://api.telegram.org/bot" . rawurlencode($botToken) . "/sendMessage";

      $post = [
         'chat_id' => (string)$chatId,
         'text'    => $text,
      ];
      if ($parseMode) {
         $post['parse_mode'] = $parseMode;
      }
      // group topics: message_thread_id
      if ($topicId !== null && $topicId !== '') {
         $post['message_thread_id'] = (int)$topicId;
      }

      $ch = curl_init($endpoint);
      if ($ch === false) {
         return false;
      }

      curl_setopt_array($ch, [
         CURLOPT_POST           => true,
         CURLOPT_POSTFIELDS     => http_build_query($post),
         CURLOPT_RETURNTRANSFER => true,
         CURLOPT_CONNECTTIMEOUT => 8,
         CURLOPT_TIMEOUT        => 12,
         CURLOPT_SSL_VERIFYPEER => true,
         CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded']
      ]);

      $resp = curl_exec($ch);
      $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
      curl_close($ch);

      if ($resp === false || $code < 200 || $code >= 300) {
         return false;
      }

      $json = json_decode($resp, true);
      return is_array($json) && !empty($json['ok']);
   }

   /**
    * Run client bot updates polling and command handling
    * Uses longman/telegram-bot.
    */
   public static function runClientBot(): array
   {
      $cfg = self::getConfig();
      $token = trim((string)($cfg['client_bot_token'] ?? ''));
      if ($token === '') {
         return ['ok' => true, 'handled' => 0, 'note' => 'client bot token empty'];
      }

      // Commands path
      $commandsPath = __DIR__ . '/../commands';

      try {
         $telegram = new Telegram($token, 'GLPIClientBot');
         $telegram->addCommandsPath($commandsPath);

         // longman Request init
         Request::initialize($telegram);

         $offset = (int)($cfg['client_last_update_id'] ?? 0);
         $handled = 0;

         $response = Request::getUpdates([
            'offset'  => $offset > 0 ? $offset : null,
            'timeout' => 0,
            'limit'   => 50
         ]);

         if (!$response->isOk()) {
            return ['ok' => false, 'handled' => 0, 'error' => $response->getDescription()];
         }

         $result = $response->getResult();
         if (!is_array($result) || count($result) === 0) {
            return ['ok' => true, 'handled' => 0];
         }

         $maxUpdateId = $offset;
         foreach ($result as $update) {
            $updateId = (int)($update->getUpdateId() ?? 0);
            if ($updateId > $maxUpdateId) {
               $maxUpdateId = $updateId;
            }

            // Handle commands/messages through library
            $telegram->processUpdate($update);
            $handled++;
         }

         // store next offset
         self::updateConfig([
            'client_last_update_id' => $maxUpdateId + 1
         ]);

         return ['ok' => true, 'handled' => $handled];
      } catch (\Throwable $e) {
         return ['ok' => false, 'handled' => 0, 'error' => $e->getMessage()];
      }
   }
}
