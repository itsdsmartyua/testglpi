<?php
declare(strict_types=1);

function plugin_version_telegrambot(): array
{
   return [
      'name'           => 'Telegram Bot',
      'version'        => '0.1.0',
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
   // GLPI itself checks requirements; keep simple.
   return true;
}

function plugin_telegrambot_check_config(): bool
{
   // Plugin has a config page; always "configurable".
   return true;
}
