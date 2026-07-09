DROP TABLE IF EXISTS `{#}comments`;
CREATE TABLE `{#}comments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) unsigned DEFAULT NULL COMMENT 'Parent comment ID',
  `level` tinyint(4) unsigned DEFAULT NULL COMMENT '',
  `ordering` int(11) unsigned DEFAULT NULL COMMENT 'Order number in the tree',
  `user_id` int(11) unsigned DEFAULT NULL COMMENT 'Author ID',
  `date_pub` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Publication date',
  `date_last_modified` timestamp NULL DEFAULT NULL COMMENT 'Last modified date',
  `target_controller` varchar(32) DEFAULT NULL COMMENT 'Commented content controller',
  `target_subject` varchar(32) DEFAULT NULL COMMENT 'Commented object',
  `target_id` int(11) unsigned DEFAULT NULL COMMENT 'Commented object ID',
  `target_url` varchar(250) DEFAULT NULL COMMENT 'Commented object URL',
  `target_title` varchar(100) DEFAULT NULL COMMENT 'Commented object title',
  `author_name` varchar(100) DEFAULT NULL COMMENT 'Author (guest) name',
  `author_email` varchar(100) DEFAULT NULL COMMENT 'Author (guest) E-mail',
  `author_ip` varbinary(16) DEFAULT NULL COMMENT 'Author ip',
  `content` text COMMENT 'Comment text',
  `content_html` text COMMENT 'Sanitized text',
  `is_deleted` tinyint(1) unsigned DEFAULT NULL COMMENT 'Comment deleted?',
  `is_private` tinyint(1) unsigned DEFAULT '0' COMMENT 'Only for friends?',
  `rating` int(11) NOT NULL DEFAULT '0',
  `is_approved` tinyint(1) unsigned DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `target_id` (`target_id`,`target_controller`,`target_subject`,`ordering`),
  KEY `author_ip` (`author_ip`),
  KEY `is_approved` (`is_approved`,`is_deleted`,`date_pub`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='User comments';

DROP TABLE IF EXISTS `{#}comments_rating`;
CREATE TABLE `{#}comments_rating` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `comment_id` int(11) unsigned DEFAULT NULL,
  `user_id` int(11) unsigned DEFAULT NULL,
  `score` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `comment_id` (`comment_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{#}comments_tracks`;
CREATE TABLE `{#}comments_tracks` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned DEFAULT NULL,
  `target_controller` varchar(32) DEFAULT NULL,
  `target_subject` varchar(32) DEFAULT NULL,
  `target_id` int(11) unsigned DEFAULT NULL,
  `target_url` varchar(250) DEFAULT NULL,
  `target_title` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `target_id` (`target_id`,`target_controller`,`target_subject`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='User subscriptions to new comments';

INSERT INTO `{#}controllers` (`title`, `name`, `is_enabled`, `options`, `author`, `url`, `version`, `is_backend`) VALUES
('Comments', 'comments', 1, '---\ndisable_icms_comments: null\nis_guests: 1\nguest_ip_delay: 1\nrestricted_ips: \"\"\ndim_negative: 1\nupdate_user_rating: 1\nlimit: 20\nseo_keys: \"\"\nseo_desc: \"\"\nis_guests_moderate: 1\nrestricted_emails: \"\"\nrestricted_names: \"\"\nlimit_nesting: 5\nshow_author_email: 1\neditor: \"4\"\neditor_presets: null\nshow_list:\n  - \"0\"\ntypograph_id: \"1\"\n', 'InstantCMS Team', 'https://instantcms.ru', '2.0', 1);

INSERT INTO `{#}events` (`event`, `listener`, `ordering`, `is_enabled`) VALUES
('content_after_update', 'comments', 14, 1),
('admin_dashboard_chart', 'comments', 16, 1),
('user_privacy_types', 'comments', 17, 1),
('user_login', 'comments', 18, 1),
('user_notify_types', 'comments', 19, 1),
('user_delete', 'comments', 20, 1),
('user_tab_info', 'comments', 21, 1),
('user_tab_show', 'comments', 22, 1),
('moderation_list', 'comments', 122, 1),
('content_before_item', 'comments', 156, 1),
('content_item_form', 'comments', 158, 1),
('ctype_basic_form', 'comments', 159, 1),
('photos_before_item', 'comments', 163, 1),
('content_before_list', 'comments', 166, 1),
('content_after_delete', 'comments', 202, 1),
('content_after_restore', 'comments', 207, 1),
('content_after_trash_put', 'comments', 208, 1),
('restore_user', 'comments', 216, 1),
('set_user_is_deleted', 'comments', 217, 1),
('photos_after_delete_list', 'comments', 239, 1),
('admin_content_filter', 'comments', 240, 1),
('admin_content_dataset_fields_list', 'comments', 241, 1);

INSERT INTO `{#}menu_items` (`menu_id`, `parent_id`, `title`, `url`, `ordering`, `options`, `groups_view`, `groups_hide`) VALUES
(1, 0, 'Comments', 'comments', 8, '---\nclass:', '---\n- 0\n', NULL);

INSERT INTO `{#}perms_rules` (`controller`, `name`, `type`, `options`) VALUES
('content', 'disable_comments', 'flag', NULL),
('comments', 'add', 'flag', NULL),
('comments', 'edit', 'list', 'own,all'),
('comments', 'delete', 'list', 'own,all,full_delete'),
('comments', 'view_all', 'flag', NULL),
('comments', 'rate', 'flag', NULL),
('comments', 'karma', 'number', NULL),
('comments', 'add_approved', 'flag', NULL),
('comments', 'times', 'number', NULL);

INSERT INTO `{#}users_tabs` (`title`, `controller`, `name`, `is_active`, `ordering`) VALUES
('Comments', 'comments', 'comments', 1, 10);

INSERT INTO `{#}widgets` (`controller`, `name`, `title`, `author`, `url`, `version`, `is_external`) VALUES
('comments', 'list', 'New comments', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL);