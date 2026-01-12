<?php
declare(strict_types=1);

class PluginTelegrambotFields
{
   public static function getUserFieldValue(int $users_id, string $field_shortname): ?string
   {
      global $DB;

      $field_shortname = trim($field_shortname);
      if ($users_id <= 0 || $field_shortname === '') {
         return null;
      }

      $candidates = [
         ['table' => 'glpi_plugin_fields_user',  'idcol' => 'users_id'],
         ['table' => 'glpi_plugin_fields_users', 'idcol' => 'items_id'],
      ];

      foreach ($candidates as $c) {
         $table = $c['table'];
         $idcol = $c['idcol'];
         if (!$DB->tableExists($table)) {
            continue;
         }
         $sql = "SELECT `$field_shortname` AS val FROM `$table` WHERE `$idcol`=" . (int)$users_id . " LIMIT 1";
         $res = $DB->query($sql);
         if ($res && $DB->numrows($res) > 0) {
            $row = $DB->fetchAssoc($res);
            if (!empty($row['val'])) {
               return (string)$row['val'];
            }
         }
      }
      return null;
   }

   public static function getGroupFieldValue(int $groups_id, string $field_shortname): ?string
   {
      global $DB;

      $field_shortname = trim($field_shortname);
      if ($groups_id <= 0 || $field_shortname === '') {
         return null;
      }

      $candidates = [
         ['table' => 'glpi_plugin_fields_group',  'idcol' => 'groups_id'],
         ['table' => 'glpi_plugin_fields_groups', 'idcol' => 'items_id'],
      ];

      foreach ($candidates as $c) {
         $table = $c['table'];
         $idcol = $c['idcol'];
         if (!$DB->tableExists($table)) {
            continue;
         }
         $sql = "SELECT `$field_shortname` AS val FROM `$table` WHERE `$idcol`=" . (int)$groups_id . " LIMIT 1";
         $res = $DB->query($sql);
         if ($res && $DB->numrows($res) > 0) {
            $row = $DB->fetchAssoc($res);
            if (!empty($row['val'])) {
               return (string)$row['val'];
            }
         }
      }
      return null;
   }
}
