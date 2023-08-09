INSERT INTO `{#}activity` (`id`, `type_id`, `user_id`, `group_id`, `subject_title`, `subject_id`, `subject_url`, `reply_url`, `images`, `images_count`, `date_pub`, `is_private`, `is_parent_hidden`) VALUES
(3, 13, 1, NULL, 'Elliptical perigee in the XXI century', 1, 'articles/1-elliptical-perigee-in-the-xxi-century.html', NULL, NULL, NULL, '2013-07-24 10:49:30', 0, NULL),
(6, 13, 1, NULL, 'Undersaturated diamond: preconditions and development', 4, 'articles/4-undersaturated-diamond-preconditions-and-development.html', NULL, NULL, NULL, '2013-07-24 11:22:39', 0, NULL),
(10, 12, 1, NULL, 'We are all made of stars © Moby', NULL, NULL, 'users/1?wid=1&reply=1', NULL, NULL, CURRENT_TIMESTAMP, 0, NULL),
(17, 1, 1, NULL, 'About', 1, 'pages/about.html', NULL, NULL, NULL, '2013-08-08 15:07:27', 0, NULL),
(18, 1, 1, NULL, 'Site Rules', 2, 'pages/rules.html', NULL, NULL, NULL, DATE_SUB(NOW(),INTERVAL 10 DAY), 0, NULL),
(33, 17, 1, NULL, 'Toys are becoming more expensive', 2, 'news/2-igrushki-stanovjatsja-dorozhe.html', NULL, NULL, NULL, '2013-09-09 16:02:07', 0, NULL),
(34, 17, 1, NULL, 'Сar service for vintage cars', 3, 'news/3-v-gorode-otkryt-servis-dlja-retro-avtomobilei.html', NULL, NULL, NULL, '2013-09-09 16:03:25', 0, NULL),
(35, 17, 1, NULL, 'Summer season has officially begun', 4, 'news/4-dachnyi-sezon-otkryt.html', NULL, NULL, NULL, '2013-09-09 16:04:25', 0, NULL),
(36, 17, 1, NULL, 'Business expects to reduce taxes', 5, 'news/5-snizhenie-nalogov-dlja-biznesa.html', NULL, NULL, NULL, '2013-09-09 16:05:26', 0, NULL),
(37, 17, 1, NULL, 'More and more people are buying homes abroad', 6, 'news/6-vse-bolshe-rossijan-pokupayut-nedvizhimost-za-granicei.html', NULL, NULL, NULL, '2013-09-12 12:09:25', 0, NULL),
(38, 17, 1, NULL, 'Reduced the number of crimes', 7, 'news/7-kolichestvo-prestuplenii-v-rossii-sokraschaetsja.html', NULL, NULL, NULL, '2013-09-12 12:10:55', 0, NULL),
(40, 17, 1, NULL, 'We have won the World Championship!', 9, 'news/9-rossijane-stali-pervymi-na-chempionate-mira.html', NULL, NULL, NULL, '2013-09-12 12:14:13', 0, NULL),
(56, 13, 1, NULL, 'Mythological recipient', 10, 'articles/10-mythological-recipient.html', NULL, NULL, NULL, '2013-10-09 14:48:19', 0, NULL),
(57, 13, 1, NULL, 'Public review of international experience', 11, 'articles/11-public-review-of-international-experience.html', NULL, NULL, NULL, '2013-10-09 14:54:36', 0, NULL),
(67, 14, 1, 1, 'My first post in the Community', 5, 'posts/5-moi-pervyi-post-v-soobschestve.html', NULL, NULL, NULL, '2013-11-13 16:43:07', 0, NULL);

INSERT INTO `{#}activity_types` (`id`, `is_enabled`, `controller`, `name`, `title`, `description`) VALUES
(13, 1, 'content', 'add.articles', 'Adding articles', 'added article %s'),
(14, 1, 'content', 'add.posts', 'Adding posts', 'added post %s'),
(15, 0, 'content', 'add.albums', 'Adding albums', 'added album %s'),
(17, 1, 'content', 'add.news', 'Adding news', 'added news %s');

INSERT INTO `{#}comments` (`id`, `parent_id`, `level`, `ordering`, `user_id`, `date_pub`, `target_controller`, `target_subject`, `target_id`, `target_url`, `target_title`, `author_name`, `author_email`, `author_ip`, `content`, `content_html`, `is_deleted`, `is_private`, `rating`) VALUES
(1, 0, 1, 1, 1, CURRENT_TIMESTAMP, 'content', 'articles', 4, 'articles/4-undersaturated-diamond-preconditions-and-development.html', 'Undersaturated diamond: preconditions and development', NULL, NULL, NULL, 'This article is so complicated...', 'This article is so complicated…', NULL, 0, 0),
(2, 1, 2, 2, 1, CURRENT_TIMESTAMP, 'content', 'articles', 4, 'articles/4-undersaturated-diamond-preconditions-and-development.html', 'Undersaturated diamond: preconditions and development', NULL, NULL, NULL, 'I didn''t understand anything', 'I didn''t understand anything', NULL, 0, 0);

INSERT INTO `{#}content_datasets` (`id`, `ctype_id`, `name`, `title`, `ordering`, `is_visible`, `filters`, `sorting`, `index`, `groups_view`, `groups_hide`) VALUES
(1, 5, 'all', 'Latest', 1, 1, NULL, '---\n- \n  by: date_pub\n  to: desc\n', 'date_pub', '---\n- 0\n', NULL),
(2, 5, 'reviews', 'Reviews', 2, 1, '---\n- \n  field: kind\n  condition: eq\n  value: 2\n', '---\n- \n  by: date_pub\n  to: desc\n', 'dataset_reviews', '---\n- 0\n', NULL),
(3, 5, 'translations', 'Translations', 3, 1, '---\n- \n  field: kind\n  condition: eq\n  value: 3\n', '---\n- \n  by: date_pub\n  to: desc\n', 'dataset_reviews', '---\n- 0\n', NULL),
(4, 5, 'featured', 'Editor''s choice', 4, 1, '---\n- \n  field: featured\n  condition: eq\n  value: 1\n', '---\n- \n  by: date_pub\n  to: desc\n', 'dataset_featured', '---\n- 0\n', NULL),
(5, 5, 'rating', 'Top articles', 5, 1, '---\n- \n  field: rating\n  condition: gt\n  value: 0\n', '---\n- \n  by: rating\n  to: desc\n', 'dataset_rating', '---\n- 0\n', NULL),
(6, 6, 'latest', 'Latest', 1, 1, NULL, '---\n- \n  by: date_pub\n  to: desc\n', 'date_pub', '---\n- 0\n', NULL),
(7, 6, 'daily', 'Daily top', 2, 1, '---\n- \n  field: date_pub\n  condition: dy\n  value: 1\n', '---\n- \n  by: rating\n  to: desc\n', 'dataset_daily', '---\n- 0\n', NULL),
(8, 6, 'weekly', 'Weekly top', 3, 1, '---\n- \n  field: date_pub\n  condition: dy\n  value: 7\n', '---\n- \n  by: rating\n  to: desc\n', 'dataset_daily', '---\n- 0\n', NULL),
(9, 6, 'monthly', 'Monthly top', 4, 1, '---\n- \n  field: date_pub\n  condition: dy\n  value: 31\n', '---\n- \n  by: date_pub\n  to: desc\n', 'date_pub', '---\n- 0\n', NULL),
(10, 10, 'latest', 'Latest', 1, 1, NULL, '---\n- \n  by: date_pub\n  to: desc\n', 'date_pub', '---\n- 0\n', NULL),
(11, 10, 'discussed', 'Discussed', 2, 1, NULL, '---\n- \n  by: comments\n  to: desc\n', 'dataset_discussed', '---\n- 0\n', NULL),
(12, 10, 'popular', 'Popular', 3, 1, NULL, '---\n- \n  by: rating\n  to: desc\n', 'dataset_popular', '---\n- 0\n', NULL);

INSERT INTO `{#}content_datasets` (`id`, `ctype_id`, `name`, `title`, `description`, `ordering`, `is_visible`, `filters`, `sorting`, `index`, `groups_view`, `groups_hide`, `seo_keys`, `seo_desc`, `seo_title`, `cats_view`, `cats_hide`, `max_count`, `target_controller`) VALUES
(13, NULL, 'rating', 'Top Groups', NULL, 3, 1, NULL, '---\n- \n  by: rating\n  to: desc\n', 'rating', '---\n- 0\n', NULL, NULL, NULL, NULL, NULL, NULL, 0, 'groups'),
(14, NULL, 'all', 'New Groups', NULL, 2, 1, NULL, '---\n- \n  by: date_pub\n  to: desc\n', 'date_pub', '---\n- 0\n', NULL, NULL, NULL, NULL, NULL, NULL, 0, 'groups'),
(15, NULL, 'popular', 'Popular', NULL, 1, 1, NULL, '---\n- \n  by: members_count\n  to: desc\n', 'members_count', '---\n- 0\n', NULL, NULL, NULL, NULL, NULL, NULL, 0, 'groups');

INSERT INTO `{#}content_folders` (`id`, `ctype_id`, `user_id`, `title`) VALUES
(5, 6, 1, 'My thoughts');

