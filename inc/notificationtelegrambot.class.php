<?php
declare(strict_types=1);

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access this file directly");
}

class PluginTelegrambotNotificationTelegrambot implements NotificationInterface
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
         'fields_container',
         'field_chat_id',
         'field_enabled',
         'field_out_enabled',
      ]);

      $token = (string)($conf['bot_token_out'] ?? '');
      if ($token === '') {
         return false;
      }

      $usersId = (int)($options['to'] ?? 0);
      if ($usersId <= 0) {
         return false;
      }

      // Fields(User)
      if (!class_exists('PluginFieldsField')) {
         return false;
      }

      $container  = (string)($conf['fields_container'] ?? 'telegram');
      $fieldChat  = (string)($conf['field_chat_id'] ?? 'tg_chat_id_notify');
      $fieldEn    = (string)($conf['field_enabled'] ?? 'tg_enabled');
      $fieldOutEn = (string)($conf['field_out_enabled'] ?? 'tg_bot_out_enabled');

      try {
         $fields = PluginFieldsField::getFields('User', $usersId, $container);
      } catch (Throwable $e) {
         return false;
      }

      if (!is_array($fields)) {
         return false;
      }

      $enabled = (int)($fields[$fieldEn] ?? 0);
      $outEn   = (int)($fields[$fieldOutEn] ?? 0);
      $chatId  = trim((string)($fields[$fieldChat] ?? ''));

      if (!$enabled || !$outEn || $chatId === '') {
         return false;
      }

      $text = (string)($options['content_text'] ?? '');
      if ($text === '') {
         $text = (string)($options['subject'] ?? '');
      }
      if ($text === '') {
         return false;
      }

      $parseMode = (string)($conf['parse_mode'] ?? 'HTML');
      $debug     = (int)($conf['debug'] ?? 0);

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
