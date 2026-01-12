<?php

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class StartCommand extends UserCommand {

   protected $name = 'start';
   protected $description = 'Start command';
   protected $usage = '/start';
   protected $version = '1.0.0';

   public function execute(): ServerResponse {
      $chat_id = $this->getMessage()->getChat()->getId();

      $text = "Доступные команды:\n";
      $text .= "/mytickets — мои заявки\n";
      $text .= "/myunresolved — мои нерешённые заявки\n";
      $text .= "/myassets — моя техника";

      return Request::sendMessage([
         'chat_id' => $chat_id,
         'text'    => $text
      ]);
   }
}
