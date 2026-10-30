<?php

return [
    'auth' => [
        'register' => [
            'type' => 'success',
            'text' => 'Регистрация успешно выполнена',
        ],
        'verify' => [
            'type' => 'success',
            'text' => 'Аккаунт активирован',
        ],
        'verify-mail' => [
            'type' => 'info',
            'text' => 'Письмо отправлено. Проверьте почту',
        ],
    ],

    'limit' => [
        'login' => [
            'type' => 'danger',
            'text' => 'Вы превысили количество попыток входа',
        ],
    ],

    'password' => [
        'reset' => [
            'type' => 'info',
            'text' => 'Пароль успешно сброшен',
            'email' => [
                'type' => 'info',
                'text' => 'Ссылка для сброса пароля отправлена на почту',
            ],
        ],
        'change' => [
            'type' => 'primary',
            'text' => 'Пароль успешно изменен',
        ],
    ],

    'profile' => [
        'delete' => [
            'type' => 'info',
            'text' => 'Профиль удален',
        ],
        'delete-failed' => [
            'type' => 'error',
            'text' => 'Не удалось удалить профиль',
        ],
        'update' => [
            'type' => 'success',
            'text' => 'Данные профиля успешно изменены!',
        ],
        'change-avatar-fail' => [
            'type' => 'error',
            'text' => 'Не удалось изменить аватар. Попробуйте позднее!',
        ],
        'change-avatar' => [
            'type' => 'success',
            'text' => 'Аватар изменен',
        ],
        'delete-avatar' => [
            'type' => 'warning',
            'text' => 'Аватар удален',
        ],
        'change-password' => [
            'type' => 'success',
            'text' => 'Пароль успешно изменен!',
        ],
        'phone' => [
            'store' => [
                'type' => 'success',
                'text' => 'Номер успешно добавлен. Нужно его подтвердить!',
            ],
            'destroy' => [
                'type' => 'warning',
                'text' => 'Номер удален',
            ],
        ],
        'autocomplete-address-fail' => [
            'type' => 'error',
            'text' => 'Ошибка получения адреса',
        ],
    ],

    'images' => [
        'store' => [
            'type' => 'success',
            'text' => 'Картинка успешно добавлена',
        ],
        'destroy' => [
            'type' => 'warning',
            'text' => 'Картинка удалена',
        ],
    ],
    'categories' => [
        'trash' => [
            'update' => [
                'type' => 'info',
                'text' => 'Категория успешно восстановлена',
            ],
            'destroy' => [
                'type' => 'warning',
                'text' => 'Категория удалена из базы',
            ],
        ],
        'store' => [
            'type' => 'success',
            'text' => 'Категория добавлена',
        ],
        'update' => [
            'type' => 'info',
            'text' => 'Категория обновлена',
        ],
        'destroy' => [
            'type' => 'warning',
            'text' => 'Категория удалена в корзину',
        ],
        'errors' => [
            'delete' => [
                'type' => 'error',
                'text' => 'Не удалось удалить категорию есть привязанные продукты или посты',
            ],
        ],
    ],

    'orders' => [
        'admin' => [
            'update-status' => [
                'type' => 'info',
                'text' => 'Статус заказа обновлен',
            ],
            'update-settings' => [
                'type' => 'info',
                'text' => 'Настройки заказа обновлены',
            ],
        ],
    ],

    'posts' => [
        'admin' => [
            'trash' => [
                'update' => [
                    'type' => 'info',
                    'text' => 'Пост успешно восстановлен',
                ],
                'destroy' => [
                    'type' => 'warning',
                    'text' => 'Пост удален из базы',
                ],
            ],
        ],
        'store' => [
            'type' => 'success',
            'text' => 'Пост успешно создан',
        ],
        'update' => [
            'type' => 'info',
            'text' => 'Пост успешно обновлен',
        ],
        'destroy' => [
            'type' => 'warning',
            'text' => 'Пост удален в корзину',
        ],
        'moderating' => [
            'type' => 'info',
            'text' => 'Пост отправлен на модерацию',
        ],
        'publish' => [
            'type' => 'success',
            'text' => 'Пост опубликован',
        ],
    ],

    'products' => [
        'admin' => [
            'trash' => [
                'update' => [
                    'type' => 'info',
                    'text' => 'Продукт успешно восстановлен',
                ],
                'destroy' => [
                    'type' => 'warning',
                    'text' => 'Продукт удален из базы',
                ],
            ],
            'store' => [
                'type' => 'success',
                'text' => 'Продукт успешно добавлен',
            ],
            'update' => [
                'type' => 'info',
                'text' => 'Продукт успешно обновлен',
            ],
            'destroy' => [
                'type' => 'warning',
                'text' => 'Продукт удален в корзину',
            ],
            'active' => [
                'type' => 'info',
                'text' => 'Активность продукта изменена',
            ],
            'errors' => [
                'destroy' => [
                    'type' => 'error',
                    'text' => 'Не удалось удалить продукт обратитесь к администратору',
                ],
            ],
        ],
    ],

    'tags' => [
        'admin' => [
            'store' => [
                'type' => 'success',
                'text' => 'Тег успешно создан',
            ],
            'update' => [
                'type' => 'info',
                'text' => 'Тег успешно обновлен',
            ],
            'destroy' => [
                'type' => 'warning',
                'text' => 'Тег удален',
            ],
        ],
    ],
    'users' => [
        'admin' => [
            'change-roles' => [
                'type' => 'info',
                'text' => 'Роли пользователя изменены',
            ],
            'block' => [
                'on' => [
                    'type' => 'warning',
                    'text' => 'Пользователь заблокирован',
                ],
                'off' => [
                    'type' => 'info',
                    'text' => 'Пользователь разблокирован',
                ],
            ],
            'change-address' => [
                'type' => 'info',
                'text' => 'Адрес пользователя изменен',
            ],
        ],
    ],
    'notifications' => [
        'readAll' => [
            'type' => 'info',
            'text' => 'Все непрочитанные сообщения успешно прочитаны!',
        ],
    ],
];
