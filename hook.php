<?php
declare(strict_types=1);

function plugin_init_telegrambot(): void
{
   global $PLUGIN_HOOKS;

   $PLUGIN_HOOKS['csrf_compliant']['telegrambot'] = true;

   // Страница настроек плагина
   $PLUGIN_HOOKS['config_page']['telegrambot'] = 'front/notificationwebsocketsetting.form.php';

   // Подключаем только реально существующие классы
   require_once __DIR__ . '/inc/bot.class.php';
   require_once __DIR__ . '/inc/notificationwebsocketsetting.class.php';
   require_once __DIR__ . '/inc/notificationtelegramsetting.class.php';

   // Если плагин не активен — не регистрируем режимы/настройки
   if (!(new Plugin())->isActivated('telegrambot')) {
      return;
   }

   // 1) Регистрируем режим "telegram" (появится в "Режим" у шаблонов уведомлений)
   if (class_exists('Notification_NotificationTemplate')) {
      Notification_NotificationTemplate::registerMode(
         'telegram',
         __('Telegram', 'telegrambot'),
         'telegrambot'
      );
   }

   // 2) Регистрируем форму настроек режима на странице "Настройки → Уведомления"
   if (class_exists('NotificationSettingConfig')) {
      NotificationSettingConfig::register(
         'telegram',
         PluginTelegrambotNotificationTelegramSetting::class
      );
   }
}
