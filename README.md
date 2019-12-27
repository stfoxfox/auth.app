Auth app
===============================

Вся конфигурация для авторизационного приложения настраиваестся в файле .env

Значение ключей конфигурации:
-----------------------------
| Ключ | Описание | Пример |
| ------ | ------ | -----  |
| DATABASE_URL | Строка подключения к базе данных | postgresql://db_user:password@host:port/db_name |
| JWT_SECRET_KEY | Приватный ключ для шифрования JWT | %kernel.project_dir%/config/jwt/private.pem |
| JWT_PUBLIC_KEY | Публичный ключ для шифрования JWT | %kernel.project_dir%/config/jwt/public.pem |
| JWT_PASSPHRASE | Ключевая фраза для JWT_SECRET_KEY | 0ef58e1ed035d70c78df94c61225ad73 |
| LOCATE | Стандартная локаль приложения | eng |
| SECURITY_SALT | Соль для шифрования паролей | efwqnbfwqjhbkvbwlervbwrkbvw |
| FBID | ID приложения Facebook | 165713260961531 |
| FBSECRET | Секретный ключ для Facebook | fc57336f62bffbd87a9948eeeca8f655 |
| COOCKIE_DOMAIN | Домен, для которого будут сохранятся в cookie JWT | localhost |
| AUTENTICATION_COOKIE_NAME | Имя cookie, в которое будет сохранятся JWT | AUTHCOOKIE |
| TOKEN_LIFETIME | Время жизни cookie | 1800 |
| LENGTH_PASSWORD | Минимальная длинна пароля | 6 |
| MAIL_URL | URL smtp сервера | MAIL_URL |
| FROM_EMAIL | C какого email будет отправлятся письмо | example@example.com |
| MAIL_PASSWORD | Пароль для FROM_MAIL |
| MAIL_HOST | Домен | localgost |
| MAIL_PORT | Порт smtp сервера | 465 |
| MAIL_ENCRYPTION | Шифрование для подключения с smtp сервером | ssl |
| SITE_DOMAIN | Домен сайта | localhost |
| COOKIE_LANGUAGE | Имя cookie, в которое сохраняется локаль | language |
| PRIVATE_API_ENDPOINT| URL приватного АПИ | http://localhost:3000/api/v1 |
| MOBILE_API_ENDPOINT | URL мобильного АПИ | http://localhost:5000/api/v2 |
| REDIS_DNS | URL Redis | redis://localhost |
| REDIS_PORT | PORT Redis | 6739 |
| REDIS_DATABASE | БД Redis | 1 |

Пример конфигурации можно посмотреть в файле **.env.dist**

Развёртывание приложения:
-----------------------------
Все действия выполняются из директории приложения
1. Выполнить **composer update**
2. Выполнить **php bin/console doctrine:database:create**
3. Выполнить **php bin/console doctrine:migrations:migrate**

Создание приложений
------------------------
Все действия выполняются из директории приложения
1. Выполнить **php bin/console app:create "Public application"**
1. Выполнить **php bin/console app:create "Private Api application"**
1. Выполнить **php bin/console app:create "Mobile Api Application"**
1. Выполнить **php bin/console app:create "Public Api Application"**

Для создания docker контейнера выполнить **docker-compose up -d**

Тестирование
------------------------
Все действия выполняются из директории приложения
1. Выполнить **php vendor/codeception/codeception/codecept build**
1. Выполнить **php vendor/codeception/codeception/codecept run**

Тестовая страница - домен/test
