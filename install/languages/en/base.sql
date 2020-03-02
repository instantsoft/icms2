DROP TABLE IF EXISTS `{#}layout_cols`;
CREATE TABLE `{#}layout_cols` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `row_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'Row id',
  `title` varchar(255) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL COMMENT 'Position title',
  `ordering` int(11) UNSIGNED DEFAULT NULL COMMENT 'Column order in source code',
  `is_body` tinyint(1) UNSIGNED DEFAULT NULL COMMENT 'Display page body',
  `is_breadcrumb` tinyint(1) UNSIGNED DEFAULT NULL COMMENT 'Display breadcrumb',
  `class` varchar(100) DEFAULT NULL COMMENT 'Column CSS class',
  `options` text COMMENT 'Column options',
  PRIMARY KEY (`id`),
  KEY `name` (`name`) USING BTREE,
  KEY `row_id` (`row_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Scheme position columns';

DROP TABLE IF EXISTS `{#}layout_rows`;
CREATE TABLE `{#}layout_rows` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'Parent column ID',
  `title` varchar(255) DEFAULT NULL,
  `template` varchar(30) DEFAULT NULL COMMENT 'Binding to the template',
  `ordering` int(11) DEFAULT NULL COMMENT 'Row order in source code',
  `groups` text COMMENT 'Display Access',
  `nested_position` enum('after','before') DEFAULT NULL COMMENT 'Nested row position',
  `class` varchar(100) DEFAULT NULL COMMENT 'Row CSS class',
  `options` text COMMENT 'Row options',
  PRIMARY KEY (`id`),
  KEY `template` (`template`,`ordering`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Scheme position rows';

DROP TABLE IF EXISTS `{#}jobs`;
CREATE TABLE `{#}jobs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` varchar(100) DEFAULT NULL COMMENT 'Queue Name',
  `payload` text COMMENT 'Job data',
  `last_error` varchar(200) DEFAULT NULL COMMENT 'Last Error',
  `priority` tinyint(1) UNSIGNED DEFAULT '1' COMMENT 'A priority',
  `attempts` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Attempts',
  `is_locked` tinyint(1) UNSIGNED DEFAULT NULL COMMENT 'Lock simultaneous run',
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Queued date',
  `date_started` timestamp NULL DEFAULT NULL COMMENT 'Date of the last attempt to complete the task',
  PRIMARY KEY (`id`),
  KEY `queue` (`queue`),
  KEY `attempts` (`attempts`,`is_locked`,`date_started`,`priority`,`date_created`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Queue';

DROP TABLE IF EXISTS `{#}subscriptions`;
CREATE TABLE `{#}subscriptions` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL,
  `controller` varchar(32) DEFAULT NULL,
  `subject` varchar(50) DEFAULT NULL,
  `subject_url` varchar(255) DEFAULT NULL,
  `params` text,
  `subscribers_count` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `hash` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `hash` (`hash`),
  KEY `target_controller` (`controller`,`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Subscription Lists';

DROP TABLE IF EXISTS `{#}subscriptions_bind`;
CREATE TABLE `{#}subscriptions_bind` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `subscription_id` int(11) UNSIGNED DEFAULT NULL,
  `user_id` int(11) UNSIGNED DEFAULT NULL,
  `guest_email` varchar(100) DEFAULT NULL,
  `guest_name` varchar(50) DEFAULT NULL,
  `is_confirmed` tinyint(1) UNSIGNED DEFAULT '1',
  `confirm_token` varchar(32) DEFAULT NULL,
  `date_pub` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`subscription_id`) USING BTREE,
  KEY `guest_email` (`guest_email`,`subscription_id`) USING BTREE,
  KEY `confirm_token` (`confirm_token`),
  KEY `subscription_id` (`subscription_id`,`is_confirmed`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Subscriptions';

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{#}activity_types`;
CREATE TABLE `{#}activity_types` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `is_enabled` tinyint(1) unsigned DEFAULT '1',
  `controller` varchar(32) NOT NULL,
  `name` varchar(32) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` varchar(200) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `is_enabled` (`is_enabled`),
  KEY `controller` (`controller`),
  KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `{#}activity_types` (`id`, `is_enabled`, `controller`, `name`, `title`, `description`) VALUES
(2, 1, 'comments', 'vote.comment', 'Rating comments', 'evaluate a comment on the %s page'),
(7, 1, 'users', 'friendship', 'Friendship', 'and %s became friends'),
(8, 1, 'users', 'signup', 'New users', 'registered. Welcome!'),
(10, 1, 'groups', 'join', 'Group joining', 'joined the group %s'),
(11, 1, 'groups', 'leave', 'Group leaving', 'left the group %s'),
(12, 1, 'users', 'status', 'Status changing', '&rarr; %s'),
(18, 1, 'photos', 'add.photos', 'Photo uploading', 'uploaded photos to the album %s'),
(19, 1, 'users', 'avatar', 'Avatar changing', 'changed avatar'),
(20, 1, 'content', 'add.pages', 'Adding pages', 'added the page %s'),
(21, 1, 'subscriptions', 'subscribe', 'Subscription to content', 'subscribes to the %s list');

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
  `author_url` varchar(15) DEFAULT NULL COMMENT 'Author ip',
  `content` text COMMENT 'Comment text',
  `content_html` text COMMENT 'Sanitized text',
  `is_deleted` tinyint(1) unsigned DEFAULT NULL COMMENT 'Comment deleted?',
  `is_private` tinyint(1) unsigned DEFAULT '0' COMMENT 'Only for friends?',
  `rating` int(11) NOT NULL DEFAULT '0',
  `is_approved` tinyint(1) unsigned DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `target_id` (`target_id`,`target_controller`,`target_subject`,`ordering`),
  KEY `author_url` (`author_url`),
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

DROP TABLE IF EXISTS `{#}content_datasets`;
CREATE TABLE `{#}content_datasets` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ctype_id` int(11) unsigned DEFAULT NULL COMMENT 'Content typpe ID',
  `name` varchar(32) NOT NULL COMMENT 'Dataset title',
  `title` varchar(100) NOT NULL COMMENT 'Dataset heading',
  `description` text COMMENT 'Dataset description',
  `ordering` int(11) unsigned DEFAULT NULL COMMENT 'Order number',
  `is_visible` tinyint(1) unsigned DEFAULT NULL COMMENT 'Show dataset on site?',
  `filters` text COMMENT 'Dataset filters array',
  `sorting` text COMMENT 'Sorting rules array',
  `index` varchar(40) DEFAULT NULL COMMENT 'Index title',
  `groups_view` text COMMENT 'Show to groups',
  `groups_hide` text COMMENT 'Hide from groups',
  `seo_keys` varchar(256) DEFAULT NULL,
  `seo_desc` varchar(256) DEFAULT NULL,
  `seo_title` varchar(256) DEFAULT NULL,
  `seo_h1` varchar(256) DEFAULT NULL,
  `cats_view` text COMMENT 'Show to cats',
  `cats_hide` text COMMENT 'Hide from cats',
  `max_count` smallint(5) unsigned NOT NULL DEFAULT '0',
  `target_controller` varchar(32) DEFAULT NULL,
  `list` text,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `ctype_id` (`ctype_id`,`ordering`),
  KEY `index` (`index`),
  KEY `target_controller` (`target_controller`,`ordering`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Content type datasets';

DROP TABLE IF EXISTS `{#}content_folders`;
CREATE TABLE `{#}content_folders` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ctype_id` int(11) unsigned DEFAULT NULL,
  `user_id` int(11) unsigned DEFAULT NULL,
  `title` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`ctype_id`,`title`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{#}content_relations`;
CREATE TABLE `{#}content_relations` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(256) DEFAULT NULL,
  `target_controller` varchar(32) NOT NULL DEFAULT 'content',
  `ctype_id` int(11) unsigned DEFAULT NULL,
  `child_ctype_id` int(11) unsigned DEFAULT NULL,
  `layout` varchar(32) DEFAULT NULL,
  `options` text,
  `seo_keys` varchar(256) DEFAULT NULL,
  `seo_desc` varchar(256) DEFAULT NULL,
  `seo_title` varchar(256) DEFAULT NULL,
  `ordering` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `ctype_id` (`ctype_id`,`ordering`),
  KEY `child_ctype_id` (`child_ctype_id`,`target_controller`,`ordering`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{#}content_relations_bind`;
CREATE TABLE `{#}content_relations_bind` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_ctype_id` int(11) unsigned DEFAULT NULL,
  `parent_item_id` int(11) unsigned DEFAULT NULL,
  `child_ctype_id` int(11) unsigned DEFAULT NULL,
  `child_item_id` int(11) unsigned DEFAULT NULL,
  `target_controller` varchar(32) NOT NULL DEFAULT 'content',
  PRIMARY KEY (`id`),
  KEY `parent_ctype_id` (`parent_ctype_id`),
  KEY `child_ctype_id` (`child_ctype_id`),
  KEY `parent_item_id` (`parent_item_id`,`target_controller`),
  KEY `child_item_id` (`child_item_id`,`target_controller`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{#}content_types`;
CREATE TABLE `{#}content_types` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `name` varchar(32) NOT NULL COMMENT 'System name',
  `description` text COMMENT 'Description',
  `ordering` int(11) DEFAULT NULL,
  `is_enabled` tinyint(1) UNSIGNED DEFAULT '1',
  `is_date_range` tinyint(1) unsigned DEFAULT NULL,
  `is_premod_add` tinyint(1) unsigned DEFAULT NULL COMMENT 'Pre-moderate new content?',
  `is_premod_edit` tinyint(1) unsigned DEFAULT NULL COMMENT 'Pre-moderate edited content',
  `is_cats` tinyint(1) unsigned DEFAULT NULL COMMENT 'Enable categories?',
  `is_cats_recursive` tinyint(1) unsigned DEFAULT NULL COMMENT 'End-to-end view of categories?',
  `is_folders` tinyint(1) unsigned DEFAULT NULL,
  `is_in_groups` tinyint(1) unsigned DEFAULT NULL COMMENT 'Create in groups',
  `is_in_groups_only` tinyint(1) unsigned DEFAULT NULL COMMENT 'Create only in groups',
  `is_comments` tinyint(1) unsigned DEFAULT NULL COMMENT 'Comments enabled?',
  `is_comments_tree` tinyint(1) unsigned DEFAULT NULL,
  `is_rating` tinyint(1) unsigned DEFAULT NULL COMMENT 'Allow rating?',
  `is_rating_pos` tinyint(1) unsigned DEFAULT NULL,
  `is_tags` tinyint(1) unsigned DEFAULT NULL,
  `is_auto_keys` tinyint(1) unsigned DEFAULT NULL COMMENT 'Autogeneration of keywords?',
  `is_auto_desc` tinyint(1) unsigned DEFAULT NULL COMMENT 'Autogeneration of description?',
  `is_auto_url` tinyint(1) unsigned DEFAULT NULL COMMENT 'Generate URL from title?',
  `is_fixed_url` tinyint(1) unsigned DEFAULT NULL COMMENT 'Do not change URL when editing an item?',
  `url_pattern` varchar(255) DEFAULT '{id}-{title}',
  `options` text COMMENT 'Options array',
  `labels` text COMMENT 'Labels array',
  `seo_keys` varchar(256) DEFAULT NULL COMMENT 'Keywords',
  `seo_desc` varchar(256) DEFAULT NULL COMMENT 'Description',
  `seo_title` varchar(256) DEFAULT NULL,
  `item_append_html` text,
  `is_fixed` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `ordering` (`ordering`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Content types';

INSERT INTO `{#}content_types` (`id`, `title`, `name`, `description`, `is_date_range`, `is_cats`, `is_cats_recursive`, `is_folders`, `is_in_groups`, `is_in_groups_only`, `is_comments`, `is_comments_tree`, `is_rating`, `is_rating_pos`, `is_tags`, `is_auto_keys`, `is_auto_desc`, `is_auto_url`, `is_fixed_url`, `url_pattern`, `options`, `labels`, `seo_keys`, `seo_desc`, `seo_title`, `item_append_html`, `is_fixed`) VALUES
(1, 'Pages', 'pages', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, '{id}-{title}', '---\nis_cats_change: null\nis_cats_open_root: null\nis_cats_only_last: null\nis_show_cats: null\nis_tags_in_list: null\nis_tags_in_item: null\nis_rss: null\nlist_on: null\nprofile_on: null\nlist_show_filter: null\nlist_expand_filter: null\nlist_style:\nitem_on: 1\nis_cats_keys: null\nis_cats_desc: null\nis_cats_auto_url: 1\nseo_title_pattern:\nseo_keys_pattern: \'{content|string_get_meta_keywords}\'\nseo_desc_pattern: \'{content|string_get_meta_description}\'\n', '---\none: page\ntwo: pages\nmany: pages\ncreate: page\nlist:\nprofile:\n', NULL, NULL, NULL, NULL, 1),
(7, 'Photo albums', 'albums', '<p>User photo albums</p>', NULL, NULL, NULL, NULL, 1, NULL, 1, NULL, 1, NULL, 1, 1, 1, 1, 1, '{id}-{title}', '---\nis_cats_change: null\nis_cats_open_root: null\nis_cats_only_last: null\nis_show_cats: null\nis_tags_in_list: null\nis_tags_in_item: 1\nis_rss: 1\nlist_on: 1\nprofile_on: 1\nlist_show_filter: null\nlist_expand_filter: null\nitem_on: 1\nis_cats_keys: null\nis_cats_desc: null\nis_cats_auto_url: 1\nseo_title_pattern:\nseo_keys_pattern: \'{content|string_get_meta_keywords}\'\nseo_desc_pattern: \'{content|string_get_meta_description}\'\n', '---\none: album\ntwo: albums\nmany: albums\ncreate: album\nlist:\nprofile:\n', NULL, NULL, NULL, NULL, 1);

DROP TABLE IF EXISTS `{#}controllers`;
CREATE TABLE `{#}controllers` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(64) NOT NULL,
  `name` varchar(32) NOT NULL COMMENT 'System name',
  `slug` varchar(64) DEFAULT NULL,
  `is_enabled` tinyint(1) unsigned DEFAULT '1' COMMENT 'Enabled?',
  `options` text COMMENT 'Settings array',
  `author` varchar(128) NOT NULL COMMENT 'Author name',
  `url` varchar(250) DEFAULT NULL COMMENT 'Author site',
  `version` varchar(8) NOT NULL COMMENT 'Version',
  `is_backend` tinyint(1) unsigned DEFAULT NULL COMMENT 'Admin panel?',
  `is_external` tinyint(1) unsigned DEFAULT NULL COMMENT 'Third-party component',
  `files` text COMMENT 'Controller files list (for third-party components)',
  `addon_id` int(11) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Components';

INSERT INTO `{#}controllers` (`id`, `title`, `name`, `is_enabled`, `options`, `author`, `url`, `version`, `is_backend`) VALUES
(1, 'Control Panel', 'admin', 1, '---\ndashboard_order:\n  stat: 0\n  activity: 1\n  news: 2\n  sysinfo: 3\n  resources: 4\n  users_online: 5\n', 'InstantCMS Team', 'https://instantcms.ru', '2.0', 0),
(2, 'Content', 'content', 1, NULL, 'InstantCMS Team', 'https://instantcms.ru', '2.0', 0),
(3, 'User Profiles', 'users', 1, '---\nis_ds_online: 1\nis_ds_rating: 1\nis_ds_popular: 1\nis_filter: 1\nis_auth_only: null\nis_status: 1\nis_wall: 1\nis_themes_on: 1\nmax_tabs: 6\nis_friends_on: 1\nis_karma_comments: 1\nkarma_time: 30\n', 'InstantCMS Team', 'https://instantcms.ru', '2.0', 1),
(4, 'Comments', 'comments', 1, '---\ndisable_icms_comments: null\nis_guests: 1\nguest_ip_delay: 1\nrestricted_ips:\ndim_negative: 1\nupdate_user_rating: 1\nlimit: 20\nseo_keys:\nseo_desc:\nis_guests_moderate: 1\nrestricted_emails:\nrestricted_names:\nlimit_nesting: 5\nshow_author_email: 1\neditor: redactor\neditor_presets: null\nshow_list:\n  - 0\n', 'InstantCMS Team', 'https://instantcms.ru', '2.0', 1),
(5, 'Private messages', 'messages', 1, '---\nlimit: 10\ngroups_allowed:\n  - 0\neditor: markitup\neditor_presets: null\ntime_delete_old: 0\nrealtime_mode: ajax\nrefresh_time: 15\nsocket_host:\nsocket_port: 3000\nuse_queue: null\n', 'InstantCMS Team', 'https://instantcms.ru/', '2.0', 1),
(6, 'Authorization & Registration', 'auth', 1, '---\nis_reg_enabled: 1\nreg_reason: >\n  We apologize, but,\n  we do not accept\n  new users at the moment\nis_reg_invites: null\nreg_captcha: null\nverify_email: null\nverify_exp: 48\nauth_captcha: null\nrestricted_emails: |\n  *@shitmail.me\r\n  *@mailspeed.ru\r\n  *@temp-mail.ru\r\n  *@guerrillamail.com\r\n  *@12minutemail.com\r\n  *@mytempemail.com\r\n  *@spamobox.com\r\n  *@disposableinbox.com\r\n  *@filzmail.com\r\n  *@freemail.ms\r\n  *@anonymbox.com\r\n  *@lroid.com\r\n  *@yopmail.com\r\n  *@TempEmail.net\r\n  *@spambog.com\r\n  *@mailforspam.com\r\n  *@spam.su\r\n  *@no-spam.ws\r\n  *@mailinator.com\r\n  *@spamavert.com\r\n  *@trashcanmail.com\nrestricted_names: |\n  admin*\r\n  moderator\nrestricted_ips:\nis_invites: 1\nis_invites_strict: 1\ninvites_period: 7\ninvites_qty: 3\ninvites_min_karma: 0\ninvites_min_rating: 0\ninvites_min_days: 0\nreg_auto_auth: 1\nfirst_auth_redirect: profileedit\nauth_redirect: none\ndef_groups:\n  - 3\nis_site_only_auth_users: null\nguests_allow_controllers:\n  - auth\n  - geo\nseo_keys:\nseo_desc:\n', 'InstantCMS Team', 'https://instantcms.ru', '2.0', 1),
(7, 'Activity Feed', 'activity', 1, '---\ntypes:\n  - 10\n  - 11\n  - 17\n  - 16\n  - 14\n  - 13\n  - 18\n  - 7\n  - 19\n  - 12\n  - 8\n', 'InstantCMS Team', 'https://instantcms.ru', '2.0', 1),
(8, 'Groups', 'groups', 1, '---\nis_ds_rating: 1\nis_ds_popular: 1\nis_wall: 1\n', 'InstantCMS Team', 'https://instantcms.ru', '2.0', 1),
(10, 'Rating', 'rating', 1, '---\nis_hidden: 1\nis_show: 1\nallow_guest_vote: null\ntemplate: widget\n', 'InstantCMS Team', 'https://instantcms.ru', '2.0', 1),
(11, 'Wall', 'wall', 1, '---\nlimit: 15\norder_by: date_last_reply\nshow_entries: 5\neditor: redactor\neditor_presets: null\n', 'InstantCMS Team', 'https://instantcms.ru', '2.0', 1),
(12, 'reCAPTCHA', 'recaptcha', 1, '---\npublic_key:\nprivate_key:\ntheme: light\nlang: en\nsize: normal\n', 'InstantCMS Team', 'https://instantcms.ru', '2.0', 1),
(13, 'Moderation Panel', 'moderation', 1, NULL, 'InstantCMS Team', 'https://instantcms.ru', '2.0', 1),
(14, 'Tags', 'tags', 1, '---\nordering: frequency\nstyle: cloud\nmax_fs: 22\nmin_fs: 12\nmin_freq: 0\nmin_len: 0\nlimit: 10\ncolors:\nshuffle: 1\nseo_keys:\nseo_desc:\n', 'InstantCMS Team', 'https://instantcms.ru', '2.0', 1),
(15, 'RSS feeds', 'rss', 1, NULL, 'InstantCMS Team', 'https://instantcms.ru', '2.0', 1),
(16, 'Sitemap and robots.txt generator', 'sitemap', 1, '---\nsources:\n  content|pages: 1\n  content|albums: 1\n  content|articles: 1\n  content|posts: 1\n  content|board: 1\n  content|news: 1\n  frontpage|root: 1\n  groups|profiles: 1\n  users|profiles: 1\nshow_lastmod: 1\nshow_changefreq: 1\ndefault_changefreq: daily\nshow_priority: 1\nrobots: |\n  User-agent: *\r\n  Disallow:\ngenerate_html_sitemap: null\nchangefreq:\n  content:\n    pages:\n    albums:\n    articles:\n    posts:\n    board:\n    news:\n  frontpage:\n    root:\n  groups:\n    profiles:\n  users:\n    profiles:\npriority:\n  content:\n    pages:\n    albums:\n    articles:\n    posts:\n    board:\n    news:\n  frontpage:\n    root: 1.0\n  groups:\n    profiles: 0.8\n  users:\n    profiles: 0.8\n', 'InstantCMS Team', 'https://instantcms.ru', '2.0', 1),
(17, 'Search', 'search', 1, '---\nctypes:\n  - articles\n  - posts\n  - albums\n  - board\n  - news\nperpage: 15\n', 'InstantCMS Team', 'https://instantcms.ru', '2.0', 1),
(18, 'Photos', 'photos', 1, '---\nsizes:\n  - normal\n  - small\n  - big\nis_origs: 1\npreset: big\npreset_small: normal\ntypes: |\n  1 | Photo\r\n  2 | Vectors\r\n  3 | Illustrations\nordering: date_pub\norderto: desc\nlimit: 20\ndownload_view:\n  normal:\n    - 0\n  micro:\n    - 0\n  small:\n    - 0\n  big:\n    - 0\n  original:\n    - 0\ndownload_hide:\n  normal: null\n  micro: null\n  small: null\n  big: null\n  original:\n    - 1\n    - 3\n    - 4\nurl_pattern: \'{id}-{title}\'\npreset_related: normal\nrelated_limit: 0\neditor: 1\neditor_presets: null\nseo_keys:\nseo_desc:\n', 'InstantCMS Team', 'https://instantcms.ru', '2.0', 1),
(19, 'Image Upload', 'images', 1, NULL, 'InstantCMS Team', 'https://instantcms.ru', '2.0', 1),
(20, 'Redirects', 'redirect', 1, '---\nno_redirect_list:\nblack_list:\nis_check_link: null\nwhite_list:\nredirect_time: 10\nis_check_refer: null\n', 'InstantCMS Team', 'https://instantcms.ru', '2.0', 1),
(21, 'Geobase', 'geo', 1, '---\nauto_detect: 1\nauto_detect_provider: ipgeobase\ndefault_country_id: null\ndefault_country_id_cache: null\ndefault_region_id: null\ndefault_region_id_cache: null\n', 'InstantCMS Team', 'https://instantcms.ru', '2.0', 1),
(22, 'Subscriptions', 'subscriptions', 1, '---\nguest_email_confirmation: 1\nneed_auth: null\nverify_exp: 24\nupdate_user_rating: 1\nrating_value: 1\nadmin_email:\nlimit: 20\n', 'InstantCMS Team', 'https://instantcms.ru', '2.0', 1),
(23, 'Wysiwyg editors', 'wysiwygs', 1, NULL, 'InstantCMS Team', 'https://instantcms.ru', '2.0', 1);

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
  `is_pub` tinyint(1) DEFAULT '1',
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
  FULLTEXT KEY `title` (`title`)
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
  KEY `ordering` (`ordering`),
  KEY `is_in_list` (`is_in_list`),
  KEY `is_in_item` (`is_in_item`),
  KEY `is_in_filter` (`is_in_filter`),
  KEY `is_private` (`is_private`),
  KEY `is_fixed` (`is_fixed`),
  KEY `is_fixed_type` (`is_fixed_type`),
  KEY `is_system` (`is_system`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `{#}con_albums_fields` (`id`, `ctype_id`, `name`, `title`, `hint`, `ordering`, `fieldset`, `type`, `is_in_list`, `is_in_item`, `is_in_filter`, `is_private`, `is_fixed`, `is_fixed_type`, `is_system`, `values`, `options`, `groups_read`, `groups_edit`) VALUES
(1, 7, 'title', 'Album title', NULL, 1, NULL, 'caption', 1, 1, 1, NULL, 1, 1, 0, NULL, '---\nlabel_in_list: none\nlabel_in_item: none\nis_required: 1\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\nprofile_value:\n', '---\n- 0\n', '---\n- 0\n'),
(2, 7, 'date_pub', 'Publication date', NULL, 2, NULL, 'date', 1, 1, 1, NULL, 1, 1, 1, NULL, '---\nlabel_in_list: none\nlabel_in_item: left\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\nprofile_value:\n', '---\n- 0\n', '---\n- 0\n'),
(3, 7, 'user', 'Author', NULL, 3, NULL, 'user', 1, 1, NULL, NULL, 1, 1, 1, NULL, '---\nlabel_in_list: none\nlabel_in_item: left\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\nprofile_value:\n', '---\n- 0\n', '---\n- 0\n'),
(4, 7, 'content', 'Album description', NULL, 4, NULL, 'text', 1, 1, NULL, NULL, 1, NULL, NULL, NULL, '---\nmin_length: 0\nmax_length: 2048\nis_html_filter: null\nlabel_in_list: none\nlabel_in_item: none\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\nprofile_value:\n', '---\n- 0\n', '---\n- 0\n'),
(5, 7, 'cover_image', 'Album cover image', NULL, 5, NULL, 'image', 1, NULL, NULL, NULL, 1, 1, 1, NULL, '---\nlabel_in_list: left\nlabel_in_item: left\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\nprofile_value:\n', '---\n- 0\n', '---\n- 0\n'),
(6, 7, 'is_public', 'Public photo album', 'Other users will be able to upload photos in this album', 6, NULL, 'checkbox', 0, 0, NULL, NULL, 1, NULL, NULL, NULL, '---\nlabel_in_list: none\nlabel_in_item: none\n', NULL, NULL);

DROP TABLE IF EXISTS `{#}con_albums_props`;
CREATE TABLE `{#}con_albums_props` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ctype_id` int(11) DEFAULT NULL,
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

DROP TABLE IF EXISTS `{#}con_pages`;
CREATE TABLE `{#}con_pages` (
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
  `is_pub` tinyint(1) DEFAULT '1',
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
  `attach` text,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `folder_id` (`folder_id`),
  KEY `slug` (`slug`),
  KEY `date_pub` (`is_pub`,`is_parent_hidden`,`is_deleted`,`is_approved`,`date_pub`),
  KEY `parent_id` (`parent_id`,`parent_type`,`date_pub`),
  KEY `user_id` (`user_id`,`date_pub`),
  KEY `date_pub_end` (`date_pub_end`),
  FULLTEXT KEY `title` (`title`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{#}con_pages_cats`;
CREATE TABLE `{#}con_pages_cats` (
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

INSERT INTO `{#}con_pages_cats` (`id`, `parent_id`, `title`, `slug`, `slug_key`, `seo_keys`, `seo_desc`, `seo_title`, `ordering`, `ns_left`, `ns_right`, `ns_level`, `ns_differ`, `ns_ignore`) VALUES
(1, 0, '---', NULL, NULL, NULL, NULL, NULL, 1, 1, 2, 0, '', 0);

DROP TABLE IF EXISTS `{#}con_pages_cats_bind`;
CREATE TABLE `{#}con_pages_cats_bind` (
  `item_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  KEY `item_id` (`item_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{#}con_pages_fields`;
CREATE TABLE `{#}con_pages_fields` (
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
  KEY `ordering` (`ordering`),
  KEY `is_in_list` (`is_in_list`),
  KEY `is_in_item` (`is_in_item`),
  KEY `is_in_filter` (`is_in_filter`),
  KEY `is_private` (`is_private`),
  KEY `is_fixed` (`is_fixed`),
  KEY `is_system` (`is_system`),
  KEY `is_fixed_type` (`is_fixed_type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `{#}con_pages_fields` (`id`, `ctype_id`, `name`, `title`, `hint`, `ordering`, `fieldset`, `type`, `is_in_list`, `is_in_item`, `is_in_filter`, `is_private`, `is_fixed`, `is_fixed_type`, `is_system`, `values`, `options`, `groups_read`, `groups_edit`) VALUES
(1, 1, 'title', 'Title', NULL, 1, NULL, 'caption', 1, 1, 1, NULL, 1, 1, NULL, NULL, '---\nlabel_in_list: none\nlabel_in_item: none\nis_required: 1\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\nprofile_value:\n', '---\n- 0\n', '---\n- 0\n'),
(2, 1, 'date_pub', 'Publication date', NULL, 2, NULL, 'date', NULL, NULL, NULL, NULL, 1, NULL, 1, NULL, '---\nlabel_in_list: none\nlabel_in_item: left\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\nprofile_value:\n', '---\n- 0\n', '---\n- 0\n'),
(3, 1, 'user', 'Author', NULL, 3, NULL, 'user', NULL, NULL, NULL, NULL, 1, NULL, 1, NULL, '---\nlabel_in_list: none\nlabel_in_item: left\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\nprofile_value:\n', '---\n- 0\n', '---\n- 0\n'),
(4, 1, 'content', 'Page content', NULL, 4, NULL, 'html', NULL, 1, NULL, NULL, 1, NULL, NULL, NULL, '---\neditor: redactor\nis_html_filter: null\nteaser_len:\nlabel_in_list: none\nlabel_in_item: none\nis_required: 1\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\nprofile_value:\n', '---\n- 0\n', '---\n- 0\n'),
(5, 1, 'attach', 'Download', 'Attach a file to the page', 5, NULL, 'file', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, '---\nshow_name: 0\nextensions: jpg, gif, png\nmax_size_mb: 2\nshow_size: 1\nlabel_in_list: none\nlabel_in_item: none\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\nprofile_value:\n', '---\n- 0\n', '---\n- 0\n');

DROP TABLE IF EXISTS `{#}con_pages_props`;
CREATE TABLE `{#}con_pages_props` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ctype_id` int(11) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `fieldset` varchar(32) DEFAULT NULL,
  `type` varchar(16) DEFAULT NULL,
  `is_in_filter` tinyint(1) DEFAULT NULL,
  `values` text,
  `options` text,
  PRIMARY KEY (`id`),
  KEY `is_active` (`is_in_filter`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{#}con_pages_props_bind`;
CREATE TABLE `{#}con_pages_props_bind` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `prop_id` int(11) DEFAULT NULL,
  `cat_id` int(11) DEFAULT NULL,
  `ordering` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `prop_id` (`prop_id`),
  KEY `ordering` (`cat_id`,`ordering`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{#}con_pages_props_values`;
CREATE TABLE `{#}con_pages_props_values` (
  `prop_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  KEY `prop_id` (`prop_id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{#}events`;
CREATE TABLE `{#}events` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `event` varchar(64) DEFAULT NULL COMMENT 'Event',
  `listener` varchar(32) DEFAULT NULL COMMENT 'Listener (component)',
  `ordering` int(5) unsigned DEFAULT NULL COMMENT 'Order number',
  `is_enabled` tinyint(1) unsigned DEFAULT '1' COMMENT 'Activity`',
  PRIMARY KEY (`id`),
  KEY `hook` (`event`),
  KEY `listener` (`listener`),
  KEY `is_enabled` (`is_enabled`,`ordering`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Binding hooks to events';

INSERT INTO `{#}events` (`id`, `event`, `listener`, `ordering`, `is_enabled`) VALUES
(1, 'content_after_add_approve', 'activity', 1, 1),
(2, 'content_after_update_approve', 'activity', 2, 1),
(3, 'publish_delayed_content', 'activity', 3, 1),
(4, 'user_delete', 'activity', 4, 1),
(5, 'user_tab_info', 'activity', 5, 1),
(6, 'user_tab_show', 'activity', 6, 1),
(7, 'menu_admin', 'admin', 7, 1),
(8, 'user_login', 'admin', 8, 1),
(9, 'admin_confirm_login', 'admin', 9, 1),
(10, 'user_profile_update', 'auth', 10, 1),
(11, 'frontpage', 'auth', 11, 1),
(12, 'page_is_allowed', 'auth', 12, 1),
(13, 'frontpage_types', 'auth', 13, 1),
(14, 'content_after_update', 'comments', 14, 1),
(16, 'admin_dashboard_chart', 'comments', 16, 1),
(17, 'user_privacy_types', 'comments', 17, 1),
(18, 'user_login', 'comments', 18, 1),
(19, 'user_notify_types', 'comments', 19, 1),
(20, 'user_delete', 'comments', 20, 1),
(21, 'user_tab_info', 'comments', 21, 1),
(22, 'user_tab_show', 'comments', 22, 1),
(23, 'fulltext_search', 'content', 23, 1),
(24, 'admin_dashboard_chart', 'content', 24, 1),
(25, 'menu_content', 'content', 25, 1),
(26, 'user_delete', 'content', 26, 1),
(27, 'user_privacy_types', 'content', 27, 1),
(28, 'sitemap_sources', 'content', 28, 1),
(29, 'rss_feed_list', 'content', 29, 1),
(30, 'rss_content_controller_form', 'content', 30, 1),
(31, 'rss_content_controller_after_update', 'content', 31, 1),
(32, 'frontpage', 'content', 32, 1),
(33, 'frontpage_types', 'content', 33, 1),
(34, 'ctype_relation_childs', 'content', 34, 1),
(35, 'admin_content_dataset_fields_list', 'content', 35, 1),
(36, 'moderation_list', 'content', 36, 1),
(37, 'ctype_lists_context', 'content', 37, 1),
(38, 'ctype_after_update', 'frontpage', 38, 1),
(39, 'ctype_after_delete', 'frontpage', 39, 1),
(40, 'admin_dashboard_chart', 'groups', 40, 1),
(41, 'content_view_hidden', 'groups', 41, 1),
(42, 'content_before_list', 'groups', 42, 1),
(43, 'rating_vote', 'groups', 43, 1),
(44, 'user_privacy_types', 'groups', 44, 1),
(45, 'user_profile_buttons', 'groups', 45, 1),
(46, 'user_notify_types', 'groups', 46, 1),
(47, 'user_delete', 'groups', 47, 1),
(48, 'user_tab_info', 'groups', 48, 1),
(49, 'user_tab_show', 'groups', 49, 1),
(50, 'menu_groups', 'groups', 50, 1),
(51, 'sitemap_sources', 'groups', 51, 1),
(52, 'sitemap_urls', 'groups', 52, 1),
(53, 'content_privacy_types', 'groups', 53, 1),
(54, 'content_add_permissions', 'groups', 54, 1),
(55, 'fulltext_search', 'groups', 55, 1),
(56, 'content_before_childs', 'groups', 56, 1),
(57, 'ctype_relation_childs', 'groups', 57, 1),
(58, 'admin_groups_dataset_fields_list', 'groups', 58, 1),
(59, 'content_validate', 'groups', 59, 1),
(60, 'moderation_list', 'groups', 60, 1),
(61, 'content_before_item', 'groups', 61, 1),
(62, 'user_delete', 'images', 62, 1),
(63, 'admin_dashboard_chart', 'messages', 63, 1),
(64, 'menu_messages', 'messages', 64, 1),
(65, 'users_profile_view', 'messages', 65, 1),
(66, 'user_privacy_types', 'messages', 66, 1),
(67, 'user_delete', 'messages', 67, 1),
(68, 'user_notify_types', 'messages', 68, 1),
(69, 'admin_dashboard_block', 'moderation', 69, 1),
(70, 'content_after_trash_put', 'moderation', 70, 1),
(71, 'content_after_restore', 'moderation', 71, 1),
(72, 'content_before_delete', 'moderation', 72, 1),
(73, 'menu_moderation', 'moderation', 73, 1),
(74, 'content_albums_items_html', 'photos', 74, 1),
(75, 'fulltext_search', 'photos', 75, 1),
(76, 'admin_albums_ctype_menu', 'photos', 76, 1),
(77, 'content_albums_after_add', 'photos', 77, 1),
(78, 'content_albums_after_delete', 'photos', 78, 1),
(79, 'content_albums_item_html', 'photos', 79, 1),
(80, 'content_albums_before_item', 'photos', 80, 1),
(81, 'content_albums_before_list', 'photos', 81, 1),
(82, 'user_delete', 'photos', 82, 1),
(83, 'user_delete', 'rating', 83, 1),
(84, 'content_before_list', 'rating', 84, 1),
(85, 'captcha_html', 'recaptcha', 85, 1),
(86, 'captcha_validate', 'recaptcha', 86, 1),
(87, 'ctype_basic_form', 'rss', 87, 1),
(88, 'ctype_before_add', 'rss', 88, 1),
(89, 'ctype_after_add', 'rss', 89, 1),
(90, 'ctype_before_edit', 'rss', 90, 1),
(91, 'ctype_before_update', 'rss', 91, 1),
(92, 'ctype_after_delete', 'rss', 92, 1),
(93, 'content_before_category', 'rss', 93, 1),
(94, 'content_before_profile', 'rss', 94, 1),
(95, 'photos_before_item', 'search', 95, 1),
(96, 'content_before_list', 'search', 96, 1),
(97, 'content_before_item', 'search', 97, 1),
(98, 'before_print_head', 'search', 98, 1),
(99, 'html_filter', 'typograph', 99, 1),
(100, 'admin_dashboard_chart', 'users', 100, 1),
(101, 'menu_users', 'users', 101, 1),
(102, 'rating_vote', 'users', 102, 1),
(103, 'user_notify_types', 'users', 103, 1),
(104, 'user_privacy_types', 'users', 104, 1),
(105, 'user_tab_info', 'users', 105, 1),
(106, 'auth_login', 'users', 106, 1),
(107, 'user_loaded', 'users', 107, 1),
(108, 'wall_permissions', 'users', 108, 1),
(109, 'wall_after_add', 'users', 109, 1),
(110, 'wall_after_delete', 'users', 110, 1),
(111, 'content_privacy_types', 'users', 111, 1),
(112, 'content_view_hidden', 'users', 112, 1),
(113, 'sitemap_sources', 'users', 113, 1),
(114, 'content_before_childs', 'users', 114, 1),
(115, 'ctype_relation_childs', 'users', 115, 1),
(116, 'admin_dashboard_chart', 'wall', 116, 1),
(117, 'user_notify_types', 'wall', 117, 1),
(118, 'user_delete', 'wall', 118, 1),
(119, 'page_is_allowed', 'widgets', 119, 1),
(120, 'ctype_lists_context', 'groups', 120, 1),
(121, 'ctype_lists_context', 'tags', 121, 1),
(122, 'moderation_list', 'comments', 122, 1),
(123, 'content_groups_before_delete', 'moderation', 123, 1),
(124, 'comments_after_refuse', 'moderation', 124, 1),
(125, 'subscribe', 'activity', 125, 1),
(126, 'unsubscribe', 'activity', 126, 1),
(127, 'admin_subscriptions_list', 'content', 127, 1),
(128, 'admin_subscriptions_list', 'photos', 128, 1),
(129, 'user_delete', 'subscriptions', 129, 1),
(130, 'content_toolbar_html', 'subscriptions', 130, 1),
(131, 'photos_toolbar_html', 'subscriptions', 131, 1),
(132, 'content_filter_buttons_html', 'subscriptions', 132, 1),
(133, 'user_tab_info', 'subscriptions', 133, 1),
(134, 'content_photos_after_add', 'subscriptions', 134, 1),
(135, 'user_notify_types', 'subscriptions', 135, 1),
(136, 'user_tab_show', 'subscriptions', 136, 1),
(137, 'content_after_add_approve', 'subscriptions', 137, 1),
(138, 'publish_delayed_content', 'subscriptions', 138, 1),
(139, 'ctype_basic_form', 'subscriptions', 139, 1),
(140, 'admin_dashboard_block', 'users', 140, 1),
(141, 'engine_start', 'sitemap', 141, 1),
(142, 'sitemap_sources', 'frontpage', 142, 1),
(143, 'sitemap_sources', 'photos', 143, 1),
(144, 'ctype_basic_form', 'tags', 144, 1),
(145, 'content_after_add', 'tags', 145, 1),
(146, 'content_before_update', 'tags', 146, 1),
(147, 'content_item_form', 'tags', 147, 1),
(148, 'content_before_item', 'tags', 148, 1),
(149, 'content_before_list', 'tags', 149, 1),
(150, 'tags_search_subjects', 'content', 150, 1),
(151, 'images_before_upload', 'typograph', 151, 1),
(152, 'engine_start', 'content', 152, 1),
(153, 'content_category_after_update', 'subscriptions', 153, 1),
(154, 'grid_admin_content_items_args', 'admin', 154, 1),
(155, 'user_notify_types', 'rating', 155, 1),
(156, 'content_before_item', 'comments', 156, 1),
(157, 'content_before_item', 'rating', 157, 1),
(158, 'content_item_form', 'comments', 158, 1),
(159, 'ctype_basic_form', 'comments', 159, 1),
(160, 'ctype_basic_form', 'rating', 160, 1),
(161, 'ctype_basic_form', 'groups', 161, 1),
(162, 'photos_before_item', 'rating', 162, 1),
(163, 'photos_before_item', 'comments', 163, 1),
(164, 'comments_targets', 'content', 164, 1),
(165, 'comments_targets', 'photos', 165, 1),
(166, 'content_before_list', 'comments', 166, 1),
(167, 'admin_dashboard_block', 'admin', 167, 1),
(168, 'admin_dashboard_block', 'activity', 168, 1),
(169, 'user_notify_types', 'content', 169, 1),
(170, 'form_users_password_2fa', 'authga', 170, 1),
(171, 'controller_auth_after_save_options', 'authga', 171, 1),
(172, 'form_users_password', 'auth', 172, 1),
(173, 'auth_twofactor_list', 'authga', 173, 1),
(174, 'users_before_edit_password', 'authga', 174, 1),
(175, 'admin_inline_save_subscriptions', 'activity', 175, 1),
(176, 'admin_col_scheme_options_modern', 'bootstrap4', 176, 1),
(177, 'template_modern_before_save_options', 'bootstrap4', 177, 1),
(178, 'admin_row_scheme_options_modern', 'bootstrap4', 178, 1);

DROP TABLE IF EXISTS `{#}groups`;
CREATE TABLE `{#}groups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `owner_id` int(11) unsigned DEFAULT NULL COMMENT 'Owner',
  `date_pub` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Creation date',
  `title` varchar(128) NOT NULL COMMENT 'Title',
  `description` text COMMENT 'Description',
  `logo` text COMMENT 'Group logo',
  `rating` int(11) NOT NULL DEFAULT '0' COMMENT 'Rating',
  `members_count` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Number of members',
  `join_policy` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Join policy',
  `edit_policy` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Edit policy',
  `wall_policy` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Wall policy',
  `wall_reply_policy` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Wall reply policy',
  `is_closed` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Private?',
  `cover` text COMMENT 'Group cover',
  `slug` varchar(100) DEFAULT NULL,
  `content_policy` varchar(500) DEFAULT NULL COMMENT 'Group content policy',
  `content_groups` varchar(1000) DEFAULT NULL COMMENT 'Users groups that are allowed to add content',
  `roles` varchar(2000) DEFAULT NULL,
  `content_roles` varchar(1000) DEFAULT NULL,
  `join_roles` varchar(1000) DEFAULT NULL COMMENT 'Roles when you join a group',
  `is_approved` tinyint(1) NOT NULL DEFAULT '1',
  `approved_by` int(11) DEFAULT NULL,
  `date_approved` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `members_count` (`members_count`),
  KEY `date_pub` (`date_pub`),
  KEY `rating` (`rating`),
  KEY `owner_id` (`owner_id`,`members_count`),
  KEY `slug` (`slug`),
  FULLTEXT KEY `title` (`title`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Groups (communities)';

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
  KEY `ordering` (`ordering`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Groups fields';

INSERT INTO `{#}groups_fields` (`id`, `ctype_id`, `name`, `title`, `hint`, `ordering`, `fieldset`, `type`, `is_in_list`, `is_in_item`, `is_in_filter`, `is_in_closed`, `is_private`, `is_fixed`, `is_fixed_type`, `is_system`, `values`, `options`, `groups_read`, `groups_edit`, `filter_view`) VALUES
(1, NULL, 'title', 'Title', NULL, 1, 'Summary', 'caption', 1, 1, 1, 1, NULL, 1, 1, 1, NULL, '---\nmin_length: 1\nmax_length: 128\nin_fulltext_search: 1\nprofile_value:\n', '---\n- 0\n', '---\n- 0\n', '---\n- 0\n'),
(2, NULL, 'description', 'Group description', NULL, 2, 'Summary', 'html', 1, 1, NULL, 1, NULL, 1, 1, NULL, NULL, '---\neditor: redactor\nis_html_filter: 1\nbuild_redirect_link: 1\nteaser_len: 200\nin_fulltext_search: null\nlabel_in_list: none\nlabel_in_item: none\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\nprofile_value:\n', '---\n- 0\n', '---\n- 0\n', '---\n- 0\n'),
(3, NULL, 'logo', 'Group logo', NULL, 3, 'Summary', 'image', 1, 1, NULL, 1, NULL, 1, 1, 1, NULL, '---\nsize_teaser: small\nsize_full: micro\nsize_modal:\nsizes:\n  - micro\n  - small\nallow_import_link: 1\nprofile_value:\n', '---\n- 0\n', '---\n- 0\n', '---\n- 0\n'),
(5, NULL, 'cover', 'Group cover', NULL, 4, 'Summary', 'image', NULL, 1, NULL, 1, NULL, 1, 1, 1, NULL, '---\nsize_teaser: small\nsize_full: original\nsize_modal:\nsizes:\n  - small\n  - original\nallow_import_link: 1\nprofile_value:\n', '---\n- 0\n', '---\n- 0\n', '---\n- 0\n');

DROP TABLE IF EXISTS `{#}groups_invites`;
CREATE TABLE `{#}groups_invites` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(11) unsigned DEFAULT NULL COMMENT 'Group ID',
  `user_id` int(11) unsigned DEFAULT NULL COMMENT 'Inviter ID',
  `invited_id` int(11) unsigned DEFAULT NULL COMMENT 'Invited user ID',
  PRIMARY KEY (`id`),
  KEY `group_id` (`group_id`),
  KEY `user_id` (`user_id`),
  KEY `invited_id` (`invited_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Invitations to groups';

DROP TABLE IF EXISTS `{#}groups_members`;
CREATE TABLE `{#}groups_members` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(11) unsigned DEFAULT NULL,
  `user_id` int(11) unsigned DEFAULT NULL,
  `role` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT 'User role in a group',
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Role update date',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `group_id` (`group_id`,`date_updated`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Group (community) members';

DROP TABLE IF EXISTS `{#}groups_member_roles`;
CREATE TABLE `{#}groups_member_roles` (
  `user_id` int(11) unsigned DEFAULT NULL,
  `group_id` int(11) unsigned DEFAULT NULL,
  `role_id` tinyint(1) unsigned NOT NULL DEFAULT '0',
  KEY `user_id` (`user_id`),
  KEY `group_id` (`group_id`,`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Roles of group members';

DROP TABLE IF EXISTS `{#}images_presets`;
CREATE TABLE `{#}images_presets` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) DEFAULT NULL,
  `title` varchar(128) DEFAULT NULL,
  `width` int(11) unsigned DEFAULT NULL,
  `height` int(11) unsigned DEFAULT NULL,
  `is_square` tinyint(1) unsigned DEFAULT NULL,
  `is_watermark` tinyint(1) unsigned DEFAULT NULL,
  `wm_image` text,
  `wm_origin` varchar(16) DEFAULT NULL,
  `wm_margin` int(11) unsigned DEFAULT NULL,
  `is_internal` tinyint(1) unsigned DEFAULT NULL,
  `quality` tinyint(1) unsigned DEFAULT '90',
  `gamma_correct` tinyint(1) unsigned DEFAULT NULL,
  `crop_position` tinyint(1) unsigned DEFAULT '2',
  `allow_enlarge` tinyint(1) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Presets for image conversion';

INSERT INTO `{#}images_presets` (`id`, `name`, `title`, `width`, `height`, `is_square`, `is_watermark`, `wm_image`, `wm_origin`, `wm_margin`, `is_internal`, `quality`) VALUES
(1, 'micro', 'Micro', 32, 32, 1, NULL, NULL, NULL, NULL, NULL, 75),
(2, 'small', 'Small', 64, 64, 1, NULL, NULL, NULL, NULL, NULL, 80),
(3, 'normal', 'Medium', NULL, 256, NULL, NULL, NULL, NULL, NULL, NULL, 85),
(4, 'big', 'Big', 690, 690, NULL, NULL, NULL, 'bottom-right', NULL, NULL, 90),
(5, 'wysiwyg_markitup', 'Editor: markItUp!', 400, 400, NULL, NULL, NULL, 'top-left', NULL, 1, 85),
(6, 'wysiwyg_redactor', 'Editor: Redactor', 800, 800, NULL, NULL, NULL, 'top-left', NULL, 1, 90);

DROP TABLE IF EXISTS `{#}menu`;
CREATE TABLE `{#}menu` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL COMMENT 'System name',
  `title` varchar(64) DEFAULT NULL COMMENT 'Menu title',
  `is_fixed` tinyint(1) unsigned DEFAULT NULL COMMENT 'Forbidden to delete?',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Site menu';

INSERT INTO `{#}menu` (`id`, `name`, `title`, `is_fixed`) VALUES
(1, 'main', 'Main menu', 1),
(2, 'personal', 'Personal Menu', 1),
(4, 'toolbar', 'Actions menu', 1),
(5, 'header', 'Header menu', NULL),
(6, 'notices', 'Notifications', NULL);

DROP TABLE IF EXISTS `{#}menu_items`;
CREATE TABLE `{#}menu_items` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `menu_id` int(11) unsigned DEFAULT NULL COMMENT 'Menu ID',
  `parent_id` int(11) unsigned DEFAULT '0' COMMENT 'Parent item ID',
  `is_enabled` tinyint(1) UNSIGNED DEFAULT '1' COMMENT 'Is enabled?',
  `title` varchar(64) DEFAULT NULL COMMENT 'Item title',
  `url` varchar(255) DEFAULT NULL COMMENT 'URL',
  `ordering` int(11) unsigned DEFAULT NULL COMMENT 'Order number',
  `options` text COMMENT 'Options array',
  `groups_view` text COMMENT 'Allowed user groups array',
  `groups_hide` text COMMENT 'Disallowed user groups array',
  PRIMARY KEY (`id`),
  KEY `menu_id` (`menu_id`),
  KEY `parent_id` (`parent_id`),
  KEY `ordering` (`ordering`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Menu items';

INSERT INTO `{#}menu_items` (`id`, `menu_id`, `parent_id`, `title`, `url`, `ordering`, `options`, `groups_view`, `groups_hide`) VALUES
(6, 1, 0, 'Photos', 'albums', 4, '---\ntarget: _self\nclass:', '---\n- 0\n', NULL),
(13, 2, 0, 'My profile', 'users/{user.id}', 1, '---\ntarget: _self\nclass: profile', '---\n- 0\n', NULL),
(14, 2, 0, 'My messages', '{messages:view}', 2, '---\ntarget: _self\nclass: messages messages-counter ajax-modal', '---\n- 0\n', NULL),
(24, 2, 0, 'Add', '{content:add}', 6, '---\ntarget: _self\nclass: add', '---\n- 0\n', NULL),
(25, 2, 0, 'Control panel', '{admin:menu}', 7, '---\ntarget: _self\nclass: cpanel', '---\n- 6\n', NULL),
(29, 1, 0, 'Users', 'users', 8, '---\ntarget: _self\nclass:', '---\n- 0\n', NULL),
(30, 6, 0, 'Notifications', '{messages:notices}', 1, '---\ntarget: _self\nclass: bell ajax-modal notices-counter\n', '---\n- 0\n', '---\n- 1\n'),
(31, 1, 0, 'Activity', 'activity', 6, '---\ntarget: _self\nclass:', '---\n- 0\n', NULL),
(32, 1, 0, 'Groups', 'groups', 5, '---\ntarget: _self\nclass:', '---\n- 0\n', NULL),
(33, 2, 0, 'My groups', '{groups:my}', 5, '---\ntarget: _self\nclass: group', '---\n- 0\n', NULL),
(34, 5, 0, 'Log in', 'auth/login', 9, '---\ntarget: _self\nclass: ajax-modal key', '---\n- 1\n', NULL),
(35, 5, 0, 'Sign up', 'auth/register', 10, '---\ntarget: _self\nclass: user_add', '---\n- 1\n', NULL),
(36, 2, 0, 'Drafts', '{moderation:draft}', 4, '---\ntarget: _self\nclass: draft\n', '---\n- 0\n', NULL),
(37, 2, 0, 'Moderation', '{moderation:panel}', 4, '---\ntarget: _self\nclass: checklist', '---\n- 5\n- 6\n', NULL),
(41, 2, 0, 'On moderation', '{moderation:user_panel}', 4, '---\ntarget: _self\nclass: onchecklist\n', '---\n- 0\n', NULL),
(38, 1, 0, 'Comments', 'comments', 7, '---\ntarget: _self\nclass:', '---\n- 0\n', NULL),
(43, 2, 0, 'Log out', 'auth/logout', 12, '---\ntarget: _self\nclass: logout', '---\n- 0\n', NULL);

DROP TABLE IF EXISTS `{#}moderators`;
CREATE TABLE `{#}moderators` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned DEFAULT NULL,
  `date_assigned` timestamp NULL DEFAULT NULL,
  `ctype_name` varchar(32) DEFAULT NULL,
  `count_approved` int(11) unsigned NOT NULL DEFAULT '0',
  `count_deleted` int(11) unsigned NOT NULL DEFAULT '0',
  `count_idle` int(11) unsigned NOT NULL DEFAULT '0',
  `trash_left_time` int(5) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `ctype_name` (`ctype_name`),
  KEY `count_idle` (`count_idle`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{#}moderators_tasks`;
CREATE TABLE `{#}moderators_tasks` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `moderator_id` int(11) unsigned DEFAULT NULL,
  `author_id` int(11) unsigned DEFAULT NULL,
  `item_id` int(11) unsigned DEFAULT NULL,
  `ctype_name` varchar(32) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `url` varchar(256) DEFAULT NULL,
  `date_pub` timestamp NULL DEFAULT NULL,
  `is_new_item` tinyint(1) unsigned DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `moderator_id` (`moderator_id`),
  KEY `author_id` (`author_id`),
  KEY `ctype_name` (`ctype_name`),
  KEY `date_pub` (`date_pub`),
  KEY `item_id` (`item_id`),
  KEY `is_new` (`is_new_item`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{#}moderators_logs`;
CREATE TABLE `{#}moderators_logs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `moderator_id` int(11) unsigned DEFAULT NULL,
  `author_id` int(11) unsigned DEFAULT NULL,
  `action` tinyint(1) unsigned DEFAULT NULL,
  `date_pub` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_expired` timestamp NULL DEFAULT NULL,
  `target_id` int(11) unsigned DEFAULT NULL,
  `target_controller` varchar(32) DEFAULT NULL,
  `target_subject` varchar(32) DEFAULT NULL,
  `data` text,
  PRIMARY KEY (`id`),
  KEY `moderator_id` (`moderator_id`),
  KEY `target_id` (`target_id`,`target_subject`,`target_controller`),
  KEY `author_id` (`author_id`),
  KEY `date_expired` (`date_expired`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{#}perms_rules`;
CREATE TABLE `{#}perms_rules` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `controller` varchar(32) DEFAULT NULL COMMENT 'Component (owner)',
  `name` varchar(32) NOT NULL COMMENT 'Rule title',
  `type` enum('flag','list','number') NOT NULL DEFAULT 'flag' COMMENT 'Selection type (flag,list...)',
  `options` varchar(128) DEFAULT NULL COMMENT 'An array of possible values',
  `show_for_guest_group` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `controller` (`controller`),
  KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='A list of all available permission rules';

INSERT INTO `{#}perms_rules` (`id`, `controller`, `name`, `type`, `options`) VALUES
(1, 'content', 'add', 'list', 'premod,yes'),
(2, 'content', 'edit', 'list', 'premod_own,own,premod_all,all'),
(3, 'content', 'delete', 'list', 'own,all'),
(4, 'content', 'add_cat', 'flag', NULL),
(5, 'content', 'edit_cat', 'flag', NULL),
(6, 'content', 'delete_cat', 'flag', NULL),
(8, 'content', 'rate', 'flag', NULL),
(9, 'content', 'privacy', 'flag', NULL),
(10, 'comments', 'add', 'flag', NULL),
(11, 'comments', 'edit', 'list', 'own,all'),
(12, 'comments', 'delete', 'list', 'own,all,full_delete'),
(13, 'content', 'view_all', 'flag', NULL),
(14, 'comments', 'view_all', 'flag', NULL),
(15, 'groups', 'add', 'list', 'premod,yes'),
(16, 'groups', 'edit', 'list', 'own,all'),
(17, 'groups', 'delete', 'list', 'own,all'),
(18, 'content', 'limit', 'number', NULL),
(19, 'users', 'vote_karma', 'flag', NULL),
(20, 'comments', 'rate', 'flag', NULL),
(21, 'comments', 'karma', 'number', NULL),
(22, 'content', 'karma', 'number', NULL),
(23, 'activity', 'delete', 'flag', NULL),
(24, 'content', 'pub_late', 'flag', NULL),
(25, 'content', 'pub_long', 'list', 'days,any'),
(26, 'content', 'pub_max_days', 'number', NULL),
(27, 'content', 'pub_max_ext', 'flag', NULL),
(28, 'content', 'pub_on', 'flag', NULL),
(29, 'content', 'disable_comments', 'flag', NULL),
(30, 'comments', 'add_approved', 'flag', NULL),
(32, 'content', 'add_to_parent', 'list', 'to_own,to_other,to_all'),
(33,  'content',  'bind_to_parent',  'list',  'own_to_own,own_to_other,own_to_all,other_to_own,other_to_other,other_to_all,all_to_own,all_to_other,all_to_all'),
(34, 'content',  'bind_off_parent',  'list',  'own,all'),
(35, 'content', 'move_to_trash', 'list', 'own,all'),
(36, 'content', 'restore', 'list', 'own,all'),
(37, 'content', 'trash_left_time', 'number', NULL),
(38, 'users', 'delete', 'list', 'my,anyuser'),
(39, 'groups', 'invite_users', 'flag', NULL),
(40, 'groups', 'bind_to_parent', 'list', 'own_to_own,own_to_other,own_to_all,other_to_own,other_to_other,other_to_all,all_to_own,all_to_other,all_to_all'),
(41, 'users', 'bind_to_parent', 'list', 'own_to_own,own_to_other,own_to_all,other_to_own,other_to_other,other_to_all,all_to_own,all_to_other,all_to_all'),
(42, 'groups', 'bind_off_parent', 'list', 'own,all'),
(43, 'users', 'bind_off_parent', 'list', 'own,all'),
(44, 'groups', 'content_access', 'flag', NULL),
(45, 'auth', 'view_closed', 'flag', NULL),
(46, 'content', 'view_list', 'list', 'all,other,allow'),
(47, 'content', 'limit24', 'number', NULL),
(48, 'users', 'change_email', 'flag', NULL),
(49, 'users', 'change_email_period', 'number', NULL);

DROP TABLE IF EXISTS `{#}perms_users`;
CREATE TABLE `{#}perms_users` (
  `rule_id` int(11) unsigned DEFAULT NULL COMMENT 'Rule ID',
  `group_id` int(11) unsigned DEFAULT NULL COMMENT 'Group ID',
  `subject` varchar(32) DEFAULT NULL COMMENT 'Rule subject',
  `value` varchar(16) NOT NULL COMMENT 'Rule value',
  KEY `rule_id` (`rule_id`),
  KEY `group_id` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Binding permission rules to user groups';

INSERT INTO `{#}perms_users` (`rule_id`, `group_id`, `subject`, `value`) VALUES
(10, 4, 'comments', '1'),
(11, 4, 'comments', 'own'),
(15, 4, 'groups', 'yes'),
(17, 4, 'groups', 'own'),
(16, 4, 'groups', 'own'),
(19, 4, 'users', '1'),
(10, 5, 'comments', '1'),
(12, 5, 'comments', 'all'),
(11, 5, 'comments', 'all'),
(14, 5, 'comments', '1'),
(15, 5, 'groups', 'yes'),
(17, 5, 'groups', 'all'),
(16, 5, 'groups', 'all'),
(19, 5, 'users', '1'),
(10, 3, 'comments', '1'),
(12, 3, 'comments', 'own'),
(11, 3, 'comments', 'own'),
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
(10, 6, 'comments', '1'),
(12, 6, 'comments', 'all'),
(11, 6, 'comments', 'all'),
(20, 4, 'comments', '1'),
(20, 5, 'comments', '1'),
(20, 6, 'comments', '1'),
(14, 6, 'comments', '1'),
(21, 4, 'comments', '1'),
(23, 5, 'activity', '1'),
(23, 6, 'activity', '1'),
(1, 3, 'albums', 'yes'),
(3, 3, 'albums', 'own'),
(2, 3, 'albums', 'own');

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{#}rating_log`;
CREATE TABLE `{#}rating_log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned DEFAULT NULL COMMENT 'User ID',
  `target_controller` varchar(32) DEFAULT NULL COMMENT 'Component (target content owner)',
  `target_subject` varchar(32) DEFAULT NULL COMMENT 'Subject (target content type)',
  `target_id` int(11) unsigned DEFAULT NULL COMMENT 'Subject ID (target content items)',
  `score` tinyint(1) DEFAULT NULL COMMENT 'Score value',
  `ip` int(10) unsigned DEFAULT NULL COMMENT 'ip-address',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `target_id` (`target_id`,`target_controller`,`target_subject`),
  KEY `ip` (`ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Rating scores';

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

INSERT INTO `{#}rss_feeds` (`id`, `ctype_id`, `ctype_name`, `title`, `description`, `image`, `mapping`, `limit`, `is_enabled`, `is_cache`, `cache_interval`, `date_cached`) VALUES
(1, NULL, 'comments', 'Comments', NULL, NULL, '---\r\ntitle: target_title\r\ndescription: content_html\r\npubDate: date_pub\r\n', 15, 1, NULL, 60, NULL),
(4, 7, 'albums', 'Photo albums', NULL, NULL, '---\ntitle: title\ndescription: content\npubDate: date_pub\nimage: cover_image\nimage_size: normal\n', 15, 1, NULL, 60, NULL);

DROP TABLE IF EXISTS `{#}scheduler_tasks`;
CREATE TABLE `{#}scheduler_tasks` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(250) DEFAULT NULL,
  `controller` varchar(32) DEFAULT NULL,
  `hook` varchar(32) DEFAULT NULL,
  `period` int(11) UNSIGNED DEFAULT NULL,
  `is_strict_period` tinyint(1) UNSIGNED DEFAULT NULL,
  `date_last_run` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) UNSIGNED DEFAULT NULL,
  `is_new` tinyint(1) UNSIGNED DEFAULT '1',
  `consistent_run` tinyint(1) UNSIGNED DEFAULT NULL,
  `ordering` int(11) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `period` (`period`),
  KEY `date_last_run` (`date_last_run`),
  KEY `is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `{#}scheduler_tasks` (`id`, `title`, `controller`, `hook`, `period`, `is_strict_period`, `date_last_run`, `is_active`, `is_new`) VALUES
(1, 'User migrations between groups', 'users', 'migration', 1440, NULL, NULL, 1, 0),
(2, 'Sitemap generation', 'sitemap', 'generate', 1440, NULL, NULL, 1, 0),
(3, 'Issuing invitations to users', 'auth', 'send_invites', 1440, NULL, NULL, 1, 0),
(4, 'Publish Content on a schedule', 'content', 'publication', 1440, NULL, NULL, 1, 1),
(5, 'Cleaning deleted private messages', 'messages', 'clean', 1440, NULL, NULL, 1, 1),
(6, 'Delete unverified users', 'auth', 'delete_expired_unverified', 60, NULL, NULL, 1, 1),
(7, 'Deleting of expired items from the trash', 'moderation', 'trash', 30, NULL, NULL, 1, 1),
(8, 'Run system queue tasks', 'queue', 'run_queue', 1, NULL, NULL, 1, 1),
(9, 'Removes expired unconfirmed guest subscriptions', 'subscriptions', 'delete_expired_unconfirmed', 1440, 1, DATE_FORMAT(NOW(), '%Y-%m-%d 00:00:05'), 1, 1),
(10, 'Removes expired PHP session', 'users', 'sessionclean', 10, NULL, NULL, 1, 1),
(11, 'Sends notifications about the end of publication', 'content', 'publication_notify', 1440, 1, DATE_FORMAT(NOW(), '%Y-%m-%d 00:00:05'), 1, 1);

DROP TABLE IF EXISTS `{#}sessions_online`;
CREATE TABLE `{#}sessions_online` (
  `user_id` int(11) unsigned DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `user_id` (`user_id`),
  KEY `date_created` (`date_created`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{#}tags`;
CREATE TABLE `{#}tags` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tag` varchar(32) NOT NULL,
  `frequency` int(11) unsigned NOT NULL DEFAULT '1',
  `tag_title` varchar(300) DEFAULT NULL,
  `tag_desc` varchar(300) DEFAULT NULL,
  `tag_h1` varchar(300) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tag` (`tag`),
  UNIQUE KEY `frequency` (`frequency`,`tag`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Tag list';

DROP TABLE IF EXISTS `{#}tags_bind`;
CREATE TABLE `{#}tags_bind` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tag_id` int(11) unsigned DEFAULT NULL,
  `target_controller` varchar(32) DEFAULT NULL,
  `target_subject` varchar(32) DEFAULT NULL,
  `target_id` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `target_id` (`target_id`,`target_controller`,`target_subject`),
  KEY `tag_id` (`tag_id`),
  KEY `target_controller` (`target_controller`,`target_subject`,`tag_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Binding tags to content';

DROP TABLE IF EXISTS `{#}uploaded_files`;
CREATE TABLE `{#}uploaded_files` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `path` varchar(255) DEFAULT NULL COMMENT 'File path',
  `name` varchar(255) DEFAULT NULL COMMENT 'File name',
  `size` int(11) unsigned DEFAULT NULL COMMENT 'File size',
  `counter` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Download counter',
  `type` varchar(32) DEFAULT 'file' COMMENT  'File type',
  `target_controller` varchar(32) DEFAULT NULL COMMENT 'Controller',
  `target_subject` varchar(32) DEFAULT NULL COMMENT 'Subject',
  `target_id` int(11) unsigned DEFAULT NULL COMMENT 'Subject ID',
  `user_id` int(11) unsigned DEFAULT NULL COMMENT 'Owner ID',
  `date_add` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `path` (`path`),
  KEY `user_id` (`user_id`),
  KEY `target_controller` (`target_controller`,`target_subject`,`target_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{#}users_auth_tokens`;
CREATE TABLE `{#}users_auth_tokens` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `auth_token` varchar(32) DEFAULT NULL,
  `date_auth` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `date_log` timestamp NULL DEFAULT NULL,
  `user_id` int(11) unsigned DEFAULT NULL,
  `access_type` varchar(100) DEFAULT NULL,
  `ip` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `auth_token` (`auth_token`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{#}users`;
CREATE TABLE `{#}users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `groups` text COMMENT 'User groups array',
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) DEFAULT NULL COMMENT 'Password hash',
  `password` varchar(32) DEFAULT NULL COMMENT 'Password hash (deprecated)',
  `password_salt` varchar(16) DEFAULT NULL COMMENT 'Password salt (deprecated)',
  `is_admin` tinyint(1) unsigned DEFAULT NULL COMMENT 'Administrator?',
  `nickname` varchar(100) NOT NULL COMMENT 'Name',
  `date_reg` timestamp NULL DEFAULT NULL COMMENT 'Sign up date',
  `date_log` timestamp NULL DEFAULT NULL COMMENT 'Last log in',
  `date_group` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Last group change date',
  `ip` varchar(45) DEFAULT NULL,
  `2fa` varchar(32) DEFAULT NULL,
  `is_deleted` tinyint(1) unsigned DEFAULT NULL COMMENT 'Deleted',
  `is_locked` tinyint(1) unsigned DEFAULT NULL COMMENT 'Blocked',
  `lock_until` timestamp NULL DEFAULT NULL COMMENT 'Blocked till',
  `lock_reason` varchar(250) DEFAULT NULL COMMENT 'Blocking reason',
  `pass_token` varchar(32) DEFAULT NULL COMMENT 'Password recovery key',
  `date_token` timestamp NULL DEFAULT NULL COMMENT 'Password recovery key creation date',
  `friends_count` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Number of friends',
  `subscribers_count` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Subscribers count',
  `time_zone` varchar(32) DEFAULT NULL COMMENT 'Time zone',
  `karma` int(11) NOT NULL DEFAULT '0' COMMENT 'Reputation',
  `rating` int(11) NOT NULL DEFAULT '0' COMMENT 'Rating',
  `theme` text COMMENT 'Profile theme settings',
  `notify_options` text COMMENT 'Notification settings',
  `privacy_options` text COMMENT 'Privacy settings',
  `status_id` int(11) unsigned DEFAULT NULL COMMENT 'Text status',
  `status_text` varchar(140) DEFAULT NULL COMMENT 'Status text',
  `inviter_id` int(11) unsigned DEFAULT NULL,
  `invites_count` int(11) unsigned NOT NULL DEFAULT '0',
  `date_invites` timestamp NULL DEFAULT NULL,
  `birth_date` datetime DEFAULT NULL,
  `city` int(11) unsigned DEFAULT NULL,
  `city_cache` varchar(128) DEFAULT NULL,
  `hobby` text,
  `avatar` text,
  `icq` varchar(255) DEFAULT NULL,
  `skype` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `music` varchar(255) DEFAULT NULL,
  `movies` varchar(255) DEFAULT NULL,
  `site` text,
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='Users';

INSERT INTO `{#}users` (`id`, `groups`, `email`, `password_hash`, `is_admin`, `nickname`, `date_reg`, `date_log`, `date_group`, `ip`, `is_locked`, `lock_until`, `lock_reason`, `pass_token`, `date_token`, `friends_count`, `subscribers_count`, `time_zone`, `karma`, `rating`, `theme`, `notify_options`, `privacy_options`, `status_id`, `status_text`, `inviter_id`, `invites_count`, `date_invites`, `birth_date`, `city`, `city_cache`, `hobby`, `avatar`, `icq`, `skype`, `phone`, `music`, `movies`, `site`) VALUES
(1, '---\n- 6\n', 'admin@example.com', NULL, 1, 'admin', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, 0, 0, 'Europe/London', 0, 0, '---\nbg_img: null\nbg_color: ''#ffffff''\nbg_repeat: no-repeat\nbg_pos_x: left\nbg_pos_y: top\nmargin_top: 0\n', '---\nusers_friend_add: both\nusers_friend_delete: both\ncomments_new: both\ncomments_reply: email\nusers_friend_accept: pm\ngroups_invite: email\nusers_wall_write: email\n', '---\nusers_profile_view: anyone\nmessages_pm: anyone\n', NULL, NULL, NULL, 0, NULL, '1985-10-15 00:00:00', 12008, 'London', 'Style too own civil out along. Perfectly offending attempted add arranging age gentleman concluded.', NULL, '987654321', 'admin', '100-20-30', 'Disco House, Minimal techno', 'various interesting', 'instantcms.ru');

DROP TABLE IF EXISTS `{#}users_contacts`;
CREATE TABLE `{#}users_contacts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned DEFAULT NULL COMMENT 'User ID',
  `contact_id` int(11) unsigned DEFAULT NULL COMMENT 'Contact (other user) ID',
  `date_last_msg` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Last message date',
  `messages` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Messages count',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`contact_id`),
  KEY `contact_id` (`contact_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='User contacts';

DROP TABLE IF EXISTS `{#}users_fields`;
CREATE TABLE `{#}users_fields` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ctype_id` int(11) unsigned DEFAULT NULL,
  `name` varchar(20) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `hint` varchar(200) DEFAULT NULL,
  `ordering` int(11) unsigned DEFAULT NULL,
  `is_enabled` tinyint(1) UNSIGNED DEFAULT '1',
  `fieldset` varchar(32) DEFAULT NULL,
  `type` varchar(16) DEFAULT NULL,
  `is_in_list` tinyint(1) unsigned DEFAULT NULL,
  `is_in_item` tinyint(1) unsigned DEFAULT NULL,
  `is_in_filter` tinyint(1) unsigned DEFAULT NULL,
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
  KEY `ordering` (`ordering`),
  KEY `is_in_list` (`is_in_list`),
  KEY `is_in_item` (`is_in_item`),
  KEY `is_in_filter` (`is_in_filter`),
  KEY `is_private` (`is_private`),
  KEY `is_fixed` (`is_fixed`),
  KEY `is_system` (`is_system`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='User profile fields';

INSERT INTO `{#}users_fields` (`id`, `ctype_id`, `name`, `title`, `hint`, `ordering`, `fieldset`, `type`, `is_in_list`, `is_in_item`, `is_in_filter`, `is_private`, `is_fixed`, `is_fixed_type`, `is_system`, `values`, `options`, `groups_read`, `groups_edit`) VALUES
(1, NULL, 'birth_date', 'Age', NULL, 4, 'About', 'age', NULL, 1, 1, NULL, NULL, NULL, NULL, NULL, '---\ndate_title: Date of birth\nshow_y: 1\nshow_m: null\nshow_d: null\nshow_h: null\nshow_i: null\nrange: YEAR\nlabel_in_item: left\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\n', '---\n- 0\n', '---\n- 0\n'),
(2, NULL, 'city', 'City', 'Select the city where you live', 3, 'About', 'city', NULL, 1, 1, NULL, NULL, NULL, NULL, NULL, '---\nlabel_in_item: left\nis_required: 1\nis_digits: \nis_alphanumeric: \nis_email: \nis_unique: \n', '---\n- 0\n', '---\n- 0\n'),
(3, NULL, 'hobby', 'Tell us about yourself', 'Tell us about your interests and hobbies', 11, 'About me', 'text', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, '---\nmin_length: 0\nmax_length: 255\nis_html_filter: null\nlabel_in_item: none\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\n', '---\n- 0\n', '---\n- 0\n'),
(5, NULL, 'nickname', 'Username', 'Your display name on the website', 1, 'About', 'string', 1, 1, 1, NULL, 1, NULL, 1, NULL, '---\r\nlabel_in_list: left\r\nlabel_in_item: left\r\nis_required: 1\r\nis_digits: \r\nis_number: \r\nis_alphanumeric: \r\nis_email: \r\nis_unique: \r\nshow_symbol_count: 1\r\nmin_length: 2\r\nmax_length: 100\r\n', '---\n- 0\n', '---\n- 0\n'),
(6, NULL, 'avatar', 'Avatar', 'Your main photo', 2, 'About', 'image', 1, 1, NULL, NULL, 1, NULL, 1, NULL, '---\nsize_teaser: micro\nsize_full: normal\nsizes:\n  - micro\n  - small\n  - normal\nlabel_in_item: left\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\n', '---\n- 0\n', '---\n- 0\n'),
(7, NULL, 'icq', 'ICQ', NULL, 8, 'Contacts', 'string', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, '---\nmin_length: 0\nmax_length: 9\nlabel_in_item: left\nis_required: null\nis_digits: 1\nis_alphanumeric: null\nis_email: null\n', '---\n- 0\n', '---\n- 0\n'),
(8, NULL, 'skype', 'Skype', NULL, 9, 'Contacts', 'string', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, '---\nmin_length: 0\nmax_length: 32\nlabel_in_item: left\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\n', '---\n- 0\n', '---\n- 0\n'),
(9, NULL, 'phone', 'Phone number', NULL, 7, 'Contacts', 'string', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, '---\nmin_length: 0\nmax_length: 255\nlabel_in_item: left\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\n', '---\n- 0\n', '---\n- 0\n'),
(10, NULL, 'music', 'Favorite Music', NULL, 6, 'Preferences', 'string', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, '---\nmin_length: 0\nmax_length: 255\nlabel_in_item: left\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\n', '---\n- 0\n', '---\n- 0\n'),
(11, NULL, 'movies', 'Favorite Movies', NULL, 5, 'Preferences', 'string', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, '---\nmin_length: 0\nmax_length: 255\nlabel_in_item: left\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\n', '---\n- 0\n', '---\n- 0\n'),
(12, NULL, 'site', 'Website', 'Your personal website', 10, 'Contacts', 'url', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, '---\nredirect: 1\nauto_http: 1\nlabel_in_item: left\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\n', '---\n- 0\n', '---\n- 0\n');

DROP TABLE IF EXISTS `{#}users_friends`;
CREATE TABLE `{#}users_friends` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned DEFAULT NULL COMMENT 'User ID',
  `friend_id` int(11) unsigned DEFAULT NULL COMMENT 'Friend ID',
  `is_mutual` tinyint(1) unsigned DEFAULT NULL COMMENT 'Is friendship mutual?',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`is_mutual`),
  KEY `friend_id` (`friend_id`,`is_mutual`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Friendship between users';

DROP TABLE IF EXISTS `{#}users_groups`;
CREATE TABLE `{#}users_groups` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(32) DEFAULT NULL COMMENT 'System name',
  `title` varchar(32) DEFAULT NULL COMMENT 'Group title',
  `is_fixed` tinyint(1) UNSIGNED DEFAULT NULL COMMENT 'System?',
  `is_public` tinyint(1) UNSIGNED DEFAULT NULL COMMENT 'Choose group upon registration?',
  `is_filter` tinyint(1) UNSIGNED DEFAULT NULL COMMENT 'Show group in the user filter?',
  `ordering` int(11) UNSIGNED DEFAULT '1' COMMENT 'Ordering',
  PRIMARY KEY (`id`),
  KEY `ordering` (`ordering`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='User groups';

INSERT INTO `{#}users_groups` (`id`, `name`, `title`, `is_fixed`, `is_public`, `is_filter`, `ordering`) VALUES
(1, 'guests', 'Guests', 1, NULL, NULL, 1),
(3, 'newbies', 'Newbies', NULL, NULL, NULL, 2),
(4, 'members', 'Members', NULL, NULL, NULL, 3),
(5, 'moderators', 'Moderators', NULL, NULL, NULL, 4),
(6, 'admins', 'Administrators', NULL, NULL, 1, 5);

DROP TABLE IF EXISTS `{#}users_groups_members`;
CREATE TABLE `{#}users_groups_members` (
  `user_id` int(11) unsigned NOT NULL,
  `group_id` int(11) unsigned NOT NULL,
  KEY `user_id` (`user_id`),
  KEY `group_id` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Binding users to groups';

INSERT INTO `{#}users_groups_members` (`user_id`, `group_id`) VALUES
(1, 6);

DROP TABLE IF EXISTS `{#}users_groups_migration`;
CREATE TABLE `{#}users_groups_migration` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `is_active` tinyint(1) unsigned DEFAULT NULL,
  `title` varchar(256) DEFAULT NULL,
  `group_from_id` int(11) unsigned DEFAULT NULL,
  `group_to_id` int(11) unsigned DEFAULT NULL,
  `is_keep_group` tinyint(1) unsigned DEFAULT NULL,
  `is_passed` tinyint(1) unsigned DEFAULT NULL,
  `is_rating` tinyint(1) unsigned DEFAULT NULL,
  `is_karma` tinyint(1) unsigned DEFAULT NULL,
  `passed_days` int(11) unsigned DEFAULT NULL,
  `passed_from` tinyint(1) unsigned DEFAULT NULL,
  `rating` int(11) DEFAULT NULL,
  `karma` int(11) DEFAULT NULL,
  `is_notify` tinyint(1) unsigned DEFAULT NULL,
  `notify_text` text,
  PRIMARY KEY (`id`),
  KEY `group_from_id` (`group_from_id`),
  KEY `group_to_id` (`group_to_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Migration rules between groups';

INSERT INTO `{#}users_groups_migration` (`id`, `is_active`, `title`, `group_from_id`, `group_to_id`, `is_keep_group`, `is_passed`, `is_rating`, `is_karma`, `passed_days`, `passed_from`, `rating`, `karma`, `is_notify`, `notify_text`) VALUES
(1, 1, 'Time test', 3, 4, 0, 1, NULL, NULL, 3, 0, NULL, NULL, 1, '3 days elapsed from the moment of your registration.\r\nAll site features are available to you now');

DROP TABLE IF EXISTS `{#}users_ignors`;
CREATE TABLE `{#}users_ignors` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL COMMENT 'User ID',
  `ignored_user_id` int(11) unsigned NOT NULL COMMENT 'Ignored user ID',
  PRIMARY KEY (`id`),
  KEY `ignored_user_id` (`ignored_user_id`,`user_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{#}users_invites`;
CREATE TABLE `{#}users_invites` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `code` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `email` (`email`),
  KEY `key` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{#}users_karma`;
CREATE TABLE `{#}users_karma` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned DEFAULT NULL COMMENT 'Who voted',
  `profile_id` int(11) unsigned DEFAULT NULL COMMENT 'Vote for',
  `date_pub` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Vote date',
  `points` tinyint(2) DEFAULT NULL COMMENT 'Score',
  `comment` varchar(256) DEFAULT NULL COMMENT 'Comment',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `profile_id` (`profile_id`),
  KEY `date_pub` (`date_pub`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='User reputation votes';

DROP TABLE IF EXISTS `{#}users_messages`;
CREATE TABLE `{#}users_messages` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `from_id` int(11) unsigned NOT NULL COMMENT 'Sender ID',
  `to_id` int(11) unsigned NOT NULL COMMENT 'Recipient ID',
  `date_pub` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Creation date',
  `date_delete` timestamp NULL DEFAULT NULL COMMENT 'Delete date',
  `is_new` tinyint(1) unsigned DEFAULT '1' COMMENT 'Unread?',
  `content` text NOT NULL COMMENT 'Message',
  `is_deleted` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `from_id` (`from_id`,`to_id`),
  KEY `to_id` (`to_id`,`is_new`,`is_deleted`),
  KEY `date_delete` (`date_delete`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='User private messages';

DROP TABLE IF EXISTS `{#}users_notices`;
CREATE TABLE `{#}users_notices` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `date_pub` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `content` text,
  `options` text,
  `actions` text,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`date_pub`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{#}users_statuses`;
CREATE TABLE `{#}users_statuses` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned DEFAULT NULL COMMENT 'User',
  `date_pub` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Publication date',
  `content` varchar(140) DEFAULT NULL COMMENT 'Status text',
  `replies_count` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Number of replies',
  `wall_entry_id` int(11) unsigned DEFAULT NULL COMMENT 'Wall post ID',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `date_pub` (`date_pub`),
  KEY `replies_count` (`replies_count`),
  KEY `wall_entry_id` (`wall_entry_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='User text statuses';

DROP TABLE IF EXISTS `{#}users_tabs`;
CREATE TABLE `{#}users_tabs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(32) DEFAULT NULL,
  `controller` varchar(32) DEFAULT NULL,
  `name` varchar(32) DEFAULT NULL,
  `is_active` tinyint(1) unsigned DEFAULT NULL,
  `ordering` int(11) unsigned DEFAULT NULL,
  `groups_view` text,
  `groups_hide` text,
  `show_only_owner` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `is_active` (`is_active`,`ordering`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `{#}users_tabs` (`id`, `title`, `controller`, `name`, `is_active`, `ordering`) VALUES
(1, 'Feed', 'activity', 'activity', 1, 1),
(3, 'Friends', 'users', 'friends', 1, 2),
(4, 'Comments', 'comments', 'comments', 1, 4),
(5, 'Groups', 'groups', 'groups', 1, 3),
(6, 'Reputation', 'users', 'karma', 1, 5),
(7, 'Subscribers', 'users', 'subscribers', 1, 3),
(8, 'Subscriptions', 'subscriptions', 'subscriptions', 1, 3);

DROP TABLE IF EXISTS `{#}users_personal_settings`;
CREATE TABLE `{#}users_personal_settings` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `skey` varchar(150) DEFAULT NULL,
  `settings` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`skey`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{#}wall_entries`;
CREATE TABLE `{#}wall_entries` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `date_pub` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Publication date',
  `date_last_reply` timestamp NULL DEFAULT NULL COMMENT 'Last reply date',
  `date_last_modified` timestamp NULL DEFAULT NULL COMMENT 'Last modified date',
  `controller` varchar(32) DEFAULT NULL COMMENT 'Profile owner component',
  `profile_type` varchar(32) DEFAULT NULL COMMENT 'Profile type (user/group)',
  `profile_id` int(11) unsigned DEFAULT NULL COMMENT 'Profile ID',
  `user_id` int(11) unsigned DEFAULT NULL COMMENT 'Author ID',
  `parent_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Parent item ID',
  `status_id` int(11) unsigned DEFAULT NULL COMMENT 'User status binding',
  `content` text COMMENT 'Item content',
  `content_html` text COMMENT 'Sanitized text',
  PRIMARY KEY (`id`),
  KEY `date_pub` (`date_pub`),
  KEY `user_id` (`user_id`),
  KEY `parent_id` (`parent_id`),
  KEY `profile_id` (`profile_id`,`profile_type`),
  KEY `status_id` (`status_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Profile wall posts';

DROP TABLE IF EXISTS `{#}widgets`;
CREATE TABLE `{#}widgets` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `controller` varchar(32) DEFAULT NULL COMMENT 'Controler',
  `name` varchar(32) NOT NULL COMMENT 'System name',
  `title` varchar(64) DEFAULT NULL COMMENT 'Title',
  `author` varchar(128) DEFAULT NULL COMMENT 'Author name',
  `url` varchar(250) DEFAULT NULL COMMENT 'Author site',
  `version` varchar(8) DEFAULT NULL COMMENT 'Version',
  `is_external` tinyint(1) DEFAULT '1',
  `files` text COMMENT 'List of widget files (for third-party widgets)',
  `addon_id` int(11) UNSIGNED DEFAULT NULL,
  `image_hint_path` varchar(100) DEFAULT NULL COMMENT 'Hint image',
  PRIMARY KEY (`id`),
  KEY `version` (`version`),
  KEY `name` (`name`),
  KEY `controller` (`controller`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `{#}widgets` (`id`, `controller`, `name`, `title`, `author`, `url`, `version`, `is_external`) VALUES
(1, NULL, 'text', 'Text block', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL),
(2, 'users', 'list', 'User list', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL),
(3, NULL, 'menu', 'Menu', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL),
(4, 'content', 'list', 'Content list', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL),
(5, 'content', 'categories', 'Categories', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL),
(6, 'activity', 'list', 'Activity feed', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL),
(7, 'comments', 'list', 'New comments', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL),
(8, 'users', 'online', 'Who is online', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL),
(9, 'users', 'avatar', 'User Avatar', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL),
(10, 'tags', 'cloud', 'Tag cloud', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL),
(11, 'content', 'slider', 'Content slider', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL),
(12, 'auth', 'auth', 'Authorization form', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL),
(13, 'search', 'search', 'Search', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL),
(14, NULL, 'html', 'HTML block', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL),
(15, 'content', 'filter', 'Content filter', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL),
(16, 'photos', 'list', 'Photos list', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL),
(17, 'groups', 'list', 'Groups list', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL),
(18, 'subscriptions', 'button', 'Subscription buttons', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL),
(19, 'auth', 'register', 'Registration form', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL);

DROP TABLE IF EXISTS `{#}widgets_bind`;
CREATE TABLE `{#}widgets_bind` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `template_layouts` varchar(500) DEFAULT NULL,
  `languages` varchar(100) DEFAULT NULL,
  `widget_id` int(11) UNSIGNED NOT NULL,
  `title` varchar(128) NOT NULL COMMENT 'Title',
  `links` text,
  `class` varchar(64) DEFAULT NULL COMMENT 'CSS class',
  `class_title` varchar(64) DEFAULT NULL,
  `class_wrap` varchar(64) DEFAULT NULL,
  `is_title` tinyint(1) UNSIGNED DEFAULT '1' COMMENT 'Show title',
  `is_tab_prev` tinyint(1) UNSIGNED DEFAULT NULL COMMENT 'Group with the previous?',
  `groups_view` text COMMENT 'Show to groups',
  `groups_hide` text COMMENT 'Do not show to groups',
  `options` text COMMENT 'Options',
  `tpl_body` varchar(128) DEFAULT NULL,
  `tpl_wrap` varchar(128) DEFAULT NULL,
  `device_types` varchar(50) DEFAULT NULL,
  `is_cacheable` tinyint(1) UNSIGNED DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `widget_id` (`widget_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Site widgets';

INSERT INTO `{#}widgets_bind` (`id`, `template_layouts`, `languages`, `widget_id`, `title`, `links`, `class`, `class_title`, `class_wrap`, `is_title`, `is_tab_prev`, `groups_view`, `groups_hide`, `options`, `tpl_body`, `tpl_wrap`, `device_types`) VALUES
(1, NULL, NULL, 3, 'Main menu', NULL, NULL, NULL, NULL, NULL, NULL, '---\n- 0\n', NULL, '---\nmenu: main\nis_detect: 1\nmax_items: 8\n', NULL, NULL, NULL),
(2, NULL, NULL, 3, 'Auth menu', NULL, NULL, NULL, NULL, NULL, NULL, '---\n- 1\n', NULL, '---\nmenu: header\nis_detect: 1\nmax_items: 0\n', NULL, NULL, NULL),
(5, NULL, NULL, 3, 'Actions menu', NULL, NULL, NULL, 'fixed_actions_menu', NULL, NULL, '---\n- 0\n', NULL, '---\nmenu: toolbar\ntemplate: menu\nis_detect: null\nmax_items: 0\n', 'menu', 'wrapper', NULL),
(20, NULL, NULL, 12, 'Log in', NULL, NULL, NULL, NULL, 1, NULL, '---\n- 0\n', NULL, '', NULL, NULL, NULL),
(22, NULL, NULL, 9, 'User menu', NULL, NULL, NULL, NULL, NULL, NULL, '---\n- 0\n', '---\n- 1\n', '---\nmenu: personal\nis_detect: 1\nmax_items: 0\n', 'avatar', 'wrapper', NULL),
(23, NULL, NULL, 3, 'Notifications', NULL, NULL, NULL, NULL, NULL, NULL, '---\n- 0\n', '---\n- 1\n', '---\nmenu: notices\ntemplate: menu\nis_detect: null\nmax_items: 0\n', 'menu', 'wrapper', NULL);

DROP TABLE IF EXISTS `{#}widgets_bind_pages`;
CREATE TABLE `{#}widgets_bind_pages` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `bind_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'Widget options id',
  `template` varchar(30) DEFAULT NULL COMMENT 'Template binding',
  `is_enabled` tinyint(1) UNSIGNED DEFAULT NULL COMMENT 'Enabled?',
  `page_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'Page ID',
  `position` varchar(32) DEFAULT NULL COMMENT 'Position title',
  `ordering` int(11) UNSIGNED DEFAULT NULL COMMENT 'Order number',
  PRIMARY KEY (`id`),
  KEY `position` (`position`),
  KEY `page_id` (`page_id`,`position`,`ordering`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Bind widgets to pages';

INSERT INTO `{#}widgets_bind_pages` (`id`, `bind_id`, `template`, `is_enabled`, `page_id`, `position`, `ordering`) VALUES
(1, 1, 'default', 1, 0, 'top', 1),
(2, 2, 'default', 1, 0, 'header', 1),
(3, 5, 'default', 1, 0, 'left-top', 1),
(4, 20, 'default', 1, 0, 'right-center', 1),
(5, 22, 'default', 1, 0, 'header', 3),
(6, 23, 'default', 1, 0, 'header', 3);

DROP TABLE IF EXISTS `{#}widgets_pages`;
CREATE TABLE `{#}widgets_pages` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `controller` varchar(32) DEFAULT NULL COMMENT 'Component',
  `name` varchar(64) DEFAULT NULL COMMENT 'System name',
  `title_const` varchar(64) DEFAULT NULL COMMENT 'Page title (language constant)',
  `title_subject` varchar(64) DEFAULT NULL COMMENT 'Subject title (transferred to the language constant)',
  `title` varchar(64) DEFAULT NULL,
  `url_mask` text COMMENT 'URL mask',
  `url_mask_not` text COMMENT 'Negative mask',
  `groups` text COMMENT 'Access groups',
  `countries` text COMMENT 'Access countries',
  PRIMARY KEY (`id`),
  KEY `controller` (`controller`),
  KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `{#}widgets_pages` (`id`, `controller`, `name`, `title_const`, `title_subject`, `title`, `url_mask`, `url_mask_not`) VALUES
(100, 'users', 'list', 'LANG_USERS_LIST', NULL, NULL, 'users\r\nusers/index\r\nusers/index/*', NULL),
(101, 'users', 'profile', 'LANG_USERS_PROFILE', NULL, NULL, 'users/%*', 'users/%/edit'),
(102, 'users', 'edit', 'LANG_USERS_EDIT_PROFILE', NULL, NULL, 'users/%/edit', NULL),
(155, 'content', 'albums.all', 'LANG_WP_CONTENT_ALL_PAGES', NULL, NULL, 'albums\nalbums-*\nalbums/*', NULL),
(156, 'content', 'albums.list', 'LANG_WP_CONTENT_LIST', NULL, NULL, 'albums\nalbums-*\nalbums/*', 'albums/*/view-*\nalbums/*.html\nalbums/add\nalbums/add/%\nalbums/addcat\nalbums/addcat/%\nalbums/editcat/%\nalbums/edit/*'),
(157, 'content', 'albums.item', 'LANG_WP_CONTENT_ITEM', NULL, NULL, 'albums/*.html', NULL),
(158, 'content', 'albums.edit', 'LANG_WP_CONTENT_ITEM_EDIT', NULL, NULL, 'albums/add\nalbums/add/%\nalbums/edit/*', NULL),
(167, 'photos', 'item', 'LANG_PHOTOS_WP_ITEM', NULL, NULL, 'photos/*.html', NULL),
(168, 'photos', 'upload', 'LANG_PHOTOS_WP_UPLOAD', NULL, NULL, 'photos/upload/%\r\nphotos/upload', NULL),
(200, NULL, 'all', 'LANG_WP_ALL_PAGES', NULL, NULL, NULL, NULL);

UPDATE `{#}widgets_pages` SET `id` = 0 WHERE `id` = 200;

DROP TABLE IF EXISTS `{#}wysiwygs_presets`;
CREATE TABLE `{#}wysiwygs_presets` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `wysiwyg_name` varchar(40) DEFAULT NULL COMMENT 'Editor name',
  `options` text COMMENT 'Options',
  `title` varchar(100) DEFAULT NULL COMMENT 'Preset title',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Wysiwyg editors presets';

INSERT INTO `{#}wysiwygs_presets` (`id`, `wysiwyg_name`, `options`, `title`) VALUES
(1, 'markitup', '{\"buttons\":[\"0\",\"1\",\"2\",\"3\",\"4\",\"5\",\"7\",\"14\"],\"skin\":\"simple\"}', 'Photos'),
(2, 'redactor', '{\"plugins\":[\"smiles\"],\"buttons\":[\"bold\",\"italic\",\"deleted\",\"unorderedlist\",\"image\",\"video\",\"link\"],\"convertVideoLinks\":1,\"convertDivs\":null,\"toolbarFixedBox\":null,\"autoresize\":null,\"pastePlainText\":1,\"removeEmptyTags\":1,\"linkNofollow\":1,\"minHeight\":\"58\",\"placeholder\":\"\\u0412\\u0432\\u0435\\u0434\\u0438\\u0442\\u0435 \\u0441\\u043e\\u043e\\u0431\\u0449\\u0435\\u043d\\u0438\\u0435\"}', 'Editor for private messages');