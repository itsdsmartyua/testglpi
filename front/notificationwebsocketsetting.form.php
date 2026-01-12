<?php

include ('../../../inc/includes.php');

Session::checkRight('config', UPDATE);
$notificationwebsocket = new PluginTelegrambotNotificationWebsocketSetting();

// TODO
if (!empty($_POST['test_webhook_send'])) {
   PluginTelegrambotNotificationWebsocket::testNotification();
   Html::back();
} else if (!empty($_POST['update'])) {
   PluginTelegrambotBot::setConfig('notification_token', $_POST['notification_token'] ?? '');
   PluginTelegrambotBot::setConfig('notification_bot_username', $_POST['notification_bot_username'] ?? '');
   PluginTelegrambotBot::setConfig('client_token', $_POST['client_token'] ?? '');
   PluginTelegrambotBot::setConfig('client_bot_username', $_POST['client_bot_username'] ?? '');
   PluginTelegrambotBot::setConfig('user_chat_field', $_POST['user_chat_field'] ?? '');
   PluginTelegrambotBot::setConfig('user_topic_field', $_POST['user_topic_field'] ?? '');
   PluginTelegrambotBot::setConfig('group_chat_field', $_POST['group_chat_field'] ?? '');
   PluginTelegrambotBot::setConfig('group_topic_field', $_POST['group_topic_field'] ?? '');
   PluginTelegrambotBot::setConfig('client_user_chat_field', $_POST['client_user_chat_field'] ?? '');
   PluginTelegrambotBot::setConfig('client_user_topic_field', $_POST['client_user_topic_field'] ?? '');
   PluginTelegrambotBot::setConfig('client_group_chat_field', $_POST['client_group_chat_field'] ?? '');
   PluginTelegrambotBot::setConfig('client_group_topic_field', $_POST['client_group_topic_field'] ?? '');
   Html::back();
}

Html::header(
   Notification::getTypeName(Session::getPluralNumber()),
   $_SERVER['PHP_SELF'],
   'config',
   'notification', 'config'
);

$notificationwebsocket->display(['id' => 1]);

Html::footer();
