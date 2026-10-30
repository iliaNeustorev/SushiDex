# SushiDex

Веб-приложение для заказа суши на Laravel 12 и Vue 3. Публичная часть включает каталог и блог, личный кабинет — профиль и историю заказов. В административной панели можно управлять контентом, товарами, заказами и пользователями.

## Возможности

- Каталог товаров, категории и гостевая корзина с сохранением после входа.
- Заказы с доставкой или самовывозом, онлайн-оплата и возвраты через ЮKassa.
- Автоподстановка адресов через DaData в профиле и при редактировании заказа в админке.
- Профиль, аватар, смена пароля и подтверждение телефона.
- Регистрация, вход, восстановление пароля, подтверждение email и удаление профиля.
- Блог с тегами и модерацией публикаций.
- Управление товарами, заказами и пользователями с доступом по ролям.
- Уведомления в приложении, по email и в Slack.
- Загрузка изображений и изменение их размеров в фоновых заданиях.

Заказы принимаются с 11:00 до 23:00. У пользователя может быть не больше двух активных заказов. Для доставки нужен адрес в профиле. Доступны онлайн-оплата, оплата картой или наличными курьеру и оплата в пункте выдачи. Отмена заказа учитывает его статус и состояние платежа.

## Технологии и библиотеки

### Backend

| Технология / пакет | Для чего используется |
| --- | --- |
| PHP 8.3, Laravel 12 | Серверная часть, маршруты, авторизация, очереди и уведомления. |
| Eloquent, PostgreSQL 17 | Работа с моделями, связями и базой данных. |
| Redis | Сервис для кеширования; подключён в Docker Compose. |
| `inertiajs/inertia-laravel` | Передача данных из Laravel в страницы Vue. |
| `spatie/laravel-data` | DTO, валидация входных данных и ресурсы ответов. |
| `spatie/laravel-query-builder` | Фильтрация и сортировка списков. |
| `spatie/laravel-typescript-transformer` | Генерация TypeScript-типов из PHP DTO и ресурсов. |
| `laravel/wayfinder` | Генерация типизированных функций маршрутов для фронтенда. |
| `intervention/image` | Обработка изображений с драйверами GD и Imagick. |
| `hflabs/dadata` | Получение подсказок адресов от DaData. |
| `yoomoney/yookassa-sdk-php` | Платежи, статусы оплаты и возвраты через ЮKassa. |
| `laravel/slack-notification-channel` | Отправка уведомлений в Slack. |

### Frontend

| Технология / пакет | Для чего используется |
| --- | --- |
| Vue 3, TypeScript | Компоненты интерфейса и типизация данных. |
| `@inertiajs/vue3` | Навигация, формы и получение данных страниц от Laravel. |
| Vuetify 3 | Поля ввода, таблицы, диалоги и другие компоненты интерфейса. |
| `@mdi/js` | Иконки Material Design. |
| Pinia | Хранилище состояния корзины. |
| Axios | JSON-запросы, в том числе загрузка подсказок адреса. |
| Lodash ES | Вспомогательные функции, включая задержку поиска и объединение настроек. |
| Vite 7, `@vue/server-renderer` | Сборка клиента, обновление при разработке и серверный рендеринг (SSR). |

Для локального окружения используются Docker Compose, Nginx, PHP-FPM и Node.js 24. PHPUnit и Mockery проверяют backend; Pint, ESLint и `vue-tsc` — форматирование и типы. Для диагностики установлены Laravel Telescope и Debugbar, для тестовых данных — Faker.

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

### DaData

Подсказки адресов подключены в профиле пользователя и в окне изменения заказа в админке. Переиспользуемый компонент `AddressAutocomplete.vue` запрашивает варианты после ввода трёх символов с задержкой 350 мс. Можно выбрать подсказку или ввести адрес вручную.

Запрос к DaData выполняется на бэкенде через `DaDataAdapter`; ключи остаются на сервере. Укажите их в `.env`:

```dotenv
DADATA_TOKEN=
DADATA_SECRET_KEY=
```

Для DaData используется реальный API и в локальном окружении. Для подсказок нужны ключи и доступ к сервису.

### ЮKassa

При онлайн-оплате пользователь переходит на страницу провайдера. Подтверждение оплаты приходит через webhook, а при отмене оплаченного заказа выполняется возврат.

```dotenv
YKASSA_SHOP_ID=
YKASSA_SECRET_KEY=
```

В кабинете провайдера укажите `POST https://your-domain.example/remittances/callbacks`. Обрабатываются события оплаты, ожидания подтверждения, отмены платежа и возврата. В `production` проверяется IP отправителя webhook.

В окружениях `local` и `testing` используется заглушка без реальных платежей. Добавление ключей не переключает локальное окружение на реальный адаптер.

### Slack, email и SMS

- **Slack:** уведомления о новом заказе и отмене клиентом. Нужны `SLACK_BOT_USER_OAUTH_TOKEN`, `SLACK_BOT_USER_DEFAULT_CHANNEL` и работающая очередь. У бота должно быть право `chat:write` и доступ к каналу. При пустых настройках отправка пропускается.
- **Email:** подтверждение почты, восстановление пароля и уведомления. По умолчанию письма записываются в `storage/logs/laravel.log` (`MAIL_MAILER=log`); для отправки настройте почтовый транспорт в `.env`.
- **SMS:** подтверждение номера через адаптер. В `local` и `testing` работает заглушка. Вызов API в `SmsAero` пока не реализован.

Уведомления пользователя также доступны в разделе `/notifications`: их можно просматривать и отмечать прочитанными.

После изменения настроек:

```bash
docker compose exec app php artisan config:clear
docker compose exec app php artisan queue:restart
```

## Структура и доступ

- `app/Http/Controllers` — контроллеры публичной части, профиля и админки.
- `app/Http/Requests`, `app/Http/RequestDTO` — обработка и валидация входных данных.
- `app/Services` — бизнес-логика, `app/Integrations` — адаптеры внешних сервисов.
- `app/Jobs`, `app/Notifications` — фоновые задания и уведомления.
- `resources/js/Pages`, `resources/js/components` — страницы и общие Vue-компоненты.
- `resources/generated` — автоматически созданные типы и маршруты.

В проекте есть роли `user`, `author`, `admin` и `dev`. Авторы работают с публикациями, администраторы модерируют блог и обрабатывают заказы. Роль `dev` также даёт доступ к справочникам, товарам, пользователям и входу под другим пользователем. Доступ проверяется через Gates и политики.

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

Тесты проверяют доступ, валидацию, профиль, корзину, заказы, платежи, возвраты, уведомления и подсказки адресов. Внешний API DaData в тестах подменяется. Параметры тестовой базы находятся в `phpunit.xml`.

Для запуска отдельного набора:

```bash
docker compose exec -T app php artisan test --filter=AddressAutocompleteTest
```

После изменения DTO или маршрутов обновите типы и Wayfinder:

```bash
docker compose exec app composer generate-types
docker compose exec app composer routes
```

Файлы в `resources/generated` создаются автоматически.

Очередь запускается отдельно от Compose. Для локального выполнения расписания (в том числе очистки Telescope) используйте `docker compose exec app php artisan schedule:work`.

## Остановка

```bash
docker compose down
```
