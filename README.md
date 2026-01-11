# telegrambot (GLPI 11)

Минимальный скелет плагина под GLPI 11.0.4.

## Установка
1) Клонировать в каталог плагинов GLPI:
- `/var/www/glpi/plugins/telegrambot`

2) Права:
- владелец: `www-data:www-data`

3) В GLPI:
- Setup → Plugins → Telegram Bot → Install → Enable

## Настройка
Setup → Plugins → Telegram Bot

### Поля Fields (User)
Создать поля в плагине Fields для объекта User:

- `tg_chat_id` (String) — Telegram Chat ID
- `tg_enabled` (Boolean) — включены уведомления
- `tg_bot_out_enabled` (Boolean) — разрешить исходящие уведомления

Имена полей можно изменить в настройках плагина.

## Статус
Сейчас реализована только страница настроек (без отправки уведомлений).
Дальше добавляем:
- Notification mode "telegrambot" (outbound)
- Bot #2 (polling) для создания/комментирования заявок
