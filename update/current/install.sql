DROP TABLE IF EXISTS `{#}content_relations`;
CREATE TABLE `{#}content_relations` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(256) DEFAULT NULL,
  `ctype_id` int(11) unsigned DEFAULT NULL,
  `child_ctype_id` int(11) unsigned DEFAULT NULL,
  `layout` varchar(32) DEFAULT NULL,
  `options` text,
  `seo_keys` varchar(256) DEFAULT NULL,
  `seo_desc` varchar(256) DEFAULT NULL,
  `seo_title` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ctype_id` (`ctype_id`),
  KEY `child_ctype_id` (`child_ctype_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{#}content_relations_bind`;
CREATE TABLE `{#}content_relations_bind` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_ctype_id` int(11) unsigned DEFAULT NULL,
  `parent_item_id` int(11) unsigned DEFAULT NULL,
  `child_ctype_id` int(11) unsigned DEFAULT NULL,
  `child_item_id` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_ctype_id` (`parent_ctype_id`),
  KEY `parent_item_id` (`parent_item_id`),
  KEY `child_ctype_id` (`child_ctype_id`),
  KEY `child_item_id` (`child_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `{#}geo_cities` ADD `ordering` INT(11) NOT NULL DEFAULT '10000' AFTER `name`;
ALTER TABLE `{#}geo_regions` ADD `ordering` INT(11) NOT NULL DEFAULT '1000' AFTER `name`;