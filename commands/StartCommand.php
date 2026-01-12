<?php
declare(strict_types=1);

namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class StartCommand extends UserCommand
{
   protected $name = 'start';
   protected $description = 'Start command';
   protected $usage = '/start';
   protected $version = '1.0.0';

   public function execute(): ServerResponse
   {
      $message = $this->getMessage();
      $chat = $message->getChat();
      $chatId = (string)$chat->getId();

      require_once __DIR__ . '/../inc/bot.class.php';
      require_once __DIR__ . '/../inc/fields.class.php';

      $cfg = \PluginTelegrambotBot::getConfig();

      $field = trim((string)($cfg['client_user_chat_field'] ?: $cfg['user_chat_field']));
      if ($field === '') {
         $field = 'telegram_chat_id';
      }

      $userId = \PluginTelegrambotFields::findFirstItemIdByValue('User', $field, $chatId);

      $text = "Привет! 🤖\n";
      if ($userId) {
         $text .= "Я вижу тебя как пользователя GLPI (ID: {$userId}).\n\n";
         $text .= "Доступные команды:\n";
         $text .= "/mytickets — мои заявки\n";
         $text .= "/myunresolved — мои нерешённые\n";
         $text .= "/myassets — моя техника\n";
      } else {
         $text .= "Я не нашёл тебя в GLPI по chat_id.\n";
         $text .= "Попроси администратора заполнить поле Fields у пользователя (chat_id).\n";
      }

      return Request::sendMessage([
         'chat_id' => $chatId,
         'text'    => $text
      ]);
   }
}
