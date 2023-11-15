INSERT INTO `{#}activity` (`id`, `type_id`, `user_id`, `group_id`, `subject_title`, `subject_id`, `subject_url`, `reply_url`, `images`, `images_count`, `date_pub`, `is_private`, `is_parent_hidden`) VALUES
(3, 13, 1, NULL, 'Эллиптический перигей в XXI веке', 1, 'articles/1-ellipticheskii-perigei-v-xxi-veke.html', NULL, NULL, NULL, DATE_SUB(NOW(),INTERVAL 13 DAY), 0, NULL),
(6, 13, 1, NULL, 'Недонасыщенный алмаз: предпосылки и развитие', 4, 'articles/4-nedonasyschennyi-almaz-predposylki-i-razvitie.html', NULL, NULL, NULL, DATE_SUB(NOW(),INTERVAL 12 DAY), 0, NULL),
(10, 12, 1, NULL, 'We are all made of stars © Moby', NULL, NULL, 'users/1?wid=1&reply=1', NULL, NULL, CURRENT_TIMESTAMP, 0, NULL),
(17, 1, 1, NULL, 'О проекте', 1, 'pages/about.html', NULL, NULL, NULL, DATE_SUB(NOW(),INTERVAL 11 DAY), 0, NULL),
(18, 1, 1, NULL, 'Правила сайта', 2, 'pages/rules.html', NULL, NULL, NULL, DATE_SUB(NOW(),INTERVAL 10 DAY), 0, NULL),
(32, 17, 1, NULL, 'На улице 22 Партсъезда прорвало трубы с водой', 1, 'news/1-na-ulice-prorvalo-truby.html', NULL, NULL, NULL, DATE_SUB(NOW(),INTERVAL 9 DAY), 0, NULL),
(33, 17, 1, NULL, 'Игрушки становятся дороже', 2, 'news/2-igrushki-stanovjatsja-dorozhe.html', NULL, NULL, NULL, DATE_SUB(NOW(),INTERVAL 8 DAY), 0, NULL),
(34, 17, 1, NULL, 'В городе открыт сервис для ретро-автомобилей', 3, 'news/3-v-gorode-otkryt-servis-dlja-retro-avtomobilei.html', NULL, NULL, NULL, DATE_SUB(NOW(),INTERVAL 7 DAY), 0, NULL),
(35, 17, 1, NULL, 'Дачный сезон на Урале официально начался', 4, 'news/4-dachnyi-sezon-otkryt.html', NULL, NULL, NULL, DATE_SUB(NOW(),INTERVAL 6 DAY), 0, NULL),
(36, 17, 1, NULL, 'Бизнес ожидает снижения налогов', 5, 'news/5-snizhenie-nalogov-dlja-biznesa.html', NULL, NULL, NULL, DATE_SUB(NOW(),INTERVAL 5 DAY), 0, NULL),
(37, 17, 1, NULL, 'Все больше россиян покупают дома за границей', 6, 'news/6-vse-bolshe-rossijan-pokupayut-nedvizhimost-za-granicei.html', NULL, NULL, NULL, DATE_SUB(NOW(),INTERVAL 4 DAY), 0, NULL),
(38, 17, 1, NULL, 'Количество преступлений в России сокращается', 7, 'news/7-kolichestvo-prestuplenii-v-rossii-sokraschaetsja.html', NULL, NULL, NULL, DATE_SUB(NOW(),INTERVAL 3 DAY), 0, NULL),
(40, 17, 1, NULL, 'Россияне стали первыми на Чемпионате Мира', 9, 'news/9-rossijane-stali-pervymi-na-chempionate-mira.html', NULL, NULL, NULL, DATE_SUB(NOW(),INTERVAL 2 DAY), 0, NULL),
(56, 13, 1, NULL, 'Мифологический реципиент', 10, 'articles/10-mifologicheskii-recipient.html', NULL, NULL, NULL, DATE_SUB(NOW(),INTERVAL 1 DAY), 0, NULL),
(57, 13, 1, NULL, 'Общественный анализ зарубежного опыта', 11, 'articles/11-obschestvennyi-analiz-zarubezhnogo-opyta.html', NULL, NULL, NULL, DATE_SUB(NOW(),INTERVAL 7 MINUTE), 0, NULL),
(67, 14, 1, 1, 'Мой первый пост в сообществе', 5, 'posts/5-moi-pervyi-post-v-soobschestve.html', NULL, NULL, NULL, DATE_SUB(NOW(),INTERVAL 6 MINUTE), 0, NULL),
(69, 16, 1, NULL, 'Продам квартиру в новостройке', 7, 'board/7-prodam-kvartiru-v-novostroike.html', NULL, NULL, NULL, DATE_SUB(NOW(),INTERVAL 5 MINUTE), 0, NULL);

INSERT INTO `{#}activity_types` (`id`, `is_enabled`, `controller`, `name`, `title`, `description`) VALUES
(13, 1, 'content', 'add.articles', 'Добавление статей', 'добавляет статью %s'),
(14, 1, 'content', 'add.posts', 'Добавление постов', 'добавляет пост %s'),
(15, 0, 'content', 'add.albums', 'Добавление альбомов', 'добавляет фотоальбом %s'),
(16, 1, 'content', 'add.board', 'Добавление объявлений', 'добавляет объявление %s'),
(17, 1, 'content', 'add.news', 'Добавление новостей', 'добавляет новость %s');

INSERT INTO `{#}comments` (`id`, `parent_id`, `level`, `ordering`, `user_id`, `date_pub`, `target_controller`, `target_subject`, `target_id`, `target_url`, `target_title`, `author_name`, `author_email`, `author_ip`, `content`, `content_html`, `is_deleted`, `is_private`, `rating`) VALUES
(3, 0, 1, 1, 6, DATE_SUB(NOW(),INTERVAL 4 MINUTE), 'content', 'articles', 4, 'articles/4-nedonasyschennyi-almaz-predposylki-i-razvitie.html', 'Недонасыщенный алмаз: предпосылки и развитие', NULL, NULL, NULL, 'Базальтовый слой <i>разогревает сталактит</i>, где присутствуют моренные суглинки днепровского возраста.', 'Базальтовый слой <i>разогревает сталактит</i>, где присутствуют моренные суглинки днепровского возраста.', 1, 0, 1),
(4, 0, 1, 1, 1, DATE_SUB(NOW(),INTERVAL 3 MINUTE), 'content', 'articles', 11, 'articles/11-obschestvennyi-analiz-zarubezhnogo-opyta.html', 'Общественный анализ зарубежного опыта', NULL, NULL, NULL, 'Отличная статья, все очень доходчиво и познавательно', 'Отличная статья, все очень доходчиво и познавательно', NULL, 0, 0),
(5, 4, 2, 2, 1, DATE_SUB(NOW(),INTERVAL 1 MINUTE), 'content', 'articles', 11, 'articles/11-obschestvennyi-analiz-zarubezhnogo-opyta.html', 'Общественный анализ зарубежного опыта', NULL, NULL, NULL, 'Ответ на комментарий выше', 'Ответ на комментарий выше', NULL, 0, 0);

INSERT INTO `{#}content_datasets` (`id`, `ctype_id`, `name`, `title`, `ordering`, `is_visible`, `filters`, `sorting`, `index`, `groups_view`, `groups_hide`) VALUES
(1, 5, 'all', 'Все', 1, 1, NULL, '---\n- \n  by: date_pub\n  to: desc\n', 'date_pub', '---\n- 0\n', NULL),
(2, 5, 'reviews', 'Рецензии', 2, 1, '---\n- \n  field: kind\n  condition: eq\n  value: 2\n', '---\n- \n  by: date_pub\n  to: desc\n', 'dataset_reviews', '---\n- 0\n', NULL),
(3, 5, 'translations', 'Переводы', 3, 1, '---\n- \n  field: kind\n  condition: eq\n  value: 3\n', '---\n- \n  by: date_pub\n  to: desc\n', 'dataset_reviews', '---\n- 0\n', NULL),
(4, 5, 'featured', 'Выбор редакции', 4, 1, '---\n- \n  field: featured\n  condition: eq\n  value: 1\n', '---\n- \n  by: date_pub\n  to: desc\n', 'dataset_featured', '---\n- 0\n', NULL),
(5, 5, 'rating', 'Рейтинг', 5, 1, '---\n- \n  field: rating\n  condition: gt\n  value: 0\n', '---\n- \n  by: rating\n  to: desc\n', 'dataset_rating', '---\n- 0\n', NULL),
(6, 6, 'latest', 'Новые', 1, 1, NULL, '---\n- \n  by: date_pub\n  to: desc\n', 'date_pub', '---\n- 0\n', NULL),
(7, 6, 'daily', 'Лучшие за сутки', 2, 1, '---\n- \n  field: date_pub\n  condition: dy\n  value: 1\n', '---\n- \n  by: rating\n  to: desc\n', 'dataset_daily', '---\n- 0\n', NULL),
(8, 6, 'weekly', 'за неделю', 3, 1, '---\n- \n  field: date_pub\n  condition: dy\n  value: 7\n', '---\n- \n  by: rating\n  to: desc\n', 'dataset_daily', '---\n- 0\n', NULL),
(9, 6, 'monthly', 'за месяц', 4, 1, '---\n- \n  field: date_pub\n  condition: dy\n  value: 31\n', '---\n- \n  by: date_pub\n  to: desc\n', 'date_pub', '---\n- 0\n', NULL),
(10, 10, 'latest', 'Последние', 1, 1, NULL, '---\n- \n  by: date_pub\n  to: desc\n', 'date_pub', '---\n- 0\n', NULL),
(11, 10, 'discussed', 'Обсуждаемые', 2, 1, NULL, '---\n- \n  by: comments\n  to: desc\n', 'dataset_discussed', '---\n- 0\n', NULL),
(12, 10, 'popular', 'Популярные', 3, 1, NULL, '---\n- \n  by: rating\n  to: desc\n', 'dataset_popular', '---\n- 0\n', NULL),
(13, 9, 'all', 'Новые', 1, 1, NULL, '---\n- \n  by: date_pub\n  to: desc\n', 'date_pub', '---\n- 0\n', NULL),
(14, 9, 'cheap', 'Сначала дешевые', 2, 1, NULL, '---\n- \n  by: price\n  to: asc\n', 'dataset_cheap', '---\n- 0\n', NULL),
(15, 9, 'expensive', 'Сначала дорогие', 3, 1, NULL, '---\n- \n  by: price\n  to: desc\n', 'dataset_cheap', '---\n- 0\n', NULL);

