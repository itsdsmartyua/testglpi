<?php
declare(strict_types=1);

if (!defined('GLPI_ROOT')) {
   die('Direct access not allowed');
}

Session::checkRight('config', READ);

Html::header('TelegramBot', $_SERVER['PHP_SELF'], 'config', 'plugins');

global $DB;
$table = 'glpi_plugin_telegrambot_configs';

echo '<div class="center">';
echo '<h2>TelegramBot plugin</h2>';

if ($DB->tableExists($table)) {
   echo '<div class="alert alert-success">DB table <code>' . $table . '</code> exists ✅</div>';
} else {
   echo '<div class="alert alert-warning">DB table <code>' . $table . '</code> not found ❗ (install hook did not run?)</div>';
}

echo '<p>Next step: add fields + save settings + test send message.</p>';
echo '</div>';

Html::footer();
