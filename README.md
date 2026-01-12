# GLPI TelegramBot (GLPI 11)

## Настройки (что означает каждое поле)
- **Notification bot token** — токен бота, который отправляет уведомления из GLPI в Telegram.
- **Client bot token** — токен бота, который отвечает на команды пользователей (через cron/polling).
- **user_chat_field** — shortname поля из плагина Fields (Users), где хранится Telegram chat_id пользователя.
- **group_chat_field** — shortname поля из Fields (Groups), где хранится Telegram chat_id группы/канала.
- **group_topic_field (optional)** — shortname поля из Fields (Groups), где хранится message_thread_id (topic) для супергрупп.

## Установка
```bash
cd /var/www/glpi/plugins/telegrambot
composer install --no-dev
```

Далее в GLPI: Marketplace → Плагины → TelegramBot → Установить → Включить.

## Cron
Плагин объявляет cron-задачу `messagelistener` (polling команд).

