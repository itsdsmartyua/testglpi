<?php
declare(strict_types=1);

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class MyunresolvedCommand extends UserCommand
{
   protected $name = 'myunresolved';
   protected $description = 'List my unresolved tickets';
   protected $usage = '/myunresolved';
   protected $version = '1.0.0';

   public function execute(): ServerResponse
   {
      $message = $this->getMessage();
      $chatId = (string)$message->getChat()->getId();

      require_once __DIR__ . '/../inc/bot.class.php';
      require_once __DIR__ . '/../inc/fields.class.php';

      $cfg = \PluginTelegrambotBot::getConfig();
      $field = trim((string)($cfg['client_user_chat_field'] ?: $cfg['user_chat_field']));
      if ($field === '') {
         $field = 'telegram_chat_id';
      }

      $userId = \PluginTelegrambotFields::findFirstItemIdByValue('User', $field, $chatId);
      if (!$userId) {
         return Request::sendMessage([
            'chat_id' => $chatId,
            'text'    => "Не нашёл пользователя GLPI по chat_id. Проверь заполнение Fields."
         ]);
      }

      global $DB;

      // GLPI statuses: we avoid solved/closed (commonly 5/6), but installations vary.
      // We'll treat status >= 5 as resolved-like; show status < 5.
      $rows = $DB->request([
         'SELECT' => ['id', 'name', 'status', 'date_creation'],
         'FROM'   => 'glpi_tickets',
         'WHERE'  => [
            'users_id_recipient' => (int)$userId,
            ['status' => ['<', 5]]
         ],
         'ORDER'  => ['date_creation DESC'],
         'LIMIT'  => 10
      ]);

      $text = "Нерешённые (top 10) для пользователя {$userId}:\n";
      $count = 0;
      foreach ($rows as $r) {
         $count++;
         $id = (int)$r['id'];
         $name = (string)$r['name'];
         $status = (string)$r['status'];
         $date = (string)$r['date_creation'];
         $text .= "#{$id} | {$date} | status={$status}\n{$name}\n\n";
      }

      if ($count === 0) {
         $text = "Нерешённых заявок не найдено.";
      }

      return Request::sendMessage([
         'chat_id' => $chatId,
         'text'    => $text
      ]);
   }
}
