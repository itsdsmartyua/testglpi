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

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access this file directly");
}

if (!class_exists('NotificationSetting') && class_exists('Glpi\\Notification\\NotificationSetting')) {
   class_alias('Glpi\\Notification\\NotificationSetting', 'NotificationSetting');
}

/**
*  This class manages the telegram notifications settings
*/
class PluginTelegrambotNotificationWebsocketSetting extends NotificationSetting {

   static function getTypeName($nb = 0) {
      return __('Telegram followups configuration', 'telegrambot');
   }

   public function getEnableLabel() {
      return __('Enable followups via Telegram', 'telegrambot');
   }

   static public function getMode() {
      if (defined('Notification_NotificationTemplate::MODE_TELEGRAM')) {
         return Notification_NotificationTemplate::MODE_TELEGRAM;
      }

      return Notification_NotificationTemplate::MODE_WEBSOCKET;
   }

   function showFormConfig($options = []) {
      $notification_token = PluginTelegrambotBot::getConfig('notification_token');
      $notification_bot_username = PluginTelegrambotBot::getConfig('notification_bot_username');
      $client_token = PluginTelegrambotBot::getConfig('client_token');