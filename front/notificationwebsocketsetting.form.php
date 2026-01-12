<?php
include ("../../../inc/includes.php");

global $CFG_GLPI;
header('Content-Type: text/plain; charset=utf-8');

echo "notifications_telegram = " . ($CFG_GLPI['notifications_telegram'] ?? 'NULL') . PHP_EOL;
echo "modes:" . PHP_EOL;

if (class_exists('Notification_NotificationTemplate')) {
   $modes = Notification_NotificationTemplate::getModes();
   foreach ($modes as $k => $v) {
      echo "- $k: " . ($v['label'] ?? '') . " (from=" . ($v['from'] ?? '') . ")" . PHP_EOL;
   }
} else {
   echo "Notification_NotificationTemplate class NOT loaded" . PHP_EOL;
}
