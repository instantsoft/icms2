INSERT INTO `{#}controllers` (`title`, `name`, `is_enabled`, `options`, `author`, `url`, `version`, `is_backend`) VALUES
('Теги', 'tags', 1, '---\nordering: frequency\nstyle: cloud\nmax_fs: 22\nmin_fs: 12\nmin_freq: 0\nmin_len: 0\nlimit: 10\ncolors:\nshuffle: 1\nseo_keys:\nseo_desc:\nseo_title_pattern:\nseo_desc_pattern:\nseo_h1_pattern:\n', 'InstantCMS Team', 'https://instantcms.ru', '2.0', 1);

INSERT INTO `{#}events` (`event`, `listener`, `ordering`, `is_enabled`) VALUES
('ctype_lists_context', 'tags', 121, 1),
('ctype_basic_form', 'tags', 144, 1),
('content_after_add', 'tags', 145, 1),
('content_before_update', 'tags', 146, 1),
('content_item_form', 'tags', 147, 1),
('content_before_item', 'tags', 148, 1),
('content_before_list', 'tags', 149, 1),
('content_after_delete', 'tags', 204, 1);

DROP TABLE IF EXISTS `{#}tags`;
CREATE TABLE `{#}tags` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tag` varchar(32) NOT NULL,
  `description` text DEFAULT NULL,
  `frequency` int(11) unsigned NOT NULL DEFAULT '1',
  `tag_title` varchar(300) DEFAULT NULL,
  `tag_desc` varchar(300) DEFAULT NULL,
  `tag_h1` varchar(300) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tag` (`tag`),
  UNIQUE KEY `frequency` (`frequency`,`tag`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Список тегов';

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Привязка тегов к материалам';

INSERT INTO `{#}widgets` (`controller`, `name`, `title`, `author`, `url`, `version`, `is_external`) VALUES
('tags', 'cloud', 'Облако тегов', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL);