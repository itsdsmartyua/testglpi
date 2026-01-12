<?php
declare(strict_types=1);

function plugin_init_telegrambot(): void {
    global $PLUGIN_HOOKS;

    $PLUGIN_HOOKS['csrf_compliant']['telegrambot'] = true;
    $PLUGIN_HOOKS['config_page']['telegrambot'] = 'front/config.form.php';
}
