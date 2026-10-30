# SushiDex

Приложение для заказа суши на Laravel 12 и Vue 3. Есть каталог, корзина, личный кабинет, блог и административная панель.

## Возможности

- Каталог товаров, категории и гостевая корзина с сохранением после входа.
- Заказы с доставкой или самовывозом, онлайн-оплата и возвраты через ЮKassa.
- Автоподстановка адресов через DaData в профиле и при редактировании заказа в админке.
- Профиль, аватар, смена пароля и подтверждение телефона.
- Блог с тегами и модерацией публикаций.
- Управление товарами, заказами и пользователями с доступом по ролям.
- Уведомления в приложении, по email и в Slack.

## Стек

Laravel 12, PHP, PostgreSQL, Redis, Inertia.js 2, Vue 3, TypeScript, Vuetify 3, Vite и Docker Compose. Для DTO используется Spatie Laravel Data, для типов маршрутов — Wayfinder. Поддерживается SSR.

## Запуск

Нужны Docker и Docker Compose.

1. Скопируйте настройки:

   ```bash
   cp .env.example .env
   ```

2. Укажите в `.env` параметры для Docker:

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

   REDIS_HOST=redis
   ```

   При первом запуске `DB_DATABASE` и `DB_USERNAME` должны совпадать: PostgreSQL создаёт базу с именем пользователя.

3. Запустите контейнеры и подготовьте приложение:

   ```bash
   docker compose up -d --build
   docker compose exec app composer install
   docker compose exec app php artisan key:generate
   docker compose exec app php artisan migrate
   docker compose exec app php artisan storage:link
   docker compose exec app php artisan db:seed --class=Roles
   ```

Приложение: [http://localhost:8859](http://localhost:8859). Контейнер `node` при запуске устанавливает npm-зависимости и запускает Vite, поэтому ему нужен доступ к интернету.

Для фоновых задач запустите в отдельном терминале:

```bash
docker compose exec app php artisan queue:work
```

Тестовые данные можно добавить командой `docker compose exec app php artisan db:seed`. Пароль созданных тестовых пользователей — `password`.

## Интеграции

**DaData** подсказывает адреса при вводе от трёх символов. Укажите ключи в `.env`:

```dotenv
DADATA_TOKEN=
DADATA_SECRET_KEY=
```

**ЮKassa:** настройки — `YKASSA_SHOP_ID` и `YKASSA_SECRET_KEY`. Адрес webhook — `POST /remittances/callbacks` на публичном HTTPS-домене приложения. В окружениях `local` и `testing` используется заглушка без реальных платежей.

**Slack:** для уведомлений о заказах нужны `SLACK_BOT_USER_OAUTH_TOKEN`, `SLACK_BOT_USER_DEFAULT_CHANNEL` и работающая очередь. Без этих настроек отправка пропускается.

**Email:** по умолчанию письма записываются в лог (`MAIL_MAILER=log`). **SMS:** локально используется заглушка; вызов API в `SmsAero` пока не реализован.

После изменения настроек:

```bash
docker compose exec app php artisan config:clear
docker compose exec app php artisan queue:restart
```

## Проверки и сборка

```bash
docker compose exec -T app php artisan test
docker compose exec -T app vendor/bin/pint --test
docker compose exec -T node npm run check:vue
docker compose exec -T node npm run lint
docker compose exec -T node npm run build
```

Backend-тесты используют отдельную PostgreSQL-базу `laravel_test`. Создайте её перед первым запуском тестов:

```bash
docker compose exec db sh -c 'createdb -U "$POSTGRES_USER" laravel_test'
```

После изменения DTO или маршрутов обновите типы и Wayfinder:

```bash
docker compose exec app composer generate-types
docker compose exec app composer routes
```

Файлы в `resources/generated` создаются автоматически.

## Остановка

```bash
docker compose down
```
