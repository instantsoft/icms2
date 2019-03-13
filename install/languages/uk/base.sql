SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `{#}con_pages`;
DROP TABLE IF EXISTS `{#}con_pages_cats`;
DROP TABLE IF EXISTS `{#}con_pages_cats_bind`;
DROP TABLE IF EXISTS `{#}con_pages_fields`;
DROP TABLE IF EXISTS `{#}con_pages_props`;
DROP TABLE IF EXISTS `{#}con_pages_props_bind`;
DROP TABLE IF EXISTS `{#}con_pages_props_values`;
DROP TABLE IF EXISTS `{#}content_datasets`;
DROP TABLE IF EXISTS `{#}content_folders`;
DROP TABLE IF EXISTS `{#}content_relations`;
DROP TABLE IF EXISTS `{#}content_relations_bind`;
DROP TABLE IF EXISTS `{#}content_types`;
DROP TABLE IF EXISTS `{#}controllers`;
DROP TABLE IF EXISTS `{#}events`;
DROP TABLE IF EXISTS `{#}images_presets`;
DROP TABLE IF EXISTS `{#}jobs`;
DROP TABLE IF EXISTS `{#}menu`;
DROP TABLE IF EXISTS `{#}menu_items`;
DROP TABLE IF EXISTS `{#}perms_rules`;
DROP TABLE IF EXISTS `{#}perms_users`;
DROP TABLE IF EXISTS `{#}scheduler_tasks`;
DROP TABLE IF EXISTS `{#}sessions_online`;
DROP TABLE IF EXISTS `{#}uploaded_files`;
DROP TABLE IF EXISTS `{#}users`;
DROP TABLE IF EXISTS `{#}users_auth_tokens`;
DROP TABLE IF EXISTS `{#}users_contacts`;
DROP TABLE IF EXISTS `{#}users_fields`;
DROP TABLE IF EXISTS `{#}users_groups`;
DROP TABLE IF EXISTS `{#}users_groups_members`;
DROP TABLE IF EXISTS `{#}users_groups_migration`;
DROP TABLE IF EXISTS `{#}users_ignors`;
DROP TABLE IF EXISTS `{#}users_personal_settings`;
DROP TABLE IF EXISTS `{#}users_tabs`;
DROP TABLE IF EXISTS `{#}widgets`;
DROP TABLE IF EXISTS `{#}widgets_bind`;
DROP TABLE IF EXISTS `{#}widgets_pages`;
CREATE TABLE `{#}con_pages`
(
  `id`                 int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title`              varchar(100)              DEFAULT NULL,
  `content`            text                      DEFAULT NULL,
  `slug`               varchar(100)              DEFAULT NULL,
  `seo_keys`           varchar(256)              DEFAULT NULL,
  `seo_desc`           varchar(256)              DEFAULT NULL,
  `seo_title`          varchar(256)              DEFAULT NULL,
  `tags`               varchar(1000)             DEFAULT NULL,
  `template`           varchar(150)              DEFAULT NULL,
  `date_pub`           timestamp        NOT NULL DEFAULT current_timestamp(),
  `date_last_modified` timestamp        NULL     DEFAULT NULL,
  `date_pub_end`       timestamp        NULL     DEFAULT NULL,
  `is_pub`             tinyint(1)                DEFAULT 1,
  `hits_count`         int(11)                   DEFAULT 0,
  `user_id`            int(11) unsigned          DEFAULT NULL,
  `parent_id`          int(11) unsigned          DEFAULT NULL,
  `parent_type`        varchar(32)               DEFAULT NULL,
  `parent_title`       varchar(100)              DEFAULT NULL,
  `parent_url`         varchar(255)              DEFAULT NULL,
  `is_parent_hidden`   tinyint(1)                DEFAULT NULL,
  `category_id`        int(11) unsigned NOT NULL DEFAULT 1,
  `folder_id`          int(11) unsigned          DEFAULT NULL,
  `is_comments_on`     tinyint(1) unsigned       DEFAULT 1,
  `comments`           int(11)          NOT NULL DEFAULT 0,
  `rating`             int(11)          NOT NULL DEFAULT 0,
  `is_deleted`         tinyint(1) unsigned       DEFAULT NULL,
  `is_approved`        tinyint(1)                DEFAULT 1,
  `approved_by`        int(11)                   DEFAULT NULL,
  `date_approved`      timestamp        NULL     DEFAULT NULL,
  `is_private`         tinyint(1)       NOT NULL DEFAULT 0,
  `attach`             text                      DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `folder_id` (`folder_id`),
  KEY `slug` (`slug`),
  KEY `date_pub` (`is_pub`, `is_parent_hidden`, `is_deleted`, `is_approved`, `date_pub`),
  KEY `parent_id` (`parent_id`, `parent_type`, `date_pub`),
  KEY `user_id` (`user_id`, `date_pub`),
  KEY `date_pub_end` (`date_pub_end`),
  FULLTEXT KEY `title` (`title`)
) ENGINE = MyISAM
  DEFAULT CHARSET = utf8;
CREATE TABLE `{#}con_pages_cats`
(
  `id`          int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id`   int(11) unsigned          DEFAULT NULL,
  `title`       varchar(200)              DEFAULT NULL,
  `description` text                      DEFAULT NULL,
  `slug`        varchar(255)              DEFAULT NULL,
  `slug_key`    varchar(255)              DEFAULT NULL,
  `seo_keys`    varchar(256)              DEFAULT NULL,
  `seo_desc`    varchar(256)              DEFAULT NULL,
  `seo_title`   varchar(256)              DEFAULT NULL,
  `ordering`    int(11)                   DEFAULT NULL,
  `ns_left`     int(11)                   DEFAULT NULL,
  `ns_right`    int(11)                   DEFAULT NULL,
  `ns_level`    int(11)                   DEFAULT NULL,
  `ns_differ`   varchar(32)      NOT NULL DEFAULT '',
  `ns_ignore`   tinyint(4)       NOT NULL DEFAULT 0,
  `allow_add`   text                      DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ordering` (`ordering`),
  KEY `slug` (`slug`),
  KEY `ns_left` (`ns_level`, `ns_right`, `ns_left`),
  KEY `parent_id` (`parent_id`, `ns_left`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 2
  DEFAULT CHARSET = utf8;
CREATE TABLE `{#}con_pages_cats_bind`
(
  `item_id`     int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  KEY `item_id` (`item_id`),
  KEY `category_id` (`category_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
CREATE TABLE `{#}con_pages_fields`
(
  `id`            int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ctype_id`      int(11)      DEFAULT NULL,
  `name`          varchar(40)  DEFAULT NULL,
  `title`         varchar(100) DEFAULT NULL,
  `hint`          varchar(200) DEFAULT NULL,
  `ordering`      int(11)      DEFAULT NULL,
  `fieldset`      varchar(32)  DEFAULT NULL,
  `type`          varchar(16)  DEFAULT NULL,
  `is_in_list`    tinyint(1)   DEFAULT NULL,
  `is_in_item`    tinyint(1)   DEFAULT NULL,
  `is_in_filter`  tinyint(1)   DEFAULT NULL,
  `is_private`    tinyint(1)   DEFAULT NULL,
  `is_fixed`      tinyint(1)   DEFAULT NULL,
  `is_fixed_type` tinyint(1)   DEFAULT NULL,
  `is_system`     tinyint(1)   DEFAULT NULL,
  `values`        text         DEFAULT NULL,
  `options`       text         DEFAULT NULL,
  `groups_read`   text         DEFAULT NULL,
  `groups_edit`   text         DEFAULT NULL,
  `filter_view`   text         DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ordering` (`ordering`),
  KEY `is_in_list` (`is_in_list`),
  KEY `is_in_item` (`is_in_item`),
  KEY `is_in_filter` (`is_in_filter`),
  KEY `is_private` (`is_private`),
  KEY `is_fixed` (`is_fixed`),
  KEY `is_system` (`is_system`),
  KEY `is_fixed_type` (`is_fixed_type`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 6
  DEFAULT CHARSET = utf8;
CREATE TABLE `{#}con_pages_props`
(
  `id`           int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ctype_id`     int(11)      DEFAULT NULL,
  `title`        varchar(100) DEFAULT NULL,
  `fieldset`     varchar(32)  DEFAULT NULL,
  `type`         varchar(16)  DEFAULT NULL,
  `is_in_filter` tinyint(1)   DEFAULT NULL,
  `values`       text         DEFAULT NULL,
  `options`      text         DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `is_active` (`is_in_filter`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
CREATE TABLE `{#}con_pages_props_bind`
(
  `id`       int(11) unsigned NOT NULL AUTO_INCREMENT,
  `prop_id`  int(11) DEFAULT NULL,
  `cat_id`   int(11) DEFAULT NULL,
  `ordering` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `prop_id` (`prop_id`),
  KEY `ordering` (`cat_id`, `ordering`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
CREATE TABLE `{#}con_pages_props_values`
(
  `prop_id` int(11)      DEFAULT NULL,
  `item_id` int(11)      DEFAULT NULL,
  `value`   varchar(255) DEFAULT NULL,
  KEY `prop_id` (`prop_id`),
  KEY `item_id` (`item_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
CREATE TABLE `{#}content_datasets`
(
  `id`                int(11) unsigned     NOT NULL AUTO_INCREMENT,
  `ctype_id`          int(11) unsigned              DEFAULT NULL COMMENT 'ID типа контента',
  `name`              varchar(32)          NOT NULL COMMENT 'Название набора',
  `title`             varchar(100)         NOT NULL COMMENT 'Заголовок набора',
  `description`       text                          DEFAULT NULL COMMENT 'Описание',
  `ordering`          int(11) unsigned              DEFAULT NULL COMMENT 'Порядковый номер',
  `is_visible`        tinyint(1) unsigned           DEFAULT NULL COMMENT 'Отображать набор на сайте?',
  `filters`           text                          DEFAULT NULL COMMENT 'Массив фильтров набора',
  `sorting`           text                          DEFAULT NULL COMMENT 'Массив правил сортировки',
  `index`             varchar(40)                   DEFAULT NULL COMMENT 'Название используемого индекса',
  `groups_view`       text                          DEFAULT NULL COMMENT 'Показывать группам',
  `groups_hide`       text                          DEFAULT NULL COMMENT 'Скрывать от групп',
  `seo_keys`          varchar(256)                  DEFAULT NULL,
  `seo_desc`          varchar(256)                  DEFAULT NULL,
  `seo_title`         varchar(256)                  DEFAULT NULL,
  `cats_view`         text                          DEFAULT NULL COMMENT 'Показывать в категориях',
  `cats_hide`         text                          DEFAULT NULL COMMENT 'Не показывать в категориях',
  `max_count`         smallint(5) unsigned NOT NULL DEFAULT 0,
  `target_controller` varchar(32)                   DEFAULT NULL,
  `list`              text                          DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `ctype_id` (`ctype_id`, `ordering`),
  KEY `index` (`index`),
  KEY `target_controller` (`target_controller`, `ordering`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8 COMMENT ='Наборы для типов контента';
CREATE TABLE `{#}content_folders`
(
  `id`       int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ctype_id` int(11) unsigned DEFAULT NULL,
  `user_id`  int(11) unsigned DEFAULT NULL,
  `title`    varchar(128)     DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`, `ctype_id`, `title`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8 COMMENT ='Папки для записей типов контента';
CREATE TABLE `{#}content_relations`
(
  `id`                int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title`             varchar(256)              DEFAULT NULL,
  `target_controller` varchar(32)      NOT NULL DEFAULT 'content',
  `ctype_id`          int(11) unsigned          DEFAULT NULL,
  `child_ctype_id`    int(11) unsigned          DEFAULT NULL,
  `layout`            varchar(32)               DEFAULT NULL,
  `options`           text                      DEFAULT NULL,
  `seo_keys`          varchar(256)              DEFAULT NULL,
  `seo_desc`          varchar(256)              DEFAULT NULL,
  `seo_title`         varchar(256)              DEFAULT NULL,
  `ordering`          int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `ctype_id` (`ctype_id`, `ordering`),
  KEY `child_ctype_id` (`child_ctype_id`, `target_controller`, `ordering`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8 COMMENT ='Свзяи типов контента';
CREATE TABLE `{#}content_relations_bind`
(
  `id`                int(11)     NOT NULL AUTO_INCREMENT,
  `parent_ctype_id`   int(11) unsigned     DEFAULT NULL,
  `parent_item_id`    int(11) unsigned     DEFAULT NULL,
  `child_ctype_id`    int(11) unsigned     DEFAULT NULL,
  `child_item_id`     int(11) unsigned     DEFAULT NULL,
  `target_controller` varchar(32) NOT NULL DEFAULT 'content',
  PRIMARY KEY (`id`),
  KEY `parent_ctype_id` (`parent_ctype_id`),
  KEY `child_ctype_id` (`child_ctype_id`),
  KEY `parent_item_id` (`parent_item_id`, `target_controller`),
  KEY `child_item_id` (`child_item_id`, `target_controller`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
CREATE TABLE `{#}content_types`
(
  `id`                int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title`             varchar(100)     NOT NULL,
  `name`              varchar(32)      NOT NULL COMMENT 'Системное имя',
  `description`       text                DEFAULT NULL COMMENT 'Описание',
  `ordering`          int(11)             DEFAULT NULL,
  `is_date_range`     tinyint(1) unsigned DEFAULT NULL,
  `is_premod_add`     tinyint(1) unsigned DEFAULT NULL COMMENT 'Модерация при создании?',
  `is_premod_edit`    tinyint(1) unsigned DEFAULT NULL COMMENT 'Модерация при редактировании',
  `is_cats`           tinyint(1) unsigned DEFAULT NULL COMMENT 'Категории включены?',
  `is_cats_recursive` tinyint(1) unsigned DEFAULT NULL COMMENT 'Сквозной просмотр категорий?',
  `is_folders`        tinyint(1) unsigned DEFAULT NULL,
  `is_in_groups`      tinyint(1) unsigned DEFAULT NULL COMMENT 'Создание в группах',
  `is_in_groups_only` tinyint(1) unsigned DEFAULT NULL COMMENT 'Создание только в группах',
  `is_comments`       tinyint(1) unsigned DEFAULT NULL COMMENT 'Комментарии включены?',
  `is_comments_tree`  tinyint(1) unsigned DEFAULT NULL,
  `is_rating`         tinyint(1) unsigned DEFAULT NULL COMMENT 'Разрешить рейтинг?',
  `is_rating_pos`     tinyint(1) unsigned DEFAULT NULL,
  `is_tags`           tinyint(1) unsigned DEFAULT NULL,
  `is_auto_keys`      tinyint(1) unsigned DEFAULT NULL COMMENT 'Автоматическая генерация ключевых слов?',
  `is_auto_desc`      tinyint(1) unsigned DEFAULT NULL COMMENT 'Автоматическая генерация описания?',
  `is_auto_url`       tinyint(1) unsigned DEFAULT NULL COMMENT 'Генерировать URL из заголовка?',
  `is_fixed_url`      tinyint(1) unsigned DEFAULT NULL COMMENT 'Не изменять URL при изменении записи?',
  `url_pattern`       varchar(255)        DEFAULT '{id}-{title}',
  `options`           text                DEFAULT NULL COMMENT 'Массив опций',
  `labels`            text                DEFAULT NULL COMMENT 'Массив заголовков',
  `seo_keys`          varchar(256)        DEFAULT NULL COMMENT 'Ключевые слова',
  `seo_desc`          varchar(256)        DEFAULT NULL COMMENT 'Описание',
  `seo_title`         varchar(256)        DEFAULT NULL,
  `item_append_html`  text                DEFAULT NULL,
  `is_fixed`          tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `ordering` (`ordering`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 8
  DEFAULT CHARSET = utf8 COMMENT ='Типы контента';
CREATE TABLE `{#}controllers`
(
  `id`          int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title`       varchar(64)      NOT NULL,
  `name`        varchar(32)      NOT NULL COMMENT 'Системное имя',
  `slug`        varchar(64)         DEFAULT NULL,
  `is_enabled`  tinyint(1) unsigned DEFAULT 1 COMMENT 'Включен?',
  `options`     text                DEFAULT NULL COMMENT 'Массив настроек',
  `author`      varchar(128)     NOT NULL COMMENT 'Имя автора',
  `url`         varchar(250)        DEFAULT NULL COMMENT 'Сайт автора',
  `version`     varchar(8)       NOT NULL COMMENT 'Версия',
  `is_backend`  tinyint(1) unsigned DEFAULT NULL COMMENT 'Есть админка?',
  `is_external` tinyint(1) unsigned DEFAULT NULL COMMENT 'Сторонний компонент',
  `files`       text                DEFAULT NULL COMMENT 'Список файлов контроллера (для стороних компонентов)',
  `addon_id`    int(11) unsigned    DEFAULT NULL COMMENT 'ID дополнения в официальном каталоге',
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 23
  DEFAULT CHARSET = utf8 COMMENT ='Компоненты';
CREATE TABLE `{#}events`
(
  `id`         int(11) unsigned NOT NULL AUTO_INCREMENT,
  `event`      varchar(64)         DEFAULT NULL COMMENT 'Событие',
  `listener`   varchar(32)         DEFAULT NULL COMMENT 'Слушатель (компонент)',
  `ordering`   int(5) unsigned     DEFAULT NULL COMMENT 'Порядковый номер ',
  `is_enabled` tinyint(1) unsigned DEFAULT 1 COMMENT 'Активность',
  PRIMARY KEY (`id`),
  KEY `hook` (`event`),
  KEY `listener` (`listener`),
  KEY `is_enabled` (`is_enabled`, `ordering`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 153
  DEFAULT CHARSET = utf8 COMMENT ='Привязка хуков к событиям';
CREATE TABLE `{#}images_presets`
(
  `id`           int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name`         varchar(32)               DEFAULT NULL,
  `title`        varchar(128)              DEFAULT NULL,
  `width`        int(11) unsigned          DEFAULT NULL,
  `height`       int(11) unsigned          DEFAULT NULL,
  `is_square`    tinyint(1) unsigned       DEFAULT NULL,
  `is_watermark` tinyint(1) unsigned       DEFAULT NULL,
  `wm_image`     text                      DEFAULT NULL,
  `wm_origin`    varchar(16)               DEFAULT NULL,
  `wm_margin`    int(11) unsigned          DEFAULT NULL,
  `is_internal`  tinyint(1) unsigned       DEFAULT NULL,
  `quality`      tinyint(1)       NOT NULL DEFAULT 90,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `is_square` (`is_square`),
  KEY `is_watermark` (`is_watermark`),
  KEY `is_internal` (`is_internal`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 8
  DEFAULT CHARSET = utf8 COMMENT ='Пресеты для конвертации изображений';
CREATE TABLE `{#}jobs`
(
  `id`           bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue`        varchar(100)                 DEFAULT NULL COMMENT 'Название очереди',
  `payload`      text                         DEFAULT NULL COMMENT 'Данные задания',
  `last_error`   varchar(200)                 DEFAULT NULL COMMENT 'Последняя ошибка',
  `priority`     tinyint(1) unsigned          DEFAULT 1 COMMENT 'Приоритет',
  `attempts`     tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT 'Попытки выполнения',
  `is_locked`    tinyint(1) unsigned          DEFAULT NULL COMMENT 'Блокировка одновременного запуска',
  `date_created` timestamp           NOT NULL DEFAULT current_timestamp() COMMENT 'Дата постановки в очередь',
  `date_started` timestamp           NULL     DEFAULT NULL COMMENT 'Дата последней попытки выполнения задания',
  PRIMARY KEY (`id`),
  KEY `queue` (`queue`),
  KEY `attempts` (`attempts`, `is_locked`, `date_started`, `priority`, `date_created`) USING BTREE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8 COMMENT ='Очередь';
CREATE TABLE `{#}menu`
(
  `id`       int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name`     varchar(32)      NOT NULL COMMENT 'Системное имя',
  `title`    varchar(64)         DEFAULT NULL COMMENT 'Название меню',
  `is_fixed` tinyint(1) unsigned DEFAULT NULL COMMENT 'Запрещено удалять?',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 7
  DEFAULT CHARSET = utf8 COMMENT ='Меню сайта';
CREATE TABLE `{#}menu_items`
(
  `id`          int(11) unsigned NOT NULL AUTO_INCREMENT,
  `menu_id`     int(11) unsigned    DEFAULT NULL COMMENT 'ID меню',
  `parent_id`   int(11) unsigned    DEFAULT 0 COMMENT 'ID родительского пункта',
  `is_enabled`  tinyint(1) unsigned DEFAULT 1 COMMENT 'Включен?',
  `title`       varchar(64)         DEFAULT NULL COMMENT 'Заголовок пункта',
  `url`         varchar(255)        DEFAULT NULL COMMENT 'Ссылка',
  `ordering`    int(11) unsigned    DEFAULT NULL COMMENT 'Порядковый номер',
  `options`     text                DEFAULT NULL COMMENT 'Массив опций',
  `groups_view` text                DEFAULT NULL COMMENT 'Массив разрешенных групп пользователей',
  `groups_hide` text                DEFAULT NULL COMMENT 'Массив запрещенных групп пользователей',
  PRIMARY KEY (`id`),
  KEY `menu_id` (`menu_id`),
  KEY `parent_id` (`parent_id`),
  KEY `ordering` (`ordering`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 44
  DEFAULT CHARSET = utf8 COMMENT ='Пункты меню';
CREATE TABLE `{#}perms_rules`
(
  `id`                   int(11) unsigned              NOT NULL AUTO_INCREMENT,
  `controller`           varchar(32)                            DEFAULT NULL COMMENT 'Компонент (владелец)',
  `name`                 varchar(32)                   NOT NULL COMMENT 'Название правила',
  `type`                 enum ('flag','list','number') NOT NULL DEFAULT 'flag' COMMENT 'Тип выбора (flag,list...)',
  `options`              varchar(128)                           DEFAULT NULL COMMENT 'Массив возможных значений',
  `show_for_guest_group` tinyint(1)                             DEFAULT NULL COMMENT 'Показывать правило для группы гости',
  PRIMARY KEY (`id`),
  KEY `controller` (`controller`),
  KEY `name` (`name`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 47
  DEFAULT CHARSET = utf8 COMMENT ='Перечь всех возможных правил доступа';
CREATE TABLE `{#}perms_users`
(
  `rule_id`  int(11) unsigned DEFAULT NULL COMMENT 'ID правила',
  `group_id` int(11) unsigned DEFAULT NULL COMMENT 'ID группы',
  `subject`  varchar(32)      DEFAULT NULL COMMENT 'Субъект действия правила',
  `value`    varchar(16) NOT NULL COMMENT 'Значение правила',
  KEY `rule_id` (`rule_id`),
  KEY `group_id` (`group_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8 COMMENT ='Привязка правил доступа к группам пользователей';
CREATE TABLE `{#}scheduler_tasks`
(
  `id`               int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title`            varchar(250)          DEFAULT NULL,
  `controller`       varchar(32)           DEFAULT NULL,
  `hook`             varchar(32)           DEFAULT NULL,
  `period`           int(11) unsigned      DEFAULT NULL,
  `is_strict_period` tinyint(1) unsigned   DEFAULT NULL,
  `date_last_run`    timestamp        NULL DEFAULT NULL,
  `is_active`        tinyint(1) unsigned   DEFAULT NULL,
  `is_new`           tinyint(1) unsigned   DEFAULT 1,
  `consistent_run`   tinyint(1) unsigned   DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `period` (`period`),
  KEY `date_last_run` (`date_last_run`),
  KEY `is_active` (`is_active`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 10
  DEFAULT CHARSET = utf8 COMMENT ='Задачи планировщика';
CREATE TABLE `{#}sessions_online`
(
  `user_id`      int(11) unsigned   DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  UNIQUE KEY `user_id` (`user_id`),
  KEY `date_created` (`date_created`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8 COMMENT ='Онлайн сессии';
CREATE TABLE `{#}uploaded_files`
(
  `id`                int(11) unsigned                      NOT NULL AUTO_INCREMENT,
  `path`              varchar(255)                                   DEFAULT NULL COMMENT 'Путь к файлу',
  `name`              varchar(255)                                   DEFAULT NULL COMMENT 'Имя файла',
  `size`              int(11) unsigned                               DEFAULT NULL COMMENT 'Размер файла',
  `counter`           int(11) unsigned                      NOT NULL DEFAULT 0 COMMENT 'Счетчик скачиваний',
  `type`              enum ('file','image','audio','video') NOT NULL DEFAULT 'file' COMMENT 'Тип файла',
  `target_controller` varchar(32)                                    DEFAULT NULL COMMENT 'Контроллер привязки',
  `target_subject`    varchar(32)                                    DEFAULT NULL COMMENT 'Субъект привязки',
  `target_id`         int(11) unsigned                               DEFAULT NULL COMMENT 'ID субъекта',
  `user_id`           int(11) unsigned                               DEFAULT NULL COMMENT 'ID владельца',
  `date_add`          timestamp                             NOT NULL DEFAULT current_timestamp() COMMENT 'Дата добавления',
  PRIMARY KEY (`id`),
  UNIQUE KEY `path` (`path`),
  KEY `user_id` (`user_id`),
  KEY `target_controller` (`target_controller`, `target_subject`, `target_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8 COMMENT ='Загруженные файлы';
CREATE TABLE `{#}users`
(
  `id`                int(11) unsigned NOT NULL AUTO_INCREMENT,
  `groups`            text                      DEFAULT NULL COMMENT 'Массив групп пользователя',
  `email`             varchar(100)     NOT NULL,
  `password`          varchar(100)     NOT NULL COMMENT 'Хэш пароля',
  `password_salt`     varchar(16)               DEFAULT NULL COMMENT 'Соль пароля',
  `is_admin`          tinyint(1) unsigned       DEFAULT NULL COMMENT 'Администратор?',
  `nickname`          varchar(100)     NOT NULL COMMENT 'Имя',
  `date_reg`          timestamp        NULL     DEFAULT NULL COMMENT 'Дата регистрации',
  `date_log`          timestamp        NULL     DEFAULT NULL COMMENT 'Дата последней авторизации',
  `date_group`        timestamp        NOT NULL DEFAULT current_timestamp() COMMENT 'Время последней смены группы',
  `ip`                varchar(45)               DEFAULT NULL,
  `is_deleted`        tinyint(1) unsigned       DEFAULT NULL COMMENT 'Удалён',
  `is_locked`         tinyint(1) unsigned       DEFAULT NULL COMMENT 'Заблокирован',
  `lock_until`        timestamp        NULL     DEFAULT NULL COMMENT 'Блокировка до',
  `lock_reason`       varchar(250)              DEFAULT NULL COMMENT 'Причина блокировки',
  `pass_token`        varchar(32)               DEFAULT NULL COMMENT 'Ключ для восстановления пароля',
  `date_token`        timestamp        NULL     DEFAULT NULL COMMENT 'Дата создания ключа восстановления пароля',
  `friends_count`     int(11) unsigned NOT NULL DEFAULT 0 COMMENT 'Кол-во друзей',
  `subscribers_count` int(11) unsigned NOT NULL DEFAULT 0 COMMENT 'Кол-во подписчиков',
  `time_zone`         varchar(32)               DEFAULT NULL COMMENT 'Часовой пояс',
  `karma`             int(11)          NOT NULL DEFAULT 0 COMMENT 'Репутация',
  `rating`            int(11)          NOT NULL DEFAULT 0 COMMENT 'Рейтинг',
  `theme`             text                      DEFAULT NULL COMMENT 'Настройки темы профиля',
  `notify_options`    text                      DEFAULT NULL COMMENT 'Настройки уведомлений',
  `privacy_options`   text                      DEFAULT NULL COMMENT 'Настройки приватности',
  `status_id`         int(11) unsigned          DEFAULT NULL COMMENT 'Текстовый статус',
  `status_text`       varchar(140)              DEFAULT NULL COMMENT 'Текст статуса',
  `inviter_id`        int(11) unsigned          DEFAULT NULL,
  `invites_count`     int(11) unsigned NOT NULL DEFAULT 0,
  `date_invites`      timestamp        NULL     DEFAULT NULL,
  `birth_date`        datetime                  DEFAULT NULL,
  `city`              int(11) unsigned          DEFAULT NULL,
  `city_cache`        varchar(128)              DEFAULT NULL,
  `hobby`             text                      DEFAULT NULL,
  `avatar`            text                      DEFAULT NULL,
  `icq`               varchar(255)              DEFAULT NULL,
  `skype`             varchar(255)              DEFAULT NULL,
  `phone`             varchar(255)              DEFAULT NULL,
  `music`             varchar(255)              DEFAULT NULL,
  `movies`            varchar(255)              DEFAULT NULL,
  `site`              text                      DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `pass_token` (`pass_token`),
  KEY `birth_date` (`birth_date`),
  KEY `city` (`city`),
  KEY `is_admin` (`is_admin`),
  KEY `friends_count` (`friends_count`),
  KEY `karma` (`karma`),
  KEY `rating` (`rating`),
  KEY `is_locked` (`is_locked`),
  KEY `date_reg` (`date_reg`),
  KEY `date_log` (`date_log`),
  KEY `date_group` (`date_group`),
  KEY `inviter_id` (`inviter_id`),
  KEY `date_invites` (`date_invites`),
  KEY `ip` (`ip`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 2
  DEFAULT CHARSET = utf8
  ROW_FORMAT = DYNAMIC COMMENT ='Пользователи';
CREATE TABLE `{#}users_auth_tokens`
(
  `id`          int(11) unsigned NOT NULL AUTO_INCREMENT,
  `auth_token`  varchar(32)           DEFAULT NULL,
  `date_auth`   timestamp        NULL DEFAULT current_timestamp(),
  `date_log`    timestamp        NULL DEFAULT NULL,
  `user_id`     int(11) unsigned      DEFAULT NULL,
  `access_type` varchar(100)          DEFAULT NULL,
  `ip`          int(10) unsigned      DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `auth_token` (`auth_token`),
  KEY `user_id` (`user_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8 COMMENT ='Токены авторизации';
CREATE TABLE `{#}users_contacts`
(
  `id`            int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id`       int(11) unsigned          DEFAULT NULL COMMENT 'ID пользователя',
  `contact_id`    int(11) unsigned          DEFAULT NULL COMMENT 'ID контакта (другого пользователя)',
  `date_last_msg` timestamp        NULL     DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Дата последнего сообщения',
  `messages`      int(11) unsigned NOT NULL DEFAULT 0 COMMENT 'Кол-во сообщений',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`, `contact_id`),
  KEY `contact_id` (`contact_id`, `user_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8 COMMENT ='Контакты пользователей';
CREATE TABLE `{#}users_fields`
(
  `id`            int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ctype_id`      int(11) unsigned    DEFAULT NULL,
  `name`          varchar(20)         DEFAULT NULL,
  `title`         varchar(100)        DEFAULT NULL,
  `hint`          varchar(200)        DEFAULT NULL,
  `ordering`      int(11) unsigned    DEFAULT NULL,
  `fieldset`      varchar(32)         DEFAULT NULL,
  `type`          varchar(16)         DEFAULT NULL,
  `is_in_list`    tinyint(1) unsigned DEFAULT NULL,
  `is_in_item`    tinyint(1) unsigned DEFAULT NULL,
  `is_in_filter`  tinyint(1) unsigned DEFAULT NULL,
  `is_private`    tinyint(1) unsigned DEFAULT NULL,
  `is_fixed`      tinyint(1) unsigned DEFAULT NULL,
  `is_fixed_type` tinyint(1) unsigned DEFAULT NULL,
  `is_system`     tinyint(1) unsigned DEFAULT NULL,
  `values`        text                DEFAULT NULL,
  `options`       text                DEFAULT NULL,
  `groups_read`   text                DEFAULT NULL,
  `groups_edit`   text                DEFAULT NULL,
  `filter_view`   text                DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ordering` (`ordering`),
  KEY `is_in_list` (`is_in_list`),
  KEY `is_in_item` (`is_in_item`),
  KEY `is_in_filter` (`is_in_filter`),
  KEY `is_private` (`is_private`),
  KEY `is_fixed` (`is_fixed`),
  KEY `is_system` (`is_system`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 13
  DEFAULT CHARSET = utf8 COMMENT ='Поля профилей пользователей';
CREATE TABLE `{#}users_groups`
(
  `id`        int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name`      varchar(32)      NOT NULL COMMENT 'Системное имя',
  `title`     varchar(32)      NOT NULL COMMENT 'Название группы',
  `is_fixed`  tinyint(1) unsigned DEFAULT NULL COMMENT 'Системная?',
  `is_public` tinyint(1) unsigned DEFAULT NULL COMMENT 'Группу можно выбрать при регистрации?',
  `is_filter` tinyint(1) unsigned DEFAULT NULL COMMENT 'Выводить группу в фильтре пользователей?',
  PRIMARY KEY (`id`),
  KEY `is_fixed` (`is_fixed`),
  KEY `is_public` (`is_public`),
  KEY `is_filter` (`is_filter`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 7
  DEFAULT CHARSET = utf8 COMMENT ='Группы пользователей';
CREATE TABLE `{#}users_groups_members`
(
  `user_id`  int(11) unsigned NOT NULL,
  `group_id` int(11) unsigned NOT NULL,
  KEY `user_id` (`user_id`),
  KEY `group_id` (`group_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8 COMMENT ='Привязка пользователей к группам';
CREATE TABLE `{#}users_groups_migration`
(
  `id`            int(11) unsigned NOT NULL AUTO_INCREMENT,
  `is_active`     tinyint(1) unsigned DEFAULT NULL,
  `title`         varchar(256)        DEFAULT NULL,
  `group_from_id` int(11) unsigned    DEFAULT NULL,
  `group_to_id`   int(11) unsigned    DEFAULT NULL,
  `is_keep_group` tinyint(1) unsigned DEFAULT NULL,
  `is_passed`     tinyint(1) unsigned DEFAULT NULL,
  `is_rating`     tinyint(1) unsigned DEFAULT NULL,
  `is_karma`      tinyint(1) unsigned DEFAULT NULL,
  `passed_days`   int(11) unsigned    DEFAULT NULL,
  `passed_from`   tinyint(1) unsigned DEFAULT NULL,
  `rating`        int(11)             DEFAULT NULL,
  `karma`         int(11)             DEFAULT NULL,
  `is_notify`     tinyint(1) unsigned DEFAULT NULL,
  `notify_text`   text                DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `group_from_id` (`group_from_id`),
  KEY `group_to_id` (`group_to_id`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 2
  DEFAULT CHARSET = utf8 COMMENT ='Правила перевода между группами';
CREATE TABLE `{#}users_ignors`
(
  `id`              int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id`         int(11) unsigned NOT NULL COMMENT 'ID пользователя',
  `ignored_user_id` int(11) unsigned NOT NULL COMMENT 'ID игнорируемого пользователя',
  PRIMARY KEY (`id`),
  KEY `ignored_user_id` (`ignored_user_id`, `user_id`),
  KEY `user_id` (`user_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8 COMMENT ='Списки игнорирования';
CREATE TABLE `{#}users_personal_settings`
(
  `id`       int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id`  int(11) unsigned NOT NULL,
  `skey`     varchar(150) DEFAULT NULL,
  `settings` text         DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`, `skey`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8 COMMENT ='Универсальные персональные настройки пользователей';
CREATE TABLE `{#}users_tabs`
(
  `id`              int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title`           varchar(32)         DEFAULT NULL,
  `controller`      varchar(32)         DEFAULT NULL,
  `name`            varchar(32)         DEFAULT NULL,
  `is_active`       tinyint(1) unsigned DEFAULT NULL,
  `ordering`        int(11) unsigned    DEFAULT NULL,
  `groups_view`     text                DEFAULT NULL,
  `groups_hide`     text                DEFAULT NULL,
  `show_only_owner` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `is_active` (`is_active`, `ordering`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 9
  DEFAULT CHARSET = utf8 COMMENT ='Табы профилей';
CREATE TABLE `{#}widgets`
(
  `id`          int(11) unsigned NOT NULL AUTO_INCREMENT,
  `controller`  varchar(32)      DEFAULT NULL COMMENT 'Контроллер',
  `name`        varchar(32)      NOT NULL COMMENT 'Системное имя',
  `title`       varchar(64)      DEFAULT NULL COMMENT 'Название',
  `author`      varchar(128)     DEFAULT NULL COMMENT 'Имя автора',
  `url`         varchar(250)     DEFAULT NULL COMMENT 'Сайт автора',
  `version`     varchar(8)       DEFAULT NULL COMMENT 'Версия',
  `is_external` tinyint(1)       DEFAULT 1,
  `files`       text             DEFAULT NULL COMMENT 'Список файлов виджета (для стороних виджетов)',
  `addon_id`    int(11) unsigned DEFAULT NULL COMMENT 'ID дополнения в официальном каталоге',
  PRIMARY KEY (`id`),
  KEY `version` (`version`),
  KEY `name` (`name`),
  KEY `controller` (`controller`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 20
  DEFAULT CHARSET = utf8 COMMENT ='Доступные виджеты CMS';
CREATE TABLE `{#}widgets_bind`
(
  `id`               int(11) unsigned NOT NULL AUTO_INCREMENT,
  `template`         varchar(30)         DEFAULT NULL COMMENT 'Привязка к шаблону',
  `template_layouts` varchar(500)        DEFAULT NULL,
  `languages`        varchar(100)        DEFAULT NULL,
  `widget_id`        int(11) unsigned NOT NULL,
  `title`            varchar(128)     NOT NULL COMMENT 'Заголовок',
  `links`            text                DEFAULT NULL,
  `class`            varchar(64)         DEFAULT NULL COMMENT 'CSS класс',
  `class_title`      varchar(64)         DEFAULT NULL,
  `class_wrap`       varchar(64)         DEFAULT NULL,
  `is_title`         tinyint(1) unsigned DEFAULT 1 COMMENT 'Показывать заголовок',
  `is_enabled`       tinyint(1) unsigned DEFAULT NULL COMMENT 'Включен?',
  `is_tab_prev`      tinyint(1) unsigned DEFAULT NULL COMMENT 'Объединять с предыдущим?',
  `groups_view`      text                DEFAULT NULL COMMENT 'Показывать группам',
  `groups_hide`      text                DEFAULT NULL COMMENT 'Не показывать группам',
  `options`          text                DEFAULT NULL COMMENT 'Опции',
  `page_id`          int(11) unsigned    DEFAULT NULL COMMENT 'ID страницы для вывода',
  `position`         varchar(32)         DEFAULT NULL COMMENT 'Имя позиции',
  `ordering`         int(11) unsigned    DEFAULT NULL COMMENT 'Порядковый номер',
  `tpl_body`         varchar(128)        DEFAULT NULL,
  `tpl_wrap`         varchar(128)        DEFAULT NULL,
  `device_types`     varchar(50)         DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `position` (`position`),
  KEY `widget_id` (`widget_id`),
  KEY `page_id` (`page_id`, `position`, `ordering`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 24
  DEFAULT CHARSET = utf8 COMMENT ='Виджеты сайта';
CREATE TABLE `{#}widgets_pages`
(
  `id`            int(11) unsigned NOT NULL AUTO_INCREMENT,
  `controller`    varchar(32) DEFAULT NULL COMMENT 'Компонент',
  `name`          varchar(64) DEFAULT NULL COMMENT 'Системное имя',
  `title_const`   varchar(64) DEFAULT NULL COMMENT 'Название страницы (языковая константа)',
  `title_subject` varchar(64) DEFAULT NULL COMMENT 'Название субъекта (передается в языковую константу)',
  `title`         varchar(64) DEFAULT NULL,
  `url_mask`      text        DEFAULT NULL COMMENT 'Маска URL',
  `url_mask_not`  text        DEFAULT NULL COMMENT 'Отрицательная маска',
  `groups`        text        DEFAULT NULL COMMENT 'Группы доступа',
  `countries`     text        DEFAULT NULL COMMENT 'Страны доступа',
  PRIMARY KEY (`id`),
  KEY `controller` (`controller`),
  KEY `name` (`name`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 201
  DEFAULT CHARSET = utf8;
BEGIN;
LOCK TABLES `{#}con_pages` WRITE;
DELETE
FROM `{#}con_pages`;
UNLOCK TABLES;
COMMIT;
BEGIN;
LOCK TABLES `{#}con_pages_cats` WRITE;
DELETE
FROM `{#}con_pages_cats`;
INSERT INTO `{#}con_pages_cats` (`id`, `parent_id`, `title`, `description`, `slug`, `slug_key`, `seo_keys`, `seo_desc`, `seo_title`, `ordering`, `ns_left`, `ns_right`, `ns_level`, `ns_differ`,
                                 `ns_ignore`, `allow_add`)
VALUES (1, 0, '---', NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 2, 0, '', 0, NULL);
UNLOCK TABLES;
COMMIT;
BEGIN;
LOCK TABLES `{#}con_pages_cats_bind` WRITE;
DELETE
FROM `{#}con_pages_cats_bind`;
UNLOCK TABLES;
COMMIT;
BEGIN;
LOCK TABLES `{#}con_pages_fields` WRITE;
DELETE
FROM `{#}con_pages_fields`;
INSERT INTO `{#}con_pages_fields` (`id`, `ctype_id`, `name`, `title`, `hint`, `ordering`, `fieldset`, `type`, `is_in_list`, `is_in_item`, `is_in_filter`, `is_private`, `is_fixed`, `is_fixed_type`,
                                   `is_system`, `values`, `options`, `groups_read`, `groups_edit`, `filter_view`)
VALUES (1, 1, 'title', 'Заголовок', NULL, 1, NULL, 'caption', 1, 1, 1, NULL, 1, 1, NULL, NULL, '---\nlabel_in_list: none\nlabel_in_item: none\nmin_length: 3\nmax_length: 100\nis_required: true\n',
        NULL, NULL, NULL),
       (2, 1, 'date_pub', 'Дата публикации', NULL, 2, NULL, 'date', NULL, NULL, NULL, NULL, 1, NULL, 1, NULL,
        '---\nlabel_in_list: none\nlabel_in_item: left\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\n', '---\n- 0\n', '---\n- 0\n', NULL),
       (3, 1, 'user', 'Автор', NULL, 3, NULL, 'user', NULL, NULL, NULL, NULL, 1, NULL, 1, NULL,
        '---\nlabel_in_list: none\nlabel_in_item: left\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\n', '---\n- 0\n', '---\n- 0\n', NULL),
       (4, 1, 'content', 'Текст страницы', NULL, 4, NULL, 'html', NULL, 1, NULL, NULL, 1, NULL, NULL, NULL,
        '---\neditor: redactor\nis_html_filter: null\nlabel_in_list: none\nlabel_in_item: none\nis_required: 1\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\n',
        '---\n- 0\n', '---\n- 0\n', NULL),
       (5, 1, 'attach', 'Скачать', 'Приложите файл к странице', 5, NULL, 'file', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL,
        '---\nshow_name: 0\nextensions: jpg, gif, png\nmax_size_mb: 2\nshow_size: 1\nlabel_in_list: none\nlabel_in_item: none\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\n',
        '---\n- 0\n', '---\n- 0\n', NULL);
UNLOCK TABLES;
COMMIT;
BEGIN;
LOCK TABLES `{#}con_pages_props` WRITE;
DELETE
FROM `{#}con_pages_props`;
UNLOCK TABLES;
COMMIT;
BEGIN;
LOCK TABLES `{#}con_pages_props_bind` WRITE;
DELETE
FROM `{#}con_pages_props_bind`;
UNLOCK TABLES;
COMMIT;
BEGIN;
LOCK TABLES `{#}con_pages_props_values` WRITE;
DELETE
FROM `{#}con_pages_props_values`;
UNLOCK TABLES;
COMMIT;
BEGIN;
LOCK TABLES `{#}content_datasets` WRITE;
DELETE
FROM `{#}content_datasets`;
UNLOCK TABLES;
COMMIT;
BEGIN;
LOCK TABLES `{#}content_folders` WRITE;
DELETE
FROM `{#}content_folders`;
UNLOCK TABLES;
COMMIT;
BEGIN;
LOCK TABLES `{#}content_relations` WRITE;
DELETE
FROM `{#}content_relations`;
UNLOCK TABLES;
COMMIT;
BEGIN;
LOCK TABLES `{#}content_relations_bind` WRITE;
DELETE
FROM `{#}content_relations_bind`;
UNLOCK TABLES;
COMMIT;
BEGIN;
LOCK TABLES `{#}content_types` WRITE;
DELETE
FROM `{#}content_types`;
INSERT INTO `{#}content_types` (`id`, `title`, `name`, `description`, `ordering`, `is_date_range`, `is_premod_add`, `is_premod_edit`, `is_cats`, `is_cats_recursive`, `is_folders`, `is_in_groups`,
                                `is_in_groups_only`, `is_comments`, `is_comments_tree`, `is_rating`, `is_rating_pos`, `is_tags`, `is_auto_keys`, `is_auto_desc`, `is_auto_url`, `is_fixed_url`,
                                `url_pattern`, `options`, `labels`, `seo_keys`, `seo_desc`, `seo_title`, `item_append_html`, `is_fixed`)
VALUES (1, 'Страницы', 'pages', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, '{id}-{title}',
        '---\nis_cats_change: null\nis_cats_open_root: null\nis_cats_only_last: null\nis_tags_in_list: null\nis_tags_in_item: null\nis_rss: null\nlist_on: null\nprofile_on: null\nlist_show_filter: null\nlist_expand_filter: null\nitem_on: 1\n',
        '---\none: страница\ntwo: страницы\nmany: страниц\ncreate: страницу\n', NULL, NULL, NULL, NULL, 1);
UNLOCK TABLES;
COMMIT;
BEGIN;
LOCK TABLES `{#}controllers` WRITE;
DELETE
FROM `{#}controllers`;
INSERT INTO `{#}controllers` (`id`, `title`, `name`, `slug`, `is_enabled`, `options`, `author`, `url`, `version`, `is_backend`, `is_external`, `files`, `addon_id`)
VALUES (1, 'Панель управления', 'admin', NULL, 1, NULL, 'InstantCMS Team', 'https://instantcms.ru', '2.0', 0, NULL, NULL, NULL),
       (2, 'Контент', 'content', NULL, 1, NULL, 'InstantCMS Team', 'https://instantcms.ru', '2.0', 0, NULL, NULL, NULL),
       (3, 'Профили пользователей', 'users', NULL, 1,
        '---\nis_ds_online: 1\nis_ds_rating: 1\nis_ds_popular: 1\nis_filter: 1\nis_auth_only: null\nis_status: 1\nis_wall: 1\nis_themes_on: 1\nmax_tabs: 6\nis_friends_on: 1\nis_karma_comments: 1\nkarma_time: 30\n',
        'InstantCMS Team', 'https://instantcms.ru', '2.0', 1, NULL, NULL, NULL),
       (6, 'Авторизация и регистрация', 'auth', NULL, 1,
        '---\nis_reg_enabled: 1\nreg_reason: >\n  К сожалению, нам пока\n  не нужны новые\n  пользователи\nis_reg_invites: null\nreg_captcha: 1\nverify_email: null\nverify_exp: 48\nauth_captcha: 0\nrestricted_emails: |\n  *@shitmail.me\r\n  *@mailspeed.ru\r\n  *@temp-mail.ru\r\n  *@guerrillamail.com\r\n  *@12minutemail.com\r\n  *@mytempemail.com\r\n  *@spamobox.com\r\n  *@disposableinbox.com\r\n  *@filzmail.com\r\n  *@freemail.ms\r\n  *@anonymbox.com\r\n  *@lroid.com\r\n  *@yopmail.com\r\n  *@TempEmail.net\r\n  *@spambog.com\r\n  *@mailforspam.com\r\n  *@spam.su\r\n  *@no-spam.ws\r\n  *@mailinator.com\r\n  *@spamavert.com\r\n  *@trashcanmail.com\nrestricted_names: |\n  admin*\r\n  админ*\r\n  модератор\r\n  moderator\nrestricted_ips:\nis_invites: 1\nis_invites_strict: 1\ninvites_period: 7\ninvites_qty: 3\ninvites_min_karma: 0\ninvites_min_rating: 0\ninvites_min_days: 0\nreg_auto_auth: 1\nfirst_auth_redirect: profileedit\nauth_redirect: none\n',
        'InstantCMS Team', 'https://instantcms.ru', '2.0', 1, NULL, NULL, NULL),
       (12, 'Капча reCAPTCHA', 'recaptcha', NULL, 1, '---\npublic_key:\nprivate_key:\ntheme: light\nlang: ru\nsize: normal\n', 'InstantCMS Team', 'https://instantcms.ru', '2.0', 1, NULL, NULL, NULL),
       (17, 'Поиск', 'search', NULL, 1, '---\nctypes:\n  - articles\n  - posts\n  - albums\n  - board\n  - news\nperpage: 15\n', 'InstantCMS Team', 'https://instantcms.ru', '2.0', 1, NULL, NULL,
        NULL),
       (19, 'Загрузка изображений', 'images', NULL, 1, NULL, 'InstantCMS Team', 'https://instantcms.ru', '2.0', 1, NULL, NULL, NULL),
       (20, 'Редиректы', 'redirect', NULL, 1, '---\nno_redirect_list:\nblack_list:\nis_check_link: null\nwhite_list:\nredirect_time: 10\nis_check_refer: null\n', 'InstantCMS Team',
        'https://instantcms.ru', '2.0', 1, NULL, NULL, NULL),
       (21, 'География', 'geo', NULL, 1,
        '---\nauto_detect: 1\nauto_detect_provider: ipgeobase\ndefault_country_id: null\ndefault_country_id_cache: null\ndefault_region_id: null\ndefault_region_id_cache: null\n', 'InstantCMS Team',
        'https://instantcms.ru', '2.0', 1, NULL, NULL, NULL);
UNLOCK TABLES;
COMMIT;
BEGIN;
LOCK TABLES `{#}events` WRITE;
DELETE
FROM `{#}events`;
INSERT INTO `{#}events` (`id`, `event`, `listener`, `ordering`, `is_enabled`)
VALUES (7, 'menu_admin', 'admin', 7, 1),
       (8, 'user_login', 'admin', 8, 1),
       (9, 'admin_confirm_login', 'admin', 9, 1),
       (10, 'user_profile_update', 'auth', 10, 1),
       (11, 'frontpage', 'auth', 11, 1),
       (12, 'page_is_allowed', 'auth', 12, 1),
       (13, 'frontpage_types', 'auth', 13, 1),
       (23, 'fulltext_search', 'content', 23, 1),
       (24, 'admin_dashboard_chart', 'content', 24, 1),
       (25, 'menu_content', 'content', 25, 1),
       (26, 'user_delete', 'content', 26, 1),
       (27, 'user_privacy_types', 'content', 27, 1),
       (32, 'frontpage', 'content', 32, 1),
       (33, 'frontpage_types', 'content', 33, 1),
       (34, 'ctype_relation_childs', 'content', 34, 1),
       (35, 'admin_content_dataset_fields_list', 'content', 35, 1),
       (37, 'ctype_lists_context', 'content', 37, 1),
       (38, 'ctype_after_update', 'frontpage', 38, 1),
       (39, 'ctype_after_delete', 'frontpage', 39, 1),
       (62, 'user_delete', 'images', 62, 1),
       (85, 'captcha_html', 'recaptcha', 85, 1),
       (86, 'captcha_validate', 'recaptcha', 86, 1),
       (96, 'content_before_list', 'search', 96, 1),
       (97, 'content_before_item', 'search', 97, 1),
       (98, 'before_print_head', 'search', 98, 1),
       (99, 'html_filter', 'typograph', 99, 1),
       (100, 'admin_dashboard_chart', 'users', 100, 1),
       (101, 'menu_users', 'users', 101, 1),
       (104, 'user_privacy_types', 'users', 104, 1),
       (105, 'user_tab_info', 'users', 105, 1),
       (106, 'auth_login', 'users', 106, 1),
       (107, 'user_loaded', 'users', 107, 1),
       (111, 'content_privacy_types', 'users', 111, 1),
       (112, 'content_view_hidden', 'users', 112, 1),
       (114, 'content_before_childs', 'users', 114, 1),
       (115, 'ctype_relation_childs', 'users', 115, 1),
       (119, 'page_is_allowed', 'widgets', 119, 1),
       (140, 'admin_dashboard_block', 'users', 140, 1),
       (151, 'images_before_upload', 'typograph', 151, 1),
       (152, 'engine_start', 'content', 152, 1);
UNLOCK TABLES;
COMMIT;
BEGIN;
LOCK TABLES `{#}images_presets` WRITE;
DELETE
FROM `{#}images_presets`;
INSERT INTO `{#}images_presets` (`id`, `name`, `title`, `width`, `height`, `is_square`, `is_watermark`, `wm_image`, `wm_origin`, `wm_margin`, `is_internal`, `quality`)
VALUES (1, 'micro', 'Микро', 32, 32, 1, NULL, NULL, NULL, NULL, NULL, 75),
       (2, 'small', 'Маленький', 64, 64, 1, NULL, NULL, NULL, NULL, NULL, 80),
       (3, 'normal', 'Средний', NULL, 256, NULL, NULL, NULL, NULL, NULL, NULL, 85),
       (4, 'big', 'Большой', 690, 690, NULL, NULL, NULL, 'bottom-right', NULL, NULL, 90),
       (5, 'wysiwyg_markitup', 'Редактор: markItUp!', 400, 400, NULL, NULL, NULL, 'top-left', NULL, 1, 85),
       (6, 'wysiwyg_redactor', 'Редактор: Redactor', 800, 800, NULL, NULL, NULL, 'top-left', NULL, 1, 90),
       (7, 'wysiwyg_live', 'Редактор: Live', 690, 690, NULL, NULL, NULL, 'top-left', NULL, 1, 90);
UNLOCK TABLES;
COMMIT;
BEGIN;
LOCK TABLES `{#}jobs` WRITE;
DELETE
FROM `{#}jobs`;
UNLOCK TABLES;
COMMIT;
BEGIN;
LOCK TABLES `{#}menu` WRITE;
DELETE
FROM `{#}menu`;
INSERT INTO `{#}menu` (`id`, `name`, `title`, `is_fixed`)
VALUES (1, 'main', 'Главное меню', 1),
       (2, 'personal', 'Персональное меню', 1),
       (4, 'toolbar', 'Меню действий', 1),
       (5, 'header', 'Верхнее меню', NULL);
UNLOCK TABLES;
COMMIT;
BEGIN;
LOCK TABLES `{#}menu_items` WRITE;
DELETE
FROM `{#}menu_items`;
INSERT INTO `{#}menu_items` (`id`, `menu_id`, `parent_id`, `is_enabled`, `title`, `url`, `ordering`, `options`, `groups_view`, `groups_hide`)
VALUES (13, 2, 0, 1, 'Мой профиль', 'users/{user.id}', 1, '---\ntarget: _self\nclass: profile', '---\n- 0\n', NULL),
       (24, 2, 0, 1, 'Создать', '{content:add}', 6, '---\nclass: add\n', NULL, NULL),
       (25, 2, 0, 1, 'Панель управления', '{admin:menu}', 7, '---\nclass: cpanel\n', '---\n- 6\n', NULL),
       (29, 1, 0, 1, 'Люди', 'users', 9, '---\nclass: \n', '---\n- 0\n', NULL),
       (34, 5, 0, 1, 'Войти', 'auth/login', 9, '---\nclass: ajax-modal key', '---\n- 1\n', NULL),
       (35, 5, 0, 1, 'Регистрация', 'auth/register', 10, '---\nclass: user_add', '---\n- 1\n', NULL),
       (43, 2, 0, 1, 'Выйти', 'auth/logout', 12, '---\ntarget: _self\nclass: logout', '---\n- 0\n', NULL);
UNLOCK TABLES;
COMMIT;
BEGIN;
LOCK TABLES `{#}perms_rules` WRITE;
DELETE
FROM `{#}perms_rules`;
INSERT INTO `{#}perms_rules` (`id`, `controller`, `name`, `type`, `options`, `show_for_guest_group`)
VALUES (1, 'content', 'add', 'list', 'premod,yes', NULL),
       (2, 'content', 'edit', 'list', 'premod_own,own,premod_all,all', NULL),
       (3, 'content', 'delete', 'list', 'own,all', NULL),
       (4, 'content', 'add_cat', 'flag', NULL, NULL),
       (5, 'content', 'edit_cat', 'flag', NULL, NULL),
       (6, 'content', 'delete_cat', 'flag', NULL, NULL),
       (9, 'content', 'privacy', 'flag', NULL, NULL),
       (13, 'content', 'view_all', 'flag', NULL, NULL),
       (18, 'content', 'limit', 'number', NULL, NULL),
       (24, 'content', 'pub_late', 'flag', NULL, NULL),
       (25, 'content', 'pub_long', 'list', 'days,any', NULL),
       (26, 'content', 'pub_max_days', 'number', NULL, NULL),
       (27, 'content', 'pub_max_ext', 'flag', NULL, NULL),
       (28, 'content', 'pub_on', 'flag', NULL, NULL),
       (32, 'content', 'add_to_parent', 'list', 'to_own,to_other,to_all', NULL),
       (33, 'content', 'bind_to_parent', 'list', 'own_to_own,own_to_other,own_to_all,other_to_own,other_to_other,other_to_all,all_to_own,all_to_other,all_to_all', NULL),
       (34, 'content', 'bind_off_parent', 'list', 'own,all', NULL),
       (35, 'content', 'move_to_trash', 'list', 'own,all', NULL),
       (36, 'content', 'restore', 'list', 'own,all', NULL),
       (37, 'content', 'trash_left_time', 'number', NULL, NULL),
       (38, 'users', 'delete', 'list', 'my,anyuser', NULL),
       (41, 'users', 'bind_to_parent', 'list', 'own_to_own,own_to_other,own_to_all,other_to_own,other_to_other,other_to_all,all_to_own,all_to_other,all_to_all', NULL),
       (43, 'users', 'bind_off_parent', 'list', 'own,all', NULL),
       (45, 'auth', 'view_closed', 'flag', NULL, NULL),
       (46, 'content', 'view_list', 'list', 'other,all', NULL);
UNLOCK TABLES;
COMMIT;
BEGIN;
LOCK TABLES `{#}perms_users` WRITE;
DELETE
FROM `{#}perms_users`;
INSERT INTO `{#}perms_users` (`rule_id`, `group_id`, `subject`, `value`)
VALUES (19, 4, 'users', '1'),
       (19, 5, 'users', '1');
UNLOCK TABLES;
COMMIT;
BEGIN;
LOCK TABLES `{#}scheduler_tasks` WRITE;
DELETE
FROM `{#}scheduler_tasks`;
INSERT INTO `{#}scheduler_tasks` (`id`, `title`, `controller`, `hook`, `period`, `is_strict_period`, `date_last_run`, `is_active`, `is_new`, `consistent_run`)
VALUES (1, 'Перевод пользователей между группами', 'users', 'migration', 1440, NULL, NULL, 1, 0, NULL),
       (4, 'Публикация контента по расписанию', 'content', 'publication', 1440, NULL, NULL, 1, 1, NULL),
       (6, 'Удаление пользователей, не прошедших верификацию', 'auth', 'delete_expired_unverified', 60, NULL, NULL, 1, 1, NULL),
       (7, 'Удаление просроченных записей из корзины', 'moderation', 'trash', 30, NULL, NULL, 1, 1, NULL),
       (8, 'Выполняет задачи системной очереди', 'queue', 'run_queue', 1, NULL, NULL, 1, 1, NULL);
UNLOCK TABLES;
COMMIT;
BEGIN;
LOCK TABLES `{#}sessions_online` WRITE;
DELETE
FROM `{#}sessions_online`;
UNLOCK TABLES;
COMMIT;
BEGIN;
LOCK TABLES `{#}uploaded_files` WRITE;
DELETE
FROM `{#}uploaded_files`;
UNLOCK TABLES;
COMMIT;
BEGIN;
LOCK TABLES `{#}users` WRITE;
DELETE
FROM `{#}users`;
INSERT INTO `{#}users` (`id`, `groups`, `email`, `password`, `password_salt`, `is_admin`, `nickname`, `date_reg`, `date_log`, `date_group`, `ip`, `is_deleted`, `is_locked`, `lock_until`,
                        `lock_reason`, `pass_token`, `date_token`, `friends_count`, `subscribers_count`, `time_zone`, `karma`, `rating`, `theme`, `notify_options`, `privacy_options`, `status_id`,
                        `status_text`, `inviter_id`, `invites_count`, `date_invites`, `birth_date`, `city`, `city_cache`, `hobby`, `avatar`, `icq`, `skype`, `phone`, `music`, `movies`, `site`)
VALUES (1, '---\n- 6\n', 'admin@example.com', '', '', 1, 'admin', '2018-12-13 10:01:53', '2018-12-13 10:01:53', '2018-12-13 10:01:53', '127.0.0.1', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0,
        'Europe/Moscow', 0, 0, '---\nbg_img: null\nbg_color: \'#ffffff\'\nbg_repeat: no-repeat\nbg_pos_x: left\nbg_pos_y: top\nmargin_top: 0\n',
        '---\nusers_friend_add: both\nusers_friend_delete: both\ncomments_new: both\ncomments_reply: email\nusers_friend_accept: pm\ngroups_invite: email\nusers_wall_write: email\n',
        '---\nusers_profile_view: anyone\nmessages_pm: anyone\n', NULL, NULL, NULL, 0, NULL, '1985-10-15 00:00:00', 4400, 'Москва',
        'Ротор векторного поля, очевидно, неоднозначен. По сути, уравнение в частных производных масштабирует нормальный лист Мёбиуса, при этом, вместо 13 можно взять любую другую константу.', NULL,
        '987654321', 'admin', '100-20-30', 'Disco House, Minimal techno', 'разные интересные', 'instantcms.ru');
UNLOCK TABLES;
COMMIT;
BEGIN;
LOCK TABLES `{#}users_auth_tokens` WRITE;
DELETE
FROM `{#}users_auth_tokens`;
UNLOCK TABLES;
COMMIT;
BEGIN;
LOCK TABLES `{#}users_contacts` WRITE;
DELETE
FROM `{#}users_contacts`;
UNLOCK TABLES;
COMMIT;
BEGIN;
LOCK TABLES `{#}users_fields` WRITE;
DELETE
FROM `{#}users_fields`;
INSERT INTO `{#}users_fields` (`id`, `ctype_id`, `name`, `title`, `hint`, `ordering`, `fieldset`, `type`, `is_in_list`, `is_in_item`, `is_in_filter`, `is_private`, `is_fixed`, `is_fixed_type`,
                               `is_system`, `values`, `options`, `groups_read`, `groups_edit`, `filter_view`)
VALUES (1, NULL, 'birth_date', 'Возраст', NULL, 4, 'Анкета', 'age', NULL, 1, 1, NULL, NULL, NULL, NULL, NULL,
        '---\ndate_title: Дата рождения\nshow_y: 1\nshow_m: \nshow_d: \nshow_h: \nshow_i: \nrange: YEAR\nlabel_in_item: left\nis_required: \nis_digits: \nis_alphanumeric: \nis_email: \nis_unique: \n',
        '---\n- 0\n', '---\n- 0\n', NULL),
       (2, NULL, 'city', 'Город', 'Укажите город, в котором вы живете', 3, 'Анкета', 'city', NULL, 1, 1, NULL, NULL, NULL, NULL, NULL,
        '---\nlabel_in_item: left\nis_required: 1\nis_digits: null\nis_alphanumeric: null\nis_email: null\n', '---\n- 0\n', '---\n- 0\n', NULL),
       (3, NULL, 'hobby', 'Расскажите о себе', 'Расскажите о ваших интересах и увлечениях', 11, 'О себе', 'text', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL,
        '---\nmin_length: 0\nmax_length: 255\nlabel_in_item: none\nis_required: \nis_digits: \nis_alphanumeric: \nis_email: \nis_unique: \n', '---\n- 0\n', '---\n- 0\n', NULL),
       (5, NULL, 'nickname', 'Никнейм', 'Ваше имя для отображения на сайте', 1, 'Анкета', 'string', 1, 1, 1, NULL, 1, NULL, 1, NULL,
        '---\r\nlabel_in_list: left\r\nlabel_in_item: left\r\nis_required: 1\r\nis_digits: \r\nis_number: \r\nis_alphanumeric: \r\nis_email: \r\nis_unique: \r\nshow_symbol_count: 1\r\nmin_length: 2\r\nmax_length: 100\r\n',
        '---\n- 0\n', '---\n- 0\n', NULL),
       (6, NULL, 'avatar', 'Аватар', 'Ваша основная фотография', 2, 'Анкета', 'image', 1, 1, NULL, NULL, 1, NULL, 1, NULL,
        '---\nsize_teaser: micro\nsize_full: normal\nsizes:\n  - micro\n  - small\n  - normal\nlabel_in_item: left\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\n',
        '---\n- 0\n', '---\n- 0\n', NULL),
       (7, NULL, 'icq', 'ICQ', NULL, 8, 'Контакты', 'string', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL,
        '---\nmin_length: 0\nmax_length: 9\nlabel_in_item: left\nis_required: \nis_digits: 1\nis_alphanumeric: \nis_email: \nis_unique: \n', '---\n- 0\n', '---\n- 0\n', NULL),
       (8, NULL, 'skype', 'Skype', NULL, 9, 'Контакты', 'string', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL,
        '---\nmin_length: 0\nmax_length: 32\nlabel_in_item: left\nis_required: \nis_digits: \nis_alphanumeric: \nis_email: \nis_unique: \n', '---\n- 0\n', '---\n- 0\n', NULL),
       (9, NULL, 'phone', 'Телефон', NULL, 7, 'Контакты', 'string', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL,
        '---\nmin_length: 0\nmax_length: 255\nlabel_in_item: left\nis_required: \nis_digits: \nis_alphanumeric: \nis_email: \nis_unique: \n', '---\n- 0\n', '---\n- 0\n', NULL),
       (10, NULL, 'music', 'Любимая музыка', NULL, 6, 'Предпочтения', 'string', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL,
        '---\nmin_length: 0\nmax_length: 255\nlabel_in_item: left\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\n', '---\n- 0\n', '---\n- 0\n', NULL),
       (11, NULL, 'movies', 'Любимые фильмы', NULL, 5, 'Предпочтения', 'string', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL,
        '---\nmin_length: 0\nmax_length: 255\nlabel_in_item: left\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\n', '---\n- 0\n', '---\n- 0\n', NULL),
       (12, NULL, 'site', 'Сайт', 'Ваш персональный веб-сайт', 10, 'Контакты', 'url', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL,
        '---\nredirect: 1\nauto_http: 1\nlabel_in_item: left\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\n', '---\n- 0\n', '---\n- 0\n', NULL);
UNLOCK TABLES;
COMMIT;
BEGIN;
LOCK TABLES `{#}users_groups` WRITE;
DELETE
FROM `{#}users_groups`;
INSERT INTO `{#}users_groups` (`id`, `name`, `title`, `is_fixed`, `is_public`, `is_filter`)
VALUES (1, 'guests', 'Гости', 1, NULL, NULL),
       (3, 'newbies', 'Новые', NULL, NULL, NULL),
       (4, 'members', 'Пользователи', NULL, NULL, NULL),
       (5, 'moderators', 'Модераторы', NULL, NULL, NULL),
       (6, 'admins', 'Администраторы', NULL, NULL, 1);
UNLOCK TABLES;
COMMIT;
BEGIN;
LOCK TABLES `{#}users_groups_members` WRITE;
DELETE
FROM `{#}users_groups_members`;
INSERT INTO `{#}users_groups_members` (`user_id`, `group_id`)
VALUES (1, 6);
UNLOCK TABLES;
COMMIT;
BEGIN;
LOCK TABLES `{#}users_groups_migration` WRITE;
DELETE
FROM `{#}users_groups_migration`;
INSERT INTO `{#}users_groups_migration` (`id`, `is_active`, `title`, `group_from_id`, `group_to_id`, `is_keep_group`, `is_passed`, `is_rating`, `is_karma`, `passed_days`, `passed_from`, `rating`,
                                         `karma`, `is_notify`, `notify_text`)
VALUES (1, 1, 'Проверка временем', 3, 4, 0, 1, NULL, NULL, 3, 0, NULL, NULL, 1, 'С момента вашей регистрации прошло 3 дня.\r\nТеперь вам доступны все функции сайта.');
UNLOCK TABLES;
COMMIT;
BEGIN;
LOCK TABLES `{#}users_ignors` WRITE;
DELETE
FROM `{#}users_ignors`;
UNLOCK TABLES;
COMMIT;
BEGIN;
LOCK TABLES `{#}users_personal_settings` WRITE;
DELETE
FROM `{#}users_personal_settings`;
UNLOCK TABLES;
COMMIT;
BEGIN;
LOCK TABLES `{#}users_tabs` WRITE;
DELETE
FROM `{#}users_tabs`;
UNLOCK TABLES;
COMMIT;
BEGIN;
LOCK TABLES `{#}widgets` WRITE;
DELETE
FROM `{#}widgets`;
INSERT INTO `{#}widgets` (`id`, `controller`, `name`, `title`, `author`, `url`, `version`, `is_external`, `files`, `addon_id`)
VALUES (1, NULL, 'text', 'Текстовый блок', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL, NULL, NULL),
       (2, 'users', 'list', 'Список пользователей', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL, NULL, NULL),
       (3, NULL, 'menu', 'Меню', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL, NULL, NULL),
       (4, 'content', 'list', 'Список контента', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL, NULL, NULL),
       (5, 'content', 'categories', 'Категории', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL, NULL, NULL),
       (8, 'users', 'online', 'Кто онлайн', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL, NULL, NULL),
       (9, 'users', 'avatar', 'Аватар пользователя', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL, NULL, NULL),
       (11, 'content', 'slider', 'Слайдер контента', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL, NULL, NULL),
       (12, 'auth', 'auth', 'Форма авторизации', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL, NULL, NULL),
       (13, 'search', 'search', 'Поиск', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL, NULL, NULL),
       (14, NULL, 'html', 'HTML блок', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL, NULL, NULL),
       (15, 'content', 'filter', 'Фильтр контента', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL, NULL, NULL),
       (19, 'auth', 'register', 'Форма регистрации', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL, NULL, NULL);
UNLOCK TABLES;
COMMIT;
BEGIN;
LOCK TABLES `{#}widgets_bind` WRITE;
DELETE
FROM `{#}widgets_bind`;
INSERT INTO `{#}widgets_bind` (`id`, `template`, `template_layouts`, `languages`, `widget_id`, `title`, `links`, `class`, `class_title`, `class_wrap`, `is_title`, `is_enabled`, `is_tab_prev`,
                               `groups_view`, `groups_hide`, `options`, `page_id`, `position`, `ordering`, `tpl_body`, `tpl_wrap`, `device_types`)
VALUES (1, 'default', NULL, NULL, 3, 'Главное меню', NULL, NULL, NULL, NULL, NULL, 1, NULL, '---\n- 0\n', NULL, '---\nmenu: main\nis_detect: 1\nmax_items: 8\n', 0, 'top', 1, NULL, NULL, NULL),
       (2, 'default', NULL, NULL, 3, 'Меню авторизации', NULL, NULL, NULL, NULL, NULL, 1, NULL, '---\n- 1\n', NULL, '---\nmenu: header\nis_detect: 1\nmax_items: 0\n', 0, 'header', 1, NULL, NULL,
        NULL),
       (5, 'default', NULL, NULL, 3, 'Меню действий', NULL, NULL, NULL, 'fixed_actions_menu', NULL, 1, NULL, '---\n- 0\n', NULL, '---\nmenu: toolbar\ntemplate: menu\nis_detect: null\nmax_items: 0\n',
        0, 'left-top', 1, 'menu', 'wrapper', NULL),
       (20, 'default', NULL, NULL, 12, 'Войти на сайт', NULL, NULL, NULL, NULL, 1, 1, NULL, '---\n- 0\n', NULL, '', 0, 'right-center', 1, NULL, NULL, NULL),
       (22, 'default', NULL, NULL, 9, 'Меню пользователя', NULL, NULL, NULL, NULL, NULL, 1, NULL, '---\n- 0\n', '---\n- 1\n', '---\nmenu: personal\nis_detect: 1\nmax_items: 0\n', 0, 'header', 3,
        'avatar', 'wrapper', NULL);
UNLOCK TABLES;
COMMIT;
BEGIN;
LOCK TABLES `{#}widgets_pages` WRITE;
DELETE
FROM `{#}widgets_pages`;
INSERT INTO `{#}widgets_pages` (`id`, `controller`, `name`, `title_const`, `title_subject`, `title`, `url_mask`, `url_mask_not`, `groups`, `countries`)
VALUES (0, NULL, 'all', 'LANG_WP_ALL_PAGES', NULL, NULL, NULL, NULL, NULL, NULL),
       (100, 'users', 'list', 'LANG_USERS_LIST', NULL, NULL, 'users\r\nusers/index\r\nusers/index/*', NULL, NULL, NULL),
       (101, 'users', 'profile', 'LANG_USERS_PROFILE', NULL, NULL, 'users/%*', 'users/%/edit', NULL, NULL),
       (102, 'users', 'edit', 'LANG_USERS_EDIT_PROFILE', NULL, NULL, 'users/%/edit', NULL, NULL, NULL);
UNLOCK TABLES;
COMMIT;
