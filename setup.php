<?php
declare(strict_types=1);

define('PLUGIN_TELEGRAMBOT_VERSION', '0.1.3');

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

/**
 * Create / fix DB schema and seed defaults.
 * This is used both for install and update to be robust.
 */
function plugin_telegrambot_schema(): bool {
    global $DB;

    $table = 'glpi_plugin_telegrambot_configs';

    // 1) Ensure table exists (if not - create minimal correct schema)
    if (!$DB->tableExists($table)) {
        $query = "CREATE TABLE `$table` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(191) NOT NULL,
            `value` LONGTEXT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uniq_name` (`name`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        if (!$DB->doQuery($query)) {
            return false;
        }
    }

    // 2) Fix legacy/wrong schema (e.g. table exists but without 'name' column)
    // Add columns only if missing (MariaDB/MySQL support for checking via fields).
    $fields = $DB->listFields($table);
    $fieldnames = array_map(static fn($f) => $f['Field'], $fields);

    if (!in_array('name', $fieldnames, true)) {
        if (!$DB->doQuery("ALTER TABLE `$table` ADD COLUMN `name` VARCHAR(191) NOT NULL DEFAULT ''")) {
            return false;
        }
    }
    if (!in_array('value', $fieldnames, true)) {
        if (!$DB->doQuery("ALTER TABLE `$table` ADD COLUMN `value` LONGTEXT NULL")) {
            return false;
        }
    }

    // Ensure unique index on name (best-effort: if it already exists, ignore error).
    // GLPI DB layer throws exception only on doQuery failure; we can safely attempt and ignore duplicates.
    try {
        $DB->doQuery("ALTER TABLE `$table` ADD UNIQUE KEY `uniq_name` (`name`)");
    } catch (Throwable $e) {
        // ignore if already exists
    }

    // 3) Seed defaults (idempotent)
    $seed = "INSERT IGNORE INTO `$table` (`name`, `value`) VALUES
        ('bot_token', ''),
        ('default_chat_id', ''),
        ('debug', '0')";
    if (!$DB->doQuery($seed)) {
        return false;
    }

    return true;
}

function plugin_telegrambot_install(): bool {
    return plugin_telegrambot_schema();
}

/**
 * GLPI calls update when plugin version changes.
 * Signature may vary, so accept optional param.
 */
function plugin_telegrambot_update($current_version = null): bool {
    return plugin_telegrambot_schema();
}

function plugin_telegrambot_uninstall(): bool {
    global $DB;
    $table = 'glpi_plugin_telegrambot_configs';
    if ($DB->tableExists($table)) {
        if (!$DB->doQuery("DROP TABLE `$table`")) {
            return false;
        }
    }
    return true;
}
