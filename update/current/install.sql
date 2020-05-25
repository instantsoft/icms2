DROP TABLE IF EXISTS `{#}layout_cols`;
CREATE TABLE `{#}layout_cols` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `row_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'ID ряда',
  `title` varchar(255) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL COMMENT 'Название позиции',
  `type` enum('typical','custom') DEFAULT 'typical' COMMENT 'Тип колонки',
  `ordering` int(11) UNSIGNED DEFAULT NULL COMMENT 'Порядок колонки в исходном коде',
  `class` varchar(100) DEFAULT NULL COMMENT 'CSS класс колонки',
  `wrapper` text COMMENT 'Шаблон колонки',
  `options` text COMMENT 'Опции колонки',
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `row_id` (`row_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Колонки схемы позиций';

DROP TABLE IF EXISTS `{#}layout_rows`;
CREATE TABLE `{#}layout_rows` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'ID колонки родителя',
  `title` varchar(255) DEFAULT NULL,
  `tag` varchar(10) DEFAULT NULL COMMENT 'Тег ряда',
  `template` varchar(30) DEFAULT NULL COMMENT 'Привязка к шаблону',
  `ordering` int(11) DEFAULT NULL COMMENT 'Порядок ряда в исходном коде',
  `nested_position` enum('after','before') DEFAULT NULL COMMENT 'Позиция вложенного ряда',
  `class` varchar(100) DEFAULT NULL COMMENT 'CSS класс ряда',
  `options` text COMMENT 'Опции ряда',
  PRIMARY KEY (`id`),
  KEY `template` (`template`,`ordering`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Ряды схемы позиций виджетов';