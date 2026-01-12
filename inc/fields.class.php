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

class PluginTelegrambotFields {

   public static function getFieldValue(string $itemtype, int $items_id, string $field_name) {
      $field = self::getFieldDefinition($itemtype, $field_name);
      if (!$field) {
         return null;
      }

      $value = self::getValueFromPluginFields($field, $itemtype, $items_id);
      if ($value !== null) {
         return $value;
      }

      return self::getValueFromLegacyTable($field, $itemtype, $items_id);
   }

   public static function findItemIdByFieldValue(string $itemtype, string $field_name, $value): ?int {
      $field = self::getFieldDefinition($itemtype, $field_name);
      if (!$field) {
         return null;
      }

      $items_id = self::findItemIdFromPluginFields($field, $itemtype, $value);
      if ($items_id !== null) {
         return $items_id;
      }

      return self::findItemIdFromLegacyTable($field, $itemtype, $value);
   }

   private static function getFieldDefinition(string $itemtype, string $field_name) {
      if (!class_exists('PluginFieldsField')) {
         return null;
      }

      $field = new PluginFieldsField();
      if ($field->getFromDBByCrit(['name' => $field_name, 'itemtype' => $itemtype])) {
         return $field;
      }

      if ($field->getFromDBByCrit(['name' => $field_name])) {
         return $field;
      }

      return null;
   }

   private static function getValueFromPluginFields($field, string $itemtype, int $items_id) {
      if (class_exists('PluginFieldsField')) {
         if (method_exists('PluginFieldsField', 'getFieldValue')) {
            return PluginFieldsField::getFieldValue($field, $itemtype, $items_id);
         }

         if (method_exists($field, 'getFieldValue')) {
            return $field->getFieldValue($itemtype, $items_id);
         }

         if (method_exists('PluginFieldsField', 'getValueForItem')) {
            return PluginFieldsField::getValueForItem($itemtype, $items_id, $field->fields['id']);
         }
      }

      return null;
   }

   private static function getValueFromLegacyTable($field, string $itemtype, int $items_id) {
      global $DB;

      if (!isset($field->fields['id'])) {
         return null;
      }

      $table = 'glpi_plugin_fields_' . strtolower($itemtype);
      $column = 'field_' . $field->fields['id'];

      if (!$DB->tableExists($table)) {
         return null;
      }

      $result = $DB->request([
         'FROM'  => $table,
         'WHERE' => ['items_id' => $items_id]
      ]);

      if ($row = $result->next()) {
         return $row[$column] ?? null;
      }

      return null;
   }

   private static function findItemIdFromPluginFields($field, string $itemtype, $value): ?int {
      if (class_exists('PluginFieldsField')) {
         if (method_exists('PluginFieldsField', 'getItemIdByFieldValue')) {
            return PluginFieldsField::getItemIdByFieldValue($itemtype, $field->fields['id'], $value);
         }
      }

      return null;
   }

   private static function findItemIdFromLegacyTable($field, string $itemtype, $value): ?int {
      global $DB;

      if (!isset($field->fields['id'])) {
         return null;
      }

      $table = 'glpi_plugin_fields_' . strtolower($itemtype);
      $column = 'field_' . $field->fields['id'];

      if (!$DB->tableExists($table)) {
         return null;
      }

      $result = $DB->request([
         'FROM'  => $table,
         'WHERE' => [$column => $value]
      ]);

      if ($row = $result->next()) {
         return (int) $row['items_id'];
      }

      return null;
   }
}
