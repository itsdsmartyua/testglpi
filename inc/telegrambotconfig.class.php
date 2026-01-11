<?php
declare(strict_types=1);

namespace GlpiPlugin\Telegrambot;

use Config;
use Html;
use Session;

final class TelegrambotConfig
{
   private const CTX = 'telegrambot';

   public static function get(): array
   {
      return Config::getConfigurationValues(self::CTX, [
         'bot_token_out',
         'parse_mode',
         'field_chat_id',
         'field_enabled',
         'field_out_enabled',
         'debug',
      ]);
   }

   public static function saveFromPost(array $post): void
   {
      $values = [
         'bot_token_out'   => (string)($post['bot_token_out'] ?? ''),
         'parse_mode'      => (string)($post['parse_mode'] ?? 'HTML'),
         'field_chat_id'   => (string)($post['field_chat_id'] ?? 'tg_chat_id'),
         'field_enabled'   => (string)($post['field_enabled'] ?? 'tg_enabled'),
         'field_out_enabled' => (string)($post['field_out_enabled'] ?? 'tg_bot_out_enabled'),
         'debug'           => isset($post['debug']) ? 1 : 0,
      ];

      Config::setConfigurationValues(self::CTX, $values);
      Session::addMessageAfterRedirect(__('Saved'), true, INFO);
   }

   public static function showForm(): void
   {
      if (!((bool)Session::haveRight('config', UPDATE))) {
         Html::displayRightError();
         return;
      }

      $c = self::get();

      $botToken = (string)($c['bot_token_out'] ?? '');
      $parseMode = (string)($c['parse_mode'] ?? 'HTML');

      $fieldChatId = (string)($c['field_chat_id'] ?? 'tg_chat_id');
      $fieldEnabled = (string)($c['field_enabled'] ?? 'tg_enabled');
      $fieldOutEnabled = (string)($c['field_out_enabled'] ?? 'tg_bot_out_enabled');

      $debug = (int)($c['debug'] ?? 0);

      echo "<form method='post' action=''>";
      echo "<table class='tab_cadre_fixe'>";

      echo "<tr><th colspan='2'>Telegram Bot (telegrambot)</th></tr>";

      echo "<tr class='tab_bg_1'>";
      echo "<td style='width:40%'>Bot token (outbound)</td>";
      echo "<td><input type='password' name='bot_token_out' style='width: 420px' value='" . Html::cleanInputText($botToken) . "'></td>";
      echo "</tr>";

      echo "<tr class='tab_bg_1'>";
      echo "<td>Parse mode</td>";
      echo "<td><select name='parse_mode'>";
      foreach (['HTML', 'Markdown', 'MarkdownV2'] as $mode) {
         $selected = ($parseMode === $mode) ? " selected='selected'" : '';
         echo "<option value='" . Html::cleanInputText($mode) . "'$selected>" . Html::cleanInputText($mode) . "</option>";
      }
      echo "</select></td>";
      echo "</tr>";

      echo "<tr class='tab_bg_1'><th colspan='2'>Fields plugin (User) — field names</th></tr>";

      echo "<tr class='tab_bg_1'>";
      echo "<td>Chat ID field</td>";
      echo "<td><input type='text' name='field_chat_id' style='width: 420px' value='" . Html::cleanInputText($fieldChatId) . "'></td>";
      echo "</tr>";

      echo "<tr class='tab_bg_1'>";
      echo "<td>Enabled field</td>";
      echo "<td><input type='text' name='field_enabled' style='width: 420px' value='" . Html::cleanInputText($fieldEnabled) . "'></td>";
      echo "</tr>";

      echo "<tr class='tab_bg_1'>";
      echo "<td>Outbound bot enabled field</td>";
      echo "<td><input type='text' name='field_out_enabled' style='width: 420px' value='" . Html::cleanInputText($fieldOutEnabled) . "'></td>";
      echo "</tr>";

      echo "<tr class='tab_bg_1'>";
      echo "<td>Debug log</td>";
      echo "<td><label><input type='checkbox' name='debug' value='1' " . ($debug ? "checked='checked'" : '') . "> Write log into files/_log/telegrambot.log</label></td>";
      echo "</tr>";

      echo "<tr class='tab_bg_2'><td class='center' colspan='2'>";
      echo "<button class='submit' type='submit' name='save_telegrambot_config' value='1'>Save</button>";
      echo "</td></tr>";

      echo "</table>";

      echo Html::hidden('_glpi_csrf_token', ['value' => Session::getNewCSRFToken()]);
      echo "</form>";
   }
}
