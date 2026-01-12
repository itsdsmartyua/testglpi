<?php
declare(strict_types=1);

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access this file directly");
}

/**
 * Kept for structure parity; GLPI 11 mainly relies on mode class above.
 * This class can be used later if you want custom event bindings,
 * but per requirement we do NOT create custom UI for events.
 */
class PluginTelegrambotNotificationEventWebsocket extends NotificationEvent
{
   // Intentionally minimal.
}
