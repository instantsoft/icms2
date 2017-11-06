UPDATE `{#}perms_rules` SET `type` = 'list', `options` = 'yes,premod' WHERE `controller` = 'groups' AND `name` = 'add';
UPDATE `{#}perms_users` SET `value`= 'yes' WHERE `rule_id` = '15';
ALTER TABLE `{#}content_datasets` CHANGE `filters` `filters` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Массив фильтров набора';
ALTER TABLE `{#}content_datasets` CHANGE `sorting` `sorting` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Массив правил сортировки';