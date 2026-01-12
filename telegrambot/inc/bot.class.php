<?php
/*
 -------------------------------------------------------------------------
 TelegramBot plugin for GLPI
 Copyright (C) 2017 by the TelegramBot Development Team.

 https://github.com/pluginsGLPI/telegrambot
 -------------------------------------------------------------------------

 LICENSE

 This file is part of TelegramBot.

 TelegramBot is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 TelegramBot is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with TelegramBot. If not, see <http://www.gnu.org/licenses/>.
 --------------------------------------------------------------------------
 */

require GLPI_ROOT . '/plugins/telegrambot/vendor/autoload.php';

use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;

class PluginTelegrambotBot {

   public const BOT_NOTIFICATION = 'notification';
   public const BOT_CLIENT = 'client';

   static public function getConfig($key) {
      $config = Config::getConfigurationValues('plugin:telegrambot');
      return $config[$key] ?? null;
   }

   static public function setConfig($key, $value) {
      Config::setConfigurationValues('plugin:telegrambot', [$key => $value]);
   }

   static public function sendMessage($recipient, $content, string $bot_type = self::BOT_NOTIFICATION) {
      $targets = self::resolveTargets($recipient, $bot_type);
      if (!$targets) {
         return false;
      }

      $telegram = self::getTelegramInstance($bot_type);
      foreach ($targets as $target) {
         $payload = [
            'chat_id' => $target['chat_id'],
            'text'    => $content
         ];

         if (!empty($target['topic_id'])) {
            $payload['message_thread_id'] = (int) $target['topic_id'];
         }

         Request::sendMessage($payload);
      }

      return true;
   }

   static public function getUpdates(string $bot_type = self::BOT_CLIENT) {
      $response = 'ok';

      try {
         $telegram = self::getTelegramInstance($bot_type);
         $telegram->enableMySql(self::getDBCredentials(), 'glpi_plugin_telegrambot_');
         $telegram->handleGetUpdates();
      } catch (Longman\TelegramBot\Exception\TelegramException $e) {
         $response = $e->getMessage();
      }

      return $response;
   }

   static public function getUserIdByChatId(int $chat_id, string $bot_type = self::BOT_CLIENT): ?int {
      $field_name = self::getUserChatField($bot_type);
      if (!$field_name) {
         return null;
      }

      return PluginTelegrambotFields::findItemIdByFieldValue('User', $field_name, $chat_id);
   }

   private static function resolveTargets($recipient, string $bot_type): array {
      $targets = [];

      if (is_array($recipient)) {
         foreach ($recipient as $entry) {
            $targets = array_merge($targets, self::resolveTargets($entry, $bot_type));
         }
         return $targets;
      }

      if (is_numeric($recipient)) {
         $targets[] = self::getTargetForUser((int) $recipient, $bot_type);
      } elseif (is_string($recipient) && strpos($recipient, 'group:') === 0) {
         $group_id = (int) substr($recipient, strlen('group:'));
         $targets[] = self::getTargetForGroup($group_id, $bot_type);
      }

      return array_values(array_filter($targets));
   }

   private static function getTargetForUser(int $user_id, string $bot_type): ?array {
      $chat_id = self::getUserChatId($user_id, $bot_type);
      if (!$chat_id) {
         return null;
      }

      return [
         'chat_id'  => $chat_id,
         'topic_id' => self::getUserTopicId($user_id, $bot_type)
      ];
   }

   private static function getTargetForGroup(int $group_id, string $bot_type): ?array {
      $chat_id = self::getGroupChatId($group_id, $bot_type);
      if (!$chat_id) {
         return null;
      }

      return [
         'chat_id'  => $chat_id,
         'topic_id' => self::getGroupTopicId($group_id, $bot_type)
      ];
   }

   private static function getUserChatId(int $user_id, string $bot_type) {
      $field_name = self::getUserChatField($bot_type);
      if (!$field_name) {
         return null;
      }

      return PluginTelegrambotFields::getFieldValue('User', $user_id, $field_name);
   }

   private static function getUserTopicId(int $user_id, string $bot_type) {
      $field_name = self::getUserTopicField($bot_type);
      if (!$field_name) {
         return null;
      }

      return PluginTelegrambotFields::getFieldValue('User', $user_id, $field_name);
   }

   private static function getGroupChatId(int $group_id, string $bot_type) {
      $field_name = self::getGroupChatField($bot_type);
      if (!$field_name) {
         return null;
      }

      return PluginTelegrambotFields::getFieldValue('Group', $group_id, $field_name);
   }

   private static function getGroupTopicId(int $group_id, string $bot_type) {
      $field_name = self::getGroupTopicField($bot_type);
      if (!$field_name) {
         return null;
      }

      return PluginTelegrambotFields::getFieldValue('Group', $group_id, $field_name);
   }

   private static function getUserChatField(string $bot_type): ?string {
      $value = self::getBotFieldConfig($bot_type, 'user_chat_field');
      return $value ?: null;
   }

   private static function getUserTopicField(string $bot_type): ?string {
      $value = self::getBotFieldConfig($bot_type, 'user_topic_field');
      return $value ?: null;
   }

   private static function getGroupChatField(string $bot_type): ?string {
      $value = self::getBotFieldConfig($bot_type, 'group_chat_field');
      return $value ?: null;
   }

   private static function getGroupTopicField(string $bot_type): ?string {
      $value = self::getBotFieldConfig($bot_type, 'group_topic_field');
      return $value ?: null;
   }

   private static function getBotFieldConfig(string $bot_type, string $suffix): ?string {
      if ($bot_type === self::BOT_CLIENT) {
         $client_key = 'client_' . $suffix;
         $client_value = self::getConfig($client_key);
         if (!empty($client_value)) {
            return $client_value;
         }
      }

      return self::getConfig($suffix);
   }

   private static function getTelegramInstance(string $bot_type): Telegram {
      $bot_api_key = self::getConfig($bot_type . '_token');
      $bot_username = self::getConfig($bot_type . '_bot_username');

      $telegram = new Telegram($bot_api_key, $bot_username);

      if ($bot_type === self::BOT_CLIENT) {
         $telegram->addCommandsPaths([GLPI_ROOT . '/plugins/telegrambot/commands']);
      }

      return $telegram;
   }

   private static function getDBCredentials(): array {
      global $DB;

      return [
         'host'     => $DB->dbhost,
         'user'     => $DB->dbuser,
         'password' => $DB->dbpassword,
         'database' => $DB->dbdefault,
      ];
   }
}
