# SushiDex

Веб-приложение на Laravel и Vue для каталога, блога и управления контентом. В проекте есть публичная часть, личный кабинет пользователя и административная панель с разграничением доступа по ролям.

## Возможности

- каталог товаров и категории;
- блог: публикации, теги, категории и корзина удалённых записей;
- регистрация, авторизация, восстановление пароля и подтверждение email;
- профиль пользователя, история заказов и подтверждение номера телефона;
- корзина и оформление заказов на уровне сервисов приложения;
- загрузка и фоновая обработка изображений;
- административная панель для управления контентом, товарами и пользователями;
- роли `user`, `author`, `admin` и `dev`;
- серверный рендеринг Inertia (SSR).

## Стек

**Backend:** PHP 8.3, Laravel 12, PostgreSQL 17, Redis, Inertia.js, Spatie Laravel Data, Spatie Query Builder.

**Frontend:** Vue 3, TypeScript, Vuetify 3, Vite 7, Wayfinder, Material Design Icons.

**Инфраструктура:** Docker Compose, Nginx, PHP-FPM, Node.js 24.

## Запуск через Docker

### Требования

- Docker;
- Docker Compose v2;
- свободный порт `8859` для приложения и `5173` для Vite.

### Установка

1. Скопируйте файл окружения:

```bash
cp .env.example .env
```

2. Настройте подключение к сервисам Docker в `.env`:

```dotenv
APP_NAME=SushiDex
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

> PostgreSQL в текущем `compose.yaml` создаёт базу с именем пользователя, поэтому значения `DB_DATABASE` и `DB_USERNAME` должны совпадать при первом запуске нового тома данных.

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

Подтверждение email, восстановление пароля и обработка изображений выполняются через очередь. В отдельном терминале запустите worker:

```bash
docker compose exec app php artisan queue:work
```

По умолчанию письма записываются в `storage/logs/laravel.log`, если используется `MAIL_MAILER=log`.

## Команды разработки

```bash
# Проверка типов Vue и TypeScript
docker compose exec node npm run check:vue

# ESLint
docker compose exec node npm run lint

# Форматирование PHP-кода
docker compose exec app vendor/bin/pint

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

## Структура проекта

```text
app/
├── Http/Controllers/     HTTP-контроллеры публичной части, профиля и админки
├── Http/RequestDTO/      входные DTO и правила валидации
├── Integrations/Sms/     SMS-интеграции и адаптеры
├── Jobs/                 фоновые задания
└── Services/             бизнес-логика приложения
resources/
├── js/Pages/             страницы Inertia/Vue
├── js/Components/        переиспользуемые Vue-компоненты
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
- `admin` и `dev` — модерация и публикация записей;
- `dev` — управление справочниками, товарами и пользователями.

Для локальной разработки назначить роли существующему пользователю можно через Tinker:

```bash
docker compose exec app php artisan tinker
```

После изменения ролей следует очистить кеш приложения:

```bash
docker compose exec app php artisan cache:clear
```

## SMS-подтверждение

В окружениях `local` и `testing` используется тестовый SMS-адаптер без реальной отправки. Для остальных окружений подключается `SmsAeroAdapter`.

Класс `SmsAero` сейчас является заготовкой и всегда сообщает об успешной отправке. Перед production-развёртыванием необходимо реализовать вызов API провайдера, добавить его настройки в окружение и обработать ответы сервиса.

## Остановка проекта

```bash
docker compose down
```
