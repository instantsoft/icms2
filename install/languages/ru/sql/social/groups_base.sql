INSERT INTO `{#}controllers` (`title`, `name`, `is_enabled`, `options`, `author`, `url`, `version`, `is_backend`) VALUES
('Группы', 'groups', 1, '---\nis_ds_rating: 1\nis_ds_popular: 1\nis_wall: 1\n', 'InstantCMS Team', 'https://instantcms.ru', '2.0', 1);

INSERT INTO `{#}events` (`event`, `listener`, `ordering`, `is_enabled`) VALUES
('admin_dashboard_chart', 'groups', 40, 1),
('content_view_hidden', 'groups', 41, 1),
('content_before_list', 'groups', 42, 1),
('rating_vote', 'groups', 43, 1),
('user_privacy_types', 'groups', 44, 1),
('user_profile_buttons', 'groups', 45, 1),
('user_notify_types', 'groups', 46, 1),
('user_delete', 'groups', 47, 1),
('user_tab_info', 'groups', 48, 1),
('user_tab_show', 'groups', 49, 1),
('menu_groups', 'groups', 50, 1),
('sitemap_sources', 'groups', 51, 1),
('sitemap_urls', 'groups', 52, 1),
('content_privacy_types', 'groups', 53, 1),
('content_add_permissions', 'groups', 54, 1),
('fulltext_search', 'groups', 55, 1),
('content_before_childs', 'groups', 56, 1),
('ctype_relation_childs', 'groups', 57, 1),
('admin_groups_dataset_fields_list', 'groups', 58, 1),
('content_validate', 'groups', 59, 1),
('moderation_list', 'groups', 60, 1),
('content_before_item', 'groups', 61, 1),
('ctype_lists_context', 'groups', 120, 1),
('ctype_basic_form', 'groups', 161, 1),
('content_item_form_context', 'groups', 194, 1),
('languages_forms', 'groups', 226, 1);

