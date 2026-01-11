<?php
declare(strict_types=1);

include('../../../inc/includes.php');

Session::checkRight('config', UPDATE);

// legacy class name that GLPI expects
$setting = new PluginTelegrambotNotificationTelegrambotSetting();

if (!empty($_POST['update'])) {
   $config = new Config();
   $config->update($_POST);
   Html::back();
}

Html::header(__('Notifications'), $_SERVER['PHP_SELF'], "config", "notification", "config");
$setting->display(['id' => 1]);
Html::footer();
