<?php
declare(strict_types=1);

if (!defined('GLPI_ROOT')) {
   die('Sorry. You cannot access this file directly.');
}

function plugin_telegrambot_cronmessagelistener(): int
{
   require_once __DIR__ . '/inc/cron.class.php';
   return plugin_telegrambot_cronMessagelistener();
}
