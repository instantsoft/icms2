INSERT INTO `{#}controllers` (`title`, `name`, `is_enabled`, `options`, `author`, `url`, `version`, `is_backend`) VALUES
('Фотоальбомы', 'photos', 1, '---\nsizes:\n  - normal\n  - small\n  - big\nis_origs: null\npreset: big\npreset_small: normal\ntypes: |\n  1 | Фото\r\n  2 | Векторы\r\n  3 | Иллюстрации\nordering: date_pub\norderto: desc\nlimit: 20\ndownload_view:\n  normal: [ ]\n  micro: [ ]\n  small: [ ]\n  content_list_small: [ ]\n  content_list: [ ]\n  big: [ ]\n  content_item: [ ]\n  original: [ ]\ndownload_hide:\n  normal: null\n  micro: null\n  small: null\n  content_list_small: null\n  content_list: null\n  big: null\n  content_item: null\n  original:\n    - \"1\"\n    - \"3\"\n    - \"4\"\nurl_pattern: \'{id}-{title}\'\npreset_related: normal\nrelated_limit: 0\neditor: \"1\"\neditor_presets: null\nseo_keys: \"\"\nseo_desc: >\n  Коллекция\n  изображений на сайте\nallow_add_public_albums: null\nallow_download: 1\nhide_photo_item_info: null\ntypograph_id: \"3\"\nseo_h1: Все изображения\nseo_h1_en: \"\"\nseo_title: \'Все изображения{page: %s}\'\nseo_title_en: \"\"\nseo_keys_en: \"\"\nseo_desc_en: \"\"\n', 'InstantCMS Team', 'https://instantcms.ru', '2.0', 1);

INSERT INTO `{#}events` (`event`, `listener`, `ordering`, `is_enabled`) VALUES
('content_albums_items_html', 'photos', 74, 1),
('fulltext_search', 'photos', 75, 1),
('admin_albums_ctype_menu', 'photos', 76, 1),
('content_albums_after_add', 'photos', 77, 1),
('content_albums_after_delete', 'photos', 78, 1),
('content_albums_item_html', 'photos', 79, 1),
('content_albums_before_item', 'photos', 80, 1),
('content_albums_before_list', 'photos', 81, 1),
('user_delete', 'photos', 82, 1),
('admin_subscriptions_list', 'photos', 128, 1),
('sitemap_sources', 'photos', 143, 1),
('comments_targets', 'photos', 165, 1);

