<?php
declare(strict_types=1);

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class MyassetsCommand extends UserCommand
{
   protected $name = 'myassets';
   protected $description = 'My assets';
   protected $usage = '/myassets';
   protected $version = '1.0.0';

   public function execute(): ServerResponse
   {
      $chat_id = $this->getMessage()->getChat()->getId();
      return Request::sendMessage(['chat_id' => $chat_id, 'text' => "Команда /myassets пока заглушка."]);
   }
}
