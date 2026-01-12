<?php
declare(strict_types=1);

require_once __DIR__ . '/bot.class.php';
require_once __DIR__ . '/fields.class.php';

class PluginTelegrambotNotificationWebsocket
{
   public static function sendToUser(int $users_id, string $message): bool
   {
      $cfg = PluginTelegrambotBot::getConfig();
      $field_chat  = (string)($cfg['user_chat_field'] ?? 'telegram_chat_id');
      $field_topic = (string)($cfg['user_topic_field'] ?? 'telegram_topic_id');

      $chat_id  = PluginTelegrambotFields::getUserFieldValue($users_id, $field_chat);
      $topic_id = PluginTelegrambotFields::getUserFieldValue($users_id, $field_topic);

      if (!$chat_id) {
         return false;
      }
      return PluginTelegrambotBot::sendMessage($message, $chat_id, $topic_id);
   }

   public static function sendToGroup(int $groups_id, string $message): bool
   {
      $cfg = PluginTelegrambotBot::getConfig();
      $field_chat  = (string)($cfg['group_chat_field'] ?? 'telegram_chat_id');
      $field_topic = (string)($cfg['group_topic_field'] ?? 'telegram_topic_id');

      $chat_id  = PluginTelegrambotFields::getGroupFieldValue($groups_id, $field_chat);
      $topic_id = PluginTelegrambotFields::getGroupFieldValue($groups_id, $field_topic);

      if (!$chat_id) {
         return false;
      }
      return PluginTelegrambotBot::sendMessage($message, $chat_id, $topic_id);
   }
}