DROP TABLE IF EXISTS `{#}photos`;
CREATE TABLE `{#}photos` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `album_id` int(11) unsigned DEFAULT NULL,
  `user_id` int(11) unsigned DEFAULT NULL,
  `date_pub` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_photo` timestamp NULL DEFAULT NULL,
  `title` varchar(128) DEFAULT NULL,
  `content_source` text,
  `content` text,
  `image` text NOT NULL,
  `exif` varchar(250) DEFAULT NULL,
  `height` smallint(5) unsigned NOT NULL DEFAULT '0',
  `width` smallint(5) unsigned NOT NULL DEFAULT '0',
  `sizes` varchar(250) DEFAULT NULL,
  `rating` int(11) NOT NULL DEFAULT '0',
  `comments` int(11) unsigned DEFAULT '0',
  `hits_count` int(11) unsigned NOT NULL DEFAULT '0',
  `orientation` enum('square','landscape','portrait','') DEFAULT NULL,
  `type` tinyint(3) unsigned DEFAULT NULL,
  `camera` varchar(50) DEFAULT NULL,
  `slug` varchar(100) DEFAULT NULL,
  `is_private` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `ordering` int(11) unsigned NOT NULL DEFAULT '0',
  `downloads_count` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`date_pub`),
  KEY `album_id` (`album_id`,`date_pub`,`id`),
  KEY `slug` (`slug`),
  KEY `camera` (`camera`),
  KEY `ordering` (`ordering`),
  FULLTEXT KEY `title` (`title`,`content`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Фотографии фотоальбомов';

INSERT INTO `{#}widgets` (`controller`, `name`, `title`, `author`, `url`, `version`, `is_external`) VALUES
('photos', 'list', 'Список фотографий', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL);

INSERT INTO `{#}widgets_pages` (`controller`, `name`, `title_const`, `title_subject`, `title`, `url_mask`, `url_mask_not`) VALUES
('photos', 'item', 'LANG_PHOTOS_WP_ITEM', NULL, NULL, 'photos/*.html', NULL),
('photos', 'upload', 'LANG_PHOTOS_WP_UPLOAD', NULL, NULL, 'photos/upload/%\r\nphotos/upload', NULL),
('content', 'albums.all', 'LANG_WP_CONTENT_ALL_PAGES', NULL, NULL, 'albums\nalbums-*\nalbums/*', NULL),
('content', 'albums.list', 'LANG_WP_CONTENT_LIST', NULL, NULL, 'albums\nalbums-*\nalbums/*', 'albums/*/view-*\nalbums/*.html\nalbums/add\nalbums/add?*\nalbums/add/%\nalbums/addcat\nalbums/addcat/%\nalbums/editcat/%\nalbums/edit/*'),
('content', 'albums.item', 'LANG_WP_CONTENT_ITEM', NULL, NULL, 'albums/*.html', NULL),
('content', 'albums.edit', 'LANG_WP_CONTENT_ITEM_EDIT', NULL, NULL, 'albums/add\nalbums/add/%\nalbums/edit/*', NULL);

INSERT INTO `{#}content_types` (`id`, `title`, `name`, `description`, `is_date_range`, `is_cats`, `is_cats_recursive`, `is_folders`, `is_in_groups`, `is_in_groups_only`, `is_comments`, `is_rating`, `is_tags`, `is_auto_keys`, `is_auto_desc`, `is_auto_url`, `is_fixed_url`, `url_pattern`, `options`, `labels`, `seo_keys`, `seo_desc`, `seo_title`, `item_append_html`, `is_fixed`) VALUES
(7, 'Фотоальбомы', 'albums', '<p>Альбомы с фотографиями пользователей</p>', NULL, NULL, NULL, NULL, 1, NULL, 1, 1, 1, 1, 1, 1, 1, '{id}-{title}', '---\nis_cats_change: null\nis_cats_open_root: null\nis_cats_only_last: null\nis_show_cats: null\nis_tags_in_list: null\nis_tags_in_item: 1\nis_rss: 1\nlist_on: 1\nprofile_on: 1\nlist_show_filter: null\nlist_expand_filter: null\nitem_on: 1\nis_cats_keys: null\nis_cats_desc: null\nis_cats_auto_url: 1\nseo_title_pattern:\nseo_keys_pattern: \'{content|string_get_meta_keywords}\'\nseo_desc_pattern: \'{content|string_get_meta_description}\'\n', '---\none: альбом\ntwo: альбома\nmany: альбомов\ncreate: фотоальбом\n', NULL, NULL, NULL, NULL, 1);

DROP TABLE IF EXISTS `{#}con_albums`;
CREATE TABLE `{#}con_albums` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL,
  `content` text,
  `slug` varchar(100) DEFAULT NULL,
  `seo_keys` varchar(256) DEFAULT NULL,
  `seo_desc` varchar(256) DEFAULT NULL,
  `seo_title` varchar(256) DEFAULT NULL,
  `tags` varchar(1000) DEFAULT NULL,
  `template` varchar(150) DEFAULT NULL,
  `date_pub` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_last_modified` timestamp NULL DEFAULT NULL,
  `date_pub_end` timestamp NULL DEFAULT NULL,
  `is_pub` tinyint(1) NOT NULL DEFAULT '1',
  `hits_count` int(11) DEFAULT '0',
  `user_id` int(11) unsigned DEFAULT NULL,
  `parent_id` int(11) unsigned DEFAULT NULL,
  `parent_type` varchar(32) DEFAULT NULL,
  `parent_title` varchar(100) DEFAULT NULL,
  `parent_url` varchar(255) DEFAULT NULL,
  `is_parent_hidden` tinyint(1) DEFAULT NULL,
  `category_id` int(11) unsigned NOT NULL DEFAULT '1',
  `folder_id` int(11) unsigned DEFAULT NULL,
  `is_comments_on` tinyint(1) unsigned DEFAULT '1',
  `comments` int(11) NOT NULL DEFAULT '0',
  `rating` int(11) NOT NULL DEFAULT '0',
  `is_deleted` tinyint(1) unsigned DEFAULT NULL,
  `is_approved` tinyint(1) DEFAULT '1',
  `approved_by` int(11) DEFAULT NULL,
  `date_approved` timestamp NULL DEFAULT NULL,
  `is_private` tinyint(1) NOT NULL DEFAULT '0',
  `cover_image` text,
  `photos_count` int(11) NOT NULL DEFAULT '0',
  `is_public` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `folder_id` (`folder_id`),
  KEY `slug` (`slug`),
  KEY `date_pub` (`is_pub`,`is_parent_hidden`,`is_deleted`,`is_approved`,`date_pub`),
  KEY `parent_id` (`parent_id`,`parent_type`,`date_pub`),
  KEY `user_id` (`user_id`,`date_pub`),
  KEY `date_pub_end` (`date_pub_end`),
  FULLTEXT KEY `fulltext_search` (`title`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{#}con_albums_cats`;
CREATE TABLE `{#}con_albums_cats` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) unsigned DEFAULT NULL,
  `title` varchar(200) DEFAULT NULL,
  `description` text NULL DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `slug_key` varchar(255) DEFAULT NULL,
  `seo_keys` varchar(256) DEFAULT NULL,
  `seo_desc` varchar(256) DEFAULT NULL,
  `seo_title` varchar(256) DEFAULT NULL,
  `seo_h1` varchar(256) DEFAULT NULL,
  `ordering` int(11) DEFAULT NULL,
  `ns_left` int(11) DEFAULT NULL,
  `ns_right` int(11) DEFAULT NULL,
  `ns_level` int(11) DEFAULT NULL,
  `ns_differ` varchar(32) NOT NULL DEFAULT '',
  `ns_ignore` tinyint(4) NOT NULL DEFAULT '0',
  `allow_add` text,
  `is_hidden` tinyint(1) UNSIGNED DEFAULT NULL,
  `cover` text,
  PRIMARY KEY (`id`),
  KEY `ordering` (`ordering`),
  KEY `slug` (`slug`),
  KEY `ns_left` (`ns_level`,`ns_right`,`ns_left`),
  KEY `parent_id` (`parent_id`,`ns_left`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `{#}con_albums_cats` (`id`, `parent_id`, `title`, `slug`, `slug_key`, `seo_keys`, `seo_desc`, `seo_title`, `ordering`, `ns_left`, `ns_right`, `ns_level`, `ns_differ`, `ns_ignore`) VALUES
(1, 0, '---', NULL, NULL, NULL, NULL, NULL, 1, 1, 2, 0, '', 0);

DROP TABLE IF EXISTS `{#}con_albums_cats_bind`;
CREATE TABLE `{#}con_albums_cats_bind` (
  `item_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  KEY `item_id` (`item_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{#}con_albums_fields`;
CREATE TABLE `{#}con_albums_fields` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ctype_id` int(11) DEFAULT NULL,
  `name` varchar(40) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `hint` varchar(200) DEFAULT NULL,
  `ordering` int(11) DEFAULT NULL,
  `is_enabled` tinyint(1) UNSIGNED DEFAULT '1',
  `fieldset` varchar(32) DEFAULT NULL,
  `type` varchar(16) DEFAULT NULL,
  `is_in_list` tinyint(1) DEFAULT NULL,
  `is_in_item` tinyint(1) DEFAULT NULL,
  `is_in_filter` tinyint(1) DEFAULT NULL,
  `is_private` tinyint(1) DEFAULT NULL,
  `is_fixed` tinyint(1) DEFAULT NULL,
  `is_fixed_type` tinyint(1) DEFAULT NULL,
  `is_system` tinyint(1) DEFAULT NULL,
  `values` text,
  `options` text,
  `groups_read` text,
  `groups_add` text,
  `groups_edit` text,
  `filter_view` text,
  PRIMARY KEY (`id`),
  KEY `is_enabled` (`is_enabled`,`ordering`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `{#}con_albums_fields` (`id`, `ctype_id`, `name`, `title`, `hint`, `ordering`, `fieldset`, `type`, `is_in_list`, `is_in_item`, `is_in_filter`, `is_private`, `is_fixed`, `is_fixed_type`, `is_system`, `values`, `options`, `groups_read`, `groups_edit`) VALUES
(1, 7, 'title', 'Название альбома', NULL, 1, NULL, 'caption', 1, 1, 1, NULL, 1, 1, 0, NULL, '---\nlabel_in_list: none\nlabel_in_item: none\nis_required: 1\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\n', '---\n- 0\n', '---\n- 0\n'),
(2, 7, 'date_pub', 'Дата публикации', NULL, 2, NULL, 'date', 1, 1, 1, NULL, 1, 1, 1, NULL, '---\nlabel_in_list: none\nlabel_in_item: left\nshow_time: false\n', NULL, NULL),
(3, 7, 'user', 'Автор', NULL, 3, NULL, 'user', 1, 1, 0, NULL, 1, 1, 1, NULL, '---\nlabel_in_list: none\nlabel_in_item: left\n', NULL, NULL),
(4, 7, 'content', 'Описание альбома', NULL, 4, NULL, 'text', 1, 1, NULL, NULL, 1, NULL, NULL, NULL, '---\nmin_length: 0\nmax_length: 2048\nlabel_in_list: none\nlabel_in_item: none\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\n', '---\n- 0\n', '---\n- 0\n'),
(5, 7, 'cover_image', 'Обложка альбома', NULL, 5, NULL, 'image', 1, NULL, NULL, NULL, 1, 1, 1, NULL, '---\nlabel_in_list: left\nlabel_in_item: left\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\n', '---\n- 0\n', '---\n- 0\n'),
(6, 7, 'is_public', 'Общий фотоальбом', 'Другие пользователи тоже смогут добавлять фото в этот альбом', 6, NULL, 'checkbox', 0, 0, NULL, NULL, 1, NULL, NULL, NULL, '---\nlabel_in_list: none\nlabel_in_item: none\n', NULL, NULL);

DROP TABLE IF EXISTS `{#}con_albums_props`;
CREATE TABLE `{#}con_albums_props` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL,
  `fieldset` varchar(32) DEFAULT NULL,
  `type` varchar(16) DEFAULT NULL,
  `is_in_filter` tinyint(1) DEFAULT NULL,
  `values` text,
  `options` text,
  PRIMARY KEY (`id`),
  KEY `is_active` (`is_in_filter`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{#}con_albums_props_bind`;
CREATE TABLE `{#}con_albums_props_bind` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `prop_id` int(11) DEFAULT NULL,
  `cat_id` int(11) DEFAULT NULL,
  `ordering` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `prop_id` (`prop_id`),
  KEY `ordering` (`cat_id`,`ordering`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{#}con_albums_props_values`;
CREATE TABLE `{#}con_albums_props_values` (
  `prop_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  KEY `prop_id` (`prop_id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `{#}perms_users` (`rule_id`, `group_id`, `subject`, `value`) VALUES
(1, 4, 'albums', 'yes'),
(1, 5, 'albums', 'yes'),
(1, 6, 'albums', 'yes'),
(3, 4, 'albums', 'own'),
(3, 5, 'albums', 'all'),
(3, 6, 'albums', 'all'),
(2, 4, 'albums', 'own'),
(2, 5, 'albums', 'all'),
(2, 6, 'albums', 'all'),
(9, 4, 'albums', '1'),
(9, 5, 'albums', '1'),
(9, 6, 'albums', '1'),
(8, 4, 'albums', '1'),
(8, 5, 'albums', '1'),
(8, 6, 'albums', '1'),
(13, 5, 'albums', '1'),
(13, 6, 'albums', '1'),
(1, 3, 'albums', 'yes'),
(3, 3, 'albums', 'own'),
(2, 3, 'albums', 'own');