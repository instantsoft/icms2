UPDATE `{#}perms_rules` SET `type` = 'list', `options` = 'premod,yes' WHERE `controller` = 'groups' AND `name` = 'add';
UPDATE `{#}perms_users` SET `value`= 'yes' WHERE `rule_id` = (SELECT `id` FROM `{#}perms_rules` WHERE `controller` = 'groups' AND `name` = 'add');
UPDATE `{#}perms_rules` SET `type` = 'list', `options` = 'premod,yes' WHERE `controller` = 'content' AND `name` = 'add';
UPDATE `{#}perms_users` SET `value`= 'yes' WHERE `rule_id` = (SELECT `id` FROM `{#}perms_rules` WHERE `controller` = 'content' AND `name` = 'add');
UPDATE `{#}perms_rules` SET `type` = 'list', `options` = 'premod_own,own,premod_all,all' WHERE `controller` = 'content' AND `name` = 'edit';
ALTER TABLE `{#}content_datasets` CHANGE `filters` `filters` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Массив фильтров набора';
ALTER TABLE `{#}content_datasets` CHANGE `sorting` `sorting` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Массив правил сортировки';