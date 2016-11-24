ALTER TABLE `{#}geo_cities` ADD `ordering` INT(11) NOT NULL DEFAULT '10000' AFTER `name`;
ALTER TABLE `{#}geo_regions` ADD `ordering` INT(11) NOT NULL DEFAULT '1000' AFTER `name`;
