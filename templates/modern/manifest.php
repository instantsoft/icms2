<?php
/**
 * Массив опций и свойств шаблона
 */
return [
    // Наследование от шаблона
    'inherit' => ['admincoreui'],
    'title' => 'Modern',
    // Авторство
    'author' => [
        'name' => 'InstantCMS Team',
        'url'  => 'https://instantcms.ru',
        'help' => 'https://docs.instantcms.ru/manual/settings/templates'
    ],
    // Свойства шаблона
    'properties' => [
        'vendor'                     => 'bootstrap4',
        'style_middleware'           => 'scss',
        'has_options'                => true,
        'has_profile_themes_support' => false,
        'has_profile_themes_options' => false,
        'is_dynamic_layout'          => true,
        'is_backend'                 => false,
        'is_frontend'                => true
    ]
];
