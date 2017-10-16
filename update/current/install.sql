UPDATE `{#}perms_rules` SET `type` = 'list', `options` = 'yes,premod' WHERE `controller` = 'groups' AND `name` = 'add';
UPDATE `{#}perms_users` SET `value`= 'yes' WHERE `rule_id` = '15';