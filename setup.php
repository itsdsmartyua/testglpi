<?php
declare(strict_types=1);

use Plugin;

function plugin_version_telegrambot(): array
{
    return [
        'name'           => 'Telegram Bot',
        'version'        => '0.2.0',
        'author'         => 'itsdsmartyua',
        'license'        => 'GPLv2+',
        'homepage'       => 'https://github.com/itsdsmartyua/testglpi',
        'requirements'   => [
            'glpi' => [
                'min' => '11.0.4',
                'max' => '11.9.9',
            ],
            'php'  => [
                'min' => '8.1'
            ]
        ]
    ];
}

function plugin_telegrambot_check_prerequisites(): bool
{
    return true;
}

function plugin_telegrambot_check_config(): bool
{
    return true;
}

function plugin_init_telegrambot(): void
{
    global $PLUGIN_HOOKS;

    $PLUGIN_HOOKS['csrf_compliant']['telegrambot'] = true;
    $PLUGIN_HOOKS['config_page']['telegrambot']    = 'front/config.form.php';

    // Register notification mode only when plugin is activated
    $plugin = new Plugin();
    if ($plugin->isActivated('telegrambot')) {
        // GLPI 11: notification modes are discovered by class naming convention:
        // - NotificationTelegrambot (sender)
        // - NotificationTelegrambotSetting (mode settings)
        // These classes must exist and be autoloadable.
        // No extra registerMode() call is required.
    }
}