INSERT INTO `{#}content_types` (`id`, `title`, `name`, `description`, `is_date_range`, `is_cats`, `is_cats_recursive`, `is_folders`, `is_in_groups`, `is_in_groups_only`, `is_comments`, `is_rating`, `is_tags`, `is_auto_keys`, `is_auto_desc`, `is_auto_url`, `is_fixed_url`, `url_pattern`, `options`, `labels`, `seo_keys`, `seo_desc`, `seo_title`, `item_append_html`, `is_fixed`) VALUES
(5, 'Articles', 'articles', '<p>Text materials</p>', NULL, 1, 1, NULL, NULL, NULL, 1, 1, 1, 1, 1, 1, NULL, '{id}-{title}', '---\nis_cats_change: 1\nis_cats_open_root: 1\nis_cats_only_last: null\nis_show_cats: 1\nis_tags_in_list: 1\nis_tags_in_item: 1\nis_rss: 1\nlist_on: 1\nprofile_on: 1\nlist_show_filter: 1\nlist_expand_filter: null\nlist_style:\nitem_on: 1\nhits_on: 1\nis_cats_keys: 1\nis_cats_desc: 1\nis_cats_auto_url: 1\nseo_title_pattern:\nseo_keys_pattern: \'{content|string_get_meta_keywords}\'\nseo_desc_pattern: \'{content|string_get_meta_description}\'\n', '---\none: article\ntwo: articles\nmany: articles\ncreate: article\nlist:\nprofile:\n', 'статьи, разные, интересные, полезные', NULL, NULL, NULL, NULL),
(6, 'Blog posts', 'posts', '<p>Blog posts</p>', NULL, NULL, NULL, 1, 1, NULL, 1, 1, 1, 1, 1, 1, 1, '{id}-{title}', '---\nis_cats_change: null\nis_cats_open_root: null\nis_cats_only_last: null\nis_show_cats: null\nis_tags_in_list: 1\nis_tags_in_item: 1\nis_rss: 1\nlist_on: 1\nprofile_on: 1\nlist_show_filter: null\nlist_expand_filter: null\nlist_style:\nitem_on: 1\nis_cats_keys: null\nis_cats_desc: null\nis_cats_auto_url: 1\nseo_title_pattern:\nseo_keys_pattern: \'{content|string_get_meta_keywords}\'\nseo_desc_pattern: \'{content|string_get_meta_description}\'\n', '---\none: post\ntwo: posts\nmany: posts\ncreate: post\nlist: Blogs Posts\nprofile: Blog\n', NULL, NULL, NULL, NULL, NULL),
(10, 'News', 'news', '<p>Information</p>', NULL, 1, 1, NULL, NULL, NULL, 1, 1, 1, 1, 1, 1, 1, '{id}-{title}', '---\nis_cats_change: 1\nis_cats_open_root: null\nis_cats_only_last: null\nis_show_cats: null\nis_tags_in_list: null\nis_tags_in_item: 1\nis_rss: 1\nlist_on: 1\nprofile_on: 1\nlist_show_filter: null\nlist_expand_filter: null\nlist_style: featured\nitem_on: 1\nis_cats_keys: null\nis_cats_desc: null\nis_cats_auto_url: 1\nseo_title_pattern:\nseo_keys_pattern: \'{content|string_get_meta_keywords}\'\nseo_desc_pattern: \'{content|string_get_meta_description}\'\n', '---\none: news\ntwo: news\nmany: news\ncreate: news\nlist:\nprofile:\n', NULL, NULL, NULL, NULL, NULL);

INSERT INTO `{#}con_albums` (`id`, `title`, `content`, `slug`, `seo_keys`, `seo_desc`, `seo_title`, `tags`, `date_pub`, `date_last_modified`, `date_pub_end`, `is_pub`, `hits_count`, `user_id`, `parent_id`, `parent_type`, `parent_title`, `parent_url`, `is_parent_hidden`, `category_id`, `folder_id`, `is_comments_on`, `comments`, `rating`, `is_approved`, `approved_by`, `date_approved`, `is_private`, `cover_image`, `photos_count`, `is_public`) VALUES
(16, 'The beauty of the surrounding nature', 'Photos from the deviantart.com', '16-the-beauty-of-the-surrounding-nature', 'photos, deviantart.com', 'Photos from the deviantart.com', NULL, 'example, photo', '2013-11-13 16:48:18', '2013-11-22 16:32:38', NULL, 1, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 1, 0, 0, 1, NULL, '2013-11-22 16:32:38', 0, '---\nbig: u1/004/4f11cd73.jpg\nnormal: u1/004/5b0ff517.jpg\nsmall: u1/004/5edb4681.jpg', 4, NULL),
(14, 'Other photos', 'Photos taken of me at leisure', '14-other-photos', 'photos', 'leisure', NULL, '0', '2013-10-09 16:46:43', '2013-10-09 16:46:43', NULL, 1, 0, 6, NULL, NULL, NULL, NULL, NULL, 1, NULL, 1, 0, 0, 0, NULL, NULL, 0, NULL, 0, NULL);

INSERT INTO `{#}con_albums_cats_bind` (`item_id`, `category_id`) VALUES
(16, 1),
(14, 1);

