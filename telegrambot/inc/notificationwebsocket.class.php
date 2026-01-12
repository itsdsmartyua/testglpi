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

class PluginTelegrambotNotificationWebsocket implements NotificationInterface {

   static function check($value, $options = []) {
      return true;
   }

   static function testNotification() {
      // TODO
   }

   function sendNotification($options = []) {
      $content = $options['content_text'] ?? '';

      $recipients = [];
      if (isset($options['to'])) {
         $recipients = $options['to'];
      }

      if (isset($options['users_id'])) {
         $recipients = array_merge((array) $recipients, (array) $options['users_id']);
      }

      if (isset($options['groups_id'])) {
         foreach ((array) $options['groups_id'] as $group_id) {
            $recipients[] = 'group:' . $group_id;
         }
      }

      PluginTelegrambotBot::sendMessage($recipients, $content, PluginTelegrambotBot::BOT_NOTIFICATION);
      return true;
   }
}
