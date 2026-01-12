<?php
declare(strict_types=1);

include_once __DIR__ . '/../../../inc/includes.php';

global $CFG_GLPI;

// Rights
if (isset($_POST['update'])) {
   Session::checkRight('config', UPDATE);
} else {
   Session::checkRight('config', READ);
}

require_once __DIR__ . '/../inc/bot.class.php';
require_once __DIR__ . '/../inc/notificationwebsocketsetting.class.php';

$setting = new PluginTelegrambotNotificationWebsocketSetting();

if (isset($_POST['update'])) {
   $setting->postForm($_POST);

   Html::redirect($CFG_GLPI['root_doc'] . '/plugins/telegrambot/front/notificationwebsocketsetting.form.php');
   exit;
}

Html::header(__('Telegram', 'telegrambot'), $_SERVER['PHP_SELF'], 'config', 'notification');

$setting->showForm(1, []);

Html::footer();
