<?php
declare(strict_types=1);

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access this file directly");
}

/**
 * Notification mode implementation
 * Mode name: "telegram"
 *
 * GLPI calls this class to actually send notifications.
 */
class PluginTelegrambotNotificationWebsocket extends Notification
{
   public function sendNotification(array $options = []): int
   {
      // options usually contain:
      // - 'to' recipients array
      // - 'message' body
      // - 'subject' etc.
      // We'll rely on buildMessage / getTo methods, but keep robust.

      $cfg = PluginTelegrambotBot::getConfig();
      $token = trim((string)($cfg['notification_bot_token'] ?? ''));
      if ($token === '') {
         return Notification::ERROR;
      }

      $text = $this->getMessage();
      if (!is_string($text) || trim($text) === '') {
         $text = (string)($options['message'] ?? '');
      }
      $text = (string)$text;

      // Recipients could include users and groups. We will resolve chat_id/topic_id via Fields.
      $recipients = $this->getTo();
      if (!is_array($recipients) || count($recipients) === 0) {
         // fallback from options
         $recipients = $options['to'] ?? [];
      }

      $sentAny = false;
      $errors  = 0;

      foreach ($recipients as $r) {
         // Expected structure varies. We handle common ones:
         // user: ['id' => X, 'type' => 'User'|'user'|'users']
         // group: ['id' => X, 'type' => 'Group'|'group'|'groups']
         $itemtype = null;
         $id = null;

         if (is_array($r)) {
            $id = (int)($r['id'] ?? $r['items_id'] ?? 0);
            $t  = (string)($r['type'] ?? $r['itemtype'] ?? '');
            $t  = strtolower($t);

            if (str_contains($t, 'user')) {
               $itemtype = 'User';
            } else if (str_contains($t, 'group')) {
               $itemtype = 'Group';
            }
         } else if (is_int($r)) {
            // assume user id
            $itemtype = 'User';
            $id = (int)$r;
         }

         if (!$itemtype || !$id) {
            continue;
         }

         $chatField  = $itemtype === 'User' ? (string)$cfg['user_chat_field']  : (string)$cfg['group_chat_field'];
         $topicField = $itemtype === 'User' ? (string)$cfg['user_topic_field'] : (string)$cfg['group_topic_field'];

         $chatId = PluginTelegrambotFields::getValue($itemtype, $id, $chatField);
         if ($chatId === null || trim($chatId) === '') {
            continue;
         }

         $topicId = null;
         if (trim($topicField) !== '') {
            $topicId = PluginTelegrambotFields::getValue($itemtype, $id, $topicField);
         }

         $ok = PluginTelegrambotBot::sendNotificationMessage(
            $token,
            $chatId,
            $topicId,
            $text,
            'HTML'
         );

         if ($ok) {
            $sentAny = true;
         } else {
            $errors++;
         }
      }

      if ($sentAny && $errors === 0) {
         return Notification::OK;
      }
      if ($sentAny) {
         return Notification::WARNING;
      }
      return Notification::ERROR;
   }
}
