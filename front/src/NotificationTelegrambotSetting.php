<?php
declare(strict_types=1);

namespace GlpiPlugin\Telegrambot;

use Config;
use Html;
use NotificationSetting;
use Session;
use Toolbox;

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access this file directly");
}

final class NotificationTelegrambotSetting extends NotificationSetting
{
   public static function getTypeName($nb = 0): string
   {
      return __('Telegram notifications configuration', 'telegrambot');
   }

   public function getEnableLabel(): string
   {
      return __('Enable followups via Telegram', 'telegrambot');
   }

   public static function getMode(): string
   {
      return 'telegrambot';
   }

   public function showFormConfig($options = []): void
   {
      global $CFG_GLPI;

      Session::checkRight('config', UPDATE);

      $conf = Config::getConfigurationValues('plugin:telegrambot', [
         'bot_token_out',
         'parse_mode',
         'fields_container',
         'field_chat_id_notify',
         'field_enabled',
         'field_out_enabled',
         'debug',
      ]);

      $out  = "<form action='" . Toolbox::getItemTypeFormURL(__CLASS__) . "' method='post'>";
      $out .= Html::hidden('config_context', ['value' => 'plugin:telegrambot']);
      $out .= "<input type='hidden' name='id' value='1'>";
      $out .= "<table class='tab_cadre_fixe'>";
      $out .= "<tr class='tab_bg_1'><th colspan='4'>Telegram (telegrambot)</th></tr>";

      if (!empty($CFG_GLPI['notifications_telegrambot'])) {
         $out .= "<tr class='tab_bg_1'><td style='width:25%'>Bot token (outbound)</td>";
         $out .= "<td colspan='3'><input type='password' name='bot_token_out' style='width:100%' value='" . Html::cleanInputText($conf['bot_token_out'] ?? '') . "'></td></tr>";

         $out .= "<tr class='tab_bg_1'><td>Parse mode</td>";
         $out .= "<td colspan='3'><input type='text' name='parse_mode' style='width:200px' value='" . Html::cleanInputText($conf['parse_mode'] ?? 'HTML') . "'></td></tr>";

         $out .= "<tr class='tab_bg_1'><th colspan='4'>Fields plugin mapping (User)</th></tr>";

         $out .= "<tr class='tab_bg_1'><td>Container internal name</td>";
         $out .= "<td colspan='3'><input type='text' name='fields_container' style='width:200px' value='" . Html::cleanInputText($conf['fields_container'] ?? 'telegram') . "'></td></tr>";

         $out .= "<tr class='tab_bg_1'><td>Chat ID field (notify bot)</td>";
         $out .= "<td colspan='3'><input type='text' name='field_chat_id_notify' style='width:200px' value='" . Html::cleanInputText($conf['field_chat_id_notify'] ?? 'tg_chat_id_notify') . "'></td></tr>";

         $out .= "<tr class='tab_bg_1'><td>Enabled field</td>";
         $out .= "<td colspan='3'><input type='text' name='field_enabled' style='width:200px' value='" . Html::cleanInputText($conf['field_enabled'] ?? 'tg_enabled') . "'></td></tr>";

         $out .= "<tr class='tab_bg_1'><td>Outbound enabled field</td>";
         $out .= "<td colspan='3'><input type='text' name='field_out_enabled' style='width:200px' value='" . Html::cleanInputText($conf['field_out_enabled'] ?? 'tg_bot_out_enabled') . "'></td></tr>";
      } else {
         $out .= "<tr class='tab_bg_1'><td colspan='4'>Notifications are disabled. ";
         $out .= "<a href='{$CFG_GLPI['root_doc']}/front/setup.notification.php'>See configuration</a></td></tr>";
      }

      $out .= "</table>";

      echo $out;
      $options['candel'] = false;
      $this->showFormButtons($options);
   }

   public static function canUpdate(): bool
   {
      return (bool) Session::haveRight('config', UPDATE);
   }
}
