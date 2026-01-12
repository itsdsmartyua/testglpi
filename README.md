# GLPI TelegramBot (GLPI 11.0.4 / PHP 8.2 / MariaDB)

Плагин добавляет интеграцию Telegram для GLPI 11:
- **Bot #1**: отправка уведомлений GLPI через стандартный механизм Notifications
- **Bot #2**: клиентский бот (команды пользователей / специалистов) — сейчас только статистика

Ключевая идея: **chat_id / topic_id НЕ хранятся в таблицах плагина**, а берутся из **полей плагина Fields** в объектах **Users** и **Groups**. Заполняются администратором вручную.

---

## Требования

- GLPI 11.0.4
- PHP 8.2
- MariaDB/MySQL (InnoDB + utf8mb4)
- Плагин **Fields** (для создания полей у Users/Groups)
- Composer (для зависимостей Telegram SDK)

---

## Установка

### 1) Размещение плагина
Папка должна быть:
`/var/www/glpi/plugins/telegrambot`

Обновить код:
```bash
cd /var/www/glpi/plugins/telegrambot || exit 1; git pull origin main
