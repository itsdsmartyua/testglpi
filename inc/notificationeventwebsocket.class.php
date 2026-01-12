<?php
declare(strict_types=1);

require_once __DIR__ . '/notificationwebsocket.class.php';

class PluginTelegrambotNotificationEventWebsocket
{
   public static function notify(string $text, array $recipients): void
   {
      foreach (($recipients['users'] ?? []) as $uid) {
         PluginTelegrambotNotificationWebsocket::sendToUser((int)$uid, $text);
      }
      foreach (($recipients['groups'] ?? []) as $gid) {
         PluginTelegrambotNotificationWebsocket::sendToGroup((int)$gid, $text);
      }
   }
}
