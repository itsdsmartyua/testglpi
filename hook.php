<?php
declare(strict_types=1);

function plugin_init_telegrambot(): void
{
   global $PLUGIN_HOOKS;

   $PLUGIN_HOOKS['csrf_compliant']['telegrambot'] = true;

   // Add "Setup > Plugins > Telegram Bot" configuration page
   $PLUGIN_HOOKS['config_page']['telegrambot'] = 'front/config.form.php';
}

function plugin_telegrambot_install(): bool
{
   return true;
}

function plugin_telegrambot_uninstall(): bool
{
   return true;
}
