<?php
declare(strict_types=1);

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class StartCommand extends UserCommand
{
   protected $name = 'start';
   protected $description = 'Start';
   protected $usage = '/start';
   protected $version = '1.0.0';

   public function execute(): ServerResponse
   {
      $chat_id = $this->getMessage()->getChat()->getId();

      $text = "Привет! Доступные команды:\n"
         . "/mytickets — мои заявки\n"
         . "/myunresolved — мои нерешённые\n"
         . "/myassets — моя техника\n";

      return Request::sendMessage([
         'chat_id' => $chat_id,
         'text' => $text
      ]);
   }
}