DROP TABLE IF EXISTS `{#}con_articles`;
CREATE TABLE `{#}con_articles` (
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
  `is_approved` tinyint(1) NOT NULL DEFAULT '1',
  `approved_by` int(11) DEFAULT NULL,
  `date_approved` timestamp NULL DEFAULT NULL,
  `is_private` tinyint(1) NOT NULL DEFAULT '0',
  `teaser` text,
  `kind` int(11) DEFAULT NULL,
  `notice` text,
  `source` text,
  `featured` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `approved_by` (`approved_by`),
  KEY `folder_id` (`folder_id`),
  KEY `slug` (`slug`),
  KEY `date_pub` (`is_pub`,`is_parent_hidden`,`is_deleted`,`is_approved`,`date_pub`),
  KEY `parent_id` (`parent_id`,`parent_type`,`date_pub`),
  KEY `user_id` (`user_id`,`date_pub`),
  KEY `date_pub_end` (`date_pub_end`),
  KEY `dataset_reviews` (`kind`,`is_pub`,`is_parent_hidden`,`is_deleted`,`is_approved`,`date_pub`),
  KEY `dataset_featured` (`featured`,`is_pub`,`is_parent_hidden`,`is_deleted`,`is_approved`,`date_pub`),
  KEY `dataset_rating` (`is_pub`,`is_parent_hidden`,`is_deleted`,`is_approved`,`rating`),
  FULLTEXT KEY `title` (`title`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

INSERT INTO `{#}con_articles` (`id`, `title`, `content`, `slug`, `seo_keys`, `seo_desc`, `seo_title`, `tags`, `date_pub`, `date_last_modified`, `date_pub_end`, `is_pub`, `hits_count`, `user_id`, `parent_id`, `parent_type`, `parent_title`, `parent_url`, `is_parent_hidden`, `category_id`, `folder_id`, `is_comments_on`, `comments`, `rating`, `is_approved`, `approved_by`, `date_approved`, `is_private`, `teaser`, `kind`, `notice`, `source`, `featured`) VALUES
(1, 'Elliptical perigee in the XXI century', '<p>\r\n	Yet remarkably appearance get him his projection. Diverted endeavor bed peculiar men the not desirous. Acuteness abilities ask can offending furnished fulfilled sex. Warrant fifteen exposed ye at mistake. Blush since so in noisy still built up an again. As young ye hopes no he place means. Partiality diminution gay yet entreaties admiration. In mr it he mention perhaps attempt pointed suppose. Unknown ye chamber of warrant of norland arrived.\r\n</p>\r\n<p>\r\n	Rank tall boy man them over post now. Off into she bed long fat room. Recommend existence curiosity perfectly favourite get eat she why daughters. Not may too nay busy last song must sell. An newspaper assurance discourse ye certainly. Soon gone game and why many calm have.\r\n</p>', '1-elliptical-perigee-in-the-xxi-century', 'warrant, remarkably, since, blush, mistake, exposed, noisy, still, hopes, young', 'Yet remarkably appearance get him his projection. Diverted endeavor bed peculiar men the not desirous. Acuteness abilities ask can offending furnished fulfilled sex. Warrant fifteen exposed ye at mistake', NULL, 'example, article, science', '2013-07-24 10:49:30', '2013-11-22 16:23:29', NULL, 1, 0, 1, NULL, NULL, NULL, NULL, NULL, 2, NULL, 1, 0, 0, 1, NULL, '2013-07-24 10:53:11', 0, '<p>\r\n	<p>\r\n		Yet remarkably appearance get him his projection. Diverted endeavor bed peculiar men the not desirous.\r\n	</p>\r\n</p>', 1, NULL, 'http://referats.yandex.ru/astronomy.xml', NULL),
(4, 'Undersaturated diamond: preconditions and development', '<p>\r\n	Her old collecting she considered discovered. So at parties he warrant oh staying. Square new horses and put better end. Sincerity collected happiness do is contented. Sigh ever way now many. Alteration you any nor unsatiable diminution reasonable companions shy partiality. Leaf by left deal mile oh if easy. Added woman first get led joy not early jokes.\r\n</p>\r\n<p>\r\n	Speedily say has suitable disposal add boy. On forth doubt miles of child. Exercise joy man children rejoiced. Yet uncommonly his ten who diminution astonished. Demesne new manners savings staying had. Under folly balls death own point now men. Match way these she avoid see death. She whose drift their fat off.\r\n</p>', '4-undersaturated-diamond-preconditions-and-development', 'diminution, staying, death, unsatiable, reasonable, alteration, companions, contented, early, first', 'Her old collecting she considered discovered. So at parties he warrant oh staying. Square new horses and put better end. Sincerity collected happiness do is contented. Sigh ever way now many', NULL, 'article, science', '2013-07-24 11:22:39', '2013-11-22 16:22:45', NULL, 1, 0, 1, NULL, NULL, NULL, NULL, NULL, 5, NULL, 1, 2, 0, 1, NULL, '2013-10-04 14:55:37', 0, '<p>\r\n	<p>\r\n		Her old collecting she considered discovered. So at parties he warrant oh staying.\r\n	</p>\r\n</p>', 3, NULL, 'http://referats.yandex.ru/geology.xml', NULL),
(11, 'Public review of international experience', '<p>\r\n	Her extensive perceived may any sincerity extremity. Indeed add rather may pretty see. Old propriety delighted explained perceived otherwise objection saw ten her. Doubt merit sir the right these alone keeps. By sometimes intention smallness he northward. Consisted we otherwise arranging commanded discovery it explained. Does cold even song like two yet been. Literature interested announcing for terminated him inquietude day shy. Himself he fertile chicken perhaps waiting if highest no it. Continued promotion has consulted fat improving not way.\r\n</p>\r\n<p>\r\n	Arrived compass prepare an on as. Reasonable particular on my it in sympathize. Size now easy eat hand how. Unwilling he departure elsewhere dejection at. Heart large seems may purse means few blind. Exquisite newspaper attending on certainty oh suspicion of. He less do quit evil is. Add matter family active mutual put wishes happen.\r\n</p>', '11-public-review-of-international-experience', 'otherwise, explained, perceived, arranging, commanded, consisted, northward, intention, smallness, fertile', 'Her extensive perceived may any sincerity extremity. Indeed add rather may pretty see. Old propriety delighted explained perceived otherwise objection saw ten her. Doubt merit sir the right these alone keeps', NULL, '0', '2013-10-09 14:54:36', '2013-11-22 16:21:33', NULL, 1, 6, 1, NULL, NULL, NULL, NULL, NULL, 9, NULL, 1, 2, 0, 1, NULL, '2013-10-09 15:02:33', 0, '<p>\r\n	Her extensive perceived may any sincerity extremity. Indeed add rather may pretty see. Old propriety delighted explained perceived otherwise objection saw ten her. Doubt merit sir the right these alone keeps.\r\n</p>', 2, NULL, NULL, NULL),
(10, 'Mythological recipient', '<p>\r\n	<p>\r\n		Both rest of know draw fond post as. It agreement defective to excellent. Feebly do engage of narrow. Extensive repulsive belonging depending if promotion be zealously as. Preference inquietude ask now are dispatched led appearance. Small meant in so doubt hopes. Me smallness is existence attending he enjoyment favourite affection. Delivered is to ye belonging enjoyment preferred. Astonished and acceptance men two discretion. Law education recommend did objection how old.\r\n	</p>\r\n	<p>\r\n		Greatly hearted has who believe. Drift allow green son walls years for blush. Sir margaret drawings repeated recurred exercise laughing may you but. Do repeated whatever to welcomed absolute no. Fat surprise although outlived and informed shy dissuade property. Musical by me through he drawing savings an. No we stand avoid decay heard mr. Common so wicket appear to sudden worthy on. Shade of offer ye whole stood hoped.\r\n	</p>\r\n</p>', '10-mythological-recipient', 'enjoyment, repeated, belonging, preferred, delivered, affection, favourite, smallness, existence, attending', 'Both rest of know draw fond post as. It agreement defective to excellent. Feebly do engage of narrow. Extensive repulsive belonging depending if promotion be zealously as. Preference inquietude ask now are dispatched led appearance', NULL, '0', '2013-10-09 14:48:19', '2013-11-22 16:22:04', NULL, 1, 0, 1, NULL, NULL, NULL, NULL, NULL, 6, NULL, 1, 0, 0, 1, NULL, '2013-10-09 15:03:01', 0, '<p>\r\n	<p>\r\n		Both rest of know draw fond post as. It agreement defective to excellent. Feebly do engage of narrow.\r\n	</p>\r\n</p>', 1, NULL, NULL, NULL);

DROP TABLE IF EXISTS `{#}con_articles_cats`;
CREATE TABLE `{#}con_articles_cats` (
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

INSERT INTO `{#}con_articles_cats` (`id`, `parent_id`, `title`, `slug`, `slug_key`, `seo_keys`, `seo_desc`, `seo_title`, `ordering`, `ns_left`, `ns_right`, `ns_level`, `ns_differ`, `ns_ignore`) VALUES
(1, 0, '---', NULL, NULL, NULL, NULL, NULL, 1, 1, 18, 0, '', 0),
(2, 1, 'Astronomy', 'astronomy', NULL, 'stars, space, sky, science', NULL, NULL, 1, 2, 7, 1, '', 0),
(3, 2, 'Science and space', 'astronomy/science-and-space', NULL, NULL, NULL, NULL, 1, 5, 6, 2, '', 0),
(4, 2, 'Astrophysics', 'astronomy/astrophysics', NULL, NULL, NULL, NULL, 2, 3, 4, 2, '', 0),
(5, 1, 'Geology', 'geology', NULL, NULL, NULL, NULL, 2, 8, 9, 1, '', 0),
(6, 1, 'Literature', 'literature', NULL, NULL, NULL, NULL, 3, 10, 15, 1, '', 0),
(7, 6, 'National', 'literature/national', NULL, NULL, NULL, NULL, 1, 11, 12, 2, '', 0),
(8, 6, 'Foreign', 'literature/foreign', NULL, NULL, NULL, NULL, 2, 13, 14, 2, '', 0),
(9, 1, 'Marketing', 'marketing', NULL, NULL, NULL, NULL, 4, 16, 17, 1, '', 0);

DROP TABLE IF EXISTS `{#}con_articles_cats_bind`;
CREATE TABLE `{#}con_articles_cats_bind` (
  `item_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  KEY `item_id` (`item_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `{#}con_articles_cats_bind` (`item_id`, `category_id`) VALUES
(1, 2),
(4, 5),
(11, 9),
(10, 6);

DROP TABLE IF EXISTS `{#}con_articles_fields`;
CREATE TABLE `{#}con_articles_fields` (
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

INSERT INTO `{#}con_articles_fields` (`id`, `ctype_id`, `name`, `title`, `hint`, `ordering`, `fieldset`, `type`, `is_in_list`, `is_in_item`, `is_in_filter`, `is_private`, `is_fixed`, `is_fixed_type`, `is_system`, `values`, `options`, `groups_read`, `groups_edit`) VALUES
(1, 5, 'title', 'Title', NULL, 1, NULL, 'caption', 1, 1, 1, NULL, 1, 1, 0, NULL, '---\nlabel_in_list: none\nlabel_in_item: none\nis_required: 1\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\nprofile_value:\n', '---\n- 0\n', '---\n- 0\n'),
(2, 5, 'date_pub', 'Publication date', NULL, 2, NULL, 'date', 1, 1, NULL, NULL, 1, 1, 1, NULL, '---\nlabel_in_list: none\nlabel_in_item: left\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\nprofile_value:\n', '---\n- 0\n', '---\n- 0\n'),
(3, 5, 'user', 'Author', NULL, 3, NULL, 'user', 1, 1, NULL, NULL, 1, 1, 1, NULL, '---\nlabel_in_list: none\nlabel_in_item: left\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\nprofile_value:\n', '---\n- 0\n', '---\n- 0\n'),
(4, 5, 'content', 'Article content', 'Place full article text here', 7, 'Article content', 'html', NULL, 1, NULL, NULL, 1, NULL, NULL, NULL, '---\neditor: 3\nis_html_filter: 1\nteaser_len:\nlabel_in_list: none\nlabel_in_item: none\nis_required: 1\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\nprofile_value:\n', '---\n- 0\n', '---\n- 0\n'),
(5, 5, 'teaser', 'Article teaser', 'Short article description, will be displayed in the list of articles', 6, 'Article content', 'html', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '---\neditor: 3\nis_html_filter: 1\nteaser_len:\nlabel_in_list: none\nlabel_in_item: none\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\nprofile_value:\n', '---\n- 0\n', '---\n- 0\n'),
(6, 5, 'kind', 'Article type', NULL, 4, 'About article', 'list', NULL, 1, 1, NULL, NULL, NULL, NULL, '1 | Basic article\r\n2 | Review\r\n3 | Translation', '---\nfilter_multiple: 1\nlabel_in_list: left\nlabel_in_item: left\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\nprofile_value:\n', '---\n- 0\n', '---\n- 0\n'),
(7, 5, 'notice', 'Editor''s comment', 'The field is available only for administrators and moderators', 9, 'Ancillary Data', 'text', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, '---\nmin_length: 0\nmax_length: 2048\nis_html_filter: null\nlabel_in_list: top\nlabel_in_item: top\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\nprofile_value:\n', '---\n- 0\n', '---\n- 5\n- 6\n'),
(8, 5, 'source', 'Source', 'Source text link', 5, 'About article', 'url', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, '---\nredirect: 1\nauto_http: 1\nlabel_in_list: left\nlabel_in_item: left\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\nprofile_value:\n', '---\n- 0\n', '---\n- 0\n'),
(9, 5, 'featured', 'Editor''s choice', 'The field is available only to administrators and moderators', 8, 'Ancillary Data', 'checkbox', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '---\nlabel_in_list: left\nlabel_in_item: left\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\nprofile_value:\n', '---\n- 0\n', '---\n- 5\n- 6\n');

DROP TABLE IF EXISTS `{#}con_articles_props`;
CREATE TABLE `{#}con_articles_props` (
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

DROP TABLE IF EXISTS `{#}con_articles_props_bind`;
CREATE TABLE `{#}con_articles_props_bind` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `prop_id` int(11) DEFAULT NULL,
  `cat_id` int(11) DEFAULT NULL,
  `ordering` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `prop_id` (`prop_id`),
  KEY `ordering` (`cat_id`,`ordering`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{#}con_articles_props_values`;
CREATE TABLE `{#}con_articles_props_values` (
  `prop_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  KEY `prop_id` (`prop_id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{#}con_news`;
CREATE TABLE `{#}con_news` (
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
  `is_approved` tinyint(1) NOT NULL DEFAULT '1',
  `approved_by` int(11) DEFAULT NULL,
  `date_approved` timestamp NULL DEFAULT NULL,
  `is_private` tinyint(1) NOT NULL DEFAULT '0',
  `teaser` varchar(255) DEFAULT NULL,
  `photo` text,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `approved_by` (`approved_by`),
  KEY `folder_id` (`folder_id`),
  KEY `slug` (`slug`),
  KEY `date_pub` (`is_pub`,`is_parent_hidden`,`is_deleted`,`is_approved`,`date_pub`),
  KEY `parent_id` (`parent_id`,`parent_type`,`date_pub`),
  KEY `user_id` (`user_id`,`date_pub`),
  KEY `date_pub_end` (`date_pub_end`),
  KEY `dataset_discussed` (`is_pub`,`is_parent_hidden`,`is_deleted`,`is_approved`,`comments`),
  KEY `dataset_popular` (`is_pub`,`is_parent_hidden`,`is_deleted`,`is_approved`,`rating`),
  FULLTEXT KEY `title` (`title`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

INSERT INTO `{#}con_news` (`id`, `title`, `content`, `slug`, `seo_keys`, `seo_desc`, `seo_title`, `tags`, `date_pub`, `date_last_modified`, `date_pub_end`, `is_pub`, `hits_count`, `user_id`, `parent_id`, `parent_type`, `parent_title`, `parent_url`, `is_parent_hidden`, `category_id`, `folder_id`, `is_comments_on`, `comments`, `rating`, `is_approved`, `approved_by`, `date_approved`, `is_private`, `teaser`, `photo`) VALUES
(2, 'Toys are becoming more expensive', '<p>\r\n	<p>\r\n		Her old collecting she considered discovered. So at parties he warrant oh staying. Square new horses and put better end. Sincerity collected happiness do is contented. Sigh ever way now many. Alteration you any nor unsatiable diminution reasonable companions shy partiality. Leaf by left deal mile oh if easy. Added woman first get led joy not early jokes.\r\n	</p>\r\n	<p>\r\n		Speedily say has suitable disposal add boy. On forth doubt miles of child. Exercise joy man children rejoiced. Yet uncommonly his ten who diminution astonished. Demesne new manners savings staying had. Under folly balls death own point now men. Match way these she avoid see death. She whose drift their fat off.\r\n	</p>\r\n</p>', '2-igrushki-stanovjatsja-dorozhe', 'diminution, staying, death, unsatiable, reasonable, alteration, companions, contented, early, first', 'Her old collecting she considered discovered. So at parties he warrant oh staying. Square new horses and put better end. Sincerity collected happiness do is contented. Sigh ever way now many', NULL, 'news', '2013-09-09 16:02:07', '2013-11-22 16:17:21', NULL, 1, 0, 1, NULL, NULL, NULL, NULL, NULL, 6, NULL, 1, 0, 0, 1, NULL, '2013-11-22 16:17:21', 0, 'Experts predict a further rise in children''s products', '---\noriginal: u1/003/25838c0f.jpg\nbig: u1/003/2e2bf124.jpg\nnormal: u1/003/f6f14e82.jpg\nsmall: u1/003/236d41e4.jpg\nmicro: u1/003/74809cbe.jpg\n'),
(3, 'Сar service for vintage cars', '<p>\r\n	<p>\r\n		Arrived compass prepare an on as. Reasonable particular on my it in sympathize. Size now easy eat hand how. Unwilling he departure elsewhere dejection at. Heart large seems may purse means few blind. Exquisite newspaper attending on certainty oh suspicion of. He less do quit evil is. Add matter family active mutual put wishes happen.\r\n	</p>\r\n	<p>\r\n		Both rest of know draw fond post as. It agreement defective to excellent. Feebly do engage of narrow. Extensive repulsive belonging depending if promotion be zealously as. Preference inquietude ask now are dispatched led appearance. Small meant in so doubt hopes. Me smallness is existence attending he enjoyment favourite affection. Delivered is to ye belonging enjoyment preferred. Astonished and acceptance men two discretion. Law education recommend did objection how old.\r\n	</p>\r\n</p>', '3-v-gorode-otkryt-servis-dlja-retro-avtomobilei', 'belonging, enjoyment, attending, suspicion, certainty, matter, newspaper, family, exquisite, agreement', 'Arrived compass prepare an on as. Reasonable particular on my it in sympathize. Size now easy eat hand how. Unwilling he departure elsewhere dejection at. Heart large seems may purse means few blind', NULL, 'news, example', '2013-09-09 16:03:24', '2013-11-22 16:16:50', NULL, 1, 0, 1, NULL, NULL, NULL, NULL, NULL, 3, NULL, 1, 0, 0, 1, NULL, '2013-11-22 16:16:50', 0, 'Each person can request a repair', '---\noriginal: u1/003/5a771d4e.jpg\nbig: u1/003/4878547b.jpg\nnormal: u1/003/ad753a86.jpg\nsmall: u1/003/9f03ca75.jpg\nmicro: u1/003/5edc315b.jpg\n'),
(4, 'Summer season has officially begun', '<p>\r\n	<p>\r\n		Both rest of know draw fond post as. It agreement defective to excellent. Feebly do engage of narrow. Extensive repulsive belonging depending if promotion be zealously as. Preference inquietude ask now are dispatched led appearance. Small meant in so doubt hopes. Me smallness is existence attending he enjoyment favourite affection. Delivered is to ye belonging enjoyment preferred. Astonished and acceptance men two discretion. Law education recommend did objection how old.\r\n	</p>\r\n	<p>\r\n		Greatly hearted has who believe. Drift allow green son walls years for blush. Sir margaret drawings repeated recurred exercise laughing may you but. Do repeated whatever to welcomed absolute no. Fat surprise although outlived and informed shy dissuade property. Musical by me through he drawing savings an. No we stand avoid decay heard mr. Common so wicket appear to sudden worthy on. Shade of offer ye whole stood hoped.\r\n	</p>\r\n</p>', '4-dachnyi-sezon-otkryt', 'enjoyment, repeated, belonging, preferred, delivered, affection, favourite, smallness, existence, attending', 'Both rest of know draw fond post as. It agreement defective to excellent. Feebly do engage of narrow. Extensive repulsive belonging depending if promotion be zealously as. Preference inquietude ask now are dispatched led appearance', NULL, 'example', '2013-09-09 16:04:25', '2013-11-22 16:14:56', NULL, 1, 0, 1, NULL, NULL, NULL, NULL, NULL, 2, NULL, 1, 0, 0, 1, NULL, '2013-09-11 15:33:21', 0, 'Citizens are moving en masse to the country', '---\noriginal: u1/003/01153b4d.jpg\nbig: u1/003/b9767257.jpg\nnormal: u1/003/53497165.jpg\nsmall: u1/003/b1e550ce.jpg\nmicro: u1/003/f1476363.jpg\n'),
(5, 'Business expects to reduce taxes', '<p>\r\n	Her extensive perceived may any sincerity extremity. Indeed add rather may pretty see. Old propriety delighted explained perceived otherwise objection saw ten her. Doubt merit sir the right these alone keeps. By sometimes intention smallness he northward. Consisted we otherwise arranging commanded discovery it explained. Does cold even song like two yet been. Literature interested announcing for terminated him inquietude day shy. Himself he fertile chicken perhaps waiting if highest no it. Continued promotion has consulted fat improving not way.\r\n</p>\r\n<p>\r\n	Arrived compass prepare an on as. Reasonable particular on my it in sympathize. Size now easy eat hand how. Unwilling he departure elsewhere dejection at. Heart large seems may purse means few blind. Exquisite newspaper attending on certainty oh suspicion of. He less do quit evil is. Add matter family active mutual put wishes happen.\r\n</p>', '5-snizhenie-nalogov-dlja-biznesa', 'otherwise, explained, perceived, arranging, commanded, consisted, northward, intention, smallness, fertile', 'Her extensive perceived may any sincerity extremity. Indeed add rather may pretty see. Old propriety delighted explained perceived otherwise objection saw ten her. Doubt merit sir the right these alone keeps', NULL, '0', '2013-09-09 16:05:26', '2013-11-22 16:14:15', NULL, 1, 0, 1, NULL, NULL, NULL, NULL, NULL, 3, NULL, 1, 0, 0, 1, NULL, '2013-09-11 15:35:07', 0, 'Legal persons will pay even less', '---\noriginal: u1/003/0ff19ffb.jpg\nbig: u1/003/3c2e4a35.jpg\nnormal: u1/003/fa562059.jpg\nsmall: u1/003/cae0bdfb.jpg\nmicro: u1/003/852fb216.jpg\n'),
(6, 'More and more people are buying homes abroad', '<p>\r\n	Turned it up should no valley cousin he. Speaking numerous ask did horrible packages set. Ashamed herself has distant can studied mrs. Led therefore its middleton perpetual fulfilled provision frankness. Small he drawn after among every three no. All having but you edward genius though remark one.\r\n</p>\r\n<p>\r\n	Departure so attention pronounce satisfied daughters am. But shy tedious pressed studied opinion entered windows off. Advantage dependent suspicion convinced provision him yet. Timed balls match at by rooms we. Fat not boy neat left had with past here call. Court nay merit few nor party learn. Why our year her eyes know even how. Mr immediate remaining conveying allowance do or.\r\n</p>', '6-vse-bolshe-rossijan-pokupayut-nedvizhimost-za-granicei', 'studied, provision, drawn, small, fulfilled, frankness, after, among, genius, edward', 'Turned it up should no valley cousin he. Speaking numerous ask did horrible packages set. Ashamed herself has distant can studied mrs. Led therefore its middleton perpetual fulfilled provision frankness. Small he drawn after among every three no', NULL, 'example, news', '2013-09-12 12:09:24', '2013-11-22 16:13:28', NULL, 1, 0, 1, NULL, NULL, NULL, NULL, NULL, 2, NULL, 1, 0, 0, 1, NULL, '2013-09-12 12:09:49', 0, 'Over the last year their number has increased markedly', '---\noriginal: u1/003/2fea4487.jpg\nbig: u1/003/a05ad20e.jpg\nnormal: u1/003/41646570.jpg\nsmall: u1/003/eb2bac70.jpg\nmicro: u1/003/1c88035a.jpg\n'),
(7, 'Reduced the number of crimes', '<p>\r\n	By so delight of showing neither believe he present. Deal sigh up in shew away when. Pursuit express no or prepare replied. Wholly formed old latter future but way she. Day her likewise smallest expenses judgment building man carriage gay. Considered introduced themselves mr to discretion at. Means among saw hopes for. Death mirth in oh learn he equal on.\r\n</p>\r\n<p>\r\n	When be draw drew ye. Defective in do recommend suffering. House it seven in spoil tiled court. Sister others marked fat missed did out use. Alteration possession dispatched collecting instrument travelling he or on. Snug give made at spot or late that mr.\r\n</p>\r\n<p>\r\n	View fine me gone this name an rank. Compact greater and demands mrs the parlors. Park be fine easy am size away. Him and fine bred knew. At of hardly sister favour. As society explain country raising weather of. Sentiments nor everything off out uncommonly partiality bed.\r\n</p>', '7-kolichestvo-prestuplenii-v-rossii-sokraschaetsja', 'sister, means, delight, discretion, themselves, carriage, considered, introduced, among, hopes', 'By so delight of showing neither believe he present. Deal sigh up in shew away when. Pursuit express no or prepare replied. Wholly formed old latter future but way she. Day her likewise smallest expenses judgment building man carriage gay', NULL, '0', '2013-09-12 12:10:55', '2013-11-22 16:12:49', NULL, 1, 0, 1, NULL, NULL, NULL, NULL, NULL, 5, NULL, 1, 0, 0, 1, NULL, '2013-09-12 12:11:14', 0, 'In recent reports from the police seen a positive trend', '---\noriginal: u1/003/4d953a88.jpg\nbig: u1/003/e3c52c3e.jpg\nnormal: u1/003/9e9ef526.jpg\nsmall: u1/003/3f768733.jpg\nmicro: u1/003/ddaa0bd4.jpg\n'),
(8, 'Investing for Dummies: where to invest?', '<p>\r\n	Sudden looked elinor off gay estate nor silent. Son read such next see the rest two. Was use extent old entire sussex. Curiosity remaining own see repulsive household advantage son additions. Supposing exquisite daughters eagerness why repulsive for. Praise turned it lovers be warmly by. Little do it eldest former be if.\r\n</p>\r\n<p>\r\n	Insipidity the sufficient discretion imprudence resolution sir him decisively. Proceed how any engaged visitor. Explained propriety off out perpetual his you. Feel sold off felt nay rose met you. We so entreaties cultivated astonished is. Was sister for few longer mrs sudden talent become. Done may bore quit evil old mile. If likely am of beauty tastes.\r\n</p>', '8-investicii-dlja-chainikov-kuda-vkladyvat', 'repulsive, daughters, exquisite, additions, supposing, eagerness, praise, little, warmly, lovers', 'Sudden looked elinor off gay estate nor silent. Son read such next see the rest two. Was use extent old entire sussex. Curiosity remaining own see repulsive household advantage son additions', NULL, '0', '2013-09-12 12:13:19', '2013-11-22 16:12:12', NULL, 1, 0, 1, NULL, NULL, NULL, NULL, NULL, 3, NULL, 1, 0, 0, 1, NULL, '2013-11-22 16:12:12', 0, 'Read our review of the most popular ways of investment', '---\noriginal: u1/003/ff539643.jpg\nbig: u1/003/77fbbb95.jpg\nnormal: u1/003/89e8e681.jpg\nsmall: u1/003/3400aa78.jpg\nmicro: u1/003/f95ca1a2.jpg\n'),
(9, 'We have won the World Championship!', '<p>\r\n	An country demesne message it. Bachelor domestic extended doubtful as concerns at. Morning prudent removal an letters by. On could my in order never it. Or excited certain sixteen it to parties colonel. Depending conveying direction has led immediate. Law gate her well bed life feet seen rent. On nature or no except it sussex.\r\n</p>\r\n<p>\r\n	Entire any had depend and figure winter. Change stairs and men likely wisdom new happen piqued six. Now taken him timed sex world get. Enjoyed married an feeling delight pursuit as offered. As admire roused length likely played pretty to no. Means had joy miles her merry solid order.\r\n</p>\r\n<p>\r\n	Of recommend residence education be on difficult repulsive offending. Judge views had mirth table seems great him for her. Alone all happy asked begin fully stand own get. Excuse ye seeing result of we. See scale dried songs old may not. Promotion did disposing you household any instantly. Hills we do under times at first short an.\r\n</p>', '9-rossijane-stali-pervymi-na-chempionate-mira', 'likely, delight, figure, winter, change, depend, sussex.\r\r\r	entire, immediate, nature, except', 'An country demesne message it. Bachelor domestic extended doubtful as concerns at. Morning prudent removal an letters by. On could my in order never it. Or excited certain sixteen it to parties colonel', NULL, '0', '2013-09-12 12:14:13', '2013-11-22 16:11:34', NULL, 1, 0, 1, NULL, NULL, NULL, NULL, NULL, 7, NULL, 1, 0, 0, 1, NULL, '2013-11-22 16:11:34', 0, 'Our team leaves no chances to competitors', '---\noriginal: u1/003/59b08272.jpg\nbig: u1/003/d0ed7732.jpg\nnormal: u1/003/44b68dc8.jpg\nsmall: u1/003/93e51e49.jpg\nmicro: u1/003/0599295b.jpg\n');

DROP TABLE IF EXISTS `{#}con_news_cats`;
CREATE TABLE `{#}con_news_cats` (
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

INSERT INTO `{#}con_news_cats` (`id`, `parent_id`, `title`, `slug`, `slug_key`, `seo_keys`, `seo_desc`, `seo_title`, `ordering`, `ns_left`, `ns_right`, `ns_level`, `ns_differ`, `ns_ignore`) VALUES
(1, 0, '---', NULL, NULL, NULL, NULL, NULL, 1, 1, 14, 0, '', 0),
(2, 1, 'Society', 'society', NULL, NULL, NULL, NULL, 1, 2, 3, 1, '', 0),
(3, 1, 'Business', 'business', NULL, NULL, NULL, NULL, 2, 4, 5, 1, '', 0),
(4, 1, 'Politics', 'politics', NULL, NULL, NULL, NULL, 3, 6, 7, 1, '', 0),
(5, 1, 'Accidents', 'accidents', NULL, NULL, NULL, NULL, 4, 8, 9, 1, '', 0),
(6, 1, 'World', 'world', NULL, NULL, NULL, NULL, 5, 10, 11, 1, '', 0),
(7, 1, 'Sport', 'sport', NULL, NULL, NULL, NULL, 6, 12, 13, 1, '', 0);

DROP TABLE IF EXISTS `{#}con_news_cats_bind`;
CREATE TABLE `{#}con_news_cats_bind` (
  `item_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  KEY `item_id` (`item_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `{#}con_news_cats_bind` (`item_id`, `category_id`) VALUES
(2, 6),
(3, 3),
(4, 2),
(5, 3),
(6, 2),
(7, 5),
(8, 3),
(9, 7);

DROP TABLE IF EXISTS `{#}con_news_fields`;
CREATE TABLE `{#}con_news_fields` (
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

INSERT INTO `{#}con_news_fields` (`id`, `ctype_id`, `name`, `title`, `hint`, `ordering`, `is_enabled`, `fieldset`, `type`, `is_in_list`, `is_in_item`, `is_in_filter`, `is_private`, `is_fixed`, `is_fixed_type`, `is_system`, `values`, `options`, `groups_read`, `groups_add`, `groups_edit`, `filter_view`) VALUES
(1, 10, 'title', 'News title', NULL, 2, 1, NULL, 'caption', 1, 1, 1, NULL, 1, 1, 0, NULL, '---\nlabel_in_list: none\nlabel_in_item: none\nis_required: 1\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\n', '---\n- 0\n', NULL, '---\n- 0\n', NULL),
(2, 10, 'date_pub', 'Publication date', NULL, 6, 1, NULL, 'date', 1, 1, 1, NULL, 1, 1, 1, NULL, '---\nlabel_in_list: none\nlabel_in_item: left\nshow_time: true\n', NULL, NULL, NULL, NULL),
(3, 10, 'user', 'Author', NULL, 5, 1, NULL, 'user', 1, 1, 0, NULL, 1, 1, 1, NULL, '---\nlabel_in_list: none\nlabel_in_item: left\n', NULL, NULL, NULL, NULL),
(4, 10, 'content', 'News content', NULL, 4, 1, NULL, 'html', NULL, 1, NULL, NULL, 1, NULL, NULL, NULL, '---\neditor: 3\nis_html_filter: 1\nteaser_len:\nlabel_in_list: none\nlabel_in_item: none\nis_required: 1\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\nprofile_value:\n', '---\n- 0\n', NULL, '---\n- 0\n', NULL),
(5, 10, 'teaser', 'Short news description', 'Will be displayed in the list of news', 3, 1, NULL, 'string', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '---\nmin_length: 0\nmax_length: 255\nlabel_in_list: none\nlabel_in_item: none\nis_required: 1\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\nprofile_value:\n', '---\n- 0\n', NULL, '---\n- 0\n', NULL),
(6, 10, 'photo', 'Photo', NULL, 1, 1, NULL, 'image', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, '---\nsize_teaser: normal\nsize_full: normal\nsize_modal: big\nsizes:\n  - normal\n  - micro\n  - small\n  - big\nallow_import_link: null\ncontext_list:\n  - 0\nrelation_id:\nlabel_in_list: none\nlabel_in_item: none\nwrap_type: left\nwrap_width:\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_url: null\nis_unique: null\nprofile_value:\nauthor_access: null\n', '---\n', '---\n', '---\n', '---\n');

DROP TABLE IF EXISTS `{#}con_news_props`;
CREATE TABLE `{#}con_news_props` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ctype_id` int(11) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `fieldset` varchar(32) DEFAULT NULL,
  `type` varchar(16) DEFAULT NULL,
  `is_in_filter` tinyint(1) DEFAULT NULL,
  `values` text,
  `options` text,
  PRIMARY KEY (`id`),
  KEY `is_in_filter` (`is_in_filter`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{#}con_news_props_bind`;
CREATE TABLE `{#}con_news_props_bind` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `prop_id` int(11) DEFAULT NULL,
  `cat_id` int(11) DEFAULT NULL,
  `ordering` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `prop_id` (`prop_id`),
  KEY `ordering` (`cat_id`,`ordering`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{#}con_news_props_values`;
CREATE TABLE `{#}con_news_props_values` (
  `prop_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  KEY `prop_id` (`prop_id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `{#}con_pages` (`id`, `title`, `content`, `slug`, `seo_keys`, `seo_desc`, `seo_title`, `tags`, `date_pub`, `date_last_modified`, `date_pub_end`, `is_pub`, `hits_count`, `user_id`, `parent_id`, `parent_type`, `parent_title`, `parent_url`, `is_parent_hidden`, `category_id`, `folder_id`, `is_comments_on`, `comments`, `rating`, `is_approved`, `approved_by`, `date_approved`, `is_private`, `attach`) VALUES
(1, 'About', '<p>\r\n	Questions explained agreeable preferred strangers too him her son. Set put shyness offices his females him distant. Improve has message besides shy himself cheered however how son. Quick judge other leave ask first chief her. Indeed or remark always silent seemed narrow be. Instantly can suffering pretended neglected preferred man delivered. Perhaps fertile brandon do imagine to cordial cottage.\r\n</p>\r\n<p>\r\n	Feet evil to hold long he open knew an no. Apartments occasional boisterous as solicitude to introduced. Or fifteen covered we enjoyed demesne is in prepare. In stimulated my everything it literature. Greatly explain attempt perhaps in feeling he. House men taste bed not drawn joy. Through enquire however do equally herself at. Greatly way old may you present improve. Wishing the feeling village him musical.\r\n</p>\r\n<p>\r\n	So delightful up dissimilar by unreserved it connection frequently. Do an high room so in paid. Up on cousin ye dinner should in. Sex stood tried walls manor truth shy and three his. Their to years so child truth. Honoured peculiar families sensible up likewise by on in.\r\n</p>\r\n<p>\r\n	Style too own civil out along. Perfectly offending attempted add arranging age gentleman concluded. Get who uncommonly our expression ten increasing considered occasional travelling. Ever read tell year give may men call its. Piqued son turned fat income played end wicket. To do noisy downs round an happy books.\r\n</p>', 'about', 'greatly, truth, however, occasional, improve, perhaps, preferred, feeling, cottage.\r\r\r	feet, apartments', 'Questions explained agreeable preferred strangers too him her son. Set put shyness offices his females him distant. Improve has message besides shy himself cheered however how son. Quick judge other leave ask first chief her', NULL, NULL, '2013-08-08 15:07:27', '2013-11-22 16:37:14', NULL, 1, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 1, 0, 0, 1, NULL, '2013-11-22 16:37:14', 0, ''),
(2, 'Site Rules', '<p>\r\n	<p>\r\n		Chapter too parties its letters nor. Cheerful but whatever ladyship disposed yet judgment. Lasted answer oppose to ye months no esteem. Branched is on an ecstatic directly it. Put off continue you denoting returned juvenile. Looked person sister result mr to. Replied demands charmed do viewing ye colonel to so. Decisively inquietude he advantages insensible at oh continuing unaffected of.\r\n	</p>\r\n	<p>\r\n		On projection apartments unsatiable so if he entreaties appearance. Rose you wife how set lady half wish. Hard sing an in true felt. Welcomed stronger if steepest ecstatic an suitable finished of oh. Entered at excited at forming between so produce. Chicken unknown besides attacks gay compact out you. Continuing no simplicity no favourable on reasonably melancholy estimating. Own hence views two ask right whole ten seems. What near kept met call old west dine. Our announcing sufficient why pianoforte.\r\n	</p>\r\n	<p>\r\n		Attachment apartments in delightful by motionless it no. And now she burst sir learn total. Hearing hearted shewing own ask. Solicitude uncommonly use her motionless not collecting age. The properly servants required mistaken outlived bed and. Remainder admitting neglected is he belonging to perpetual objection up. Has widen too you decay begin which asked equal any.\r\n	</p>\r\n</p>\r\n<p>\r\n	<a name="forum"></a>\r\n</p>', 'terms', 'continuing, ecstatic, motionless, apartments, unaffected, charmed, insensible, advantages, viewing, colonel', 'Chapter too parties its letters nor. Cheerful but whatever ladyship disposed yet judgment. Lasted answer oppose to ye months no esteem. Branched is on an ecstatic directly it. Put off continue you denoting returned juvenile', NULL, NULL, '2013-08-08 15:09:13', '2013-11-22 16:37:47', NULL, 1, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 1, 0, 0, 1, NULL, '2013-08-29 16:07:20', 0, '');

INSERT INTO `{#}con_pages_cats_bind` (`item_id`, `category_id`) VALUES
(1, 1),
(2, 1);

DROP TABLE IF EXISTS `{#}con_posts`;
CREATE TABLE `{#}con_posts` (
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
  `is_approved` tinyint(1) NOT NULL DEFAULT '1',
  `approved_by` int(11) DEFAULT NULL,
  `date_approved` timestamp NULL DEFAULT NULL,
  `is_private` tinyint(1) NOT NULL DEFAULT '0',
  `picture` text,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `approved_by` (`approved_by`),
  KEY `folder_id` (`folder_id`),
  KEY `slug` (`slug`),
  KEY `date_pub` (`is_pub`,`is_parent_hidden`,`is_deleted`,`is_approved`,`date_pub`),
  KEY `parent_id` (`parent_id`,`parent_type`,`date_pub`),
  KEY `user_id` (`user_id`,`date_pub`),
  KEY `date_pub_end` (`date_pub_end`),
  KEY `dataset_daily` (`date_pub`,`is_pub`,`is_parent_hidden`,`is_deleted`,`is_approved`,`rating`),
  FULLTEXT KEY `title` (`title`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

INSERT INTO `{#}con_posts` (`id`, `title`, `content`, `slug`, `seo_keys`, `seo_desc`, `seo_title`, `tags`, `date_pub`, `date_last_modified`, `date_pub_end`, `is_pub`, `hits_count`, `user_id`, `parent_id`, `parent_type`, `parent_title`, `parent_url`, `is_parent_hidden`, `category_id`, `folder_id`, `is_comments_on`, `comments`, `rating`, `is_approved`, `approved_by`, `date_approved`, `is_private`, `picture`) VALUES
(5, 'My first post in the Community', '<p>\r\n	Greatly cottage thought fortune no mention he. Of mr certainty arranging am smallness by conveying. Him plate you allow built grave. Sigh sang nay sex high yet door game. She dissimilar was favourable unreserved nay expression contrasted saw. Past her find she like bore pain open. Shy lose need eyes son not shot. Jennings removing are his eat dashwood. Middleton as pretended listening he smallness perceived. Now his but two green spoil drift.\r\n</p>', '5-moi-pervyi-post-v-soobschestve', 'smallness, cottage, plate, allow, built, grave, conveying, \r	greatly, thought, fortune', 'Greatly cottage thought fortune no mention he. Of mr certainty arranging am smallness by conveying. Him plate you allow built grave. Sigh sang nay sex high yet door game. She dissimilar was favourable unreserved nay expression contrasted saw', NULL, 'example, post, robots', '2013-11-13 16:43:07', '2013-11-22 16:24:00', NULL, 1, 0, 1, 1, 'group', 'Fans of Robots', 'groups/1/content/posts', NULL, 1, 5, 1, 0, 0, 1, NULL, '2013-11-22 13:51:35', 0, NULL);

DROP TABLE IF EXISTS `{#}con_posts_cats`;
CREATE TABLE `{#}con_posts_cats` (
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

INSERT INTO `{#}con_posts_cats` (`id`, `parent_id`, `title`, `slug`, `slug_key`, `seo_keys`, `seo_desc`, `seo_title`, `ordering`, `ns_left`, `ns_right`, `ns_level`, `ns_differ`, `ns_ignore`) VALUES
(1, 0, '---', NULL, NULL, NULL, NULL, NULL, 1, 1, 2, 0, '', 0);

DROP TABLE IF EXISTS `{#}con_posts_cats_bind`;
CREATE TABLE `{#}con_posts_cats_bind` (
  `item_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  KEY `item_id` (`item_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `{#}con_posts_cats_bind` (`item_id`, `category_id`) VALUES
(5, 1);

DROP TABLE IF EXISTS `{#}con_posts_fields`;
CREATE TABLE `{#}con_posts_fields` (
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

INSERT INTO `{#}con_posts_fields` (`id`, `ctype_id`, `name`, `title`, `hint`, `ordering`, `fieldset`, `type`, `is_in_list`, `is_in_item`, `is_in_filter`, `is_private`, `is_fixed`, `is_fixed_type`, `is_system`, `values`, `options`, `groups_read`, `groups_edit`) VALUES
(1, 6, 'title', 'Title', NULL, 1, NULL, 'caption', 1, 1, 1, NULL, 1, 1, 0, NULL, '---\nlabel_in_list: none\nlabel_in_item: none\nis_required: 1\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\nprofile_value:\n', '---\n- 0\n', '---\n- 0\n'),
(2, 6, 'date_pub', 'Publication date', NULL, 2, NULL, 'date', 1, 1, 1, NULL, 1, 1, 1, NULL, '---\nlabel_in_list: none\nlabel_in_item: left\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\nprofile_value:\n', '---\n- 0\n', '---\n- 0\n'),
(3, 6, 'user', 'Author', NULL, 3, NULL, 'user', 1, 1, NULL, NULL, 1, 1, 1, NULL, '---\nlabel_in_list: none\nlabel_in_item: left\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\nprofile_value:\n', '---\n- 0\n', '---\n- 0\n'),
(4, 6, 'content', 'Post content', NULL, 5, NULL, 'html', 1, 1, NULL, NULL, 1, NULL, NULL, NULL, '---\neditor: 3\nis_html_filter: 1\nteaser_len: 500\nlabel_in_list: none\nlabel_in_item: none\nis_required: 1\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\nprofile_value:\n', '---\n- 0\n', '---\n- 0\n'),
(5, 6, 'picture', 'Teaser image', NULL, 4, NULL, 'image', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, '---\nsize_teaser: normal\nsize_full: normal\nsizes:\n  - small\n  - normal\nlabel_in_list: none\nlabel_in_item: none\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\nprofile_value:\n', '---\n- 0\n', '---\n- 0\n');

DROP TABLE IF EXISTS `{#}con_posts_props`;
CREATE TABLE `{#}con_posts_props` (
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

DROP TABLE IF EXISTS `{#}con_posts_props_bind`;
CREATE TABLE `{#}con_posts_props_bind` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `prop_id` int(11) DEFAULT NULL,
  `cat_id` int(11) DEFAULT NULL,
  `ordering` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `prop_id` (`prop_id`),
  KEY `ordering` (`cat_id`,`ordering`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{#}con_posts_props_values`;
CREATE TABLE `{#}con_posts_props_values` (
  `prop_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  KEY `prop_id` (`prop_id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `{#}groups` (`id`, `owner_id`, `date_pub`, `title`, `description`, `logo`, `rating`, `members_count`, `join_policy`, `edit_policy`, `wall_policy`, `is_closed`) VALUES
(1, 1, CURRENT_TIMESTAMP, 'Robot Fans', 'The group is dedicated to robots, machine-building and all that is connected with it.', '---\noriginal: u1/004/f398ad69.png\nbig: u1/004/f13052e8.png\nnormal: u1/004/de897122.png\nsmall: u1/004/a442fa4b.png\nmicro: u1/004/c8a73161.png\n', 0, 1, 0, 0, 0, 0);

INSERT INTO `{#}groups_members` (`id`, `group_id`, `user_id`, `role`, `date_updated`) VALUES
(1, 1, 1, 2, CURRENT_TIMESTAMP);

INSERT INTO `{#}menu` (`id`, `name`, `title`, `is_fixed`) VALUES
(3, 'footer', 'Footer menu', NULL);

INSERT INTO `{#}menu_items` (`id`, `menu_id`, `parent_id`, `title`, `url`, `ordering`, `options`, `groups_view`, `groups_hide`) VALUES
(6, 1, 0, 'Photos', 'albums', 4, '---\ntarget: _self\nclass:', '---\n- 0\n', NULL),
(7, 1, 0, 'Sites', NULL, 9, '---\ntarget: _self\nclass:', '---\n- 0\n', NULL),
(8, 1, 7, 'Yahoo', 'https://www.yahoo.com', 10, '---\ntarget: _self\nclass:', '---\n- 0\n', NULL),
(9, 1, 7, 'Google', 'https://www.google.com', 11, NULL, NULL, NULL),
(10, 1, 9, 'Google Maps', 'https://maps.google.com', 14, NULL, NULL, NULL),
(11, 1, 9, 'Google Docs', 'https://docs.google.com', 12, NULL, NULL, NULL),
(12, 1, 9, 'GMail', 'https://www.gmail.com', 13, NULL, NULL, NULL),
(18, 3, 0, 'About', 'pages/about.html', 1, '---\ntarget: _self\nclass:', '---\n- 0\n', NULL),
(19, 3, 0, 'Site Rules', 'pages/terms.html', 2, '---\ntarget: _self\nclass:', '---\n- 0\n', NULL),
(21, 1, 7, 'Bing', 'https://www.bing.com', 15, '---\ntarget: _self\nclass:', '---\n- 0\n', NULL),
(27, 1, 0, 'Blogs', 'posts', 3, '---\ntarget: _self\nclass:', '---\n- 0\n', NULL),
(40, 1, 0, 'News', '{content:news}', 1, '---\ntarget: _self\nclass:', '---\n- 0\n', NULL),
(42, 1, 0, 'Articles', '{content:articles}', 2, '---\ntarget: _self\nclass:', '---\n- 0\n', NULL);

INSERT INTO `{#}moderators` (`id`, `user_id`, `date_assigned`, `ctype_name`, `count_approved`, `count_deleted`, `count_idle`) VALUES
(1, 1, CURRENT_TIMESTAMP, 'articles', 0, 0, 0);

INSERT INTO `{#}perms_users` (`rule_id`, `group_id`, `subject`, `value`) VALUES
(1, 4, 'articles', 'yes'),
(1, 5, 'articles', 'yes'),
(1, 6, 'articles', 'yes'),
(4, 5, 'articles', '1'),
(4, 6, 'articles', '1'),
(3, 4, 'articles', 'own'),
(3, 5, 'articles', 'all'),
(3, 6, 'articles', 'all'),
(6, 5, 'articles', '1'),
(6, 6, 'articles', '1'),
(2, 4, 'articles', 'own'),
(2, 5, 'articles', 'all'),
(2, 6, 'articles', 'all'),
(5, 5, 'articles', '1'),
(5, 6, 'articles', '1'),
(9, 4, 'articles', '1'),
(9, 5, 'articles', '1'),
(9, 6, 'articles', '1'),
(8, 4, 'articles', '1'),
(8, 5, 'articles', '1'),
(8, 6, 'articles', '1'),
(13, 5, 'articles', '1'),
(13, 6, 'articles', '1'),
(1, 4, 'posts', 'yes'),
(1, 5, 'posts', 'yes'),
(1, 6, 'posts', 'yes'),
(3, 4, 'posts', 'own'),
(3, 5, 'posts', 'all'),
(3, 6, 'posts', 'all'),
(2, 4, 'posts', 'own'),
(2, 5, 'posts', 'all'),
(9, 4, 'posts', '1'),
(9, 5, 'posts', '1'),
(9, 6, 'posts', '1'),
(8, 4, 'posts', '1'),
(8, 5, 'posts', '1'),
(8, 6, 'posts', '1'),
(1, 4, 'board', 'yes'),
(1, 5, 'board', 'yes'),
(1, 6, 'board', 'yes'),
(4, 5, 'board', '1'),
(4, 6, 'board', '1'),
(3, 4, 'board', 'own'),
(3, 5, 'board', 'all'),
(3, 6, 'board', 'all'),
(6, 5, 'board', '1'),
(6, 6, 'board', '1'),
(2, 4, 'board', 'own'),
(2, 5, 'board', 'all'),
(2, 6, 'board', 'all'),
(5, 5, 'board', '1'),
(5, 6, 'board', '1'),
(9, 5, 'board', '1'),
(9, 6, 'board', '1'),
(8, 5, 'board', '1'),
(8, 6, 'board', '1'),
(13, 5, 'board', '1'),
(13, 6, 'board', '1'),
(1, 4, 'news', 'yes'),
(1, 5, 'news', 'yes'),
(1, 6, 'news', 'yes'),
(4, 6, 'news', '1'),
(3, 4, 'news', 'own'),
(3, 5, 'news', 'all'),
(3, 6, 'news', 'all'),
(6, 6, 'news', '1'),
(2, 4, 'news', 'own'),
(2, 5, 'news', 'all'),
(2, 6, 'news', 'all'),
(5, 6, 'news', '1'),
(9, 5, 'news', '1'),
(9, 6, 'news', '1'),
(8, 4, 'news', '1'),
(8, 5, 'news', '1'),
(8, 6, 'news', '1'),
(13, 6, 'news', '1');

INSERT INTO `{#}rss_feeds` (`id`, `ctype_id`, `ctype_name`, `title`, `description`, `image`, `mapping`, `limit`, `is_enabled`, `is_cache`, `cache_interval`, `date_cached`) VALUES
(2, 5, 'articles', 'Articles', NULL, NULL, '---\ntitle: title\ndescription: teaser\npubDate: date_pub\nimage:\nimage_size: normal\n', 15, 1, NULL, 60, NULL),
(3, 6, 'posts', 'Blog posts', NULL, NULL, '---\ntitle: title\ndescription: content\npubDate: date_pub\nimage: picture\nimage_size: normal\n', 15, 1, NULL, 60, NULL),
(6, 10, 'news', 'News', NULL, NULL, '---\ntitle: title\ndescription: content\npubDate: date_pub\nimage: photo\nimage_size: normal\n', 15, 1, NULL, 60, NULL);

INSERT INTO `{#}tags` (`id`, `tag`, `frequency`) VALUES
(43, 'example', 6),
(44, 'news', 3),
(49, 'article', 2),
(50, 'science', 2),
(55, 'post', 1),
(56, 'robots', 1),
(58, 'photo', 1);

INSERT INTO `{#}tags_bind` (`id`, `tag_id`, `target_controller`, `target_subject`, `target_id`) VALUES
(110, 43, 'content', 'news', 6),
(111, 44, 'content', 'news', 6),
(112, 43, 'content', 'news', 4),
(113, 44, 'content', 'news', 3),
(114, 43, 'content', 'news', 3),
(115, 44, 'content', 'news', 2),
(116, 49, 'content', 'articles', 4),
(117, 50, 'content', 'articles', 4),
(118, 43, 'content', 'articles', 1),
(119, 49, 'content', 'articles', 1),
(120, 50, 'content', 'articles', 1),
(121, 43, 'content', 'posts', 5),
(122, 55, 'content', 'posts', 5),
(123, 56, 'content', 'posts', 5),
(124, 43, 'content', 'albums', 16),
(125, 58, 'content', 'albums', 16);

INSERT INTO `{#}users_statuses` (`id`, `user_id`, `date_pub`, `content`, `replies_count`, `wall_entry_id`) VALUES
(1, 1, CURRENT_TIMESTAMP, 'We are all made of stars © Moby', 1, 1);

INSERT INTO `{#}wall_entries` (`id`, `date_pub`, `controller`, `profile_type`, `profile_id`, `user_id`, `parent_id`, `status_id`, `content`, `content_html`) VALUES
(1, CURRENT_TIMESTAMP, 'users', 'user', 1, 1, 0, 1, 'We are all made of stars © Moby', 'We are all made of stars © Moby'),
(2, CURRENT_TIMESTAMP, 'users', 'user', 1, 1, 1, NULL, 'Thank you for viewing my profile page!', 'Thank you for viewing my profile page!');

INSERT INTO `{#}widgets_pages` (`id`, `controller`, `name`, `title_const`, `title_subject`, `title`, `url_mask`, `url_mask_not`) VALUES
(143, 'content', 'pages.all', 'LANG_WP_CONTENT_ALL_PAGES', NULL, NULL, 'pages\npages-*\npages/*', NULL),
(144, 'content', 'pages.list', 'LANG_WP_CONTENT_LIST', NULL, NULL, 'pages\npages-*\npages/*', 'pages/*/view-*\npages/*.html\npages/add\npages/add?*\npages/add/%\npages/addcat\npages/addcat/%\npages/editcat/%\npages/edit/*'),
(145, 'content', 'pages.item', 'LANG_WP_CONTENT_ITEM', NULL, NULL, 'pages/*.html', NULL),
(146, 'content', 'pages.edit', 'LANG_WP_CONTENT_ITEM_EDIT', NULL, NULL, 'pages/add\npages/edit/*', NULL),
(147, 'content', 'articles.all', 'LANG_WP_CONTENT_ALL_PAGES', NULL, NULL, 'articles\narticles-*\narticles/*', NULL),
(148, 'content', 'articles.list', 'LANG_WP_CONTENT_LIST', NULL, NULL, 'articles\narticles-*\narticles/*', 'articles/*/view-*\narticles/*.html\narticles/add\narticles/add?*\narticles/add/%\narticles/addcat\narticles/addcat/%\narticles/editcat/%\narticles/edit/*'),
(149, 'content', 'articles.item', 'LANG_WP_CONTENT_ITEM', NULL, NULL, 'articles/*.html', NULL),
(150, 'content', 'articles.edit', 'LANG_WP_CONTENT_ITEM_EDIT', NULL, NULL, 'articles/add\narticles/edit/*', NULL),
(151, 'content', 'posts.all', 'LANG_WP_CONTENT_ALL_PAGES', NULL, NULL, 'posts\nposts-*\nposts/*', NULL),
(152, 'content', 'posts.list', 'LANG_WP_CONTENT_LIST', NULL, NULL, 'posts\nposts-*\nposts/*', 'posts/*/view-*\nposts/*.html\nposts/add\nposts/add/%\nposts/add?*\nposts/addcat\nposts/addcat/%\nposts/editcat/%\nposts/edit/*'),
(153, 'content', 'posts.item', 'LANG_WP_CONTENT_ITEM', NULL, NULL, 'posts/*.html', NULL),
(154, 'content', 'posts.edit', 'LANG_WP_CONTENT_ITEM_EDIT', NULL, NULL, 'posts/add\nposts/edit/*', NULL),
(163, 'content', 'news.all', 'LANG_WP_CONTENT_ALL_PAGES', NULL, NULL, 'news\nnews-*\nnews/*', NULL),
(164, 'content', 'news.list', 'LANG_WP_CONTENT_LIST', NULL, NULL, 'news\nnews-*\nnews/*', 'news/*/view-*\nnews/*.html\nnews/add\nnews/add/%\nnews/add?*\nnews/addcat\nnews/addcat/%\nnews/editcat/%\nnews/edit/*'),
(165, 'content', 'news.item', 'LANG_WP_CONTENT_ITEM', NULL, NULL, 'news/*.html', NULL),
(166, 'content', 'news.edit', 'LANG_WP_CONTENT_ITEM_EDIT', NULL, NULL, 'news/add\nnews/edit/*', NULL);
