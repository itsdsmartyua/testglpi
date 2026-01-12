<?php
declare(strict_types=1);

// Robust include независимо от текущего working directory
include_once __DIR__ . '/../../../inc/includes.php';

Session::checkCentralAccess();

require_once __DIR__ . '/../inc/bot.class.php';
require_once __DIR__ . '/../inc/notificationwebsocketsetting.class.php';

$setting = new PluginTelegrambotNotificationWebsocketSetting();

if (isset($_POST['update'])) {
   $setting->postForm($_POST);

   Html::redirect(Plugin::getWebDir('telegrambot', false) . '/front/notificationwebsocketsetting.form.php');
   exit;
}

Html::header(__('Telegram', 'telegrambot'), $_SERVER['PHP_SELF'], 'config', 'notification');

$setting->showForm(1, []);

Html::footer();
