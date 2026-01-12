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

   // show current values (masked token)
   $rows = [];
   $res = $DB->request([
      'FROM'  => $table,
      'ORDER' => 'name'
   ]);
   foreach ($res as $row) {
      $val = (string)($row['value'] ?? '');
      if ($row['name'] === 'bot_token' && $val !== '') {
         $val = str_repeat('*', max(4, min(32, strlen($val))));
      }
      $rows[] = [
         'name'  => $row['name'],
         'value' => $val
      ];
   }

   echo '<table class="tab_cadre_fixehov">';
   echo '<tr><th>Name</th><th>Value</th></tr>';
   foreach ($rows as $r) {
      echo '<tr><td>' . htmlspecialchars($r['name']) . '</td><td>' . htmlspecialchars($r['value']) . '</td></tr>';
   }
   echo '</table>';
} else {
   echo '<div class="alert alert-warning">DB table <code>' . $table . '</code> not found ❗</div>';
}

echo '<p>Next step: make editable form + send test message.</p>';
echo '</div>';

Html::footer();
