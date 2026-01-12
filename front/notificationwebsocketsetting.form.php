<?php
declare(strict_types=1);

include_once __DIR__ . '/../../../inc/includes.php';

global $CFG_GLPI;

// Rights
Session::checkRight('config', READ);

require_once __DIR__ . '/../inc/notificationwebsocketsetting.class.php';

$setting = new PluginTelegrambotNotificationWebsocketSetting();

// Save
if (isset($_POST['update'])) {
   Session::checkRight('config', UPDATE);
   $setting->postForm($_POST);
   Html::redirect($CFG_GLPI['root_doc'] . '/plugins/telegrambot/front/notificationwebsocketsetting.form.php');
   exit;
}

Html::header(__('Telegram', 'telegrambot'), $_SERVER['PHP_SELF'], 'config', 'notification');

// NON-static call (IMPORTANT)
$setting->showFormConfig([]);

Html::footer();
