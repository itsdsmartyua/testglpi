<?php
declare(strict_types=1);

class PluginTelegrambotCron
{
   public static function cronMessagelistener(CronTask $task): int
   {
      $res = PluginTelegrambotBot::runClientBot();

      if (!empty($res['ok'])) {
         $handled = (int)($res['handled'] ?? 0);
         $task->log("Telegram client bot handled updates: {$handled}");
         return $handled;
      }

      $err = (string)($res['error'] ?? 'unknown error');
      $task->log("Telegram client bot error: {$err}");
      return 0;
   }
}

/**
 * Cron entrypoints required by GLPI
 */
function plugin_telegrambot_cronMessagelistener(CronTask $task): int
{
   return PluginTelegrambotCron::cronMessagelistener($task);
}

/**
 * GLPI sometimes calls lowercase.
 */
function plugin_telegrambot_cronmessagelistener(CronTask $task): int
{
   return plugin_telegrambot_cronMessagelistener($task);
}
