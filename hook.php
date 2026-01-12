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

/**
 * Plugin install process
 *
 * @return boolean
 */
function plugin_telegrambot_install() {
   global $DB;

   $DB->runFile(GLPI_ROOT . '/plugins/telegrambot/db/install.sql');

   Config::setConfigurationValues('core', ['notifications_websocket' => 0]);
   Config::setConfigurationValues('plugin:telegrambot', [
      'notification_token' => '',
      'notification_bot_username' => '',
      'client_token' => '',
      'client_bot_username' => '',
      'user_chat_field' => 'telegram_chat_id',
      'user_topic_field' => 'telegram_topic_id',
      'group_chat_field' => 'telegram_group_chat_id',
      'group_topic_field' => 'telegram_group_topic_id',
      'client_user_chat_field' => '',
      'client_user_topic_field' => '',
      'client_group_chat_field' => '',
      'client_group_topic_field' => ''
   ]);

   CronTask::register(
      'PluginTelegrambotCron',
      'messagelistener',
      5 * MINUTE_TIMESTAMP,
      ['comment' => '', 'mode' => CronTask::MODE_EXTERNAL]
   );

   return true;
}

/**
 * Plugin uninstall process
 *
 * @return boolean
 */
function plugin_telegrambot_uninstall() {
   global $DB;
   $DB->runFile(GLPI_ROOT . '/plugins/telegrambot/db/uninstall.sql');

   $config = new Config();
   $config->deleteConfigurationValues('core', ['notifications_websocket']);
   $config->deleteConfigurationValues(
      'plugin:telegrambot',
      [
         'notification_token',
         'notification_bot_username',
         'client_token',
         'client_bot_username',
         'user_chat_field',
         'user_topic_field',
         'group_chat_field',
         'group_topic_field',
         'client_user_chat_field',
         'client_user_topic_field',
         'client_group_chat_field',
         'client_group_topic_field'
      ]
   );

   return true;
}
