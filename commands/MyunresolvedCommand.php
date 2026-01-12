<?php
declare(strict_types=1);

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class MyunresolvedCommand extends UserCommand
{
   protected $name = 'myunresolved';
   protected $description = 'My unresolved tickets';
   protected $usage = '/myunresolved';
   protected $version = '1.0.0';

   public function execute(): ServerResponse
   {
      $chat_id = $this->getMessage()->getChat()->getId();
      return Request::sendMessage(['chat_id' => $chat_id, 'text' => "Команда /myunresolved пока заглушка."]);
   }
}
