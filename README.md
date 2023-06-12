Opportunity to access public chats of the Utopia ecosystem. As well as instructions for installing the Utopia client and how to get into chat by id

![screenshot](https://github.com/Sagleft/utopia-qr-chat/raw/master/screenshot.png)

## Installation

```bash
composer update
cp example.env .env
mkdir view/cache
chmod 777 view/cache
cd controller/public_html
cp example.htaccess .htaccess
```

Dump tables available in file ```tables.sql```

## Usage

http GET query:

```
/id/<channelid>
```

for example:

```
/id/D53B4431FD604E2F0261792444797AA4
```

## Updating Cron Chat Messages

Put the script to run: ```controller/cron/update_channels.php```

---
[![udocs](https://github.com/Sagleft/ures/blob/master/udocs-btn.png?raw=true)](https://udocs.gitbook.io/utopia-api/)
