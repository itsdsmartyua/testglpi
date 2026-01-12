<?php
declare(strict_types=1);

/**
 * Reads values written by plugin "Fields" for Users and Groups.
 * IMPORTANT: we do NOT store chat_id/topic_id in our own tables.
 *
 * Fields plugin schemas differ by version; this class tries multiple known patterns.
 */
class PluginTelegrambotFields
{
   /**
    * Get single field value for item.
    */
   public static function getValue(string $itemtype, int $itemsId, string $fieldName): ?string
   {
      global $DB;

      $fieldName = trim($fieldName);
      if ($fieldName === '' || $itemsId <= 0) {
         return null;
      }

      // 1) If Fields provides an API class/method, try it (best case)
      // (We do defensive checks; if not available, fall back to DB probing.)
      try {
         if (class_exists('PluginFieldsField') && method_exists('PluginFieldsField', 'getFieldValue')) {
            /** @noinspection PhpUndefinedMethodInspection */
            $v = PluginFieldsField::getFieldValue($itemtype, $itemsId, $fieldName);
            if ($v !== null && $v !== '') {
               return is_scalar($v) ? (string)$v : null;
            }
         }
      } catch (\Throwable $e) {
         // ignore and fallback
      }

      // 2) Try a generic table "glpi_plugin_fields_values" (some installs)
      if ($DB->tableExists('glpi_plugin_fields_values')) {
         $where = [
            'itemtype' => $itemtype,
            'items_id' => $itemsId,
         ];

         // some schemas use "name" or "field" to store field identifier
         if ($DB->fieldExists('glpi_plugin_fields_values', 'name')) {
            $where['name'] = $fieldName;
         } else if ($DB->fieldExists('glpi_plugin_fields_values', 'field')) {
            $where['field'] = $fieldName;
         }

         $it = $DB->request([
            'FROM'  => 'glpi_plugin_fields_values',
            'WHERE' => $where,
            'LIMIT' => 1
         ]);

         $row = $it->current();
         if ($row) {
            foreach (['value', 'content', 'text'] as $col) {
               if (array_key_exists($col, $row) && $row[$col] !== null && $row[$col] !== '') {
                  return (string)$row[$col];
               }
            }
         }
      }

      // 3) Container tables: "glpi_plugin_fields_fields" + per-container "glpi_plugin_fields_containers_*"
      // Very common: values stored in a table named "glpi_plugin_fields_container_<ID>" OR "glpi_plugin_fields_<something>"
      // We'll locate field by name, get container id, then try common table naming patterns.
      if ($DB->tableExists('glpi_plugin_fields_fields')) {
         $fieldRow = $DB->request([
            'FROM'  => 'glpi_plugin_fields_fields',
            'WHERE' => [
               'name'     => $fieldName,
               'itemtype' => $itemtype
            ],
            'LIMIT' => 1
         ])->current();

         if ($fieldRow && isset($fieldRow['plugin_fields_containers_id'])) {
            $containerId = (int)$fieldRow['plugin_fields_containers_id'];
            if ($containerId > 0) {
               $candidateTables = [
                  'glpi_plugin_fields_container_' . $containerId,
                  'glpi_plugin_fields_containers_' . $containerId,
                  'glpi_plugin_fields_' . $containerId
               ];

               foreach ($candidateTables as $t) {
                  if (!$DB->tableExists($t)) {
                     continue;
                  }
                  // usually column equals field name OR "field_<id>"
                  $colByName = $fieldName;
                  $colById   = 'field_' . (int)$fieldRow['id'];

                  $selectCol = null;
                  if ($DB->fieldExists($t, $colByName)) {
                     $selectCol = $colByName;
                  } else if ($DB->fieldExists($t, $colById)) {
                     $selectCol = $colById;
                  }

                  if ($selectCol) {
                     $r = $DB->request([
                        'FROM'  => $t,
                        'WHERE' => ['items_id' => $itemsId],
                        'LIMIT' => 1
                     ])->current();

                     if ($r && array_key_exists($selectCol, $r) && $r[$selectCol] !== null && $r[$selectCol] !== '') {
                        return (string)$r[$selectCol];
                     }
                  }
               }
            }
         }
      }

      return null;
   }

   /**
    * Find item IDs by field value (for /start, /mytickets, etc).
    * Returns first match (or null) for performance.
    */
   public static function findFirstItemIdByValue(string $itemtype, string $fieldName, string $value): ?int
   {
      global $DB;

      $fieldName = trim($fieldName);
      $value     = trim($value);

      if ($fieldName === '' || $value === '') {
         return null;
      }

      // Try API first
      try {
         if (class_exists('PluginFieldsField') && method_exists('PluginFieldsField', 'findItems')) {
            /** @noinspection PhpUndefinedMethodInspection */
            $ids = PluginFieldsField::findItems($itemtype, $fieldName, $value);
            if (is_array($ids) && count($ids) > 0) {
               return (int)array_values($ids)[0];
            }
         }
      } catch (\Throwable $e) {
         // ignore
      }

      // generic values table
      if ($DB->tableExists('glpi_plugin_fields_values')) {
         $where = [
            'itemtype' => $itemtype,
         ];

         if ($DB->fieldExists('glpi_plugin_fields_values', 'name')) {
            $where['name'] = $fieldName;
         } else if ($DB->fieldExists('glpi_plugin_fields_values', 'field')) {
            $where['field'] = $fieldName;
         }

         // content column differs
         $valueCol = null;
         foreach (['value', 'content', 'text'] as $c) {
            if ($DB->fieldExists('glpi_plugin_fields_values', $c)) {
               $valueCol = $c;
               break;
            }
         }

         if ($valueCol) {
            $req = $DB->request([
               'SELECT' => ['items_id'],
               'FROM'   => 'glpi_plugin_fields_values',
               'WHERE'  => $where + [$valueCol => $value],
               'LIMIT'  => 1
            ])->current();

            if ($req && isset($req['items_id'])) {
               return (int)$req['items_id'];
            }
         }
      }

      // container-based schema
      if ($DB->tableExists('glpi_plugin_fields_fields')) {
         $fieldRow = $DB->request([
            'FROM'  => 'glpi_plugin_fields_fields',
            'WHERE' => [
               'name'     => $fieldName,
               'itemtype' => $itemtype
            ],
            'LIMIT' => 1
         ])->current();

         if ($fieldRow && isset($fieldRow['plugin_fields_containers_id'])) {
            $containerId = (int)$fieldRow['plugin_fields_containers_id'];
            $candidateTables = [
               'glpi_plugin_fields_container_' . $containerId,
               'glpi_plugin_fields_containers_' . $containerId,
               'glpi_plugin_fields_' . $containerId
            ];

            foreach ($candidateTables as $t) {
               if (!$DB->tableExists($t)) {
                  continue;
               }

               $colByName = $fieldName;
               $colById   = 'field_' . (int)$fieldRow['id'];

               $selectCol = null;
               if ($DB->fieldExists($t, $colByName)) {
                  $selectCol = $colByName;
               } else if ($DB->fieldExists($t, $colById)) {
                  $selectCol = $colById;
               }

               if ($selectCol) {
                  $r = $DB->request([
                     'SELECT' => ['items_id'],
                     'FROM'   => $t,
                     'WHERE'  => [$selectCol => $value],
                     'LIMIT'  => 1
                  ])->current();

                  if ($r && isset($r['items_id'])) {
                     return (int)$r['items_id'];
                  }
               }
            }
         }
      }

      return null;
   }
}
