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

define('PLUGIN_TELEGRAMBOT_VERSION', '11.0.4');

/**
 * Init hooks of the plugin.
 * REQUIRED
 *
 * @return void
 */
function plugin_init_telegrambot() {
   global $PLUGIN_HOOKS;

   $PLUGIN_HOOKS['csrf_compliant']['telegrambot'] = true;

   $plugin = new Plugin();

   if ($plugin->isActivated('telegrambot')) {
      $mode = Notification_NotificationTemplate::MODE_WEBSOCKET;
      if (defined('Notification_NotificationTemplate::MODE_TELEGRAM')) {
         $mode = Notification_NotificationTemplate::MODE_TELEGRAM;
      }

      Notification_NotificationTemplate::registerMode(
         $mode,
         __('Telegram', 'plugin_telegrambot'),
         'telegrambot'
      );
   }
}

/**
 * Get the name and the version of the plugin
 * REQUIRED
 *
 * @return array
 */
function plugin_version_telegrambot() {
   return [
      'name'           => 'TelegramBot',
      'version'        => PLUGIN_TELEGRAMBOT_VERSION,
      'author'         => '<a href="http://trulymanager.com" target="_blank">Truly Systems</a>',
      'license'        => 'GPLv2+',
      'homepage'       => 'https://github.com/pluginsGLPI/telegrambot',
      'minGlpiVersion' => '11.0.0'
   ];
}

/**
 * Check pre-requisites before install
 * OPTIONNAL, but recommanded
 *
 * @return boolean
 */
function plugin_telegrambot_check_prerequisites() {
   // Strict version check (could be less strict, or could allow various version)
   if (version_compare(GLPI_VERSION, '11.0.0', 'lt')) {
      if (method_exists('Plugin', 'messageIncompatible')) {
         echo Plugin::messageIncompatible('core', '11.0.0');
      } else {
         echo "This plugin requires GLPI >= 11.0.0";
      }
      return false;
   }
   return true;
}

/**
 * Check configuration process
 *
 * @param boolean $verbose Whether to display message on failure. Defaults to false
 *
 * @return boolean
 */
function plugin_telegrambot_check_config($verbose = false) {
   if (true) { // Your configuration check
      return true;
   }

   if ($verbose) {
      _e('Installed / not configured', 'telegrambot');
   }
   return false;
}
