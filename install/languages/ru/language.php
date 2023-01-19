<?php

    define('LANG_PAGE_TITLE',               'Установка InstantCMS');
    define('LANG_INSTALLATION_WIZARD',      'Мастер установки');
    define('LANG_NEXT',                     'Далее &rarr;');
    define('LANG_ERROR',                    'Ошибка');

    define('LANG_MANUAL',                   '<a href="https://docs.instantcms.ru/manual/install" target="_blank" rel="noopener noreferrer">Инструкция по установке</a>');

    define('LANG_LANGUAGE_SELECT_RU',       'Пожалуйста, выберите язык');
    define('LANG_LANGUAGE_SELECT_EN',       'Please, select a language');

    define('LANG_STEP_LANGUAGE',            'Выбор языка');
    define('LANG_STEP_START',               'Вступление');
    define('LANG_STEP_LICENSE',             'Лицензия');
    define('LANG_STEP_PHP_CHECK',           'Проверка PHP');
    define('LANG_STEP_PATHS',               'Указание путей');
    define('LANG_STEP_DATABASE',            'База данных');
    define('LANG_STEP_SITE',                'Сайт');
    define('LANG_STEP_ADMIN',               'Администратор');
    define('LANG_STEP_CONFIG',              'Конфигурация');
    define('LANG_STEP_CRON',                'Планировщик');
    define('LANG_STEP_FINISH',              'Завершение');

    define('LANG_STEP_START_1',             'Мастер установки InstantCMS проверит удовлетворяет ли ваш сервер системным требованиям.');
    define('LANG_STEP_START_2',             'В процессе работы мастер задаст несколько вопросов, необходимых для корректной установки и настройки InstantCMS.');

    define('LANG_LICENSE_AGREE',            'Я согласен с условиями лицензии');
    define('LANG_LICENSE_ERROR',            'Вы должны согласиться с условиями лицензии');
    define('LANG_LICENSE_NOTE',             'InstantCMS распространяется по лицензии <a href="http://www.gnu.org/licenses/gpl-2.0.html" target="_blank" rel="noopener noreferrer">GNU/GPL</a> версии 2.');
    define('LANG_LICENSE_ORIGINAL',         'Оригинал');
    define('LANG_LICENSE_TRANSLATION',      'Перевод');

    define('LANG_PHP_VERSION',              'Версия интерпретатора');
    define('LANG_PHP_VERSION_REQ',          'Требуется PHP 7.0 или выше');
    define('LANG_PHP_VERSION_DESC',         'Установленная версия');
	define('LANG_PHP_VARIABLES',            'Опции настройки PHP');
	define('LANG_PHP_VARIABLES_HINT',       'Серым цветом указано требуемое значение');
	define('LANG_PHP_VARIABLES_ON',			'Вкл');
	define('LANG_PHP_VARIABLES_OFF',		'Выкл');
    define('LANG_PHP_EXTENSIONS',           'Требуемые расширения');
    define('LANG_PHP_EXTENSIONS_REQ',       'Данные расширения необходимы для работы InstantCMS');
    define('LANG_PHP_EXTENSIONS_EXTRA',     'Рекомендуемые расширения');
    define('LANG_PHP_EXTENSIONS_EXTRA_REQ', 'Данные расширения не являются необходимыми, но без них<br>может быть недоступна часть функционала по обновлению и кешированию');
    define('LANG_PHP_EXT_INSTALLED',        'Установлено');
    define('LANG_PHP_EXT_NOT_INSTALLED',    'Не найдено');
    define('LANG_PHP_CHECK_ERROR',          'Вы не сможете продолжить установку до тех пор, пока условия отмеченные красным не будут исправлены.');
    define('LANG_PHP_CHECK_ERROR_HINT',     'Обратитесь в службу поддержки вашего хостинга с просьбой обеспечить необходимые условия. Затем перезапустите установку.');

    define('LANG_PATHS_ROOT_INFO',          'Все пути указываются относительно:<br/><span class="root-path">%s</span>');
    define('LANG_PATHS_ROOT_CHANGE',        'изменить');
    define('LANG_PATHS_CHANGE_INFO',        'После установки пути можно будет изменить отредактировав файл конфигурации.<br/>Не забудьте сделать это при переносе сайта с локального сервера на хостинг!');
    define('LANG_PATHS_MUST_WRITABLE',      'Должна быть доступна для записи');
    define('LANG_PATHS_NOT_WRITABLE',       'не доступна для записи!');
    define('LANG_PATHS_WRITABLE_HINT',      'Выставьте правильные права на эту папку');

    define('LANG_PATHS_ROOT',               'Корень');
    define('LANG_PATHS_ROOT_PATH',          'Корневая папка');
    define('LANG_PATHS_ROOT_HOST',          'Корневой URL');
    define('LANG_PATHS_UPLOAD',             'Загрузки');
    define('LANG_PATHS_UPLOAD_PATH',        'Папка для загрузок');
    define('LANG_PATHS_UPLOAD_HOST',        'URL для загрузок');
    define('LANG_PATHS_CACHE',              'Кеш');
    define('LANG_PATHS_CACHE_PATH',         'Папка для кеша');
    define('LANG_PATHS_SESSION',            'Сессии');
    define('LANG_PATHS_SESSION_PATH',       'Директория хранения сессий');
    define('LANG_PATHS_SESSIONS_BASEDIR',    'Полный путь в файловой системе, который должен входить в один из путей ');

    define('LANG_DATABASE_INFO',            'Укажите реквизиты для подключения к базе данных MySQL');
    define('LANG_DATABASE_HOST',            'Сервер MySQL');
    define('LANG_DATABASE_USER',            'Пользователь');
    define('LANG_DATABASE_PASS',            'Пароль');
    define('LANG_DATABASE_BASE',            'База данных');
    define('LANG_DATABASE_BASE_HINT',       'Если не существует, будет создана');
    define('LANG_DATABASE_ENGINE',          'Движок базы данных');
    define('LANG_DATABASE_ENGINE_HINT',     'Не знаете что выбрать? Выбирайте InnoDB.');
    define('LANG_DATABASE_CHARSET',         'Кодировка базы данных');
    define('LANG_DATABASE_PREFIX',          'Префикс таблиц');
    define('LANG_DATABASE_USERS_TABLE',     'Таблица с пользователями');
    define('LANG_DATABASE_USERS_TABLE_NEW', 'Создать новую');
    define('LANG_DATABASE_USERS_TABLE_OLD', 'Использовать имеющуюся');
    define('LANG_DATABASE_INSTALL_DEMO',    'Установить демо данные');

    define('LANG_DATABASE_PREFIX_ERROR',    'Префикс БД может содержать только латинские буквы, цифры и знак подчёркивания');
    define('LANG_DATABASE_SELECT_ERROR',    'Невозможно выбрать базу данных %s');
    define('LANG_DATABASE_CONNECT_ERROR',   "Ошибка подключения MySQL:\n\n%s");
    define('LANG_DATABASE_BASE_ERROR',      "Ошибка импорта базы данных\nПроверьте правильность реквизитов");
    define('LANG_DATABASE_ENGINE_NO',       'Выбранный движок БД не поддерживается');
    define('LANG_DATABASE_ENGINE_DISABLED', 'Выбранный движок БД поддерживается, но отключен в настройках MySQL');
    define('LANG_DATABASE_ENGINE_ERROR',    'Выбранный движок БД не поддерживается сервером');
    define('LANG_DATABASE_CH_ERROR',        'Выбранная кодировка БД не поддерживается сервером');

    define('LANG_SITE_SITENAME',            "Название сайта");
    define('LANG_SITE_HOMETITLE',           "Заголовок главной страницы");
    define('LANG_SITE_METAKEYS',            "Ключевые слова");
    define('LANG_SITE_METADESC',            "Описание сайта");
    define('LANG_SITE_CHECK_UPDATE',        "Автоматически проверять обновления InstantCMS");
    define('LANG_SITE_TEMPLATE',            'Шаблон сайта');
    define('LANG_SITE_TEMPLATE_ADMIN',      'Шаблон админпанели');

    define('LANG_SITE_SITENAME_ERROR',      "Требуется указать название сайта");
    define('LANG_SITE_HOMETITLE_ERROR',     "Требуется указать заголовок главной страницы");

    define('LANG_ADMIN_EXTERNAL',           'Реквизиты администратора будут взяты из таблицы <b>%s</b>');
    define('LANG_ADMIN_INFO',               'Для создания главного администратора необходимо указать его реквизиты');
    define('LANG_ADMIN_NAME',               'Имя администратора');
    define('LANG_ADMIN_EMAIL',              'E-mail администратора');
    define('LANG_ADMIN_PASS',               'Пароль администратора');
    define('LANG_ADMIN_PASS2',              'Пароль повторно');

    define('LANG_ADMIN_ERROR',              'Заполните все поля');
    define('LANG_ADMIN_EMAIL_ERROR',        'Указан некорректный адрес e-mail');
    define('LANG_ADMIN_PASS_ERROR',         'Пароли не совпадают');
    define('LANG_ADMIN_PASS_HASH_ERROR',    'Ошибка создания хэша пароля, попробуйте еще раз');
    define('LANG_VALIDATE_MIN_LENGTH',      'Слишком короткое значение поля %s (мин. длина: %s)');
    define('LANG_VALIDATE_MAX_LENGTH',      'Слишком длинное значение поля %s (макс. длина: %s)');

    define('LANG_CONFIG_INFO',              'Сейчас будет создан файл конфигурации сайта.');
    define('LANG_CONFIG_PATH',              'Место расположения файла:');
    define('LANG_CONFIG_MUST_WRITABLE',     'Указанная директория должна быть доступна для записи.');
    define('LANG_CONFIG_AFTER',             'После создания файла конфигурации необходимо будет сделать эту директорию (и находящиеся в ней файлы) недоступными для записи.');
    define('LANG_CONFIG_NOT_WRITABLE',      'Директория конфигурации недоступна для записи');

    define('LANG_CRON_1',                   'Для полноценной работы InstantCMS необходимо создать задание для планировщика CRON на сервере.');
    define('LANG_CRON_2',                   'Это позволит системе выполнять периодические служебные задачи в фоновом режиме.');
    define('LANG_CRON_FILE',                'Файл для запуска: <b>%s</b>');
    define('LANG_CRON_INT',                 'Интервал: <b>5 минут</b>');
    define('LANG_CRON_EXAMPLE',             'Обычно, команда которую нужно добавить в планировщик выглядит так:');
    define('LANG_CRON_SUPPORT_1',           'Подробную информацию о настройке CRON можно найти в разделе FAQ на сайте вашего хостинг-провайдера.');
    define('LANG_CRON_SUPPORT_2',           'При затруднении обратитесь в техническую поддержку хостинга, скопировав весь текст выше.');

    define('LANG_FINISH_1',                 'Установка InstantCMS завершена.');
    define('LANG_FINISH_2',                 'Перед тем как продолжить, удалите папку <b>install</b> в корне сайта.');

    define('LANG_FINISH_TO_SITE',           'Перейти на сайт');

    define('LANG_CFG_OFF_REASON',           'Идут технические работы');
    define('LANG_CFG_SITENAME',             'InstantCMS 2');
    define('LANG_CFG_HOMETITLE',            'InstantCMS 2');
    define('LANG_CFG_DATE_FORMAT',          'd.m.Y');
    define('LANG_CFG_DATE_FORMAT_JS',       'dd.mm.yy');
    define('LANG_CFG_TIME_ZONE',            'Europe/Moscow');
    define('LANG_CFG_METAKEYS',             'ключевые, слова, сайта');
    define('LANG_CFG_METADESC',             'Описание сайта');
