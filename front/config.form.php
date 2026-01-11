<?php
declare(strict_types=1);

include('../../../inc/includes.php');

use GlpiPlugin\Telegrambot\TelegrambotConfig;

Session::checkRight('config', READ);

if (isset($_POST['save_telegrambot_config'])) {
   Session::checkRight('config', UPDATE);
   TelegrambotConfig::saveFromPost($_POST);
   Html::back();
   exit;
}

Html::header('Telegram Bot', $_SERVER['PHP_SELF'], 'config', 'plugins');
TelegrambotConfig::showForm();
Html::footer();
