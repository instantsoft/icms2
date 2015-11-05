DROP TABLE IF EXISTS `{#}users_personal_settings`;
CREATE TABLE `{#}users_personal_settings` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `skey` varchar(150) DEFAULT NULL,
  `settings` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`skey`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `{#}activity` ADD `is_pub` BOOLEAN NULL DEFAULT '1', ADD INDEX (`is_pub`);

ALTER TABLE  `{#}activity` CHANGE  `id`  `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE  `type_id`  `type_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL ,
CHANGE  `user_id`  `user_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL ,
CHANGE  `group_id`  `group_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL ,
CHANGE  `subject_id`  `subject_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL ,
CHANGE  `images_count`  `images_count` INT( 11 ) UNSIGNED NULL DEFAULT NULL ,
CHANGE  `is_private`  `is_private` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT  '0',
CHANGE  `is_parent_hidden`  `is_parent_hidden` TINYINT( 1 ) UNSIGNED NULL DEFAULT NULL ,
CHANGE  `is_pub`  `is_pub` TINYINT( 1 ) UNSIGNED NULL DEFAULT  '1';

ALTER TABLE  `{#}activity_types` CHANGE  `id`  `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE  `is_enabled`  `is_enabled` TINYINT( 1 ) UNSIGNED NULL DEFAULT  '1';

INSERT INTO `{#}activity_types` (`id`, `is_enabled`, `controller`, `name`, `title`, `description`) VALUES
(1, 1, 'pages', 'add.pages', 'Добавление страниц', 'добавляет страницу %s');

ALTER TABLE  `{#}comments` CHANGE  `id`  `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE  `parent_id`  `parent_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL COMMENT  'ID родительского комментария',
CHANGE  `level`  `level` TINYINT( 4 ) UNSIGNED NULL DEFAULT NULL COMMENT  'Уровень вложенности',
CHANGE  `ordering`  `ordering` INT( 11 ) UNSIGNED NULL DEFAULT NULL COMMENT  'Порядковый номер в дереве',
CHANGE  `user_id`  `user_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL COMMENT  'ID автора',
CHANGE  `target_id`  `target_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL COMMENT  'ID объекта комментирования',
CHANGE  `is_deleted`  `is_deleted` TINYINT( 1 ) UNSIGNED NULL DEFAULT NULL COMMENT  'Комментарий удален?',
CHANGE  `is_private`  `is_private` TINYINT( 1 ) UNSIGNED NULL DEFAULT  '0' COMMENT  'Только для друзей?';

ALTER TABLE  `{#}comments_rating` CHANGE  `id`  `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE  `comment_id`  `comment_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL ,
CHANGE  `user_id`  `user_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL ;

ALTER TABLE  `{#}comments_tracks` CHANGE  `id`  `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE  `user_id`  `user_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL ,
CHANGE  `target_id`  `target_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL ;

ALTER TABLE  `{#}content_datasets` CHANGE  `id`  `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE  `ctype_id`  `ctype_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL COMMENT  'ID типа контента',
CHANGE  `ordering`  `ordering` INT( 11 ) UNSIGNED NULL DEFAULT NULL COMMENT  'Порядковый номер',
CHANGE  `is_visible`  `is_visible` TINYINT( 1 ) UNSIGNED NULL DEFAULT NULL COMMENT  'Отображать набор на сайте?';

ALTER TABLE  `{#}content_folders` CHANGE  `id`  `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE  `ctype_id`  `ctype_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL ,
CHANGE  `user_id`  `user_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL ;

ALTER TABLE `{#}content_types` CHANGE `id` `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, CHANGE `is_date_range` `is_date_range` TINYINT(1) UNSIGNED NULL DEFAULT NULL, CHANGE `is_premod_add` `is_premod_add` TINYINT(1) UNSIGNED NULL DEFAULT NULL COMMENT 'Модерация при создании?', CHANGE `is_premod_edit` `is_premod_edit` TINYINT(1) UNSIGNED NULL DEFAULT NULL COMMENT 'Модерация при редактировании', CHANGE `is_cats` `is_cats` TINYINT(1) UNSIGNED NULL DEFAULT NULL COMMENT 'Категории включены?', CHANGE `is_cats_recursive` `is_cats_recursive` TINYINT(1) UNSIGNED NULL DEFAULT NULL COMMENT 'Сквозной просмотр категорий?', CHANGE `is_folders` `is_folders` TINYINT(1) UNSIGNED NULL DEFAULT NULL, CHANGE `is_in_groups` `is_in_groups` TINYINT(1) UNSIGNED NULL DEFAULT NULL COMMENT 'Создание в группах', CHANGE `is_in_groups_only` `is_in_groups_only` TINYINT(1) UNSIGNED NULL DEFAULT NULL COMMENT 'Создание только в группах', CHANGE `is_comments` `is_comments` TINYINT(1) UNSIGNED NULL DEFAULT NULL COMMENT 'Комментарии включены?', CHANGE `is_comments_tree` `is_comments_tree` TINYINT(1) UNSIGNED NULL DEFAULT NULL, CHANGE `is_rating` `is_rating` TINYINT(1) UNSIGNED NULL DEFAULT NULL COMMENT 'Разрешить рейтинг?', CHANGE `is_rating_pos` `is_rating_pos` TINYINT(1) UNSIGNED NULL DEFAULT NULL, CHANGE `is_tags` `is_tags` TINYINT(1) UNSIGNED NULL DEFAULT NULL, CHANGE `is_auto_keys` `is_auto_keys` TINYINT(1) UNSIGNED NULL DEFAULT NULL COMMENT 'Автоматическая генерация ключевых слов?', CHANGE `is_auto_desc` `is_auto_desc` TINYINT(1) UNSIGNED NULL DEFAULT NULL COMMENT 'Автоматическая генерация описания?', CHANGE `is_auto_url` `is_auto_url` TINYINT(1) UNSIGNED NULL DEFAULT NULL COMMENT 'Генерировать URL из заголовка?', CHANGE `is_fixed_url` `is_fixed_url` TINYINT(1) UNSIGNED NULL DEFAULT NULL COMMENT 'Не изменять URL при изменении записи?', CHANGE `is_fixed` `is_fixed` TINYINT(1) UNSIGNED NULL DEFAULT NULL;

ALTER TABLE  `{#}controllers` CHANGE  `id`  `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE  `is_enabled`  `is_enabled` TINYINT( 1 ) UNSIGNED NULL DEFAULT  '1' COMMENT  'Включен?',
CHANGE  `is_backend`  `is_backend` TINYINT( 1 ) UNSIGNED NULL DEFAULT NULL COMMENT  'Есть админка?';

ALTER TABLE  `{#}groups` CHANGE  `id`  `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE  `owner_id`  `owner_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL COMMENT  'Создатель',
CHANGE  `members_count`  `members_count` INT( 11 ) UNSIGNED NOT NULL DEFAULT  '0' COMMENT  'Кол-во членов',
CHANGE  `join_policy`  `join_policy` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT  '0' COMMENT  'Политика вступления',
CHANGE  `edit_policy`  `edit_policy` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT  '0' COMMENT  'Политика редактирования',
CHANGE  `wall_policy`  `wall_policy` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT  '0' COMMENT  'Политика стены',
CHANGE  `is_closed`  `is_closed` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT  '0' COMMENT  'Закрытая?';

ALTER TABLE  `{#}groups_invites` CHANGE  `id`  `id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
CHANGE  `group_id`  `group_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL COMMENT  'ID группы',
CHANGE  `user_id`  `user_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL COMMENT  'ID пригласившего',
CHANGE  `invited_id`  `invited_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL COMMENT  'ID приглашенного';

ALTER TABLE  `{#}groups_members` CHANGE  `id`  `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE  `group_id`  `group_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL ,
CHANGE  `user_id`  `user_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL ,
CHANGE  `role`  `role` TINYINT( 4 ) UNSIGNED NOT NULL DEFAULT  '0' COMMENT  'Роль пользователя в группе';

ALTER TABLE  `{#}images_presets` CHANGE  `id`  `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE  `width`  `width` INT( 11 ) UNSIGNED NULL DEFAULT NULL ,
CHANGE  `height`  `height` INT( 11 ) UNSIGNED NULL DEFAULT NULL ,
CHANGE  `is_square`  `is_square` TINYINT( 1 ) UNSIGNED NULL DEFAULT NULL ,
CHANGE  `is_watermark`  `is_watermark` TINYINT( 1 ) UNSIGNED NULL DEFAULT NULL ,
CHANGE  `wm_margin`  `wm_margin` INT( 11 ) UNSIGNED NULL DEFAULT NULL ,
CHANGE  `is_internal`  `is_internal` TINYINT( 1 ) UNSIGNED NULL DEFAULT NULL ;

ALTER TABLE  `{#}menu` CHANGE  `id`  `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE  `is_fixed`  `is_fixed` TINYINT( 1 ) UNSIGNED NULL DEFAULT NULL COMMENT  'Запрещено удалять?';

ALTER TABLE  `{#}menu_items` CHANGE  `id`  `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE  `menu_id`  `menu_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL COMMENT  'ID меню',
CHANGE  `parent_id`  `parent_id` INT( 11 ) UNSIGNED NULL DEFAULT  '0' COMMENT  'ID родительского пункта',
CHANGE  `ordering`  `ordering` INT( 11 ) UNSIGNED NULL DEFAULT NULL COMMENT  'Порядковый номер';

ALTER TABLE  `{#}moderators` CHANGE  `id`  `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE  `user_id`  `user_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL ,
CHANGE  `count_approved`  `count_approved` INT( 11 ) UNSIGNED NOT NULL DEFAULT  '0',
CHANGE  `count_deleted`  `count_deleted` INT( 11 ) UNSIGNED NOT NULL DEFAULT  '0',
CHANGE  `count_idle`  `count_idle` INT( 11 ) UNSIGNED NOT NULL DEFAULT  '0';

ALTER TABLE  `{#}moderators_tasks` CHANGE  `id`  `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE  `moderator_id`  `moderator_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL ,
CHANGE  `author_id`  `author_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL ,
CHANGE  `item_id`  `item_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL ,
CHANGE  `is_new_item`  `is_new_item` TINYINT( 1 ) UNSIGNED NULL DEFAULT  '1';

ALTER TABLE  `{#}perms_rules` CHANGE  `id`  `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT ;

ALTER TABLE  `{#}perms_users` CHANGE  `rule_id`  `rule_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL COMMENT  'ID правила',
CHANGE  `group_id`  `group_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL COMMENT  'ID группы';

ALTER TABLE  `{#}photos` CHANGE  `id`  `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE  `album_id`  `album_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL ,
CHANGE  `user_id`  `user_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL ,
CHANGE  `comments`  `comments` INT( 11 ) UNSIGNED NULL DEFAULT  '0';

ALTER TABLE  `{#}rating_log` CHANGE  `id`  `id` BIGINT( 20 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE  `user_id`  `user_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL COMMENT  'ID пользователя',
CHANGE  `target_id`  `target_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL COMMENT  'ID субъекта (записи оцениваемого контента)',
CHANGE  `score`  `score` TINYINT( 3 ) NULL DEFAULT NULL COMMENT  'Значение оценки';

ALTER TABLE  `{#}rss_feeds` CHANGE  `id`  `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE  `ctype_id`  `ctype_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL ,
CHANGE  `limit`  `limit` INT( 11 ) UNSIGNED NOT NULL DEFAULT  '15',
CHANGE  `is_enabled`  `is_enabled` TINYINT( 1 ) UNSIGNED NULL DEFAULT NULL ,
CHANGE  `is_cache`  `is_cache` TINYINT( 1 ) UNSIGNED NULL DEFAULT NULL ,
CHANGE  `cache_interval`  `cache_interval` INT( 11 ) UNSIGNED NULL DEFAULT  '60';

ALTER TABLE  `{#}scheduler_tasks` CHANGE  `id`  `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE  `period`  `period` INT( 11 ) UNSIGNED NULL DEFAULT NULL ,
CHANGE  `is_active`  `is_active` TINYINT( 1 ) UNSIGNED NULL DEFAULT NULL ,
CHANGE  `is_new`  `is_new` TINYINT( 1 ) UNSIGNED NULL DEFAULT  '1';

ALTER TABLE  `{#}sessions_online` CHANGE  `user_id`  `user_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL ;

ALTER TABLE  `{#}tags` CHANGE  `id`  `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE  `frequency`  `frequency` INT( 11 ) UNSIGNED NOT NULL DEFAULT  '1';

ALTER TABLE  `{#}tags_bind` CHANGE  `id`  `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE  `tag_id`  `tag_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL ,
CHANGE  `target_id`  `target_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL ;

ALTER TABLE  `{#}uploaded_files` CHANGE  `id`  `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE  `counter`  `counter` INT( 11 ) UNSIGNED NOT NULL DEFAULT  '0';

ALTER TABLE  `{#}users` CHANGE  `id`  `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE  `is_admin`  `is_admin` TINYINT( 1 ) UNSIGNED NULL DEFAULT NULL COMMENT  'Администратор?',
CHANGE  `is_locked`  `is_locked` TINYINT( 1 ) UNSIGNED NULL DEFAULT NULL COMMENT  'Заблокирован',
CHANGE  `files_count`  `files_count` INT( 11 ) UNSIGNED NOT NULL DEFAULT  '0' COMMENT  'Кол-во загруженных файлов',
CHANGE  `friends_count`  `friends_count` INT( 11 ) UNSIGNED NOT NULL DEFAULT  '0' COMMENT  'Кол-во друзей',
CHANGE  `status_id`  `status_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL COMMENT  'Текстовый статус',
CHANGE  `inviter_id`  `inviter_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL ,
CHANGE  `invites_count`  `invites_count` INT( 11 ) UNSIGNED NOT NULL DEFAULT  '0',
CHANGE  `city`  `city` INT( 11 ) UNSIGNED NULL DEFAULT NULL ;

ALTER TABLE  `{#}users_contacts` CHANGE  `id`  `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE  `user_id`  `user_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL COMMENT  'ID пользователя',
CHANGE  `contact_id`  `contact_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL COMMENT  'ID контакта (другого пользователя)',
CHANGE  `messages`  `messages` INT( 11 ) UNSIGNED NOT NULL DEFAULT  '0' COMMENT  'Кол-во сообщений';

ALTER TABLE  `{#}users_fields` CHANGE  `id`  `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE  `ctype_id`  `ctype_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL ,
CHANGE  `ordering`  `ordering` INT( 11 ) UNSIGNED NULL DEFAULT NULL ,
CHANGE  `is_in_list`  `is_in_list` TINYINT( 1 ) UNSIGNED NULL DEFAULT NULL ,
CHANGE  `is_in_item`  `is_in_item` TINYINT( 1 ) UNSIGNED NULL DEFAULT NULL ,
CHANGE  `is_in_filter`  `is_in_filter` TINYINT( 1 ) UNSIGNED NULL DEFAULT NULL ,
CHANGE  `is_private`  `is_private` TINYINT( 1 ) UNSIGNED NULL DEFAULT NULL ,
CHANGE  `is_fixed`  `is_fixed` TINYINT( 1 ) UNSIGNED NULL DEFAULT NULL ,
CHANGE  `is_fixed_type`  `is_fixed_type` TINYINT( 1 ) UNSIGNED NULL DEFAULT NULL ,
CHANGE  `is_system`  `is_system` TINYINT( 1 ) UNSIGNED NULL DEFAULT NULL ;

ALTER TABLE  `{#}users_friends` CHANGE  `id`  `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE  `user_id`  `user_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL COMMENT  'ID пользователя',
CHANGE  `friend_id`  `friend_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL COMMENT  'ID друга',
CHANGE  `is_mutual`  `is_mutual` TINYINT( 1 ) UNSIGNED NULL DEFAULT NULL COMMENT  'Дружба взаимна?';

ALTER TABLE  `{#}users_groups` CHANGE  `id`  `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE  `is_fixed`  `is_fixed` TINYINT( 1 ) UNSIGNED NULL DEFAULT NULL COMMENT  'Системная?',
CHANGE  `is_public`  `is_public` TINYINT( 1 ) UNSIGNED NULL DEFAULT NULL COMMENT  'Группу можно выбрать при регистрации?',
CHANGE  `is_filter`  `is_filter` TINYINT( 1 ) UNSIGNED NULL DEFAULT NULL COMMENT  'Выводить группу в фильтре пользователей?';

ALTER TABLE  `{#}users_groups_members` CHANGE  `user_id`  `user_id` INT( 11 ) UNSIGNED NOT NULL ,
CHANGE  `group_id`  `group_id` INT( 11 ) UNSIGNED NOT NULL ;

ALTER TABLE  `{#}users_groups_migration` CHANGE  `id`  `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE  `is_active`  `is_active` TINYINT( 1 ) UNSIGNED NULL DEFAULT NULL ,
CHANGE  `group_from_id`  `group_from_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL ,
CHANGE  `group_to_id`  `group_to_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL ,
CHANGE  `is_keep_group`  `is_keep_group` TINYINT( 1 ) UNSIGNED NULL DEFAULT NULL ,
CHANGE  `is_passed`  `is_passed` TINYINT( 1 ) UNSIGNED NULL DEFAULT NULL ,
CHANGE  `is_rating`  `is_rating` TINYINT( 1 ) UNSIGNED NULL DEFAULT NULL ,
CHANGE  `is_karma`  `is_karma` TINYINT( 1 ) UNSIGNED NULL DEFAULT NULL ,
CHANGE  `passed_days`  `passed_days` INT( 11 ) UNSIGNED NULL DEFAULT NULL ,
CHANGE  `passed_from`  `passed_from` TINYINT( 1 ) UNSIGNED NULL DEFAULT NULL ,
CHANGE  `is_notify`  `is_notify` TINYINT( 1 ) UNSIGNED NULL DEFAULT NULL ;

ALTER TABLE  `{#}users_ignors` CHANGE  `id`  `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE  `user_id`  `user_id` INT( 11 ) UNSIGNED NOT NULL COMMENT  'ID пользователя',
CHANGE  `ignored_user_id`  `ignored_user_id` INT( 11 ) UNSIGNED NOT NULL COMMENT  'ID игнорируемого пользователя';

ALTER TABLE  `{#}users_invites` CHANGE  `id`  `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE  `user_id`  `user_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL ;

ALTER TABLE  `{#}users_karma` CHANGE  `id`  `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE  `user_id`  `user_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL COMMENT  'Кто поставил',
CHANGE  `profile_id`  `profile_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL COMMENT  'Кому поставил';

ALTER TABLE  `{#}users_messages` CHANGE  `id`  `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE  `from_id`  `from_id` INT( 11 ) UNSIGNED NOT NULL COMMENT  'ID отправителя',
CHANGE  `to_id`  `to_id` INT( 11 ) UNSIGNED NOT NULL COMMENT  'ID получателя',
CHANGE  `is_new`  `is_new` TINYINT( 1 ) UNSIGNED NULL DEFAULT  '1' COMMENT  'Не прочитано?';

ALTER TABLE  `{#}users_notices` CHANGE  `id`  `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE  `user_id`  `user_id` INT( 11 ) UNSIGNED NOT NULL ;

ALTER TABLE  `{#}users_statuses` CHANGE  `id`  `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE  `user_id`  `user_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL COMMENT  'Пользователь',
CHANGE  `replies_count`  `replies_count` INT( 11 ) UNSIGNED NOT NULL DEFAULT  '0' COMMENT  'Количество ответов',
CHANGE  `wall_entry_id`  `wall_entry_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL COMMENT  'ID записи на стене';

ALTER TABLE  `{#}users_tabs` CHANGE  `id`  `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE  `is_active`  `is_active` TINYINT( 1 ) UNSIGNED NULL DEFAULT NULL ,
CHANGE  `ordering`  `ordering` INT( 11 ) UNSIGNED NULL DEFAULT NULL ;

ALTER TABLE  `{#}wall_entries` CHANGE  `id`  `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE  `profile_id`  `profile_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL COMMENT  'ID профиля',
CHANGE  `user_id`  `user_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL COMMENT  'ID автора',
CHANGE  `parent_id`  `parent_id` INT( 11 ) UNSIGNED NOT NULL DEFAULT  '0' COMMENT  'ID родительской записи',
CHANGE  `status_id`  `status_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL COMMENT  'Связь со статусом пользователя';

ALTER TABLE  `{#}widgets` CHANGE  `id`  `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT ;

ALTER TABLE  `{#}widgets_bind` CHANGE  `id`  `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE  `widget_id`  `widget_id` INT( 11 ) UNSIGNED NOT NULL ,
CHANGE  `is_title`  `is_title` TINYINT( 1 ) UNSIGNED NULL DEFAULT  '1' COMMENT  'Показывать заголовок',
CHANGE  `is_enabled`  `is_enabled` TINYINT( 1 ) UNSIGNED NULL DEFAULT NULL COMMENT  'Включен?',
CHANGE  `is_tab_prev`  `is_tab_prev` TINYINT( 1 ) UNSIGNED NULL DEFAULT NULL COMMENT  'Объединять с предыдущим?',
CHANGE  `page_id`  `page_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL COMMENT  'ID страницы для вывода',
CHANGE  `ordering`  `ordering` INT( 11 ) UNSIGNED NULL DEFAULT NULL COMMENT  'Порядковый номер';

ALTER TABLE  `{#}widgets_pages` CHANGE  `id`  `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT ;