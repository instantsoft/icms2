UPDATE `{#}comments` SET `user_id` = NULL WHERE `user_id` = 0;
UPDATE `{#}menu` SET `is_fixed` = NULL WHERE `name` = 'footer';
