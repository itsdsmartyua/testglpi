<?php
declare(strict_types=1);

define('PLUGIN_TELEGRAMBOT_VERSION', '0.1.1');

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

/**
 * Called by GLPI on plugin install.
 * Return true to confirm successful installation.
 */
function plugin_telegrambot_install(): bool {
    // Minimal skeleton: no DB schema yet.
    return true;
}

/**
 * Called by GLPI on plugin uninstall.
 * Return true to confirm successful uninstall.
 */
function plugin_telegrambot_uninstall(): bool {
    // Minimal skeleton: nothing to cleanup yet.
    return true;
}

function plugin_telegrambot_check_prerequisites(): bool {
    return true;
}

function plugin_telegrambot_check_config(): bool {
    return true;
}
