INSERT INTO `{#}controllers` (`title`, `name`, `is_enabled`, `options`, `author`, `url`, `version`, `is_backend`) VALUES
('RSS feeds', 'rss', 1, NULL, 'InstantCMS Team', 'https://instantcms.ru', '2.0', 1);

INSERT INTO `{#}events` (`event`, `listener`, `ordering`, `is_enabled`) VALUES
('ctype_basic_form', 'rss', 87, 1),
('ctype_before_add', 'rss', 88, 1),
('ctype_after_add', 'rss', 89, 1),
('ctype_before_edit', 'rss', 90, 1),
('ctype_before_update', 'rss', 91, 1),
('ctype_after_delete', 'rss', 92, 1),
('content_before_category', 'rss', 93, 1),
('content_before_profile', 'rss', 94, 1);

DROP TABLE IF EXISTS `{#}rss_feeds`;
CREATE TABLE `{#}rss_feeds` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ctype_id` int(11) unsigned DEFAULT NULL,
  `ctype_name` varchar(32) DEFAULT NULL,
  `title` varchar(128) DEFAULT NULL,
  `description` text,
  `image` text,
  `mapping` text,
  `limit` int(11) unsigned NOT NULL DEFAULT '15',
  `is_enabled` tinyint(1) unsigned DEFAULT NULL,
  `is_cache` tinyint(1) unsigned DEFAULT NULL,
  `cache_interval` int(11) unsigned DEFAULT '60',
  `date_cached` timestamp NULL DEFAULT NULL,
  `template` varchar(30) NOT NULL DEFAULT 'feed' COMMENT 'Feed template',
  PRIMARY KEY (`id`),
  KEY `ctype_id` (`ctype_id`),
  KEY `ctype_name` (`ctype_name`),
  KEY `is_enabled` (`is_enabled`),
  KEY `is_cache` (`is_cache`),
  KEY `cache_interval` (`cache_interval`),
  KEY `date_cached` (`date_cached`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `{#}rss_feeds` (`ctype_id`, `ctype_name`, `title`, `description`, `image`, `mapping`, `limit`, `is_enabled`, `is_cache`, `cache_interval`, `date_cached`) VALUES
(NULL, 'comments', 'Comments', NULL, NULL, '---\r\ntitle: target_title\r\ndescription: content_html\r\npubDate: date_pub\r\n', 15, 1, NULL, 60, NULL),
(7, 'albums', 'Photo albums', NULL, NULL, '---\ntitle: title\ndescription: content\npubDate: date_pub\nimage: cover_image\nimage_size: normal\n', 15, 1, NULL, 60, NULL);