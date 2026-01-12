<?php
declare(strict_types=1);

if (!defined('GLPI_ROOT')) {
   include_once('../../inc/includes.php');
}

Session::checkCentralAccess();

require_once __DIR__ . '/../inc/bot.class.php';
require_once __DIR__ . '/../inc/notificationwebsocketsetting.class.php';

$setting = new PluginTelegrambotNotificationWebsocketSetting();

if (isset($_POST['update'])) {
   // Save
   $setting->postForm($_POST);
   Html::back();
   exit;
}

// Display
Html::header(__('Telegram', 'telegrambot'), $_SERVER['PHP_SELF'], 'config', 'notification');

$setting->showForm(1, []);

Html::footer();