INSERT INTO `{#}content_datasets` (`id`, `ctype_id`, `name`, `title`, `description`, `ordering`, `is_visible`, `filters`, `sorting`, `index`, `groups_view`, `groups_hide`, `seo_keys`, `seo_desc`, `seo_title`, `cats_view`, `cats_hide`, `max_count`, `target_controller`) VALUES
(16, NULL, 'rating', 'Лучшие группы', NULL, 3, 1, NULL, '---\n- \n  by: rating\n  to: desc\n', 'rating', '---\n- 0\n', NULL, NULL, NULL, NULL, NULL, NULL, 0, 'groups'),
(17, NULL, 'all', 'Новые группы', NULL, 2, 1, NULL, '---\n- \n  by: date_pub\n  to: desc\n', 'date_pub', '---\n- 0\n', NULL, NULL, NULL, NULL, NULL, NULL, 0, 'groups'),
(18, NULL, 'popular', 'Популярные', NULL, 1, 1, NULL, '---\n- \n  by: members_count\n  to: desc\n', 'members_count', '---\n- 0\n', NULL, NULL, NULL, NULL, NULL, NULL, 0, 'groups');

INSERT INTO `{#}content_folders` (`id`, `ctype_id`, `user_id`, `title`) VALUES
(5, 6, 1, 'Личное');

INSERT INTO `{#}content_types` (`id`, `title`, `name`, `description`, `is_date_range`, `is_cats`, `is_cats_recursive`, `is_folders`, `is_in_groups`, `is_in_groups_only`, `is_comments`, `is_rating`, `is_tags`, `is_auto_keys`, `is_auto_desc`, `is_auto_url`, `is_fixed_url`, `url_pattern`, `options`, `labels`, `seo_keys`, `seo_desc`, `seo_title`, `item_append_html`, `is_fixed`) VALUES
(5, 'Статьи', 'articles', '<p>Текстовые материалы</p>', NULL, 1, 1, NULL, NULL, NULL, 1, 1, 1, 1, 1, 1, NULL, '{id}-{title}', '---\nis_cats_change: 1\nis_cats_open_root: 1\nis_cats_only_last: null\nis_show_cats: 1\nis_tags_in_list: 1\nis_tags_in_item: 1\nis_rss: 1\nlist_on: 1\nprofile_on: 1\nlist_show_filter: 1\nlist_expand_filter: null\nlist_style:\nitem_on: 1\nis_cats_keys: 1\nis_cats_desc: 1\nis_cats_auto_url: null\nseo_title_pattern:\nseo_keys_pattern: \'{content|string_get_meta_keywords}\'\nseo_desc_pattern: \'{content|string_get_meta_description}\'\n', '---\none: статья\ntwo: статьи\nmany: статей\ncreate: статью\n', 'статьи, разные, интересные, полезные', NULL, NULL, NULL, NULL),
(6, 'Посты', 'posts', '<p>Персональные публикации пользователей</p>', NULL, NULL, NULL, 1, 1, NULL, 1, 1, 1, 1, 1, 1, 1, '{id}-{title}', '---\nis_cats_change: null\nis_cats_open_root: null\nis_cats_only_last: null\nis_show_cats: null\nis_tags_in_list: 1\nis_tags_in_item: 1\nis_rss: 1\nlist_on: 1\nprofile_on: 1\nlist_show_filter: null\nlist_expand_filter: null\nlist_style:\nitem_on: 1\nis_cats_keys: null\nis_cats_desc: null\nis_cats_auto_url: 1\nseo_title_pattern:\nseo_keys_pattern: \'{content|string_get_meta_keywords}\'\nseo_desc_pattern: \'{content|string_get_meta_description}\'\n', '---\none: пост\ntwo: поста\nmany: постов\ncreate: пост\nlist: Лента блогов\nprofile: Блог\n', NULL, NULL, NULL, NULL, NULL),
(9, 'Объявления', 'board', '<p>Коммерческие объявления</p>', NULL, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 1, '{id}-{title}', '---\nis_cats_change: null\nis_cats_open_root: null\nis_cats_only_last: 1\nis_show_cats: 1\nis_tags_in_list: null\nis_tags_in_item: null\nis_rss: 1\nlist_on: 1\nprofile_on: 1\nlist_show_filter: 1\nlist_expand_filter: null\nlist_style: table\nitem_on: 1\nis_cats_keys: null\nis_cats_desc: null\nis_cats_auto_url: 1\nseo_title_pattern:\nseo_keys_pattern: \'{content|string_get_meta_keywords}\'\nseo_desc_pattern: \'{content|string_get_meta_description}\'\n', '---\none: объявление\ntwo: объявления\nmany: объявлений\ncreate: объявление\nlist: Доска объявлений\nprofile:\n', NULL, NULL, NULL, NULL, NULL),
(10, 'Новости', 'news', '<p>Информационные сообщения</p>', NULL, 1, 1, NULL, NULL, NULL, 1, 1, 1, 1, 1, 1, 1, '{id}-{title}', '---\nis_date_range_process: hide\nnotify_end_date_days: 1\nnotify_end_date_notice: \'Через %s публикация вашего контента <a href=\"%s\">%s</a> будет прекращена.\'\ndisable_drafts: null\nis_empty_root: null\nis_cats_multi: null\nis_cats_change: 1\nis_cats_open_root: null\nis_cats_only_last: null\nis_show_cats: null\ncover_sizes: null\ncontext_list_cover_sizes: null\nrating_template: widget\nrating_item_label:\nrating_list_label:\nrating_is_in_item: 1\nrating_is_in_list: null\ncomments_template: comment\ncomments_title_pattern:\ncomments_labels:\n  comments:\n  spellcount:\n  add:\n  none:\n  low_karma:\n  login:\n  track:\n  refresh:\n  commenting:\nis_tags_in_list: null\nis_tags_in_item: 1\nenable_subscriptions: 1\nsubscriptions_recursive_categories: 1\nis_rss: 1\nlist_off_breadcrumb: null\nlist_on: 1\nprofile_on: 1\nlist_show_filter: null\nlist_expand_filter: null\nprivacy_type: hide\nlimit: 15\nlist_style:\n  - featured\nlist_style_names: null\ncontext_list_style: null\nitem_off_breadcrumb: null\nitem_on: 1\nis_show_fields_group: null\nhits_on: null\nshare_code:\nis_manual_title: null\nseo_title_pattern:\nseo_keys_pattern: \'{content|string_get_meta_keywords}\'\nseo_desc_pattern: \'{content|string_get_meta_description}\'\nis_cats_title: null\nis_cats_h1: null\nis_cats_keys: null\nis_cats_desc: null\nis_cats_auto_url: 1\nseo_cat_h1_pattern:\nseo_cat_title_pattern:\nseo_cat_keys_pattern:\nseo_cat_desc_pattern:\nseo_ctype_h1_pattern:\nis_collapsed: null\n', '---\none: новость\ntwo: новости\nmany: новостей\ncreate: новость\nlist:\nprofile:\n', NULL, NULL, NULL, NULL, NULL);

INSERT INTO `{#}con_albums` (`id`, `title`, `content`, `slug`, `seo_keys`, `seo_desc`, `seo_title`, `tags`, `date_pub`, `date_last_modified`, `date_pub_end`, `is_pub`, `hits_count`, `user_id`, `parent_id`, `parent_type`, `parent_title`, `parent_url`, `is_parent_hidden`, `category_id`, `folder_id`, `is_comments_on`, `comments`, `rating`, `is_approved`, `approved_by`, `date_approved`, `is_private`, `cover_image`, `photos_count`, `is_public`) VALUES
(16, 'Красота окружающей природы', 'Фотографии из коллекции сайта deviantart.com', '16-krasota-okruzhayuschei-prirody', NULL, NULL, NULL, 'пример, фото', DATE_SUB(NOW(),INTERVAL 4 DAY), DATE_SUB(NOW(),INTERVAL 3 DAY), NULL, 1, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 1, 0, 0, 1, NULL, NULL, 0, NULL, 0, NULL);

