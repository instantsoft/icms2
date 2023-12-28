<?php

    define('LANG_USERS_CONTROLLER',         'Профили пользователей');

    define('LANG_USERS_LIST',               'Список пользователей');
    define('LANG_USERS_PROFILE',            'Профиль пользователя');
    define('LANG_USERS_RESTRICTIONS',       'Запреты');
    define('LANG_USERS_SOCIALITY',          'Социальность');
    define('LANG_USERS_EDIT_PROFILE',       'Редактировать профиль');
    define('LANG_USERS_EDIT_USER',          'Редактировать пользователя');

    define('LANG_RULE_USERS_CHANGE_SLUG',   'Изменять URL страницы профиля');
    define('LANG_USERS_RESTORE_PROFILE',    'Восстановить профиль');
    define('LANG_USERS_DELETE_PROFILE',     'Удалить профиль');
    define('LANG_RULE_USERS_DELETE',        'Удалять профиль');
    define('LANG_RULE_USERS_BIND_TO_PARENT', 'Привязка записей типов контента');
    define('LANG_RULE_USERS_BIND_OFF_PARENT', 'Отвязка записей типов контента');
    define('LANG_RULE_USERS_BAN',           'Блокировка пользователей');
    define('LANG_PERM_OPTION_MY',           'Только свой');
    define('LANG_PERM_OPTION_ANYUSER',      'Любой');
    define('LANG_USERS_DELETE_CONFIRM',     'Удалить профиль "%s"?');
    define('LANG_USERS_DELETE_SUCCESS',     'Профиль успешно удалён');
    define('LANG_USERS_RESTORE_SUCCESS',    'Профиль успешно восстановлен');
    define('LANG_USERS_DELETE_ADMIN_ERROR', 'Вы не можете удалять профили администраторов');
    define('LANG_USERS_IS_DELETED',         'Профиль удалён');

    define('LANG_USERS_EDIT_PROFILE_MAIN',  'Содержание');
    define('LANG_USERS_EDIT_PROFILE_THEME', 'Оформление');
    define('LANG_USERS_EDIT_PROFILE_NOTICES', 'Уведомления');
    define('LANG_USERS_EDIT_PROFILE_PRIVACY', 'Приватность');

    define('LANG_USERS_CFG_FIELDS',         'Поля профилей');
    define('LANG_USERS_CFG_TABS',           'Вкладки профилей');
    define('LANG_USERS_CFG_OPTIONS',        'Настройки');
    define('LANG_USERS_CFG_MIGRATION',      'Переводы');

    define('LANG_USERS_OPT_SHOW_USER_GROUPS', 'Показывать группы, к которым принадлежит пользователь');
    define('LANG_USERS_OPT_SHOW_REG_DATA',  'Показывать дату регистрации пользователя');
    define('LANG_USERS_OPT_SHOW_LAST_VISIT','Показывать последний визит пользователя');
    define('LANG_USERS_OPT_FRIENDSHIP',     'Разрешить добавление друзей');
    define('LANG_USERS_OPT_THEME',          'Разрешить настройку дизайна профиля');
    define('LANG_USERS_OPT_THEME_HINT',     'Работает только если шаблон сайта имеет поддержку данной функции');
    define('LANG_USERS_OPT_MAX_TABS',       'Максимальное число вкладок');
    define('LANG_USERS_OPT_MAX_TABS_HINT',  'Остальные вкладки будут помещены в пункт «Еще...»<br>0 — бесконечное количество');
    define('LANG_USERS_OPT_AUTH_ONLY',      'Разрешить просмотр профилей только авторизованным пользователям');
    define('LANG_USERS_OPT_RESTRICTED_SLUGS', 'Запрещенные адреса профилей');
    define('LANG_USERS_OPT_RESTRICTED_SLUGS_HINT', 'Один адрес на строке, можно использовать символ * для подстановки любого значения');
    define('LANG_USERS_OPT_RESTRICTED_SLUG', 'Использование адреса профиля <b>%s</b> запрещено');
    define('LANG_RULE_USERS_CHANGE_EMAIL',   'Разрешить смену email');
    define('LANG_RULE_USERS_CHANGE_EMAIL_PERIOD', 'Период возможной смены email, в днях');
    define('LANG_RULE_USERS_CHANGE_EMAIL_PERIOD_HINT', 'Не указано, можно менять всегда');
    define('LANG_USERS_EMAIL_VERIFY', 'На адрес <b>%s</b> отправлено письмо. Перейдите по ссылке из письма чтобы активировать смену почты');
    define('LANG_USERS_OPT_WALL_ENABLED',   'Включить стену профиля');
    define('LANG_USERS_OPT_STATUSES_ENABLED',   'Включить статус профиля');
    define('LANG_USERS_OPT_KARMA_ENABLED',   'Включить оценку репутации');
    define('LANG_USERS_OPT_KARMA_COMMENTS', 'Спрашивать пояснение при оценке репутации');
    define('LANG_USERS_OPT_KARMA_TIME',     'Период голосования за репутацию, дней');
    define('LANG_USERS_OPT_KARMA_TIME_HINT','Пользователь сможет оценивать репутацию другого пользователя только один раз в указанный период');
    define('LANG_USERS_OPT_MAX_FRIENDS_COUNT', 'Максимальное количество друзей на главной странице профиля');

    define('LANG_USERS_MIG_TITLE',              'Название правила');
    define('LANG_USERS_MIG_IS_ACTIVE',          'Правило активно');
    define('LANG_USERS_MIG_ADD',                'Создать правило перевода');
    define('LANG_USERS_MIG_DELETE_CONFIRM',     'Удалить правило "{title}"?');
    define('LANG_USERS_MIG_ACTION',             'Тип действия');
    define('LANG_USERS_MIG_ACTION_CHANGE',      'Сменить группу');
    define('LANG_USERS_MIG_ACTION_ADD',         'Добавить группу');
    define('LANG_USERS_MIG_FROM',               'Начальная группа');
    define('LANG_USERS_MIG_TO',                 'Конечная группа');
    define('LANG_USERS_MIG_COND_DATE',          'Ограничение по дате');
    define('LANG_USERS_MIG_PASSED_DAYS',        'Дней');
    define('LANG_USERS_MIG_PASSED',             'Прошло не менее, дней');
    define('LANG_USERS_MIG_PASSED_FROM',        'С момента');
    define('LANG_USERS_MIG_PASSED_REG',         'регистрации');
    define('LANG_USERS_MIG_PASSED_MIG',         'последнего перевода');
    define('LANG_USERS_MIG_COND_RATING',        'Ограничение по рейтингу');
    define('LANG_USERS_MIG_COND_KARMA',         'Ограничение по репутации');
    define('LANG_USERS_MIG_RATING',             'Рейтинг выше');
    define('LANG_USERS_MIG_KARMA',              'Репутация выше');
    define('LANG_USERS_MIG_NOTIFY',             'Отправить уведомление о переводе');
    define('LANG_USERS_MIG_NOTIFY_TEXT',        'Текст уведомления');

    define('LANG_USERS_KARMA_COMMENT',      'Пожалуйста, поясните почему вы ставите такую оценку');

    define('LANG_USERS_OPT_DS_SHOW',        'Показывать вкладку "%s"');
    define('LANG_USERS_DS_LATEST',          'Новые');
    define('LANG_USERS_DS_SUBSCRIBERS',     'Популярные по подписчикам');
    define('LANG_USERS_DS_POPULAR',         'Популярные');
    define('LANG_USERS_DS_ONLINE',          'Онлайн');
    define('LANG_USERS_DS_RATED',           'Рейтинг');
    define('LANG_USERS_DS_DATE_LOG',        'Дата последнего визита');
    define('LANG_USERS_OPT_LIST_ALLOWED',   'Список доступен для');

    define('LANG_USERS_OPT_FILTER_SHOW',    'Показывать фильтр');

    define('LANG_USERS_FIELD_PRIVATE',      'Показывать поле только владельцу профиля');

    define('LANG_USERS_PROFILE_INDEX',      'Профиль');
    define('LANG_USERS_PROFILE_CONTENT',    'Контент');
    define('LANG_USERS_PROFILE_FRIENDS',    'Друзья');
    define('LANG_USERS_PROFILE_ACTIVITY',   'Активность');
    define('LANG_USERS_PROFILE_FEED',       'Лента');

    define('LANG_USERS_PROFILE_WALL',       'Стена пользователя');

    define('LANG_USERS_PROFILE_REGDATE',    'Регистрация');
    define('LANG_USERS_PROFILE_INVITED_BY', 'По приглашению');

    define('LANG_USERS_PROFILE_IS_HIDDEN',  'Личная информация скрыта настройками приватности');
    define('LANG_USERS_CONTENT_IS_HIDDEN',  '%s запретил просмотр списка своих %s');

    define('LANG_USERS_FRIENDS',            'Друзья');
    define('LANG_USERS_FRIENDS_ADD',        'Добавить в друзья');
    define('LANG_USERS_FRIENDS_DELETE',     'Удалить из друзей');
    define('LANG_USERS_KEEP_IN_SUBSCRIBERS', 'Оставить в подписчиках');
    define('LANG_USERS_FRIENDS_CONFIRM',    'Отправить пользователю %s предложение дружбы?');
    define('LANG_USERS_SUBSCRIBE_CONFIRM',    'Подписаться на новости пользователя %s?');
    define('LANG_USERS_UNSUBSCRIBE_CONFIRM',    'Отписаться от пользователя %s?');
    define('LANG_USERS_FRIENDS_DELETE_CONFIRM', 'Удалить пользователя <b>%s</b> из списка ваших друзей?');
    define('LANG_USERS_FRIENDS_SUBSCRIBE_CONFIRM', 'Удалить пользователя <b>%s</b> из списка ваших друзей, оставив в подписчиках?');
    define('LANG_USERS_FRIENDS_SENT',       'Предложение дружбы отправлено');
    define('LANG_USERS_SUBSCRIBE_SUCCESS',  'Вы успешно подписались');
    define('LANG_USERS_UNSUBSCRIBE_SUCCESS',  'Вы успешно отписались');
    define('LANG_USERS_FRIENDS_DELETED',    '%s удален из списка друзей');
    define('LANG_USERS_FRIENDS_DECLINED',   '%s отклонил ваше предложение дружбы');
    define('LANG_USERS_KEEP_IN_SUBSCRIBERS_NOTICE',   '%s оставил вас в подписчиках');
    define('LANG_USERS_FRIENDS_NOTICE',     '%s предлагает вам стать друзьями');
    define('LANG_USERS_FRIENDS_DONE',       '%s стал вашим другом');
    define('LANG_USERS_SUBSCRIBE_DONE',     '%s подписался на вас');
    define('LANG_USERS_UNSUBSCRIBE_DONE',   '%s отписался от вас');
    define('LANG_USERS_FRIENDS_UNDONE',     '%s прекратил дружбу с вами');

    define('LANG_USERS_NOTIFY_VIA_NONE',    'Не уведомлять');
    define('LANG_USERS_NOTIFY_VIA_EMAIL',   'По e-mail');
    define('LANG_USERS_NOTIFY_VIA_PM',      'На сайте');
    define('LANG_USERS_NOTIFY_VIA_BOTH',    'По e-mail и на сайте');

    define('LANG_USERS_PRIVACY_FOR_ANYONE',  'Все');
    define('LANG_USERS_PRIVACY_FOR_FRIENDS', 'Только друзья');
    define('LANG_USERS_PRIVACY_FOR_NOBODY',  'Никто');

    define('LANG_USERS_NOTIFY_FRIEND_ADD',     'Уведомлять о запросах дружбы');
    define('LANG_USERS_NOTIFY_FRIEND_ACCEPT',  'Уведомлять об одобренных запросах дружбы');
    define('LANG_USERS_NOTIFY_FRIEND_DELETE',  'Уведомлять о прекращении дружбы');

    define('LANG_USERS_PRIVACY_FRIENDSHIP',    'Кто может отправлять вам запросы дружбы?');
    define('LANG_USERS_PRIVACY_SHOW_REG_DATA', 'Кто может видеть вашу дату регистрации?');
    define('LANG_USERS_PRIVACY_SHOW_LAST_VISIT', 'Кто может видеть ваш последний визит?');
    define('LANG_USERS_PRIVACY_SHOW_USER_GROUPS', 'Кто может видеть ваши группы?');
    define('LANG_USERS_PRIVACY_PROFILE_VIEW',  'Кто может просматривать ваш профиль?');
    define('LANG_USERS_PRIVACY_PROFILE_WALL',  'Кто может писать на вашей стене?');
    define('LANG_USERS_PRIVACY_PROFILE_WALL_REPLY', 'Кто может комментировать записи на стене?');
    define('LANG_USERS_PRIVACY_PROFILE_CTYPE',  'Кто может просматривать список ваших %s?');

    define('LANG_USERS_FRIENDS_SPELLCOUNT',     'друг|друга|друзей');

    define('LANG_USERS_ACTIVITY_FRIENDS',       'и %s становятся друзьями');

    define('LANG_USERS_LOCK_USER',              'Блокировка пользователя');
    define('LANG_USERS_LOCKED_NOTICE',          'Ваш профиль заблокирован.');
    define('LANG_USERS_LOCKED_NOTICE_PUBLIC',   'Заблокирован');
    define('LANG_USERS_LOCKED_NOTICE_UNTIL',    'Блокировка истекает: %s');
    define('LANG_USERS_LOCKED_NOTICE_REASON',   'Причина блокировки: %s');

    define('LANG_USERS_WHAT_HAPPENED',          'Что нового, %s?');
    define('LANG_USERS_DELETE_STATUS_CONFIRM',  'Удалить текущий статус?');

    define('LANG_RULE_USERS_VOTE_KARMA',       'Оценка чужой репутации');
    define('LANG_RULE_USERS_WALL_ADD',         'Добавление записей на стене');
    define('LANG_RULE_USERS_WALL_DELETE',      'Удаление записей со стены');

    define('LANG_USERS_KARMA_LOG',          'История репутации');
    define('LANG_USERS_KARMA_LOG_EMPTY',    'Пока никто не ставил оценок');

    define('LANG_USERS_MY_INVITES',         'Мои приглашения');
    define('LANG_USERS_INVITES_COUNT',      'Вы можете отправить %s');
    define('LANG_USERS_INVITES_LINKS',      'Или распространите ссылки для приглашения');
    define('LANG_USERS_INVITES_SPELLCOUNT', 'приглашение|приглашения|приглашений');
    define('LANG_USERS_INVITES_EMAIL',      'Адрес e-mail для отправки приглашения');
    define('LANG_USERS_INVITES_EMAILS',     'Адреса e-mail для отправки приглашений');
    define('LANG_USERS_INVITES_EMAILS_HINT','По одному адресу в строке');
    define('LANG_USERS_INVITES_SENT_TO',    'Приглашения успешно отправлены на адреса');
    define('LANG_USERS_INVITES_FAILED_TO',  'Не удалось отправить приглашения на адреса');
    define('LANG_USERS_SESSIONS',  'Сеансы');
    define('LANG_USERS_SESSIONS_DELETE',  'Сеанс успешно закрыт');
    define('LANG_SESS_DESKTOP',  'Настольный ПК');
    define('LANG_SESS_TABLET',  'Планшет');
    define('LANG_SESS_MOBILE',  'Телефон');
    define('LANG_SESS_APP',  'Приложение');
    define('LANG_SESS_NOT_FOUND',  'Нет активных сохранённых сеансов.');
    define('LANG_SESS_DROP',  'завершить');
    define('LANG_SESS_DROP_CONFIRM',  'Завершить этот сеанс?');
    define('LANG_SESS_IP',  'IP-адрес');
    define('LANG_SESS_LAST_DATE',  'Последняя активность');
    define('LANG_SESS_TYPE',  'Тип доступа');
    define('LANG_SESSIONS_HINT',  'Здесь показаны сеансы с активным доступом, когда при авторизации вы ставили чекбокс "Запомнить меня" или сеансы с мобильного приложения. Вы можете в любой момент прекратить любой из сеансов.');
    define('LANG_USERS_SLUG',  'Адрес Вашей страницы');
