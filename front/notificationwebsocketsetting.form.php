<?php
declare(strict_types=1);

require_once __DIR__ . '/../../../inc/includes.php';

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access this file directly");
}

Session::checkLoginUser();

$setting = new PluginTelegrambotNotificationWebsocketSetting();

if (isset($_POST['update'])) {
   if ($setting->postForm($_POST)) {
      Html::back();
   }
   Html::back();
}

Html::header(__('Telegram', 'telegrambot'), $_SERVER['PHP_SELF'], 'config', 'notification', 'config');

echo "<div class='center'>";
$setting->showForm();
echo "</div>";

Html::footer();
