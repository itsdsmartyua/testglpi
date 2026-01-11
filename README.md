# telegrambot (GLPI 11)

Telegram notification mode for GLPI 11.0.4+ using standard Notifications.

## Fields(User)
Create Fields container for **User**:
- Container internal name: `telegram`

Fields inside container:
- `tg_chat_id_notify` (Text) — chat_id for outbound notifications
- `tg_enabled` (Yes/No) — enable Telegram notifications for user
- `tg_bot_out_enabled` (Yes/No) — allow outbound bot notifications

## Server sync from GitHub
```bash
cd /var/www/glpi/plugins/telegrambot && \
sudo -u www-data HOME=/var/lib/www-data git fetch origin && \
sudo -u www-data HOME=/var/lib/www-data git reset --hard origin/main && \
sudo -u www-data php /var/www/glpi/bin/console glpi:cache:clear && \
sudo systemctl restart apache2
