INSERT INTO `{#}controllers` (`title`, `name`, `is_enabled`, `options`, `author`, `url`, `version`, `is_backend`) VALUES
('Стена', 'wall', 1, '---\nlimit: 15\norder_by: date_last_reply\nshow_entries: 5\neditor: \"4\"\neditor_presets: null\ntypograph_id: \"1\"\n', 'InstantCMS Team', 'https://instantcms.ru', '2.0', 1);

INSERT INTO `{#}events` (`event`, `listener`, `ordering`, `is_enabled`) VALUES
('admin_dashboard_chart', 'wall', 116, 1),
('user_notify_types', 'wall', 117, 1),
('user_delete', 'wall', 118, 1),
('process_render_users_profile_view', 'wall', 179, 1),
('process_render_groups_group_view', 'wall', 180, 1),
('user_add_status', 'wall', 182, 1),
('form_groups_options', 'wall', 183, 1),
('form_users_options', 'wall', 184, 1),
('user_privacy_types', 'wall', 185, 1);

INSERT INTO `{#}perms_rules` (`controller`, `name`, `type`, `options`) VALUES
('users', 'wall_add', 'flag', NULL),
('users', 'wall_delete', 'list', 'own,all');

DROP TABLE IF EXISTS `{#}wall_entries`;
CREATE TABLE `{#}wall_entries` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `date_pub` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Дата публикации',
  `date_last_reply` timestamp NULL DEFAULT NULL COMMENT 'Дата последнего ответа',
  `date_last_modified` timestamp NULL DEFAULT NULL COMMENT 'Дата изменения',
  `controller` varchar(32) DEFAULT NULL COMMENT 'Компонент владелец профиля',
  `profile_type` varchar(32) DEFAULT NULL COMMENT 'Тип профиля (пользователь/группа)',
  `profile_id` int(11) unsigned DEFAULT NULL COMMENT 'ID профиля',
  `user_id` int(11) unsigned DEFAULT NULL COMMENT 'ID автора',
  `parent_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'ID родительской записи',
  `status_id` int(11) unsigned DEFAULT NULL COMMENT 'Связь со статусом пользователя',
  `content` text COMMENT 'Текст записи',
  `content_html` text COMMENT 'Текст после типографа',
  PRIMARY KEY (`id`),
  KEY `date_pub` (`date_pub`),
  KEY `user_id` (`user_id`),
  KEY `parent_id` (`parent_id`),
  KEY `profile_id` (`profile_id`,`profile_type`),
  KEY `status_id` (`status_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Записи на стенах профилей';