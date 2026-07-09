ALTER TABLE `{#}users` ADD `city` INT(11) UNSIGNED NULL DEFAULT NULL, ADD INDEX (`city`);
ALTER TABLE `{#}users` ADD `city_cache` VARCHAR(128) NULL DEFAULT NULL;
UPDATE `{#}users` SET `city` = '4400', `city_cache` = 'Москва' WHERE `id` = 1;

INSERT INTO `{#}users_fields` (`name`, `title`, `hint`, `ordering`, `fieldset`, `type`, `is_in_list`, `is_in_item`, `is_in_filter`, `is_private`, `is_fixed`, `is_fixed_type`, `is_system`, `values`, `options`, `groups_read`, `groups_edit`) VALUES
('city', 'Город', 'Укажите город, в котором вы живете', 3, 'Анкета', 'city', NULL, 1, 1, NULL, NULL, NULL, NULL, NULL, '---\nlabel_in_item: left\nis_required: 1\nis_digits: null\nis_alphanumeric: null\nis_email: null\n', '---\n- 0\n', '---\n- 0\n');