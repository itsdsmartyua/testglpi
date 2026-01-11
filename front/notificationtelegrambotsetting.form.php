<?php
declare(strict_types=1);

include('../../../inc/includes.php');

use GlpiPlugin\Telegrambot\NotificationTelegrambotSetting;

Session::checkRight('config', UPDATE);

$setting = new NotificationTelegrambotSetting();

if (!empty($_POST['update'])) {
   $config = new Config();
   $config->update($_POST);
   Html::back();
}

Html::header(__('Notifications'), $_SERVER['PHP_SELF'], "config", "notification", "config");
$setting->display(['id' => 1]);
Html::footer();
