<?php

    define('LANG_PAGE_TITLE',               'Установка InstantCMS');
    define('LANG_INSTALLATION_WIZARD',      'Майстер установки');
    define('LANG_NEXT',                     'Далі');

    define('LANG_MANUAL',                   '<a href="http://docs.instantcms.ru/manual/install" target="_blank" rel="noopener noreferrer">Інструкція із встановлення</a>');

    define('LANG_LANGUAGE_SELECT_RU',       'Пожалуйста, выберите язык');
    define('LANG_LANGUAGE_SELECT_UK',       'Будь ласка, виберіть мову');
    define('LANG_LANGUAGE_SELECT_EN',       'Please, select a language');

    define('LANG_STEP_LANGUAGE',            'Вибір мови');
    define('LANG_STEP_START',               'Вступ');
    define('LANG_STEP_LICENSE',             'Ліцензія');
    define('LANG_STEP_PHP_CHECK',           'Перевірка PHP');
    define('LANG_STEP_PATHS',               'Вказівка шляхів');
    define('LANG_STEP_DATABASE',            'База даних');
    define('LANG_STEP_SITE',                'Сайт');
    define('LANG_STEP_ADMIN',               'Адміністратор');
    define('LANG_STEP_CONFIG',              'Конфігурація');
    define('LANG_STEP_CRON',                'Планувальник');
    define('LANG_STEP_FINISH',              'Завершення');

    define('LANG_STEP_START_1',             'Майстер установки InstantCMS перевірить чи задовольняє ваш сервер системним вимогам.');
    define('LANG_STEP_START_2',             'У процесі роботи майстер задасть кілька запитань, необхідних для коректної установки і настройки InstantCMS.');
    define('LANG_STEP_START_3',             'Перед початком установки необхідно мати базу даних MySQL в кодуванні <b>utf8_general_ci</b>');

    define('LANG_LICENSE_AGREE',            'Я згоден з умовами ліцензії');
    define('LANG_LICENSE_ERROR',            'Ви повинні погодитися з умовами ліцензії');
    define('LANG_LICENSE_NOTE',             'InstantCMS поширюється по ліцензії <a href="http://www.gnu.org/licenses/gpl-2.0.html" target="_blank" rel="noopener noreferrer">GNU/GPL</a> версии 2.');
    define('LANG_LICENSE_ORIGINAL',         'Оригінал');
    define('LANG_LICENSE_TRANSLATION',      'Переклад');

    define('LANG_PHP_VERSION',              'Версія інтерпретатора');
    define('LANG_PHP_VERSION_REQ',          'Требуется PHP 5.5 або вище');
    define('LANG_PHP_VERSION_DESC',         'Встановлена версія');
	define('LANG_PHP_VARIABLES',            'Опції настройки PHP');
	define('LANG_PHP_VARIABLES_HINT',       'Сірим кольором вказано необхідне значення');
	define('LANG_PHP_VARIABLES_ON',			'Вкл');
	define('LANG_PHP_VARIABLES_OFF',		'Выкл');
    define('LANG_PHP_EXTENSIONS',           'Необхідні розширення');
    define('LANG_PHP_EXTENSIONS_REQ',       'Дані розширення необхідні для роботи InstantCMS');
    define('LANG_PHP_EXTENSIONS_EXTRA',     'Рекомендовані розширення');
    define('LANG_PHP_EXTENSIONS_EXTRA_REQ', 'Дані розширення не є необхідними, але без них<br>може бути недоступна частина функціоналу по оновленню і кешированию');
    define('LANG_PHP_EXT_INSTALLED',        'Встановлено');
	define('LANG_PHP_EXT_NOT_INSTALLED',    'Не знайдено');
	define('LANG_PHP_CHECK_ERROR',          'Ви не зможете продовжити до тих пір, поки умови відмічені червоним не будуть виправлені.');
	define('LANG_PHP_CHECK_ERROR_HINT',     'Зверніться в службу підтримки вашого хостингу з проханням забезпечити необхідні умови. Потім знову запустіть установку.');

    define('LANG_PATHS_ROOT_INFO',          'Всі шляхи зазначаються щодо:<br/><span class="root-path">%s</span>');
	define('LANG_PATHS_ROOT_CHANGE',        'змінити');
	define('LANG_PATHS_CHANGE_INFO',        'Після встановлення шляху можна буде змінити, відредагувавши файл конфігурації.<br/>Не забудьте зробити це при перенесення сайту з локального сервера на хостинг!');
	define('LANG_PATHS_MUST_WRITABLE',      'Повинна бути доступна для запису');
	define('LANG_PATHS_NOT_WRITABLE',       'не доступна для запису!');
	define('LANG_PATHS_WRITABLE_HINT',      'Встановити правильні права на цю папку');

	define('LANG_PATHS_ROOT',               'Корінь');
	define('LANG_PATHS_ROOT_PATH',          'Коренева папка');
	define('LANG_PATHS_ROOT_HOST',          'Кореневої URL');
	define('LANG_PATHS_UPLOAD',             'Завантаження');
	define('LANG_PATHS_UPLOAD_PATH',        'Папка для завантажень');
	define('LANG_PATHS_UPLOAD_HOST',        'URL завантажень');
	define('LANG_PATHS_CACHE',              'Кеш');
	define('LANG_PATHS_CACHE_PATH',         'Папка для кеша');

    define('LANG_DATABASE_INFO',            'Вкажіть реквізити для підключення до бази даних MySQL');
	define('LANG_DATABASE_CHARSET_INFO',    'База даних повинна бути в кодуванні <b>utf8_general_ci</b>');
	define('LANG_DATABASE_HOST',            'Сервер MySQL');
	define('LANG_DATABASE_USER',            'Користувач');
	define('LANG_DATABASE_PASS',            'Пароль');
	define('LANG_DATABASE_BASE',            'База даних');
	define('LANG_DATABASE_BASE_HINT',       'Якщо не існує, буде створена');
	define('LANG_DATABASE_ENGINE',          'Движок бази даних');
	define('LANG_DATABASE_ENGINE_HINT',     'Не знаєте що вибрати? Вибирайте MyISAM.');
	define('LANG_DATABASE_PREFIX',          'Префікс таблиць');
	define('LANG_DATABASE_USERS_TABLE',     'Таблиця з користувачами');
	define('LANG_DATABASE_USERS_TABLE_NEW', 'Створити нову');
	define('LANG_DATABASE_USERS_TABLE_OLD', 'Використовувати наявну');
	define('LANG_DATABASE_INSTALL_DEMO',    'Встановити демо дані');

	define('LANG_DATABASE_PREFIX_ERROR',    'Префікс БД може містити тільки латинські літери, цифри та знак підкреслення');
	define('LANG_DATABASE_SELECT_ERROR',    'Неможливо вибрати базу даних %s');
	define('LANG_DATABASE_CONNECT_ERROR',   "Помилка підключення MySQL:\n\n%s");
	define('LANG_DATABASE_BASE_ERROR',      "Помилка імпорту бази даних\пПроверьте правильність реквізитів");
	define('LANG_DATABASE_ENGINE_NO',       'Обраний движок БД не підтримується');
	define('LANG_DATABASE_ENGINE_DISABLED', 'Обраний движок БД підтримується, але відключений у налаштуваннях MySQL');
	define('LANG_DATABASE_ENGINE_ERROR',    'Обраний движок БД не підтримується сервером');

	define('LANG_SITE_SITENAME',            'Назва сайту');
	define('LANG_SITE_HOMETITLE',           'Заголовок головної сторінки');
	define('LANG_SITE_METAKEYS',            'Ключові слова');
	define('LANG_SITE_METADESC',            'Опис сайту');
	define('LANG_SITE_CHECK_UPDATE',        'Автоматично перевіряти оновлення InstantCMS');

	define('LANG_SITE_SITENAME_ERROR',      'Потрібно вказати назву сайту');
	define('LANG_SITE_HOMETITLE_ERROR',     'Потрібно вказати заголовок головної сторінки');

	define('LANG_ADMIN_EXTERNAL',           'Реквізити адміністратора будуть взяті з таблиці <b>%s</b>');
	define('LANG_ADMIN_INFO',               'Для створення головного адміністратора необхідно вказати його реквізити');
	define('LANG_ADMIN_NAME',               'Ім\'я адміністратора');
	define('LANG_ADMIN_EMAIL',              'E-mail адміністратора');
	define('LANG_ADMIN_PASS',               'Пароль');
	define('LANG_ADMIN_PASS2',              'Пароль повторно');

	define('LANG_ADMIN_ERROR',              'Заповніть всі поля');
	define('LANG_ADMIN_EMAIL_ERROR',        'Вказано некоректний адресу e-mail');
	define('LANG_ADMIN_PASS_ERROR',         'Паролі не збігаються');

	define('LANG_CONFIG_INFO',              'Зараз буде створений файл конфігурації сайту.');
	define('LANG_CONFIG_PATH',              'Місце розташування файлу:');
	define('LANG_CONFIG_MUST_WRITABLE',     'ця папка повинна бути доступна для запису.');
	define('LANG_CONFIG_AFTER',             'Після створення файлу конфігурації необхідно буде зробити цю папку (і знаходяться в ній файли недоступними для запису.');
	define('LANG_CONFIG_NOT_WRITABLE',      'Папка конфігурації недоступна для запису');

	define('LANG_CRON_1',                   'Для повноцінної роботи InstantCMS необхідно створити завдання для планувальника CRON на сервері.');
	define('LANG_CRON_2',                   'Це дозволить системі виконувати періодичні службові завдання у фоновому режимі.');
	define('LANG_CRON_FILE',                'Файл для запуску: <b>%s</b>');
	define('LANG_CRON_INT',                 'Інтервал: <b>5 хвилин</b>');
	define('LANG_CRON_EXAMPLE',             'Звичайно, команда яку потрібно додати в планувальник виглядає так:');
	define('LANG_CRON_SUPPORT_1',           'Детальну інформацію про налаштування CRON можна знайти в розділі FAQ на сайті вашого хостинг-провайдера.');
	define('LANG_CRON_SUPPORT_2',           'При утрудненні зверніться в технічну підтримку хостингу, скопіювавши весь текст вище.');

	define('LANG_FINISH_1',                 'Установка InstantCMS завершена.');
	define('LANG_FINISH_2',                 'Перед тим як продовжити, видаліть папку <b>install</b> в корені сайту.');

	define('LANG_FINISH_TO_SITE',           'Перейти на сайт');

	define('LANG_CFG_OFF_REASON',           'Йдуть технічні роботи');
	define('LANG_CFG_SITENAME',             'InstantCMS 2');
	define('LANG_CFG_HOMETITLE',            'InstantCMS 2');
	define('LANG_CFG_DATE_FORMAT',          'd.m.Y');
	define('LANG_CFG_DATE_FORMAT_JS',       'dd.mm.yy');
	define('LANG_CFG_TIME_ZONE',            'Europe/Moscow');
	define('LANG_CFG_METAKEYS',             'ключові слова, сайту');
	define('LANG_CFG_METADESC',             'Опис сайту');
