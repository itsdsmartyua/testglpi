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

/**
*  This class manages the telegram notifications settings
*/
if (class_exists('Glpi\\Notification\\NotificationSetting')) {
   class PluginTelegrambotNotificationWebsocketSetting extends Glpi\Notification\NotificationSetting {

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
      $client_bot_username = PluginTelegrambotBot::getConfig('client_bot_username');

      $user_chat_field = PluginTelegrambotBot::getConfig('user_chat_field');
      $user_topic_field = PluginTelegrambotBot::getConfig('user_topic_field');
@@ -110,26 +107,124 @@ class PluginTelegrambotNotificationWebsocketSetting extends NotificationSetting

      $out .= "<tr class='tab_bg_2'>";
      $out .= "<td> " . __('Group chat ID field') . "</td><td>";
      $out .= "<input type='text' name='group_chat_field' value='" . $group_chat_field . "' style='width: 100%'>";
      $out .= "</td><td> " . __('Group topic ID field') . "</td><td>";
      $out .= "<input type='text' name='group_topic_field' value='" . $group_topic_field . "' style='width: 100%'>";
      $out .= "</td></tr>";

      $out .= "<tr class='tab_bg_2'>";
      $out .= "<td> " . __('Client user chat ID field (optional)') . "</td><td>";
      $out .= "<input type='text' name='client_user_chat_field' value='" . $client_user_chat_field . "' style='width: 100%'>";
      $out .= "</td><td> " . __('Client user topic ID field (optional)') . "</td><td>";
      $out .= "<input type='text' name='client_user_topic_field' value='" . $client_user_topic_field . "' style='width: 100%'>";
      $out .= "</td></tr>";

      $out .= "<tr class='tab_bg_2'>";
      $out .= "<td> " . __('Client group chat ID field (optional)') . "</td><td>";
      $out .= "<input type='text' name='client_group_chat_field' value='" . $client_group_chat_field . "' style='width: 100%'>";
      $out .= "</td><td> " . __('Client group topic ID field (optional)') . "</td><td>";
      $out .= "<input type='text' name='client_group_topic_field' value='" . $client_group_topic_field . "' style='width: 100%'>";
      $out .= "</td></tr>";

      echo $out;
      $this->showFormButtons($options);
   }
   }
} else {
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
         $client_bot_username = PluginTelegrambotBot::getConfig('client_bot_username');

         $user_chat_field = PluginTelegrambotBot::getConfig('user_chat_field');
         $user_topic_field = PluginTelegrambotBot::getConfig('user_topic_field');
         $group_chat_field = PluginTelegrambotBot::getConfig('group_chat_field');
         $group_topic_field = PluginTelegrambotBot::getConfig('group_topic_field');
         $client_user_chat_field = PluginTelegrambotBot::getConfig('client_user_chat_field');
         $client_user_topic_field = PluginTelegrambotBot::getConfig('client_user_topic_field');
         $client_group_chat_field = PluginTelegrambotBot::getConfig('client_group_chat_field');
         $client_group_topic_field = PluginTelegrambotBot::getConfig('client_group_topic_field');

         $out = "<form action='" . Toolbox::getItemTypeFormURL(__CLASS__) . "' method='post'>";
         $out .= "<div>";
         $out .= "<table class='tab_cadre_fixe'>";
         $out .= "<tr class='tab_bg_1'>" .
            "<th colspan='4'>" . _n('Telegram notification', 'Telegram notifications', Session::getPluralNumber()) . "</th>" .
            "</tr>";

         $out .= "<tr class='tab_bg_2'>";
         $out .= "<td> " . __('Notification bot token') . "</td><td>";
         $out .= "<input type='text' name='notification_token' value='" . $notification_token . "' style='width: 100%'>";
         $out .= "</td><td colspan='2'>&nbsp;</td></tr>";

         $out .= "<tr class='tab_bg_2'>";
         $out .= "<td> " . __('Notification bot username') . "</td><td>";
         $out .= "<input type='text' name='notification_bot_username' value='" . $notification_bot_username . "' style='width: 100%'>";
         $out .= "</td><td colspan='2'>&nbsp;</td></tr>";

         $out .= "<tr class='tab_bg_2'>";
         $out .= "<td> " . __('Client bot token') . "</td><td>";
         $out .= "<input type='text' name='client_token' value='" . $client_token . "' style='width: 100%'>";
         $out .= "</td><td colspan='2'>&nbsp;</td></tr>";

         $out .= "<tr class='tab_bg_2'>";
         $out .= "<td> " . __('Client bot username') . "</td><td>";
         $out .= "<input type='text' name='client_bot_username' value='" . $client_bot_username . "' style='width: 100%'>";
         $out .= "</td><td colspan='2'>&nbsp;</td></tr>";

         $out .= "<tr class='tab_bg_1'>";
         $out .= "<th colspan='4'>" . __('Fields (plugin Fields)', 'telegrambot') . "</th>";
         $out .= "</tr>";

         $out .= "<tr class='tab_bg_2'>";
         $out .= "<td> " . __('User chat ID field') . "</td><td>";
         $out .= "<input type='text' name='user_chat_field' value='" . $user_chat_field . "' style='width: 100%'>";
         $out .= "</td><td> " . __('User topic ID field') . "</td><td>";
         $out .= "<input type='text' name='user_topic_field' value='" . $user_topic_field . "' style='width: 100%'>";
         $out .= "</td></tr>";

         $out .= "<tr class='tab_bg_2'>";
         $out .= "<td> " . __('Group chat ID field') . "</td><td>";
         $out .= "<input type='text' name='group_chat_field' value='" . $group_chat_field . "' style='width: 100%'>";
         $out .= "</td><td> " . __('Group topic ID field') . "</td><td>";
         $out .= "<input type='text' name='group_topic_field' value='" . $group_topic_field . "' style='width: 100%'>";
         $out .= "</td></tr>";

         $out .= "<tr class='tab_bg_2'>";
         $out .= "<td> " . __('Client user chat ID field (optional)') . "</td><td>";
         $out .= "<input type='text' name='client_user_chat_field' value='" . $client_user_chat_field . "' style='width: 100%'>";
         $out .= "</td><td> " . __('Client user topic ID field (optional)') . "</td><td>";
         $out .= "<input type='text' name='client_user_topic_field' value='" . $client_user_topic_field . "' style='width: 100%'>";
         $out .= "</td></tr>";

         $out .= "<tr class='tab_bg_2'>";
         $out .= "<td> " . __('Client group chat ID field (optional)') . "</td><td>";
         $out .= "<input type='text' name='client_group_chat_field' value='" . $client_group_chat_field . "' style='width: 100%'>";
         $out .= "</td><td> " . __('Client group topic ID field (optional)') . "</td><td>";
         $out .= "<input type='text' name='client_group_topic_field' value='" . $client_group_topic_field . "' style='width: 100%'>";
         $out .= "</td></tr>";

         echo $out;
         $this->showFormButtons($options);
      }
   }
}
