# SushiDex

Веб-приложение для заказа суши на Laravel и Vue: каталог, корзина, оформление и оплата заказов, блог и управление контентом. В проекте есть публичная часть, личный кабинет пользователя и административная панель с разграничением доступа по ролям.

## Возможности

- каталог товаров с пагинацией, категории и управление доступностью товаров;
- блог: публикации, теги, категории, отправка на модерацию и публикация;
- корзины удалённых публикаций, товаров и категорий с восстановлением;
- регистрация, авторизация, восстановление пароля и подтверждение email;
- профиль: редактирование данных, аватар, смена пароля и подтверждение номера телефона;
- мягкое удаление профиля с очисткой связанных данных и завершением сессии; активные заказы запрещают удаление, история заказов сохраняется;
- гостевая корзина и синхронизация с постоянной корзиной после входа;
- оформление заказов, доставка и самовывоз, текущие заказы и история с пагинацией;
- онлайн-оплата через ЮKassa, обработка платежных уведомлений и возвраты;
- отмена заказа клиентом с учётом статуса заказа и оплаты;
- уведомления о заказах, оплате, возвратах и изменении адреса; просмотр уведомлений и отметка о прочтении;
- Slack-уведомления администраторам о создании заказа и отмене клиентом;
- загрузка и фоновая обработка изображений;
- административная панель для управления контентом, товарами, заказами и пользователями;
- вход под выбранным пользователем из списка пользователей для роли `dev`;
- роли `user`, `author`, `admin` и `dev`;
- серверный рендеринг Inertia (SSR).

## Стек

**Backend:** PHP 8.3 в Docker (Composer требует PHP 8.2+), Laravel 12, PostgreSQL 17, Redis, Inertia.js 2, Spatie Laravel Data, Spatie Query Builder, SDK ЮKassa, Laravel Slack Notifications.

**Frontend:** Vue 3, TypeScript, Vuetify 3, Bootstrap 5, Vite 7, Wayfinder, Material Design Icons.

**Инфраструктура:** Docker Compose, Nginx, PHP-FPM, Node.js 24.

**Проверки и диагностика:** PHPUnit 11, Laravel Pint, Vue TypeScript, ESLint, Laravel Telescope и Debugbar.

## Запуск через Docker

### Требования

- Docker;
- Docker Compose v2;
- свободные порты `8859` для приложения, `5173` для Vite и `5432` для PostgreSQL.

### Установка

1. Скопируйте файл окружения:

```bash
cp .env.example .env
```

2. Настройте подключение к сервисам Docker в `.env`:

```dotenv
APP_NAME=SushiDex
APP_ENV=local
APP_URL=http://localhost:8859

DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=sushidex
DB_USERNAME=sushidex
DB_PASSWORD=secret

REDIS_CLIENT=phpredis
REDIS_HOST=redis
REDIS_PORT=6379

MAIL_MAILER=log
```

> PostgreSQL в текущем `docker-compose.yml` создаёт базу с именем пользователя, поэтому значения `DB_DATABASE` и `DB_USERNAME` должны совпадать при первом запуске нового каталога данных. Данные PostgreSQL хранятся в `../pgdata`, Redis — в именованном Docker-томе.

3. Соберите и запустите контейнеры:

```bash
docker compose up -d --build
```

Контейнер `node` сам выполнит `npm ci` и запустит Vite в режиме разработки.

4. Установите PHP-зависимости и подготовьте приложение:

```bash
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
docker compose exec app php artisan storage:link
```

5. При необходимости добавьте системные роли и тестовые данные:

```bash
docker compose exec app php artisan db:seed --class=Roles
docker compose exec app php artisan db:seed
```

