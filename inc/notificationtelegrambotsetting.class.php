<?php
declare(strict_types=1);

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access this file directly");
}

class PluginTelegrambotNotificationTelegrambotSetting extends NotificationSetting
{
   public static function getTypeName($nb = 0): string
   {
      return __('Telegram notifications configuration', 'telegrambot');
   }

   public function getEnableLabel(): string
   {
      return __('Enable Telegram notifications', 'telegrambot');
   }

   public static function getMode(): string
   {
      return 'telegrambot';
   }

   public static function canUpdate(): bool
   {
      return (bool) Session::haveRight('config', UPDATE);
   }

   public function showFormConfig($options = []): void
   {
      global $CFG_GLPI;

      Session::checkRight('config', UPDATE);

      $conf = Config::getConfigurationValues('plugin:telegrambot', [
         'bot_token_out',
         'parse_mode',
         'fields_container',
         'field_chat_id',
         'field_enabled',
         'field_out_enabled',
         'debug',
      ]);

      $botToken   = (string)($conf['bot_token_out'] ?? '');
      $parseMode  = (string)($conf['parse_mode'] ?? 'HTML');

      $container  = (string)($conf['fields_container'] ?? 'telegram');
      $fieldChat  = (string)($conf['field_chat_id'] ?? 'tg_chat_id_notify');
      $fieldEn    = (string)($conf['field_enabled'] ?? 'tg_enabled');
      $fieldOutEn = (string)($conf['field_out_enabled'] ?? 'tg_bot_out_enabled');

      $debug      = (int)($conf['debug'] ?? 0);

      echo "<form action='" . Toolbox::getItemTypeFormURL(__CLASS__) . "' method='post'>";
      echo Html::hidden('config_context', ['value' => 'plugin:telegrambot']);
      echo "<input type='hidden' name='id' value='1'>";

      echo "<table class='tab_cadre_fixe'>";
      echo "<tr class='tab_bg_1'><th colspan='4'>Telegram (telegrambot)</th></tr>";

      if (empty($CFG_GLPI['notifications_telegrambot'])) {
         echo "<tr class='tab_bg_1'><td colspan='4'>"
            . __('Notifications are disabled.') . " "
            . "<a href='{$CFG_GLPI['root_doc']}/front/setup.notification.php'>" . __('See configuration') . "</a>"
            . "</td></tr>";
      }

      echo "<tr class='tab_bg_1'>";
      echo "<td style='width:25%'>Bot token (outbound)</td>";
      echo "<td style='width:75%' colspan='3'><input type='password' name='bot_token_out' style='width:100%' value='" . Html::cleanInputText($botToken) . "'></td>";
      echo "</tr>";

      echo "<tr class='tab_bg_1'>";
      echo "<td>Parse mode</td>";
      echo "<td colspan='3'><select name='parse_mode'>";
      foreach (['HTML', 'Markdown', 'MarkdownV2'] as $m) {
         $sel = ($parseMode === $m) ? " selected='selected'" : '';
         echo "<option value='" . Html::cleanInputText($m) . "'$sel>" . Html::cleanInputText($m) . "</option>";
      }
      echo "</select></td>";
      echo "</tr>";

      echo "<tr class='tab_bg_1'><th colspan='4'>Fields(User) mapping</th></tr>";

      echo "<tr class='tab_bg_1'><td>Container internal name</td>";
      echo "<td colspan='3'><input type='text' name='fields_container' style='width:260px' value='" . Html::cleanInputText($container) . "'></td></tr>";

      echo "<tr class='tab_bg_1'><td>Chat ID field</td>";
      echo "<td colspan='3'><input type='text' name='field_chat_id' style='width:260px' value='" . Html::cleanInputText($fieldChat) . "'></td></tr>";

      echo "<tr class='tab_bg_1'><td>Enabled field</td>";
      echo "<td colspan='3'><input type='text' name='field_enabled' style='width:260px' value='" . Html::cleanInputText($fieldEn) . "'></td></tr>";

      echo "<tr class='tab_bg_1'><td>Outbound enabled field</td>";
      echo "<td colspan='3'><input type='text' name='field_out_enabled' style='width:260px' value='" . Html::cleanInputText($fieldOutEn) . "'></td></tr>";

      echo "<tr class='tab_bg_1'><td>Debug log</td>";
      $checked = $debug ? "checked='checked'" : '';
      echo "<td colspan='3'><label><input type='checkbox' name='debug' value='1' $checked> files/_log/telegrambot.log</label></td></tr>";

      echo "</table>";

      $options['candel'] = false;
      $this->showFormButtons($options);
      echo "</form>";
   }
}
