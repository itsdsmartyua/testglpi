<?php
declare(strict_types=1);

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access this file directly");
}

/**
 * Cron handler for TelegramBot plugin
 * - Handles client Telegram bot polling
 * - Provides lowercase wrapper for GLPI quirks
 */
class PluginTelegrambotCron
{
   /**
    * Main cron logic (CamelCase)
    */
   public static function cronMessagelistener(CronTask $task): int
   {
      // Poll client bot updates
      $result = PluginTelegrambotBot::runClientBot();

      if (!empty($result['ok'])) {
         $handled = (int)($result['handled'] ?? 0);
         $task->log("Telegram client bot handled updates: {$handled}");
         return $handled;
      }

      $error = (string)($result['error'] ?? 'unknown error');
      $task->log("Telegram client bot error: {$error}");
      return 0;
   }
}

/**
 * Cron entrypoint (CamelCase)
 */
function plugin_telegrambot_cronMessagelistener(CronTask $task): int
{
   return PluginTelegrambotCron::cronMessagelistener($task);
}

/**
 * Lowercase wrapper (GLPI sometimes calls lowercase)
 */
function plugin_telegrambot_cronmessagelistener(CronTask $task): int
{
   return plugin_telegrambot_cronMessagelistener($task);
}
