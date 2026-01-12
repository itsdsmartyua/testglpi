<?php
declare(strict_types=1);

define('PLUGIN_TELEGRAMBOT_VERSION', '0.1.2');

function plugin_version_telegrambot(): array {
    return [
        'name'           => 'TelegramBot',
        'version'        => PLUGIN_TELEGRAMBOT_VERSION,
        'author'         => 'Your Name',
        'license'        => 'GPLv2',
        'homepage'       => '',
        'requirements'   => [
            'glpi' => [
                'min' => '11.0.4',
                'max' => '11.0.99',
            ],
        ],
    ];
}

function plugin_telegrambot_check_prerequisites(): bool {
    return true;
}

function plugin_telegrambot_check_config(): bool {
    return true;
}

function plugin_telegrambot_install(): bool {
    global $DB;

    $install = GLPI_ROOT . '/plugins/telegrambot/sql/install.sql';
    if (file_exists($install)) {
        if (!$DB->runFile($install)) {
            return false;
        }
    }
    return true;
}

function plugin_telegrambot_uninstall(): bool {
    global $DB;

    $uninstall = GLPI_ROOT . '/plugins/telegrambot/sql/uninstall.sql';
    if (file_exists($uninstall)) {
        if (!$DB->runFile($uninstall)) {
            return false;
        }
    }
    return true;
}