DROP TABLE IF EXISTS `{#}groups`;
CREATE TABLE `{#}groups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `owner_id` int(11) unsigned DEFAULT NULL COMMENT 'Создатель',
  `date_pub` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Дата создания',
  `title` varchar(128) NOT NULL COMMENT 'Название',
  `description` text COMMENT 'Описание',
  `logo` text COMMENT 'Логотип группы',
  `rating` int(11) NOT NULL DEFAULT '0' COMMENT 'Рейтинг',
  `members_count` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Кол-во членов',
  `join_policy` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Политика вступления',
  `edit_policy` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Политика редактирования',
  `wall_policy` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Политика стены',
  `wall_reply_policy` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Политика комментирования стены',
  `is_closed` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Закрытая?',
  `cover` text COMMENT 'Обложка группы',
  `slug` varchar(100) DEFAULT NULL,
  `content_policy` varchar(500) DEFAULT NULL COMMENT 'Политика контента',
  `content_groups` varchar(1000) DEFAULT NULL COMMENT 'Группы, которым разрешено добавление контента',
  `roles` varchar(2000) DEFAULT NULL,
  `content_roles` varchar(1000) DEFAULT NULL,
  `join_roles` varchar(1000) DEFAULT NULL COMMENT 'Роли при вступлении в группу',
  `is_approved` tinyint(1) NOT NULL DEFAULT '1',
  `approved_by` int(11) DEFAULT NULL,
  `date_approved` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `members_count` (`members_count`),
  KEY `date_pub` (`date_pub`),
  KEY `rating` (`rating`),
  KEY `owner_id` (`owner_id`,`members_count`),
  KEY `slug` (`slug`),
  FULLTEXT KEY `fulltext_search` (`title`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Группы (сообщества)';

DROP TABLE IF EXISTS `{#}groups_fields`;
CREATE TABLE `{#}groups_fields` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ctype_id` int(11) unsigned DEFAULT NULL,
  `name` varchar(40) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `hint` varchar(200) DEFAULT NULL,
  `ordering` int(11) unsigned DEFAULT NULL,
  `is_enabled` tinyint(1) UNSIGNED DEFAULT '1',
  `fieldset` varchar(32) DEFAULT NULL,
  `type` varchar(16) DEFAULT NULL,
  `is_in_list` tinyint(1) unsigned DEFAULT NULL,
  `is_in_item` tinyint(1) unsigned DEFAULT NULL,
  `is_in_filter` tinyint(1) unsigned DEFAULT NULL,
  `is_in_closed` tinyint(3) unsigned DEFAULT NULL,
  `is_private` tinyint(1) unsigned DEFAULT NULL,
  `is_fixed` tinyint(1) unsigned DEFAULT NULL,
  `is_fixed_type` tinyint(1) unsigned DEFAULT NULL,
  `is_system` tinyint(1) unsigned DEFAULT NULL,
  `values` text,
  `options` text,
  `groups_read` text,
  `groups_add` text,
  `groups_edit` text,
  `filter_view` text,
  PRIMARY KEY (`id`),
  KEY `is_enabled` (`is_enabled`,`ordering`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Поля групп';

INSERT INTO `{#}groups_fields` (`id`, `ctype_id`, `name`, `title`, `hint`, `ordering`, `fieldset`, `type`, `is_in_list`, `is_in_item`, `is_in_filter`, `is_in_closed`, `is_private`, `is_fixed`, `is_fixed_type`, `is_system`, `values`, `options`, `groups_read`, `groups_edit`, `filter_view`) VALUES
(1, NULL, 'title', 'Заголовок', NULL, 1, 'Основная информация', 'caption', 1, 1, 1, 1, NULL, 1, 1, 1, NULL, '---\nmin_length: 1\nmax_length: 128\nin_fulltext_search: 1\nprofile_value:\n', '---\n- 0\n', '---\n- 0\n', '---\n- 0\n'),
(2, NULL, 'description', 'Описание группы', NULL, 2, 'Основная информация', 'html', 1, 1, NULL, 1, NULL, 1, 1, NULL, NULL, '---\neditor: 3\nis_html_filter: 1\nbuild_redirect_link: 1\nteaser_len: 200\nin_fulltext_search: null\nlabel_in_list: none\nlabel_in_item: none\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\nprofile_value:\n', '---\n- 0\n', '---\n- 0\n', '---\n- 0\n'),
(3, NULL, 'logo', 'Логотип группы', NULL, 3, 'Основная информация', 'image', 1, 1, NULL, 1, NULL, 1, 1, 1, NULL, '---\nsize_teaser: small\nsize_full: micro\nsize_modal:\nsizes:\n  - micro\n  - small\nallow_import_link: 1\nprofile_value:\n', '---\n- 0\n', '---\n- 0\n', '---\n- 0\n'),
(5, NULL, 'cover', 'Обложка группы', NULL, 4, 'Основная информация', 'image', NULL, 1, NULL, 1, NULL, 1, 1, 1, NULL, '---\nsize_teaser: small\nsize_full: original\nsize_modal:\nsizes:\n  - small\n  - original\nallow_import_link: 1\nprofile_value:\n', '---\n- 0\n', '---\n- 0\n', '---\n- 0\n');

DROP TABLE IF EXISTS `{#}groups_invites`;
CREATE TABLE `{#}groups_invites` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(11) unsigned DEFAULT NULL COMMENT 'ID группы',
  `user_id` int(11) unsigned DEFAULT NULL COMMENT 'ID пригласившего',
  `invited_id` int(11) unsigned DEFAULT NULL COMMENT 'ID приглашенного',
  PRIMARY KEY (`id`),
  KEY `group_id` (`group_id`),
  KEY `user_id` (`user_id`),
  KEY `invited_id` (`invited_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Приглашения в группы';

DROP TABLE IF EXISTS `{#}groups_members`;
CREATE TABLE `{#}groups_members` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(11) unsigned DEFAULT NULL,
  `user_id` int(11) unsigned DEFAULT NULL,
  `role` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT 'Роль пользователя в группе',
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Дата обновления роли',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `group_id` (`group_id`,`date_updated`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Члены групп (сообществ)';

DROP TABLE IF EXISTS `{#}groups_member_roles`;
CREATE TABLE `{#}groups_member_roles` (
  `user_id` int(11) unsigned DEFAULT NULL,
  `group_id` int(11) unsigned DEFAULT NULL,
  `role_id` tinyint(1) unsigned NOT NULL DEFAULT '0',
  KEY `user_id` (`user_id`),
  KEY `group_id` (`group_id`,`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Роли участников групп';

INSERT INTO `{#}menu_items` (`menu_id`, `parent_id`, `title`, `url`, `ordering`, `options`, `groups_view`, `groups_hide`) VALUES
(1, 0, 'Группы', 'groups', 6, '---\nclass:', '---\n- 0\n', NULL),
(2, 0, 'Мои группы', '{groups:my}', 5, '---\nclass: group', '---\n- 0\n', NULL);

INSERT INTO `{#}perms_rules` (`controller`, `name`, `type`, `options`) VALUES
('groups', 'add', 'list', 'premod,yes'),
('groups', 'edit', 'list', 'own,all'),
('groups', 'delete', 'list', 'own,all'),
('groups', 'invite_users', 'flag', NULL),
('groups', 'bind_to_parent', 'list', 'own_to_own,own_to_other,own_to_all,other_to_own,other_to_other,other_to_all,all_to_own,all_to_other,all_to_all'),
('groups', 'bind_off_parent', 'list', 'own,all'),
('groups', 'content_access', 'flag', NULL);

INSERT INTO `{#}users_tabs` (`title`, `controller`, `name`, `is_active`, `ordering`) VALUES
('Группы', 'groups', 'groups', 1, 3);

INSERT INTO `{#}widgets` (`controller`, `name`, `title`, `author`, `url`, `version`, `is_external`) VALUES
('groups', 'list', 'Список групп', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL);

INSERT INTO `{#}widgets_pages` (`id`, `controller`, `name`, `title_const`, `title_subject`, `title`, `url_mask`, `url_mask_not`) VALUES
(169, 'groups', 'list', 'LANG_GROUPS_LIST', NULL, NULL, 'groups', NULL);