После запуска приложение доступно по адресу [http://localhost:8859](http://localhost:8859).

Тестовые пользователи из `DatabaseSeeder` получают пароль `password`; их email-адреса генерируются случайно и выводятся только в базе данных.

## Фоновые задачи

Подтверждение email, восстановление пароля, обработка изображений и уведомления выполняются через очередь. В отдельном терминале запустите worker:

```bash
docker compose exec app php artisan queue:work
```

По умолчанию письма записываются в `storage/logs/laravel.log`, если используется `MAIL_MAILER=log`.

Worker не запускается автоматически через Compose. После изменения кода или настроек перезапустите его командой `docker compose exec app php artisan queue:restart`.

В `bootstrap/app.php` настроена ежедневная очистка записей Telescope старше 48 часов. Для выполнения расписания при локальной разработке:

```bash
docker compose exec app php artisan schedule:work
```

## Заказы и оплата

Заказ может быть новым, в обработке, завершённым или отменённым. У пользователя допускается не более двух активных заказов. Для доставки и оплаты курьеру требуется адрес в профиле.

Способы оплаты: онлайн, картой курьеру, наличными курьеру и в пункте выдачи. Для онлайн-оплаты создаётся платёж с перенаправлением на страницу провайдера. Возврат на сайт сам по себе не подтверждает оплату: статус обновляется через webhook. Отмена и изменение способа оплаты учитывают состояние платежа; для оплаченных онлайн-заказов предусмотрен возврат.

### ЮKassa

Настройки реального адаптера находятся в `config/ykassa.php`. Добавьте в `.env`:

```dotenv
YKASSA_SHOP_ID=
YKASSA_SECRET_KEY=
```

В кабинете ЮKassa укажите адрес уведомлений на доступном извне HTTPS-домене приложения:

```text
POST https://your-domain.example/remittances/callbacks
```

Обрабатываются события `payment.succeeded`, `payment.waiting_for_capture`, `payment.canceled` и `refund.succeeded`. Маршрут исключён из проверки CSRF; в окружении `production` входящий IP проверяется по списку `ykassa.webhook_ip`. Возврат после оплаты ведёт на `/orders`; для правильных ссылок задайте `APP_URL`.

В окружениях `local` и `testing` `AppServiceProvider` подключает `Tests\Mocks\MockYkassaAdapter`: он не обращается к API, возвращает ссылку на заказ и имитирует успешный возврат. Оплата при этом не подтверждается автоматически — можно отправить тестовый webhook с идентификатором созданного платежа. Наличие ключей ЮKassa не переключает локальную среду на реальный адаптер. В остальных окружениях используется `YKassaAdapter`.

### Slack

Настройки находятся в `config/services.php`. Для отправки уведомлений добавьте:

```dotenv
SLACK_BOT_USER_OAUTH_TOKEN=
SLACK_BOT_USER_DEFAULT_CHANNEL=
```

Укажите OAuth-токен бота с правом отправки сообщений (`chat:write`) и канал, к которому у бота есть доступ. Уведомления о новом заказе и отмене заказа клиентом ставятся в очередь только при заполненных обоих параметрах. При пустых настройках отправка в Slack пропускается. Для работы отправки нужен запущенный queue worker.

После изменения настроек интеграций сбросьте кеш конфигурации и перезапустите worker:

```bash
docker compose exec app php artisan config:clear
docker compose exec app php artisan queue:restart
```

### Уведомления в приложении

Раздел `/notifications` показывает уведомления текущего пользователя с фильтрацией и подгрузкой страниц. Доступны отметка отдельного уведомления о прочтении, переход по его ссылке и отметка всех уведомлений прочитанными. Системные сообщения об успешных действиях и ошибках показываются через Inertia flash.

## Команды разработки

```bash
# Проверка типов Vue и TypeScript
docker compose exec node npm run check:vue

# ESLint
docker compose exec node npm run lint

# Форматирование PHP-кода
docker compose exec app vendor/bin/pint

# Проверка форматирования без изменения файлов
docker compose exec app vendor/bin/pint --test

# Backend-тесты
docker compose exec app composer test

# Production-сборка клиента и SSR
docker compose exec node npm run build
```

После изменения контроллеров, DTO или маршрутов обновите сгенерированные TypeScript-файлы:

```bash
docker compose exec app composer generate-types
docker compose exec app composer routes
```

Сгенерированный код находится в `resources/generated` и не должен редактироваться вручную.

## Backend-тесты

Тесты используют PostgreSQL и отдельную базу `laravel_test`; параметры заданы в `phpunit.xml`. Перед первым запуском создайте её под пользователем из `.env`:

```bash
docker compose exec db sh -c 'createdb -U "$POSTGRES_USER" laravel_test'
```

Если база уже существует, повторно создавать её не нужно. Тестовое подключение должно иметь доступ к `db:5432`; учётные данные берутся из окружения. Тесты обновляют схему и данные тестовой базы, поэтому не направляйте их на рабочую базу.

```bash
# Полный набор
docker compose exec -T app php artisan test

# Отдельный сценарий
docker compose exec -T app php artisan test --filter=ProfileDeletionTest
```

Тесты покрывают доступ и валидацию, корзину, заказы, платежи и возвраты, webhooks, уведомления, профиль, телефон и обработку изображений. В тестовом окружении очередь выполняется синхронно, а SMS и платежи используют заглушки. Реальные вызовы ЮKassa, Slack и SMTP этими тестами не проверяются. Frontend проверяется типизацией, ESLint и сборкой.

## Структура проекта

```text
app/
├── Http/Controllers/     HTTP-контроллеры публичной части, профиля и админки
├── Http/RequestDTO/      входные DTO и правила валидации
├── Integrations/         адаптеры ЮKassa и SMS
├── Notifications/        уведомления клиента и администратора
├── Jobs/                 фоновые задания
└── Services/             бизнес-логика приложения
resources/
├── js/Pages/             страницы Inertia/Vue
├── js/components/        переиспользуемые Vue-компоненты
└── generated/            типы и маршруты, созданные генераторами
routes/
├── web.php               публичные маршруты и профиль
├── auth.php              авторизация и подтверждение email
└── admin.php             маршруты административной панели
_docker/                  конфигурация PHP, Nginx и Node.js
```

## Доступ в административную панель

Административные маршруты находятся под префиксом `/admin` и защищены ролями:

- `author` — управление публикациями и изображениями;
- `admin` и `dev` — модерация и публикация записей, текущие заказы и история заказов;
- `dev` — управление справочниками, товарами, пользователями и вход под другим пользователем.

Для локальной разработки назначить роли существующему пользователю можно через Tinker:

```bash
docker compose exec app php artisan tinker
```

В Tinker назначьте существующему пользователю роль `dev` (роли должны быть созданы сидером):

```php
$user = App\Models\User::where('email', 'developer@example.com')->firstOrFail();
$role = App\Models\Role::where('name', 'dev')->firstOrFail();
$user->roles()->syncWithoutDetaching([$role->id]);
```

После изменения ролей очистите кеш приложения:

```bash
docker compose exec app php artisan cache:clear
```

Для диагностики запросов, заданий и уведомлений установлен Telescope (`/telescope`). Его можно отключить через `TELESCOPE_ENABLED=false`; доступ вне локального окружения определяется `TelescopeServiceProvider`.

## SMS-подтверждение

В окружениях `local` и `testing` используется тестовый SMS-адаптер без реальной отправки. Для остальных окружений подключается `SmsAeroAdapter`.

Класс `SmsAero` сейчас является заготовкой и всегда сообщает об успешной отправке. Перед production-развёртыванием необходимо реализовать вызов API провайдера, добавить его настройки в окружение и обработать ответы сервиса.

## Остановка проекта

```bash
docker compose down
```
