<?php
declare(strict_types=1);

if (!defined('GLPI_ROOT')) {
    die('Direct access not allowed');
}

Session::checkRight('config', READ);

Html::header('TelegramBot', $_SERVER['PHP_SELF'], 'config', 'plugins');

echo '<div class="center">';
echo '<h2>TelegramBot plugin</h2>';
echo '<p>Plugin installed and enabled successfully.</p>';
echo '</div>';

Html::footer();
