<?php
declare(strict_types=1);

include_once('../../inc/includes.php');

Session::checkCentralAccess();

require_once __DIR__ . '/../inc/bot.class.php';
require_once __DIR__ . '/../inc/notificationwebsocketsetting.class.php';

$setting = new PluginTelegrambotNotificationWebsocketSetting();

if (isset($_POST['update'])) {
   $setting->postForm($_POST);

   // Redirect back to the same page (reliable)
   $url = Plugin::getWebDir('telegrambot', false) . '/front/notificationwebsocketsetting.form.php';
   Html::redirect($url);
   exit;
}

Html::header(__('Telegram', 'telegrambot'), $_SERVER['PHP_SELF'], 'config', 'notification');

$setting->showForm(1, []);

Html::footer();
