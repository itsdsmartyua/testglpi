<?php

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class MyunresolvedCommand extends UserCommand {

   protected $name = 'myunresolved';
   protected $description = 'Show unresolved ticket stats';
   protected $usage = '/myunresolved';
   protected $version = '1.0.0';

   public function execute(): ServerResponse {
      $chat_id = $this->getMessage()->getChat()->getId();
      $user_id = PluginTelegrambotBot::getUserIdByChatId($chat_id, PluginTelegrambotBot::BOT_CLIENT);

      if (!$user_id) {
         return Request::sendMessage([
            'chat_id' => $chat_id,
            'text'    => 'Пользователь GLPI не найден. Проверьте заполнение полей Telegram в профиле.'
         ]);
      }

      global $DB;

      $result = $DB->request([
         'FROM'  => 'glpi_tickets',
         'WHERE' => [
            'users_id_recipient' => $user_id,
            'is_deleted'         => 0,
            'status'             => ['NOT IN', [5, 6]]
         ]
      ]);

      $count = $result->count();

      return Request::sendMessage([
         'chat_id' => $chat_id,
         'text'    => sprintf('Нерешённые заявки: %d', $count)
      ]);
   }
}
