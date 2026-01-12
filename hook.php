<?php
declare(strict_types=1);

function plugin_init_telegrambot(): void
{
   global $PLUGIN_HOOKS;

   $PLUGIN_HOOKS['csrf_compliant']['telegrambot'] = true;

   // Страница настроек плагина (твоя текущая форма)
   $PLUGIN_HOOKS['config_page']['telegrambot'] = 'front/notificationwebsocketsetting.form.php';

   // Подключаем классы плагина
   require_once __DIR__ . '/inc/bot.class.php';
   require_once __DIR__ . '/inc/notificationtelegramsetting.class.php';      // <-- ВАЖНО (для /front/setup.notification.php)
   require_once __DIR__ . '/inc/notificationtelegrambot.class.php';
   require_once __DIR__ . '/inc/notificationtelegrambotsetting.class.php';

   if (!(new Plugin())->isActivated('telegrambot')) {
      return;
   }

   // 1) Регистрируем новый MODE в уведомлениях (чтобы появился в "Режим" в шаблонах)
   if (class_exists('Notification_NotificationTemplate')) {
      Notification_NotificationTemplate::registerMode(
         'telegram',
         __('Telegram', 'telegrambot'),
         'telegrambot'
      );
   }

   // 2) Регистрируем настройки MODE для страницы "Настройки → Уведомления"
   if (class_exists('NotificationSettingConfig')) {
      NotificationSettingConfig::register(
         'telegram',
         PluginTelegrambotNotificationTelegramSetting::class
      );
   }
}
