INSERT INTO `{#}controllers` (`title`, `name`, `is_enabled`, `options`, `author`, `url`, `version`, `is_backend`) VALUES
('Rating', 'rating', 1, '---\nis_hidden: 1\nis_show: 1\nallow_guest_vote: null\ntemplate: widget\n', 'InstantCMS Team', 'https://instantcms.ru', '2.0', 1);

INSERT INTO `{#}events` (`event`, `listener`, `ordering`, `is_enabled`) VALUES
('user_delete', 'rating', 83, 1),
('content_before_list', 'rating', 84, 1),
('user_notify_types', 'rating', 155, 1),
('content_before_item', 'rating', 157, 1),
('ctype_basic_form', 'rating', 160, 1),
('photos_before_item', 'rating', 162, 1),
('content_after_delete', 'rating', 203, 1),
('photos_after_delete_list', 'rating', 240, 1),
('admin_content_filter', 'rating', 241, 1),
('admin_content_dataset_fields_list', 'rating', 242, 1);

DROP TABLE IF EXISTS `{#}rating_log`;
CREATE TABLE `{#}rating_log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned DEFAULT NULL COMMENT 'User ID',
  `target_controller` varchar(32) DEFAULT NULL COMMENT 'Component (target content owner)',
  `target_subject` varchar(32) DEFAULT NULL COMMENT 'Subject (target content type)',
  `target_id` int(11) unsigned DEFAULT NULL COMMENT 'Subject ID (target content items)',
  `score` tinyint(1) DEFAULT NULL COMMENT 'Score value',
  `ip` varbinary(16) DEFAULT NULL COMMENT 'ip-address',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `target_id` (`target_id`,`target_controller`,`target_subject`),
  KEY `ip` (`ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Rating scores';