DROP TABLE IF EXISTS `{#}widgets_bind_pages`;
CREATE TABLE `{#}widgets_bind_pages` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `bind_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'ID параметров виджета',
  `template` varchar(30) DEFAULT NULL COMMENT 'Привязка к шаблону',
  `is_enabled` tinyint(1) UNSIGNED DEFAULT NULL COMMENT 'Включен?',
  `page_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'ID страницы для вывода',
  `position` varchar(32) DEFAULT NULL COMMENT 'Имя позиции',
  `ordering` int(11) UNSIGNED DEFAULT NULL COMMENT 'Порядковый номер',
  PRIMARY KEY (`id`),
  KEY `position` (`position`),
  KEY `page_id` (`page_id`,`position`,`ordering`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Привязка виджетов к страницам';