INSERT INTO `{#}con_albums_cats_bind` (`item_id`, `category_id`) VALUES
(16, 1);

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
(1, 'Эллиптический перигей в XXI веке', '<p>Как было показано выше, эклиптика отражает близкий ионный хвост – север вверху, восток слева. Прямое восхождение перечеркивает сарос, это довольно часто наблюдается у сверхновых звезд второго типа. Прямое восхождение выбирает космический космический мусор – север вверху, восток слева. Афелий неустойчив.</p>\r\n\r\n<p>Различное расположение традиционно вызывает метеорит, таким образом, часовой пробег каждой точки поверхности на экваторе равен 1666км. Млечный Путь многопланово отражает вращательный большой круг небесной сферы, данное соглашение было заключено на 2-й международной конференции "Земля из космоса - наиболее эффективные решения". В отличие от давно известных астрономам планет земной группы, ось существенно отражает вращательный тропический год, учитывая, что в одном парсеке 3,26 световых года. Бесспорно, засветка неба колеблет тропический год - это солнечное затмение предсказал ионянам Фалес Милетский. Как мы уже знаем, эффективный диаметp дает поперечник, но кольца видны только при 40–50.</p>\r\n\r\n<p>В связи с этим нужно подчеркнуть, что красноватая звездочка притягивает радиант – это скорее индикатор, чем примета. Эффективный диаметp прекрасно вызывает центральный натуральный логарифм – север вверху, восток слева. Угловая скорость вращения выбирает вращательный терминатор, Плутон не входит в эту классификацию. Поперечник отражает натуральный логарифм, но кольца видны только при 40–50.</p>', '1-ellipticheskii-perigei-v-xxi-veke', NULL, NULL, NULL, 'пример, статья, астрономия', DATE_SUB(NOW(),INTERVAL 13 DAY), DATE_SUB(NOW(),INTERVAL 13 DAY), NULL, 1, 0, 1, NULL, NULL, NULL, NULL, NULL, 2, NULL, 1, 0, 0, 1, NULL, DATE_SUB(NOW(),INTERVAL 13 DAY), 0, 'Как было показано выше, эклиптика отражает близкий ионный хвост – север вверху, восток слева.', 1, NULL, 'http://referats.yandex.ru/astronomy.xml', NULL),
(4, 'Недонасыщенный алмаз: предпосылки и развитие', '<p>\r\n	Изостазия систематически вызывает меловой орогенез, что увязывается со структурно-тектонической обстановкой, гидродинамическими условиями и литолого-минералогическим составом пород. Силл переоткладывает апофиз, поскольку непосредственно мантийные струи не наблюдаются. Исследование указанной связи должно опираться на тот факт, что ложе определяет гипергенный минерал, основными элементами которого являются обширные плосковершинные и пологоволнистые возвышенности. Пока магма остается в камере, количество пирокластического материала пластично изменяет приток, за счет чего увеличивается мощность коры под многими хребтами. Зандровое поле определяет перенос, где присутствуют моренные суглинки днепровского возраста. Пойма опускает батолит, что в общем свидетельствует о преобладании тектонических опусканий в это время.\r\n</p>\r\n<p>\r\n	Руда, главным образом в карбонатных породах палеозоя, поступает в гранит, что в конце концов приведет к полному разрушению хребта под действием собственного веса. Судя по находям древнейших моренных отложений на Онежско-Ладожском перешейке, магнитное наклонение покрывает эстуарий, что обусловлено не только первичными неровностями эрозионно-тектонического рельефа поверхности кристаллических пород, но и проявлениями долее поздней блоковой тектоники. Лавовый поток смещает железистый дрейф континентов, но приводит к загрязнению окружающей среды. Капиллярное поднятие, по которому один блок опускается относительно другого, поперечно фоссилизирует гипергенный минерал, поскольку непосредственно мантийные струи не наблюдаются. В противоположность этому порода кавернозна.\r\n</p>\r\n<p>\r\n	Базальтовый слой разогревает сталактит, где присутствуют моренные суглинки днепровского возраста. Межледниковье, с учетом региональных факторов, интенсивно деформирует фирн, что, однако, не уничтожило доледниковую переуглубленную гидросеть древних долин. Океаническое ложе косо аккумулирует окско-донской грабен, и в то же время устанавливается достаточно приподнятый над уровнем моря коренной цоколь. Краевая часть артезианского бассейна, так как не наследует древние поднятия, наклонно аккумулирует кремнистый ийолит-уртит, что, однако, не уничтожило доледниковую переуглубленную гидросеть древних долин.\r\n</p>', '4-nedonasyschennyi-almaz-predposylki-i-razvitie', NULL, NULL, NULL, 'статья, наука', DATE_SUB(NOW(),INTERVAL 12 DAY), DATE_SUB(NOW(),INTERVAL 12 DAY), NULL, 1, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 1, 1, 0, 1, NULL, DATE_SUB(NOW(),INTERVAL 12 DAY), 0, '<p>\r\n	Изостазия систематически вызывает меловой орогенез, что увязывается со структурно-тектонической обстановкой, гидродинамическими условиями и литолого-минералогическим составом пород.\r\n</p>', 3, NULL, 'http://referats.yandex.ru/geology.xml', NULL),
(11, 'Общественный анализ зарубежного опыта', '<p>\r\n	 План размещения методически тормозит из ряда вон выходящий продуктовый ассортимент, опираясь на опыт западных коллег. Структура рынка, как следует из вышесказанного, инновационна. Исходя из структуры пирамиды Маслоу, производство детерминирует рыночный традиционный канал, оптимизируя бюджеты. По сути, создание приверженного покупателя директивно трансформирует инструмент маркетинга, используя опыт предыдущих кампаний.\r\n</p>\r\n<p>\r\n	 Ребрендинг, на первый взгляд, индуцирует ролевой план размещения, используя опыт предыдущих кампаний. Маркетинговая коммуникация индуцирует креативный бюджет на размещение, размещаясь во всех медиа. Целевая аудитория, как следует из вышесказанного, все еще интересна для многих. Целевая аудитория, согласно Ф.Котлеру, программирует экспериментальный рекламный клаттер, учитывая современные тенденции.\r\n</p>', '11-obschestvennyi-analiz-zarubezhnogo-opyta', NULL, NULL, NULL, NULL, DATE_SUB(NOW(),INTERVAL 7 MINUTE), DATE_SUB(NOW(),INTERVAL 7 MINUTE), NULL, 1, 0, 1, NULL, NULL, NULL, NULL, NULL, 9, NULL, 1, 2, 0, 1, NULL, DATE_SUB(NOW(),INTERVAL 7 MINUTE), 0, '<p>\r\n	 Общество потребления не критично. Баланс спроса и предложения переворачивает комплексный стратегический рыночный план, расширяя долю рынка.\r\n</p>', 2, NULL, NULL, NULL),
(10, 'Мифологический реципиент', '<p>\r\n	   Звукопись отражает лирический субъект, где автор является полновластным хозяином своих персонажей, а они - его марионетками. Матрица редуцирует экзистенциальный стих, при этом нельзя говорить, что это явления собственно фоники, звукописи. Анжамбеман, если уловить хореический ритм или аллитерацию на "р", многопланово иллюстрирует диалогический контекст, и это придает ему свое звучание, свой характер. Речевой акт пространственно выбирает брахикаталектический стих, первым образцом которого принято считать книгу А.Бертрана "Гаспар из тьмы".\r\n</p>', '10-mifologicheskii-recipient', NULL, NULL, NULL, NULL, DATE_SUB(NOW(),INTERVAL 1 DAY), DATE_SUB(NOW(),INTERVAL 1 DAY), NULL, 1, 0, 1, NULL, NULL, NULL, NULL, NULL, 6, NULL, 1, 0, 0, 1, NULL, DATE_SUB(NOW(),INTERVAL 1 DAY), 0, '<p>\r\n	   Однако Л.В.Щерба утверждал, что метаязык диссонирует замысел, при этом нельзя говорить, что это явления собственно фоники, звукописи. Холодный цинизм притягивает былинный ямб, при этом нельзя говорить, что это явления собственно фоники, звукописи. Лирика параллельна. Палимпсест начинает экзистенциальный дискурс, где автор является полновластным хозяином своих персонажей, а они - его марионетками.\r\n</p>', 1, NULL, NULL, NULL);

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
(2, 1, 'Астрономия', 'astronomija', 'astronomija', 'звезды, космос, небо, наука', NULL, NULL, 1, 2, 7, 1, '', 0),
(3, 2, 'Наука и космос', 'astronomija/nauka-i-kosmos', NULL, NULL, NULL, NULL, 1, 5, 6, 2, '', 0),
(4, 2, 'Астрофизика', 'astronomija/astrofizika', NULL, NULL, NULL, NULL, 2, 3, 4, 2, '', 0),
(5, 1, 'Геология', 'geologija', NULL, NULL, NULL, NULL, 2, 8, 9, 1, '', 0),
(6, 1, 'Литература', 'literatura', NULL, NULL, NULL, NULL, 3, 10, 15, 1, '', 0),
(7, 6, 'Отечественная', 'literatura/otechestvennaja', NULL, NULL, NULL, NULL, 1, 11, 12, 2, '', 0),
(8, 6, 'Зарубежная', 'literatura/zarubezhnaja', NULL, NULL, NULL, NULL, 2, 13, 14, 2, '', 0),
(9, 1, 'Маркетинг', 'marketing', NULL, NULL, NULL, NULL, 4, 16, 17, 1, '', 0);

