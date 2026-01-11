<?php
declare(strict_types=1);

namespace GlpiPlugin\Telegrambot;

use Config;
use NotificationInterface;
use Toolbox;

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access this file directly");
}

final class NotificationTelegrambot implements NotificationInterface
{
   public static function check($value, $options = []): bool
   {
      return true;
   }

   public static function testNotification(): void
   {
      // later
   }

   public function sendNotification($options = []): bool
   {
      $conf = Config::getConfigurationValues('plugin:telegrambot', [
         'bot_token_out',
         'parse_mode',
         'debug',
      ]);

      $token = (string)($conf['bot_token_out'] ?? '');
      if ($token === '') {
         return false;
      }

      $parseMode = (string)($conf['parse_mode'] ?? 'HTML');
      $debug     = (int)($conf['debug'] ?? 0);

      $usersId = (int)($options['to'] ?? 0);
      if ($usersId <= 0) {
         return false;
      }

      $chatId = TelegramFieldsResolver::getChatIdForUser($usersId);
      if ($chatId === '') {
         return false;
      }

      $text = (string)($options['content_text'] ?? '');
      if ($text === '') {
         $text = (string)($options['subject'] ?? '');
      }
      if ($text === '') {
         return false;
      }

      $payload = [
         'chat_id' => $chatId,
         'text'    => $text,
      ];

      if (in_array($parseMode, ['HTML', 'Markdown', 'MarkdownV2'], true)) {
         $payload['parse_mode'] = $parseMode;
      }

      $url = "https://api.telegram.org/bot{$token}/sendMessage";

      $ch = curl_init($url);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
      curl_setopt($ch, CURLOPT_TIMEOUT, 10);

      $resp = curl_exec($ch);
      $err  = curl_error($ch);
      $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
      curl_close($ch);

      if ($debug) {
         $line = date('c') . " code={$code} err=" . ($err ?: '-') . " resp=" . ($resp ?: '-') . "\n";
         Toolbox::logInFile('telegrambot', $line);
      }

      return ($err === '' && $code >= 200 && $code < 300);
   }
}

final class TelegramFieldsResolver
{
   public static function getChatIdForUser(int $usersId): string
   {
      $conf = Config::getConfigurationValues('plugin:telegrambot', [
         'fields_container',
         'field_chat_id',
         'field_enabled',
         'field_out_enabled',
      ]);

      $container  = (string)($conf['fields_container'] ?? 'telegram');
      $fieldChat  = (string)($conf['field_chat_id'] ?? 'tg_chat_id_notify');
      $fieldEn    = (string)($conf['field_enabled'] ?? 'tg_enabled');
      $fieldOutEn = (string)($conf['field_out_enabled'] ?? 'tg_bot_out_enabled');

      if (!class_exists('\\PluginFieldsField')) {
         return '';
      }

      try {
         // Fields plugin API (best effort)
         $fields = \PluginFieldsField::getFields('User', $usersId, $container);
         if (!is_array($fields)) {
            return '';
         }

         $enabled = (int)($fields[$fieldEn] ?? 0);
         $outEn   = (int)($fields[$fieldOutEn] ?? 0);
         $chatId  = trim((string)($fields[$fieldChat] ?? ''));

         if ($enabled && $outEn && $chatId !== '') {
            return $chatId;
         }
      } catch (\Throwable $e) {
         return '';
      }

      return '';
   }
}
