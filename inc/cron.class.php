<?php
declare(strict_types=1);

require_once __DIR__ . '/bot.class.php';

function plugin_telegrambot_cronMessagelistener(): int
{
   $telegram = PluginTelegrambotBot::getTelegram(PluginTelegrambotBot::BOT_CLIENT);
   if (!$telegram) {
      return 0;
   }

   $cfg = PluginTelegrambotBot::getConfig();
   $offset = (int)($cfg['client_last_update_id'] ?? 0);
   $offset = $offset > 0 ? ($offset + 1) : 0;

   \Longman\TelegramBot\Request::initialize($telegram);
   $telegram->addCommandsPaths([__DIR__ . '/../commands']);

   try {
      $updates = \Longman\TelegramBot\Request::getUpdates([
         'offset'  => $offset,
         'timeout' => 0,
      ]);
   } catch (\Throwable $e) {
      return 0;
   }

   if (!$updates->isOk()) {
      return 0;
   }

   $result = $updates->getResult();
   if (!is_array($result) || count($result) === 0) {
      return 0;
   }

   $max_update_id = 0;
   foreach ($result as $upd) {
      if (is_object($upd) && method_exists($upd, 'getUpdateId')) {
         $max_update_id = max($max_update_id, (int)$upd->getUpdateId());
      }
   }

   try {
      $telegram->handleGetUpdates();
   } catch (\Throwable $e) {
      // ignore
   }

   if ($max_update_id > 0) {
      PluginTelegrambotBot::updateClientLastUpdateId($max_update_id);
   }

   return 1;
}

function plugin_telegrambot_cronmessagelistener(): int
{
   return plugin_telegrambot_cronMessagelistener();
}
