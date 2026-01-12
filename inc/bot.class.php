<?php
declare(strict_types=1);

class PluginTelegrambotBot
{
   public const CONFIG_TABLE = 'glpi_plugin_telegrambot_configs';

   /**
    * Safe read config (no direct SQL).
    */
   public static function getConfig(): array
   {
      global $DB;

      if (!$DB->tableExists(self::CONFIG_TABLE)) {
         return [];
      }

      $it = $DB->request([
         'FROM'  => self::CONFIG_TABLE,
         'LIMIT' => 1
      ]);

      foreach ($it as $row) {
         return $row;
      }

      return [];
   }

   /**
    * Safe save config (upsert first row).
    */
   public static function saveConfig(array $input): void
   {
      global $DB;

      if (!$DB->tableExists(self::CONFIG_TABLE)) {
         return;
      }

      $it = $DB->request([
         'SELECT' => ['id'],
         'FROM'   => self::CONFIG_TABLE,
         'LIMIT'  => 1
      ]);

      $id = null;
      foreach ($it as $row) {
         $id = (int)$row['id'];
         break;
      }

      // allowlist columns
      $allowed = [
         'notification_bot_token',
         'client_bot_token',
         'user_chat_field',
         'user_topic_field',
         'group_chat_field',
         'group_topic_field',
         'client_user_chat_field',
         'client_user_topic_field',
         'client_group_chat_field',
         'client_group_topic_field',
         'client_last_update_id',
         'updated_at',
      ];

      $data = [];
      foreach ($allowed as $k) {
         if (array_key_exists($k, $input)) {
            $data[$k] = $input[$k];
         }
      }

      // keep updated_at null or timestamp; most installs will leave it null
      if (!array_key_exists('updated_at', $data)) {
         $data['updated_at'] = null;
      }

      if ($id === null) {
         // defaults if not provided
         $defaults = [
            'notification_bot_token'   => '',
            'client_bot_token'         => '',
            'user_chat_field'          => 'telegram_chat_id',
            'user_topic_field'         => 'telegram_topic_id',
            'group_chat_field'         => 'telegram_chat_id',
            'group_topic_field'        => 'telegram_topic_id',
            'client_user_chat_field'   => '',
            'client_user_topic_field'  => '',
            'client_group_chat_field'  => '',
            'client_group_topic_field' => '',
            'client_last_update_id'    => 0,
            'updated_at'               => null,
         ];

         $DB->insert(self::CONFIG_TABLE, array_merge($defaults, $data));
      } else {
         $data['id'] = $id;
         $DB->update(self::CONFIG_TABLE, $data, ['id' => $id]);
      }
   }
}
