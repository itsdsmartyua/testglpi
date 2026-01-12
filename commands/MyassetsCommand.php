<?php
declare(strict_types=1);

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class MyassetsCommand extends UserCommand
{
   protected $name = 'myassets';
   protected $description = 'List my assets';
   protected $usage = '/myassets';
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

      // Generic relation: glpi_items_users (itemtype/items_id/users_id)
      if (!$DB->tableExists('glpi_items_users')) {
         return Request::sendMessage([
            'chat_id' => $chatId,
            'text'    => "В GLPI не найдена таблица связей техники (glpi_items_users)."
         ]);
      }

      $rows = $DB->request([
         'SELECT' => ['itemtype', 'items_id'],
         'FROM'   => 'glpi_items_users',
         'WHERE'  => ['users_id' => (int)$userId],
         'ORDER'  => ['itemtype ASC'],
         'LIMIT'  => 30
      ]);

      $lines = [];
      foreach ($rows as $r) {
         $itemtype = (string)$r['itemtype'];
         $itemsId  = (int)$r['items_id'];
         if ($itemsId <= 0 || $itemtype === '') {
            continue;
         }

         $name = null;
         $serial = null;

         // Best-effort lookups for common itemtypes
         $table = null;
         if ($itemtype === 'Computer') {
            $table = 'glpi_computers';
         } elseif ($itemtype === 'Monitor') {
            $table = 'glpi_monitors';
         } elseif ($itemtype === 'Peripheral') {
            $table = 'glpi_peripherals';
         } elseif ($itemtype === 'Printer') {
            $table = 'glpi_printers';
         } elseif ($itemtype === 'Phone') {
            $table = 'glpi_phones';
         }

         if ($table && $DB->tableExists($table)) {
            $row = $DB->request([
               'SELECT' => ['name', 'serial', 'otherserial'],
               'FROM'   => $table,
               'WHERE'  => ['id' => $itemsId],
               'LIMIT'  => 1
            ])->current();

            if ($row) {
               $name = (string)($row['name'] ?? '');
               $serial = (string)($row['serial'] ?? $row['otherserial'] ?? '');
            }
         }

         $label = $itemtype . " #" . $itemsId;
         if ($name) {
            $label .= " — " . $name;
         }
         if ($serial) {
            $label .= " (S/N: " . $serial . ")";
         }

         $lines[] = $label;
      }

      if (count($lines) === 0) {
         $text = "За пользователем {$userId} не закреплена техника.";
      } else {
         $text = "Твоя техника (до 30 записей):\n" . implode("\n", $lines);
      }

      return Request::sendMessage([
         'chat_id' => $chatId,
         'text'    => $text
      ]);
   }
}
