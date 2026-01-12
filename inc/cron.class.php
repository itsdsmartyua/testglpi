<?php
declare(strict_types=1);

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access this file directly");
}

class PluginTelegrambotCron
{
   public static function cronMessagelistener(CronTask $task): int
   {
      // client bot polling
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
 * Cron entrypoint (CamelCase) - declared once.
 */
function plugin_telegrambot_cronMessagelistener(CronTask $task): int
{
   return PluginTelegrambotCron::cronMessagelistener($task);
}

/**
 * Lowercase wrapper - GLPI sometimes calls lowercase.
 * Must NOT duplicate declarations.
 */
function plugin_telegrambot_cronmessagelistener(CronTask $task): int
{
   return plugin_telegrambot_cronMessagelistener($task);
}
