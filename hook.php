function plugin_init_telegrambot(): void
{
   global $PLUGIN_HOOKS;

   $PLUGIN_HOOKS['csrf_compliant']['telegrambot'] = true;
   $PLUGIN_HOOKS['config_page']['telegrambot'] = 'front/notificationtelegrambotsetting.form.php';

   // legacy classes
   require_once __DIR__ . '/inc/notificationtelegrambot.class.php';
   require_once __DIR__ . '/inc/notificationtelegrambotsetting.class.php';

   if ((new Plugin())->isActivated('telegrambot')) {

      // 1. Регистрируем канал уведомлений (sender)
      Notification_NotificationTemplate::registerMode(
         'telegram',
         __('Telegram', 'telegrambot'),
         'telegrambot'
      );

      // 2. Регистрируем НАСТРОЙКИ канала (КРИТИЧНО ДЛЯ UI)
      NotificationSettingConfig::register(
         'telegram',
         PluginTelegrambotNotificationTelegrambotSetting::class
      );
   }
}
