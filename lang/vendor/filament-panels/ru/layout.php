<?php

// Оверрайд vendor-файла filament-panels/ru/layout.php: дополняет пропущенные
// в vendor-словаре ключи (иначе строки выводятся сырыми ключами переводов).
// Структура обязана повторять vendor-файл целиком.

return [

    'direction' => 'ltr',

    'skip_to_content' => [
        'label' => 'Перейти к содержимому',
    ],

    'actions' => [

        'billing' => [
            'label' => 'Управление подпиской',
        ],

        'logout' => [
            'label' => 'Выйти',
        ],

        'open_database_notifications' => [
            'label' => 'Открыть уведомления',
            'label_with_unread_count' => '{1} Уведомления, :count непрочитанное|[2,*] Уведомления, :count непрочитанных',
        ],

        'open_user_menu' => [
            'label' => 'Меню пользователя',
        ],

        'sidebar' => [

            'collapse' => [
                'label' => 'Свернуть боковую панель',
            ],

            'expand' => [
                'label' => 'Развернуть боковую панель',
            ],

        ],

        'theme_switcher' => [

            'label' => 'Тема',

            'dark' => [
                'label' => 'Включить тёмную тему',
            ],

            'light' => [
                'label' => 'Включить светлую тему',
            ],

            'system' => [
                'label' => 'Включить системную тему',
            ],

        ],

    ],

    'navigation' => [
        'label' => 'Навигация',
    ],

    'topbar' => [
        'label' => 'Верхняя панель',
    ],

    'avatar' => [
        'alt' => 'Аватар :name',
    ],

    'logo' => [
        'alt' => 'Логотип :name',
    ],

    'tenant_menu' => [

        'search_field' => [
            'label' => 'Поиск',
            'placeholder' => 'Поиск',
        ],

    ],

];