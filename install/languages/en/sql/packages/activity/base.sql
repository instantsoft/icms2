DROP TABLE IF EXISTS `{#}activity`;
CREATE TABLE `{#}activity` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type_id` int(11) unsigned DEFAULT NULL,
  `user_id` int(11) unsigned DEFAULT NULL,
  `group_id` int(11) unsigned DEFAULT NULL,
  `subject_title` varchar(140) DEFAULT NULL,
  `subject_id` int(11) unsigned DEFAULT NULL,
  `subject_url` varchar(250) DEFAULT NULL,
  `reply_url` varchar(250) DEFAULT NULL,
  `images` text,
  `images_count` int(11) unsigned DEFAULT NULL,
  `date_pub` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_private` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_parent_hidden` tinyint(1) unsigned DEFAULT NULL,
  `is_pub` tinyint(1) unsigned DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `type_id` (`type_id`),
  KEY `user_id` (`user_id`),
  KEY `date_pub` (`date_pub`),
  KEY `is_private` (`is_private`),
  KEY `group_id` (`group_id`),
  KEY `is_parent_hidden` (`is_parent_hidden`),
  KEY `is_pub` (`is_pub`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Лента активности';

DROP TABLE IF EXISTS `{#}activity_types`;
CREATE TABLE `{#}activity_types` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `is_enabled` tinyint(1) unsigned DEFAULT '1',
  `controller` varchar(32) NOT NULL,
  `name` varchar(32) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` varchar(200) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `is_enabled` (`is_enabled`),
  KEY `controller` (`controller`),
  KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Типы записей в ленте активности';

INSERT INTO `{#}activity_types` (`id`, `is_enabled`, `controller`, `name`, `title`, `description`) VALUES
(1, 1, 'content', 'add.pages', 'Adding pages', 'added the page %s'),
(2, 1, 'comments', 'vote.comment', 'Rating comments', 'evaluate a comment on the %s page'),
(7, 1, 'users', 'friendship', 'Friendship', 'and %s became friends'),
(8, 1, 'users', 'signup', 'New users', 'registered. Welcome!'),
(10, 1, 'groups', 'join', 'Group joining', 'joined the group %s'),
(11, 1, 'groups', 'leave', 'Group leaving', 'left the group %s'),
(12, 1, 'users', 'status', 'Status changing', '&rarr; %s'),
(15, 0, 'content', 'add.albums', 'Adding albums', 'added album %s'),
(18, 1, 'photos', 'add.photos', 'Photo uploading', 'uploaded photos to the album %s'),
(19, 1, 'users', 'avatar', 'Avatar changing', 'changed avatar');

INSERT INTO `{#}controllers` (`title`, `name`, `is_enabled`, `options`, `author`, `url`, `version`, `is_backend`) VALUES
('Activity Feed', 'activity', 1, '---\ntypes:\n  - 10\n  - 11\n  - 17\n  - 16\n  - 14\n  - 13\n  - 18\n  - 7\n  - 19\n  - 12\n  - 8\n', 'InstantCMS Team', 'https://instantcms.ru', '2.0', 1);

INSERT INTO `{#}events` (`event`, `listener`, `ordering`, `is_enabled`) VALUES
('content_after_add_approve', 'activity', 1, 1),
('content_after_update_approve', 'activity', 2, 1),
('publish_delayed_content', 'activity', 3, 1),
('user_delete', 'activity', 4, 1),
('user_tab_info', 'activity', 5, 1),
('user_tab_show', 'activity', 6, 1),
('subscribe', 'activity', 125, 1),
('unsubscribe', 'activity', 126, 1),
('admin_dashboard_block', 'activity', 168, 1),
('admin_inline_save_subscriptions', 'activity', 175, 1),
('user_add_status_after', 'activity', 181, 1),
('users_add_friendship_mutual', 'activity', 188, 1),
('user_registered', 'activity', 189, 1),
('users_after_update', 'activity', 193, 1),
('ctype_labels_after_update', 'activity', 195, 1),
('ctype_after_delete', 'activity', 196, 1),
('comments_rate_after', 'activity', 197, 1),
('content_albums_after_delete', 'activity', 198, 1),
('content_photos_after_add', 'activity', 199, 1),
('comments_after_delete_list', 'activity', 200, 1),
('content_after_delete', 'activity', 201, 1),
('content_after_restore', 'activity', 205, 1),
('content_after_trash_put', 'activity', 206, 1),
('content_groups_after_delete', 'activity', 209, 1),
('group_after_join', 'activity', 210, 1),
('group_after_leave', 'activity', 211, 1),
('groups_after_accept_request', 'activity', 212, 1),
('groups_after_update', 'activity', 213, 1),
('languages_forms', 'activity', 227, 1);

INSERT INTO `{#}menu_items` (`menu_id`, `parent_id`, `title`, `url`, `ordering`, `options`, `groups_view`, `groups_hide`) VALUES
(1, 0, 'Activity', 'activity', 7, '---\nclass:', '---\n- 0\n', NULL);

INSERT INTO `{#}perms_rules` (`controller`, `name`, `type`, `options`) VALUES
('activity', 'delete', 'flag', NULL);

INSERT INTO `{#}users_tabs` (`title`, `controller`, `name`, `is_active`, `ordering`) VALUES
('Feed', 'activity', 'activity', 1, 1);

INSERT INTO `{#}widgets` (`controller`, `name`, `title`, `author`, `url`, `version`, `is_external`) VALUES
('activity', 'list', 'Activity feed', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL);