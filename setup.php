<?php
declare(strict_types=1);

define('PLUGIN_TELEGRAMBOT_VERSION', '0.1.0');

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