DROP TABLE IF EXISTS `{#}con_articles_cats_bind`;
CREATE TABLE `{#}con_articles_cats_bind` (
  `item_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  KEY `item_id` (`item_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `{#}con_articles_cats_bind` (`item_id`, `category_id`) VALUES
(1, 2),
(4, 1),
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
(1, 5, 'title', 'Заголовок', NULL, 1, NULL, 'caption', 1, 1, 1, NULL, 1, 1, 0, NULL, '---\nlabel_in_list: none\nlabel_in_item: none\nmin_length: 3\nmax_length: 100\nis_required: true\n', NULL, NULL),
(2, 5, 'date_pub', 'Дата публикации', NULL, 2, NULL, 'date', 1, 1, NULL, NULL, 1, 1, 1, NULL, '---\nlabel_in_list: none\nlabel_in_item: left\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\n', '---\n- 0\n', '---\n- 0\n'),
(3, 5, 'user', 'Автор', NULL, 3, NULL, 'user', 1, 1, 0, NULL, 1, 1, 1, NULL, '---\nlabel_in_list: none\nlabel_in_item: left\n', NULL, NULL),
(4, 5, 'content', 'Текст статьи', 'Введите полный текст статьи', 7, 'Содержание', 'html', NULL, 1, NULL, NULL, 1, NULL, NULL, NULL, '---\neditor: 3\nis_html_filter: 1\nteaser_len:\nlabel_in_list: none\nlabel_in_item: none\nis_required: 1\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\nprofile_value:\n', '---\n- 0\n', '---\n- 0\n'),
(5, 5, 'teaser', 'Анонс статьи', 'Краткая аннотация к статье, будет показана в общем списке статей', 6, 'Содержание', 'html', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '---\neditor: 3\nis_html_filter: 1\nteaser_len:\nlabel_in_list: none\nlabel_in_item: none\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\nprofile_value:\n', '---\n- 0\n', '---\n- 0\n'),
(6, 5, 'kind', 'Тип статьи', NULL, 4, 'Информация о статье', 'list', NULL, 1, 1, NULL, NULL, NULL, NULL, '1 | Авторская\r\n2 | Рецензия\r\n3 | Перевод', '---\nfilter_multiple: 1\nlabel_in_list: left\nlabel_in_item: left\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\n', '---\n- 0\n', '---\n- 0\n'),
(7, 5, 'notice', 'Комментарий редакции', 'Поле доступно только для администраторов и модераторов', 9, 'Служебное', 'text', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, '---\nmin_length: 0\nmax_length: 2048\nis_html_filter: null\nlabel_in_list: top\nlabel_in_item: top\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\nprofile_value:\n', '---\n- 0\n', '---\n- 5\n- 6\n'),
(8, 5, 'source', 'Источник', 'Укажите ссылку на источник текста', 5, 'Информация о статье', 'url', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, '---\nredirect: 1\nauto_http: 1\nlabel_in_list: left\nlabel_in_item: left\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\n', '---\n- 0\n', '---\n- 0\n'),
(9, 5, 'featured', 'Выбор редакции', 'Поле доступно только для администраторов и модераторов', 8, 'Служебное', 'checkbox', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '---\nlabel_in_list: left\nlabel_in_item: left\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\n', '---\n- 0\n', '---\n- 5\n- 6\n');

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

DROP TABLE IF EXISTS `{#}con_board`;
CREATE TABLE `{#}con_board` (
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
  `photo` text,
  `photos` text,
  `price` float DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `approved_by` (`approved_by`),
  KEY `folder_id` (`folder_id`),
  KEY `slug` (`slug`),
  KEY `date_pub` (`is_pub`,`is_parent_hidden`,`is_deleted`,`is_approved`,`date_pub`),
  KEY `parent_id` (`parent_id`,`parent_type`,`date_pub`),
  KEY `user_id` (`user_id`,`date_pub`),
  KEY `date_pub_end` (`date_pub_end`),
  KEY `dataset_cheap` (`is_pub`,`is_parent_hidden`,`is_deleted`,`is_approved`,`price`),
  FULLTEXT KEY `title` (`title`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

INSERT INTO `{#}con_board` (`id`, `title`, `content`, `slug`, `seo_keys`, `seo_desc`, `seo_title`, `tags`, `date_pub`, `date_last_modified`, `date_pub_end`, `is_pub`, `hits_count`, `user_id`, `parent_id`, `parent_type`, `parent_title`, `parent_url`, `is_parent_hidden`, `category_id`, `folder_id`, `is_comments_on`, `comments`, `rating`, `is_approved`, `approved_by`, `date_approved`, `is_private`, `photo`, `photos`, `price`, `phone`) VALUES
(7, 'Продам квартиру в новостройке', 'Хорошая, просторная квартира с двумя этажами. Есть вся необходимая мебель.\r\nАгентов просьба не беспокоить. Реальному покупателю - торг.\r\nФотографии by abahasep @ deviantart.com', '7-prodam-kvartiru-v-novostroike', NULL, NULL, NULL, NULL, DATE_SUB(NOW(),INTERVAL 5 MINUTE), DATE_SUB(NOW(),INTERVAL 5 MINUTE), NULL, 1, 0, 1, NULL, NULL, NULL, NULL, NULL, 3, NULL, 1, 0, 0, 1, NULL, DATE_SUB(NOW(),INTERVAL 5 MINUTE), 0, '---\noriginal: u1/004/abda415c.jpg\nbig: u1/004/3cc38009.jpg\nnormal: u1/004/29a7c938.jpg\nsmall: u1/004/8d391531.jpg\nmicro: u1/004/8b15968b.jpg\n', '---\n- \n  original: u1/004/11ee105b.jpg\n  big: u1/004/4bda72dd.jpg\n  normal: u1/004/d5ee5098.jpg\n  small: u1/004/08e78cd6.jpg\n  micro: u1/004/7d68bada.jpg\n- \n  original: u1/004/b996b3f8.jpg\n  big: u1/004/1b6fe453.jpg\n  normal: u1/004/9c09db8a.jpg\n  small: u1/004/64a79d25.jpg\n  micro: u1/004/82c0e4f6.jpg\n- \n  original: u1/004/bb7b8fda.jpg\n  big: u1/004/82645d44.jpg\n  normal: u1/004/af2a1ec7.jpg\n  small: u1/004/f9c36ec4.jpg\n  micro: u1/004/9e67cb10.jpg\n', 5500000, '100-20-30');

DROP TABLE IF EXISTS `{#}con_board_cats`;
CREATE TABLE `{#}con_board_cats` (
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

INSERT INTO `{#}con_board_cats` (`id`, `parent_id`, `title`, `slug`, `slug_key`, `seo_keys`, `seo_desc`, `seo_title`, `ordering`, `ns_left`, `ns_right`, `ns_level`, `ns_differ`, `ns_ignore`) VALUES
(1, 0, '---', NULL, NULL, NULL, NULL, NULL, 1, 1, 24, 0, '', 0),
(2, 1, 'Недвижимость', 'nedvizhimost', NULL, NULL, NULL, NULL, 1, 2, 7, 1, '', 0),
(3, 2, 'Квартиры', 'nedvizhimost/kvartiry', NULL, NULL, NULL, NULL, 1, 3, 4, 2, '', 0),
(4, 2, 'Коттеджи', 'nedvizhimost/kottedzhi', NULL, NULL, NULL, NULL, 2, 5, 6, 2, '', 0),
(5, 1, 'Автомобили', 'avtomobili', NULL, NULL, NULL, NULL, 2, 8, 17, 1, '', 0),
(8, 1, 'Работа', 'rabota', NULL, NULL, NULL, NULL, 3, 18, 23, 1, '', 0),
(9, 8, 'Вакансии', 'rabota/vakansii', NULL, NULL, NULL, NULL, 1, 19, 20, 2, '', 0),
(10, 8, 'Резюме', 'rabota/rezyume', NULL, NULL, NULL, NULL, 2, 21, 22, 2, '', 0),
(11, 5, 'Audi', 'avtomobili/audi', NULL, NULL, NULL, NULL, 1, 9, 10, 2, '', 0),
(12, 5, 'Ford', 'avtomobili/ford', NULL, NULL, NULL, NULL, 2, 11, 12, 2, '', 0),
(13, 5, 'Renault', 'avtomobili/renault', NULL, NULL, NULL, NULL, 3, 13, 14, 2, '', 0),
(14, 5, 'Kia', 'avtomobili/kia', NULL, NULL, NULL, NULL, 4, 15, 16, 2, '', 0);

DROP TABLE IF EXISTS `{#}con_board_cats_bind`;
CREATE TABLE `{#}con_board_cats_bind` (
  `item_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  KEY `item_id` (`item_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `{#}con_board_cats_bind` (`item_id`, `category_id`) VALUES
(7, 3);

DROP TABLE IF EXISTS `{#}con_board_fields`;
CREATE TABLE `{#}con_board_fields` (
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

INSERT INTO `{#}con_board_fields` (`id`, `ctype_id`, `name`, `title`, `hint`, `ordering`, `is_enabled`, `fieldset`, `type`, `is_in_list`, `is_in_item`, `is_in_filter`, `is_private`, `is_fixed`, `is_fixed_type`, `is_system`, `values`, `options`, `groups_read`, `groups_add`, `groups_edit`, `filter_view`) VALUES
(1, 9, 'title', 'Заголовок объявления', NULL, 2, 1, NULL, 'caption', 1, 1, NULL, NULL, 1, 1, 0, NULL, '---\nlabel_in_list: left\nlabel_in_item: none\nis_required: 1\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\nprofile_value:\n', '---\n- 0\n', NULL, '---\n- 0\n', NULL),
(2, 9, 'date_pub', 'Дата добавления', NULL, 7, 1, NULL, 'date', 1, 1, 1, NULL, 1, 1, 1, NULL, '---\nlabel_in_list: none\nlabel_in_item: left\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\n', '---\n- 0\n', NULL, '---\n- 0\n', NULL),
(3, 9, 'user', 'Автор', NULL, 8, 1, NULL, 'user', NULL, 1, NULL, NULL, 1, 1, 1, NULL, '---\nlabel_in_list: none\nlabel_in_item: left\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\n', '---\n- 0\n', NULL, '---\n- 0\n', NULL),
(4, 9, 'content', 'Текст объявления', NULL, 4, 1, NULL, 'text', NULL, 1, NULL, NULL, 1, NULL, NULL, NULL, '---\nmin_length: 0\nmax_length: 2048\nshow_symbol_count: null\nis_html_filter: 1\nparse_patterns: null\nbuild_redirect_link: null\nteaser_len:\nshow_show_more: null\nin_fulltext_search: null\ncontext_list:\n  - 0\nrelation_id:\nlabel_in_list: none\nlabel_in_item: none\nwrap_type: none\nwrap_width:\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_url: null\nis_unique: null\nprofile_value:\nauthor_access: null\n', '---\n', '---\n', '---\n', '---\n'),
(5, 9, 'photo', 'Фотография', NULL, 1, 1, NULL, 'image', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, '---\nsize_teaser: small\nsize_full: normal\nsize_modal:\nsizes:\n  - normal\n  - micro\n  - small\nallow_import_link: null\ncontext_list:\n  - 0\nrelation_id:\nlabel_in_list: none\nlabel_in_item: none\nwrap_type: left\nwrap_width:\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_url: null\nis_unique: null\nprofile_value:\nauthor_access: null\n', '---\n', '---\n', '---\n', '---\n'),
(6, 9, 'photos', 'Дополнительные фотографии', NULL, 3, 1, NULL, 'images', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, '---\nsize_teaser: small\nsize_full: big\nsize_small: small\nsizes:\n  - small\n  - big\nallow_import_link: null\nfirst_image_emphasize: null\nmax_photos:\ncontext_list:\n  - 0\nrelation_id:\nlabel_in_list: none\nlabel_in_item: none\nwrap_type: auto\nwrap_width:\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_url: null\nis_unique: null\nprofile_value:\nauthor_access: null\n', '---\n', '---\n', '---\n', '---\n'),
(7, 9, 'price', 'Цена', NULL, 6, 1, NULL, 'number', 1, 1, 1, NULL, NULL, NULL, NULL, NULL, '---\nfilter_range: 1\nunits: руб.\nlabel_in_list: left\nlabel_in_item: left\nis_required: 1\nis_digits: 1\nis_alphanumeric: null\nis_email: null\nis_unique: null\n', '---\n- 0\n', NULL, '---\n- 0\n', NULL),
(8, 9, 'phone', 'Телефон', NULL, 5, 1, NULL, 'string', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, '---\nmin_length: 0\nmax_length: 255\nlabel_in_list: left\nlabel_in_item: left\nis_required: 1\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\nprofile_value: phone\n', '---\n- 0\n', NULL, '---\n- 0\n', NULL);

DROP TABLE IF EXISTS `{#}con_board_props`;
CREATE TABLE `{#}con_board_props` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `{#}con_board_props` (`id`, `ctype_id`, `title`, `fieldset`, `type`, `is_in_filter`, `values`, `options`) VALUES
(1, 9, 'Марка', NULL, 'list', 1, 'Audi\r\nBMW\r\nMercedes\r\nSkoda\r\nFiat', '---\nis_required: null\nis_multiple: null\n'),
(3, 9, 'Тип кузова', NULL, 'list', 1, 'Седан\r\nХэтчбек\r\nУниверсал\r\nМинивэн\r\nКроссовер', '---\nis_required: null\nunits:\nis_filter_range: null\nis_filter_multi: null\n'),
(6, 9, 'Год выпуска', NULL, 'number', 1, NULL, '---\nis_required: null\nunits:\nis_filter_range: 1\nis_filter_multi: null\n'),
(7, 9, 'Тип предложения', NULL, 'list', 1, 'Продам\r\nКуплю\r\nСдам\r\nСниму', '---\nunits:\nis_required: null\nis_filter_multi: null\n'),
(8, 9, 'Общая площадь', 'Площадь', 'number', NULL, NULL, '---\nunits: м²\nis_required: 1\nis_filter_multi: null\n'),
(9, 9, 'Количество комнат', 'Квартира', 'number', 1, NULL, '---\nis_required: 1\nunits:\nis_filter_range: 1\nis_filter_multi: null\n'),
(11, 9, 'Количество этажей', NULL, 'number', 1, NULL, NULL),
(12, 9, 'Есть гараж', NULL, 'list', 1, 'Да\r\nНет', NULL),
(13, 9, 'Тип дома', 'Дом', 'list', 1, 'Новостройка\r\nВторичка', '---\nunits:\nis_required: null\nis_filter_multi: 1\n'),
(14, 9, 'Пробег', NULL, 'number', 1, NULL, '---\nis_required: 1\nunits: км\nis_filter_range: 1\nis_filter_multi: null\n'),
(15, 9, 'Комплектация', NULL, 'list', NULL, 'Базовая\r\nСредняя\r\nМаксимальная', '---\nis_required: null\nunits:\nis_filter_range: null\nis_filter_multi: null\n'),
(16, 9, 'Жилая площадь', 'Площадь', 'number', NULL, NULL, '---\nunits: м²\nis_required: 1\nis_filter_multi: null\n'),
(17, 9, 'Есть балкон', 'Квартира', 'list', NULL, 'Да\r\nНет', '---\nunits:\nis_required: null\nis_filter_multi: null\n'),
(18, 9, 'Санузел', 'Квартира', 'list', NULL, 'Смежный\r\nРаздельный', '---\nunits:\nis_required: null\nis_filter_multi: null\n'),
(19, 9, 'Этаж', 'Дом', 'number', 1, NULL, '---\nis_required: 1\nunits:\nis_filter_range: 1\nis_filter_multi: null\n'),
(20, 9, 'Этажей в доме', 'Дом', 'number', NULL, NULL, '---\nunits:\nis_required: 1\nis_filter_multi: null\n'),
(21, 9, 'Модель', NULL, 'string', 1, NULL, '---\nis_required: 1\nunits:\nis_filter_range: null\nis_filter_multi: null\n'),
(22, 9, 'Объем двигателя', NULL, 'list', 1, '0.5\r\n0.6\r\n0.7\r\n0.8\r\n0.9\r\n1.0\r\n1.2\r\n1.4\r\n1.6\r\n1.8\r\n2.0\r\n2.2\r\n2.5\r\n3.0\r\n3.2\r\n>3', '---\nis_required: null\nunits:\nis_filter_range: null\nis_filter_multi: null\n'),
(23, 9, 'Трансмиссия', NULL, 'list', 1, 'Механическая\r\nАвтоматическая\r\nВариатор\r\nРобот', '---\nis_required: null\nunits:\nis_filter_range: null\nis_filter_multi: null\n'),
(24, 9, 'Цвет', NULL, 'string', NULL, NULL, '---\nis_required: 1\nunits:\nis_filter_range: null\nis_filter_multi: null\n'),
(25, 9, 'Модель Audi', NULL, 'list', 1, 'A1\r\nA2\r\nA3\r\nA4\r\nA5\r\nA6\r\nA7\r\nA8\r\nQ3\r\nQ5\r\nQ7\r\nTT', '---\nis_required: null\nunits:\nis_filter_range: null\nis_filter_multi: null\n'),
(26, 9, 'Модель Ford', NULL, 'list', 1, 'Escort\r\nExplorer\r\nFiesta\r\nFocus\r\nFocus C-Max\r\nFocus RS\r\nFocus ST\r\nFusion\r\nS-max\r\nScorpio', '---\nis_required: null\nunits:\nis_filter_range: null\nis_filter_multi: null\n'),
(27, 9, 'Модель Renault', NULL, 'list', 1, 'Clio\r\nDuster\r\nEspace\r\nFluence\r\nKangoo\r\nKoleos\r\nLatitude\r\nLogan\r\nMegane\r\nSandero', '---\nis_required: null\nunits:\nis_filter_range: null\nis_filter_multi: null\n'),
(28, 9, 'Модель Kia', NULL, 'list', 1, 'Ceed\r\nCerato\r\nMohave\r\nOptima\r\nPregio\r\nQuoris\r\nRio\r\nShuma\r\nSoul\r\nSpectra', '---\nis_required: null\nunits:\nis_filter_range: null\nis_filter_multi: null\n');

DROP TABLE IF EXISTS `{#}con_board_props_bind`;
CREATE TABLE `{#}con_board_props_bind` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `prop_id` int(11) DEFAULT NULL,
  `cat_id` int(11) DEFAULT NULL,
  `ordering` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `prop_id` (`prop_id`),
  KEY `ordering` (`cat_id`,`ordering`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `{#}con_board_props_bind` (`id`, `prop_id`, `cat_id`, `ordering`) VALUES
(38, 7, 3, 1),
(39, 7, 4, 1),
(41, 8, 3, 5),
(42, 8, 4, 2),
(43, 9, 3, 7),
(45, 11, 4, 4),
(46, 12, 4, 5),
(47, 13, 3, 2),
(62, 16, 3, 6),
(63, 16, 4, 3),
(64, 17, 3, 8),
(65, 18, 3, 9),
(66, 19, 3, 3),
(67, 20, 3, 4),
(71, 6, 5, 2),
(72, 6, 11, 3),
(73, 6, 12, 3),
(74, 6, 13, 3),
(75, 6, 14, 3),
(76, 15, 5, 3),
(77, 15, 11, 4),
(78, 15, 12, 4),
(79, 15, 13, 4),
(80, 15, 14, 4),
(81, 14, 5, 4),
(82, 14, 11, 5),
(83, 14, 12, 5),
(84, 14, 13, 5),
(85, 14, 14, 5),
(86, 3, 5, 1),
(87, 3, 11, 2),
(88, 3, 12, 2),
(89, 3, 13, 2),
(90, 3, 14, 2),
(91, 22, 5, 5),
(92, 22, 11, 6),
(93, 22, 12, 6),
(94, 22, 13, 6),
(95, 22, 14, 6),
(96, 23, 5, 6),
(97, 23, 11, 7),
(98, 23, 12, 7),
(99, 23, 13, 7),
(100, 23, 14, 7),
(101, 24, 5, 7),
(102, 24, 11, 8),
(103, 24, 12, 8),
(104, 24, 13, 8),
(105, 24, 14, 8),
(106, 25, 11, 1),
(107, 26, 12, 1),
(108, 27, 13, 1),
(109, 28, 14, 1),
(110, 6, 10, 1),
(111, 17, 10, 2),
(112, 12, 10, 3),
(113, 16, 10, 4),
(114, 9, 10, 5),
(115, 11, 10, 6),
(116, 15, 10, 7),
(117, 1, 10, 8),
(118, 21, 10, 9),
(119, 25, 10, 10),
(120, 26, 10, 11),
(121, 28, 10, 12),
(122, 27, 10, 13),
(123, 8, 10, 14),
(124, 22, 10, 15),
(125, 14, 10, 16),
(126, 18, 10, 17),
(127, 13, 10, 18),
(128, 3, 10, 19),
(129, 7, 10, 20),
(130, 23, 10, 21),
(131, 24, 10, 22),
(132, 19, 10, 23),
(133, 20, 10, 24);

DROP TABLE IF EXISTS `{#}con_board_props_values`;
CREATE TABLE `{#}con_board_props_values` (
  `prop_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  KEY `prop_id` (`prop_id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `{#}con_board_props_values` (`prop_id`, `item_id`, `value`) VALUES
(7, 7, '1'),
(13, 7, '1'),
(19, 7, '1'),
(20, 7, '2'),
(8, 7, '120'),
(16, 7, '100'),
(9, 7, '5'),
(17, 7, '1'),
(18, 7, '2');

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
(1, 'На улице 22 Партсъезда прорвало трубы с водой', '<p>\r\n	Если основание движется с постоянным ускорением, проекция на подвижные оси трудна в описании. Маховик мал. Погрешность преобразует угол крена, механически интерпретируя полученные выражения. Как уже указывалось, кожух безусловно не входит своими составляющими, что очевидно, в силы нормальных реакций связей, так же как и момент силы трения, что обусловлено малыми углами карданового подвеса. Абсолютно твёрдое тело переворачивает гирогоризонт, перейдя к исследованию устойчивости линейных гироскопических систем с искусственными силами. Тангаж определяет астатический объект, что видно из уравнения кинетической энергии ротора.\r\n</p>\r\n<p>\r\n	Механическая природа, в силу третьего закона Ньютона, опасна. Векторная форма, как можно показать с помощью не совсем тривиальных вычислений, заставляет иначе взглянуть на то, что такое гирокомпас, что нельзя рассматривать без изменения системы координат. Объект учитывает угол крена, что обусловлено существованием циклического интеграла у второго уравнения системы уравнений малых колебаний. Успокоитель качки, в соответствии с модифицированным уравнением Эйлера, участвует в погрешности определения курса меньше, чем поплавковый период, основываясь на предыдущих вычислениях.\r\n</p>', '1-na-ulice-prorvalo-truby', NULL, NULL, NULL, 'новости, проишествия', DATE_SUB(NOW(),INTERVAL 9 DAY), DATE_SUB(NOW(),INTERVAL 9 DAY), NULL, 1, 0, 1, NULL, NULL, NULL, NULL, NULL, 5, NULL, 1, 0, 0, 1, NULL, DATE_SUB(NOW(),INTERVAL 9 DAY), 0, 'Радостные дети бегают по лужам', NULL),
(2, 'Игрушки становятся дороже', 'Будем, как и раньше, предполагать, что волчок устойчив. Если основание движется с постоянным ускорением, ПИГ не входит своими составляющими, что очевидно, в силы нормальных реакций связей, так же как и прецизионный гироскопический стабилизатоор, изменяя направление движения. Уравнение возмущенного движения, согласно уравнениям Лагранжа, принципиально связывает устойчивый систематический уход, что неправильно при большой интенсивности диссипативных сил. Направление вращает математический маятник, рассматривая уравнения движения тела в проекции на касательную к его траектории. Устойчивость, как следует из системы уравнений, интегрирует гравитационный суммарный поворот, что при любом переменном вращении в горизонтальной плоскости будет направлено вдоль оси. Гировертикаль косвенно требует большего внимания к анализу ошибок, которые даёт курс, составляя уравнения Эйлера для этой системы координат.', '2-igrushki-stanovjatsja-dorozhe', NULL, NULL, NULL, 'новости', DATE_SUB(NOW(),INTERVAL 8 DAY), DATE_SUB(NOW(),INTERVAL 8 DAY), NULL, 1, 0, 1, NULL, NULL, NULL, NULL, NULL, 6, NULL, 1, 0, 0, 1, NULL, NULL, 0, 'Эксперты прогнозируют дальнейший рост цен на детские товары', '---\noriginal: u1/003/25838c0f.jpg\nbig: u1/003/2e2bf124.jpg\nnormal: u1/003/f6f14e82.jpg\nsmall: u1/003/236d41e4.jpg\nmicro: u1/003/74809cbe.jpg\n'),
(3, 'В городе открыт сервис для ретро-автомобилей', 'Силовой трёхосный гироскопический стабилизатор, в силу третьего закона Ньютона, неустойчив. Установившийся режим требует перейти к поступательно перемещающейся системе координат, чем и характеризуется дифференциальный угол тангажа, составляя уравнения Эйлера для этой системы координат. Максимальное отклонение мгновенно. Отсюда следует, что ось собственного вращения даёт большую проекцию на оси, чем подвес, учитывая смещения центра масс системы по оси ротора.', '3-v-gorode-otkryt-servis-dlja-retro-avtomobilei', NULL, NULL, NULL, 'новости, пример', DATE_SUB(NOW(),INTERVAL 7 DAY), DATE_SUB(NOW(),INTERVAL 7 DAY), NULL, 1, 0, 1, NULL, NULL, NULL, NULL, NULL, 3, NULL, 1, 0, 0, 1, NULL, NULL, 0, 'Каждый желающий может обратиться с просьбой о ремонте', '---\noriginal: u1/003/5a771d4e.jpg\nbig: u1/003/4878547b.jpg\nnormal: u1/003/ad753a86.jpg\nsmall: u1/003/9f03ca75.jpg\nmicro: u1/003/5edc315b.jpg\n'),
(4, 'Дачный сезон на Урале официально начался', 'BTL, не меняя концепции, изложенной выше, консолидирует стиль менеджмента, используя опыт предыдущих кампаний. VIP-мероприятие, как следует из вышесказанного, консолидирует SWOT-анализ, используя опыт предыдущих кампаний. Медиа, отбрасывая подробности, изящно раскручивает анализ рыночных цен, невзирая на действия конкурентов. Стоит отметить, что promotion-кампания вырождена. Показ баннера, анализируя результаты рекламной кампании, концентрирует обществвенный анализ зарубежного опыта, используя опыт предыдущих кампаний. А вот по мнению аналитиков партисипативное планирование экономит эмпирический рекламоноситель, работая над проектом.', '4-dachnyi-sezon-otkryt', NULL, NULL, NULL, 'пример', DATE_SUB(NOW(),INTERVAL 6 DAY), DATE_SUB(NOW(),INTERVAL 6 DAY), NULL, 1, 0, 1, NULL, NULL, NULL, NULL, NULL, 2, NULL, 1, 0, 0, 1, NULL, DATE_SUB(NOW(),INTERVAL 6 DAY), 0, 'Горожане массово переезжают за город', '---\noriginal: u1/003/01153b4d.jpg\nbig: u1/003/b9767257.jpg\nnormal: u1/003/53497165.jpg\nsmall: u1/003/b1e550ce.jpg\nmicro: u1/003/f1476363.jpg\n'),
(5, 'Бизнес ожидает снижения налогов', 'Продвижение проекта, пренебрегая деталями, поразительно. Стратегический рыночный план решительно нейтрализует инструмент маркетинга, полагаясь на инсайдерскую информацию. Можно предположить, что VIP-мероприятие настроено позитивно. Баннерная реклама, в рамках сегодняшних воззрений, охватывает сублимированный BTL, отвоевывая рыночный сегмент.\r\n\r\nУзнавание бренда, как следует из вышесказанного, слабо притягивает ролевой медиавес, оптимизируя бюджеты. Продукт, анализируя результаты рекламной кампании, концентрирует культурный продуктовый ассортимент, повышая конкуренцию. Позиционирование на рынке конструктивно. Личность топ менеджера, безусловно, создает жизненный цикл продукции, учитывая современные тенденции.', '5-snizhenie-nalogov-dlja-biznesa', NULL, NULL, NULL, NULL, DATE_SUB(NOW(),INTERVAL 5 DAY), DATE_SUB(NOW(),INTERVAL 5 DAY), NULL, 1, 0, 1, NULL, NULL, NULL, NULL, NULL, 3, NULL, 1, 0, 0, 1, NULL, DATE_SUB(NOW(),INTERVAL 5 DAY), 0, 'Юридические лица будут платить еще меньше', '---\noriginal: u1/003/0ff19ffb.jpg\nbig: u1/003/3c2e4a35.jpg\nnormal: u1/003/fa562059.jpg\nsmall: u1/003/cae0bdfb.jpg\nmicro: u1/003/852fb216.jpg\n'),
(6, 'Все больше россиян покупают дома за границей', 'Наш современник стал особенно чутко относиться к слову, однако дольник жизненно приводит мелодический зачин, туда же попадает и еще недавно вызывавший безусловную симпатию гетевский Вертер. В заключении добавлю, полисемия отталкивает парафраз – это уже пятая стадия понимания по М.Бахтину. Однако Л.В.Щерба утверждал, что расположение эпизодов существенно отражает сюжетный абстракционизм, но не рифмами. Женское окончание начинает конструктивный скрытый смысл, об этом свидетельствуют краткость и завершенность формы, бессюжетность, своеобразие тематического развертывания. Расположение эпизодов начинает подтекст, что нельзя сказать о нередко манерных эпитетах. Если выстроить в ряд случаи инверсий у Державина, то расположение эпизодов диссонирует словесный речевой акт, но языковая игра не приводит к активно-диалогическому пониманию.', '6-vse-bolshe-rossijan-pokupayut-nedvizhimost-za-granicei', NULL, NULL, NULL, 'пример, новости', DATE_SUB(NOW(),INTERVAL 4 DAY), DATE_SUB(NOW(),INTERVAL 4 DAY), NULL, 1, 0, 1, NULL, NULL, NULL, NULL, NULL, 2, NULL, 1, 0, 0, 1, NULL, DATE_SUB(NOW(),INTERVAL 4 DAY), 0, 'За последний год их количество заметно выросло', '---\noriginal: u1/003/2fea4487.jpg\nbig: u1/003/a05ad20e.jpg\nnormal: u1/003/41646570.jpg\nsmall: u1/003/eb2bac70.jpg\nmicro: u1/003/1c88035a.jpg\n'),
(7, 'Количество преступлений в России сокращается', 'Ю.Лотман, не дав ответа, тут же запутывается в проблеме превращения не-текста в текст, поэтому нет смысла утверждать, что первое полустишие начинает механизм сочленений, так как в данном случае роль наблюдателя опосредована ролью рассказчика. Брахикаталектический стих приводит палимпсест, первым образцом которого принято считать книгу А.Бертрана "Гаспар из тьмы". Наш современник стал особенно чутко относиться к слову, однако впечатление существенно дает ямб, так как в данном случае роль наблюдателя опосредована ролью рассказчика. Цитата как бы придвигает к нам прошлое, при этом звукопись дает мелодический дактиль, также необходимо сказать о сочетании метода апроприации художественных стилей прошлого с авангардистскими стратегиями. Слово кумулятивно. Контрапункт, несмотря на внешние воздействия, существенно иллюстрирует диалогический не-текст, туда же попадает и еще недавно вызывавший безусловную симпатию гетевский Вертер.', '7-kolichestvo-prestuplenii-v-rossii-sokraschaetsja', NULL, NULL, NULL, NULL, DATE_SUB(NOW(),INTERVAL 3 DAY), DATE_SUB(NOW(),INTERVAL 3 DAY), NULL, 1, 0, 1, NULL, NULL, NULL, NULL, NULL, 5, NULL, 1, 0, 0, 1, NULL, DATE_SUB(NOW(),INTERVAL 3 DAY), 0, 'В последних отчетах МВД видна положительная тенденция', '---\noriginal: u1/003/4d953a88.jpg\nbig: u1/003/e3c52c3e.jpg\nnormal: u1/003/9e9ef526.jpg\nsmall: u1/003/3f768733.jpg\nmicro: u1/003/ddaa0bd4.jpg\n'),
(8, 'Инвестиции для чайников: куда вкладывать?', 'Из приведенных текстуальных фрагментов видно, как матрица абсурдно просветляет диалогический контекст, где автор является полновластным хозяином своих персонажей, а они - его марионетками. Эстетическое воздействие, на первый взгляд, осознаёт сюжетный генезис свободного стиха, туда же попадает и еще недавно вызывавший безусловную симпатию гетевский Вертер. Басня, как бы это ни казалось парадоксальным, доступна. Стих текстологически отталкивает поэтический амфибрахий, однако дальнейшее развитие приемов декодирования мы находим в работах академика В.Виноградова. Зачин редуцирует конструктивный анапест, об этом свидетельствуют краткость и завершенность формы, бессюжетность, своеобразие тематического развертывания.', '8-investicii-dlja-chainikov-kuda-vkladyvat', NULL, NULL, NULL, NULL, DATE_SUB(NOW(),INTERVAL 1 DAY), DATE_SUB(NOW(),INTERVAL 3 DAY), NULL, 1, 0, 1, NULL, NULL, NULL, NULL, NULL, 3, NULL, 1, 0, 0, 1, NULL, NULL, 0, 'Читайте в нашем обзоре самых популярных способов инвестиций', '---\noriginal: u1/003/ff539643.jpg\nbig: u1/003/77fbbb95.jpg\nnormal: u1/003/89e8e681.jpg\nsmall: u1/003/3400aa78.jpg\nmicro: u1/003/f95ca1a2.jpg\n'),
(9, 'Россияне стали первыми на Чемпионате Мира', 'Ударение, соприкоснувшись в чем-то со своим главным антагонистом в постструктурной поэтике, диссонирует коммунальный модернизм, туда же попадает и еще недавно вызывавший безусловную симпатию гетевский Вертер. Скрытый смысл вызывает глубокий контрапункт, но не рифмами. Олицетворение, если уловить хореический ритм или аллитерацию на "р", аннигилирует симулякр, при этом нельзя говорить, что это явления собственно фоники, звукописи. Матрица параллельна.', '9-rossijane-stali-pervymi-na-chempionate-mira', NULL, NULL, NULL, NULL, DATE_SUB(NOW(),INTERVAL 2 DAY), DATE_SUB(NOW(),INTERVAL 2 DAY), NULL, 1, 0, 1, NULL, NULL, NULL, NULL, NULL, 7, NULL, 1, 0, 0, 1, NULL, NULL, 0, 'Наша команда не оставила шансов конкурентам', '---\noriginal: u1/003/59b08272.jpg\nbig: u1/003/d0ed7732.jpg\nnormal: u1/003/44b68dc8.jpg\nsmall: u1/003/93e51e49.jpg\nmicro: u1/003/0599295b.jpg\n');

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
(2, 1, 'Общество', 'obschestvo', NULL, NULL, NULL, NULL, 1, 2, 3, 1, '', 0),
(3, 1, 'Бизнес', 'biznes', NULL, NULL, NULL, NULL, 2, 4, 5, 1, '', 0),
(4, 1, 'Политика', 'politika', NULL, NULL, NULL, NULL, 3, 6, 7, 1, '', 0),
(5, 1, 'Происшествия', 'proisshestvija', NULL, NULL, NULL, NULL, 4, 8, 9, 1, '', 0),
(6, 1, 'В мире', 'v-mire', NULL, NULL, NULL, NULL, 5, 10, 11, 1, '', 0),
(7, 1, 'Спорт', 'sport', NULL, NULL, NULL, NULL, 6, 12, 13, 1, '', 0);

DROP TABLE IF EXISTS `{#}con_news_cats_bind`;
CREATE TABLE `{#}con_news_cats_bind` (
  `item_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  KEY `item_id` (`item_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `{#}con_news_cats_bind` (`item_id`, `category_id`) VALUES
(1, 5),
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
(1, 10, 'title', 'Заголовок новости', NULL, 2, 1, NULL, 'caption', 1, 1, 1, NULL, 1, 1, 0, NULL, '---\nlabel_in_list: none\nlabel_in_item: none\nis_required: 1\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\n', '---\n- 0\n', NULL, '---\n- 0\n', NULL),
(2, 10, 'date_pub', 'Дата публикации', NULL, 6, 1, NULL, 'date', 1, 1, 1, NULL, 1, 1, 1, NULL, '---\nlabel_in_list: none\nlabel_in_item: left\nshow_time: true\n', NULL, NULL, NULL, NULL),
(3, 10, 'user', 'Автор', NULL, 5, 1, NULL, 'user', 1, 1, 0, NULL, 1, 1, 1, NULL, '---\nlabel_in_list: none\nlabel_in_item: left\n', NULL, NULL, NULL, NULL),
(4, 10, 'content', 'Текст новости', NULL, 4, 1, NULL, 'html', NULL, 1, NULL, NULL, 1, NULL, NULL, NULL, '---\neditor: 3\nis_html_filter: 1\nteaser_len:\nlabel_in_list: none\nlabel_in_item: none\nis_required: 1\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\nprofile_value:\n', '---\n- 0\n', NULL, '---\n- 0\n', NULL),
(5, 10, 'teaser', 'Краткое описание новости', 'Выводится в списке новостей', 3, 1, NULL, 'string', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '---\nmin_length: 0\nmax_length: 255\nlabel_in_list: none\nlabel_in_item: none\nis_required: 1\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\nprofile_value:\n', '---\n- 0\n', NULL, '---\n- 0\n', NULL),
(6, 10, 'photo', 'Фотография', NULL, 1, 1, NULL, 'image', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, '---\nsize_teaser: normal\nsize_full: normal\nsize_modal: big\nsizes:\n  - normal\n  - micro\n  - small\n  - big\nallow_import_link: null\ncontext_list:\n  - 0\nrelation_id:\nlabel_in_list: none\nlabel_in_item: none\nwrap_type: left\nwrap_width:\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_url: null\nis_unique: null\nprofile_value:\nauthor_access: null\n', '---\n', '---\n', '---\n', '---\n');

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
(1, 'О проекте', '<p>В пределах аккумулятивных равнин вулканическое стекло занимает коллювий, за счет чего увеличивается мощность коры под многими хребтами. Палинологическое изучение осадков онежской трансгрессии, имеющей отчетливое межморенное залегание, показало, что притеррасная низменность горизонально обогащает апофиз, поскольку непосредственно мантийные струи не наблюдаются. Межледниковье опускает гидротермальный лакколит, делая этот типологический таксон районирования носителем важнейших инженерно-геологических характеристик природных условий. Фумарола определяет шток, что в конце концов приведет к полному разрушению хребта под действием собственного веса. Минеральное сырье имеет тенденцию биокосный грунт, делая этот типологический таксон районирования носителем важнейших инженерно-геологических характеристик природных условий. Поэтому многие геологи считают, что ядро опускает межпластовый надвиг, что в общем свидетельствует о преобладании тектонических опусканий в это время.</p>\r\n\r\n<p>Алмаз, с учетом региональных факторов, наклонно сменяет лавовый купол, в тоже время поднимаясь в пределах горстов до абсолютных высот 250 м. Как видно из самых общих закономерности распределения криолитозоны, извержение варьирует эвапорит, что обусловлено не только первичными неровностями эрозионно-тектонического рельефа поверхности кристаллических пород, но и проявлениями долее поздней блоковой тектоники. Фумарола, особенно в речных долинах, кавернозна. Ледниковое озеро затруднено.</p>\r\n\r\n<p>Питание прогиба исходным материалом смещает слабоминерализованный сталагмит, причем, вероятно, быстрее, чем прочность мантийного вещества. Инфлюация, скажем, за 100 тысяч лет, несет в себе палеокриогенный замок складки, что в общем свидетельствует о преобладании тектонических опусканий в это время. Но, пожалуй, еще более убедителен ортоклаз покрывает фитолитный криптархей, что свидетельствует о проникновении днепровских льдов в бассейн Дона. Амфибол отчетливо и полно пододвигается под пегматитовый бентос, где на поверхность выведены кристаллические структуры фундамента. Изостазия имеет тенденцию молого-шекснинский криптархей, но приводит к загрязнению окружающей среды.</p>\r\n\r\n<p><a href="http://referats.yandex.ru/">Источник</a> </p>', 'about', NULL, NULL, NULL, NULL, DATE_SUB(NOW(),INTERVAL 11 DAY), DATE_SUB(NOW(),INTERVAL 11 DAY), NULL, 1, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 1, 0, 0, 1, NULL, NULL, 0, ''),
(2, 'Правила сайта', '<p>1.&nbsp;Запрещены&nbsp;любые формы оскорблений участников сообщества или администрации, в том числе нецензурные логины и никнеймы.</p>\r\n\r\n<p>2.&nbsp;Запрещен мат, в том числе завуалированный.</p>\r\n\r\n<p>3.&nbsp;Запрещено&nbsp;публичное обсуждение действий администрации и ее представителей.</p>\r\n\r\n<p>4. Администрация проекта оставляет за собой право изменять и дополнять данные правила в любой момент времени.</p>\r\n\r\n<p>5. В общении на сайте придерживайтесь норм грамматики русского языка и общепринятой вежливости. Запрещено осознанное коверканье слов, жаргон. Избегайте необоснованного перехода на "ты".</p>\r\n\r\n<p><a name="forum"></a></p>', 'rules', NULL, NULL, NULL, NULL, DATE_SUB(NOW(),INTERVAL 10 DAY), DATE_SUB(NOW(),INTERVAL 10 DAY), NULL, 1, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 1, 0, 0, 1, NULL, DATE_SUB(NOW(),INTERVAL 10 DAY), 0, '');

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
(5, 'Мой первый пост в сообществе', '<p>\r\n	 Не факт, что выставочный стенд по-прежнему устойчив к изменениям спроса. Креатив, анализируя результаты рекламной кампании, уравновешивает стиль менеджмента, осознав маркетинг как часть производства. Поэтому построение бренда не критично. Выставочный стенд, как принято считать, программирует коллективный потребительский рынок, осознав маркетинг как часть производства. Маркетингово-ориентированное издание тормозит фирменный клиентский спрос, работая над проектом. Личность топ менеджера порождена временем.\r\n</p>\r\n<p>\r\n	 Сущность и концепция маркетинговой программы экономит маркетинг, отвоевывая рыночный сегмент. В общем, медиабизнес осмысленно ускоряет из ряда вон выходящий ребрендинг, не считаясь с затратами. Основная стадия проведения рыночного исследования, пренебрегая деталями, откровенно цинична. Эффективность действий индуцирует эмпирический фактор коммуникации, невзирая на действия конкурентов.\r\n</p>', '5-moi-pervyi-post-v-soobschestve', NULL, NULL, NULL, 'пример, пост, роботы', DATE_SUB(NOW(),INTERVAL 6 MINUTE), DATE_SUB(NOW(),INTERVAL 6 MINUTE), NULL, 1, 0, 1, 1, 'group', 'Робототехника', 'groups/1/content/posts', NULL, 1, 5, 1, 0, 0, 1, NULL, DATE_SUB(NOW(),INTERVAL 6 MINUTE), 0, NULL);

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
(1, 6, 'title', 'Заголовок', NULL, 1, NULL, 'caption', 1, 1, 1, NULL, 1, 1, 0, NULL, '---\nlabel_in_list: none\nlabel_in_item: none\nmin_length: 3\nmax_length: 100\nis_required: true\n', NULL, NULL),
(2, 6, 'date_pub', 'Дата публикации', NULL, 2, NULL, 'date', 1, 1, 1, NULL, 1, 1, 1, NULL, '---\nlabel_in_list: none\nlabel_in_item: left\nshow_time: true\n', NULL, NULL),
(3, 6, 'user', 'Автор', NULL, 3, NULL, 'user', 1, 1, 0, NULL, 1, 1, 1, NULL, '---\nlabel_in_list: none\nlabel_in_item: left\n', NULL, NULL),
(4, 6, 'content', 'Текст поста', NULL, 5, NULL, 'html', 1, 1, NULL, NULL, 1, NULL, NULL, NULL, '---\neditor: 3\nis_html_filter: 1\nteaser_len: 500\nlabel_in_list: none\nlabel_in_item: none\nis_required: 1\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\nprofile_value:\n', '---\n- 0\n', '---\n- 0\n'),
(5, 6, 'picture', 'Картинка для привлечения внимания', NULL, 4, NULL, 'image', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, '---\nsize_teaser: normal\nsize_full: normal\nsizes:\n  - small\n  - normal\nlabel_in_list: none\nlabel_in_item: none\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\n', '---\n- 0\n', '---\n- 0\n');

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
(1, 1, DATE_SUB(NOW(),INTERVAL 10 MINUTE), 'Робототехника', 'Группа посвящена роботам, машиностроению и всему что с этим связано.', '---\noriginal: u1/004/f398ad69.png\nbig: u1/004/f13052e8.png\nnormal: u1/004/de897122.png\nsmall: u1/004/a442fa4b.png\nmicro: u1/004/c8a73161.png\n', 0, 1, 0, 0, 0, 0);

INSERT INTO `{#}groups_members` (`id`, `group_id`, `user_id`, `role`, `date_updated`) VALUES
(1, 1, 1, 2, CURRENT_TIMESTAMP);

INSERT INTO `{#}menu` (`id`, `name`, `title`, `is_fixed`) VALUES
(3, 'footer', 'Нижнее меню', NULL);

INSERT INTO `{#}menu_items` (`id`, `menu_id`, `parent_id`, `title`, `url`, `ordering`, `options`, `groups_view`, `groups_hide`) VALUES
(6, 1, 0, 'Фото', 'albums', 4, '---\ntarget: _self\nclass:', '---\n- 0\n', NULL),
(7, 1, 0, 'Сайты', NULL, 10, NULL, NULL, NULL),
(8, 1, 7, 'Яндекс', 'https://www.yandex.ru', 10, NULL, NULL, NULL),
(9, 1, 7, 'InstantSoft', NULL, 11, NULL, NULL, NULL),
(10, 1, 9, 'InstantVideo', 'https://instantvideo.ru/software/instantvideo2.html', 14, NULL, NULL, NULL),
(18, 3, 0, 'О проекте', 'pages/about.html', 1, '---\nclass: \n', '---\n- 0\n', NULL),
(19, 3, 0, 'Правила сайта', 'pages/rules.html', 2, '---\nclass: \n', '---\n- 0\n', NULL),
(27, 1, 0, 'Блоги', 'posts', 3, '---\ntarget: _self\nclass:', '---\n- 0\n', NULL),
(39, 1, 0, 'Объявления', 'board', 5, '---\ntarget: _self\nclass:', '---\n- 0\n', NULL),
(40, 1, 0, 'Новости', '{content:news}', 1, '---\ntarget: _self\nclass:', '---\n- 0\n', NULL),
(42, 1, 0, 'Статьи', '{content:articles}', 2, '---\ntarget: _self\nclass:', '---\n- 0\n', NULL);

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
(13, 6, 'news', '1'),
(1, 3, 'articles', 'yes'),
(3, 3, 'articles', 'own'),
(2, 3, 'articles', 'own'),
(1, 3, 'posts', 'yes'),
(3, 3, 'posts', 'own'),
(2, 3, 'posts', 'own'),
(1, 3, 'board', 'yes'),
(3, 3, 'board', 'own'),
(2, 3, 'board', 'own');

INSERT INTO `{#}rss_feeds` (`id`, `ctype_id`, `ctype_name`, `title`, `description`, `image`, `mapping`, `limit`, `is_enabled`, `is_cache`, `cache_interval`, `date_cached`) VALUES
(2, 5, 'articles', 'Статьи', NULL, NULL, '---\ntitle: title\ndescription: teaser\npubDate: date_pub\nimage:\nimage_size: normal\n', 15, 1, NULL, 60, NULL),
(3, 6, 'posts', 'Посты', 'Лента пользовательских постов', NULL, '---\ntitle: title\ndescription: content\npubDate: date_pub\nimage: picture\nimage_size: normal\n', 15, 1, NULL, 60, NULL),
(5, 9, 'board', 'Объявления', 'Коммерческие объявления', NULL, '---\ntitle: title\ndescription: content\npubDate: date_pub\nimage: photo\nimage_size: normal\n', 15, 1, NULL, 60, NULL),
(6, 10, 'news', 'Новости', 'Информационные сообщения', NULL, '---\ntitle: title\ndescription: content\npubDate: date_pub\nimage: photo\nimage_size: normal\n', 15, 1, NULL, 60, NULL);

INSERT INTO `{#}tags` (`id`, `tag`, `frequency`) VALUES
(1, 'пример', 6),
(2, 'статья', 2),
(3, 'астрономия', 1),
(9, 'наука', 1),
(36, 'новости', 4),
(37, 'проишествия', 1),
(39, 'пост', 1),
(40, 'роботы', 1),
(42, 'фото', 1);

INSERT INTO `{#}tags_bind` (`id`, `tag_id`, `target_controller`, `target_subject`, `target_id`) VALUES
(10, 1, 'content', 'articles', 1),
(11, 2, 'content', 'articles', 1),
(12, 3, 'content', 'articles', 1),
(49, 36, 'content', 'news', 2),
(50, 36, 'content', 'news', 3),
(51, 1, 'content', 'news', 3),
(56, 1, 'content', 'news', 4),
(59, 1, 'content', 'news', 6),
(60, 36, 'content', 'news', 6),
(86, 2, 'content', 'articles', 4),
(87, 9, 'content', 'articles', 4),
(103, 1, 'content', 'albums', 16),
(104, 42, 'content', 'albums', 16),
(105, 1, 'content', 'posts', 5),
(106, 39, 'content', 'posts', 5),
(107, 40, 'content', 'posts', 5),
(108, 36, 'content', 'news', 1),
(109, 37, 'content', 'news', 1);

INSERT INTO `{#}users_statuses` (`id`, `user_id`, `date_pub`, `content`, `replies_count`, `wall_entry_id`) VALUES
(1, 1, CURRENT_TIMESTAMP, 'We are all made of stars © Moby', 1, 1);

INSERT INTO `{#}wall_entries` (`id`, `date_pub`, `controller`, `profile_type`, `profile_id`, `user_id`, `parent_id`, `status_id`, `content`, `content_html`) VALUES
(1, CURRENT_TIMESTAMP, 'users', 'user', 1, 1, 0, 1, 'We are all made of stars © Moby', 'We are all made of stars © Moby'),
(2, CURRENT_TIMESTAMP, 'users', 'user', 1, 1, 1, NULL, 'Спасибо что заглянули в мой профиль!', 'Спасибо что заглянули в мой профиль!');

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
(159, 'content', 'board.all', 'LANG_WP_CONTENT_ALL_PAGES', NULL, NULL, 'board\nboard-*\nboard/*', NULL),
(160, 'content', 'board.list', 'LANG_WP_CONTENT_LIST', NULL, NULL, 'board\nboard-*\nboard/*', 'board/*/view-*\nboard/*.html\nboard/add\nboard/add/%\nboard/add?*\nboard/addcat\nboard/addcat/%\nboard/editcat/%\nboard/edit/*'),
(161, 'content', 'board.item', 'LANG_WP_CONTENT_ITEM', NULL, NULL, 'board/*.html', NULL),
(162, 'content', 'board.edit', 'LANG_WP_CONTENT_ITEM_EDIT', NULL, NULL, 'board/add\nboard/edit/*', NULL),
(163, 'content', 'news.all', 'LANG_WP_CONTENT_ALL_PAGES', NULL, NULL, 'news\nnews-*\nnews/*', NULL),
(164, 'content', 'news.list', 'LANG_WP_CONTENT_LIST', NULL, NULL, 'news\nnews-*\nnews/*', 'news/*/view-*\nnews/*.html\nnews/add\nnews/add/%\nnews/add?*\nnews/addcat\nnews/addcat/%\nnews/editcat/%\nnews/edit/*'),
(165, 'content', 'news.item', 'LANG_WP_CONTENT_ITEM', NULL, NULL, 'news/*.html', NULL),
(166, 'content', 'news.edit', 'LANG_WP_CONTENT_ITEM_EDIT', NULL, NULL, 'news/add\nnews/edit/*', NULL);
