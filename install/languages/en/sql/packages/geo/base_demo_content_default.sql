ALTER TABLE `{#}users` ADD `city` INT(11) UNSIGNED NULL DEFAULT NULL, ADD INDEX (`city`);
ALTER TABLE `{#}users` ADD `city_cache` VARCHAR(128) NULL DEFAULT NULL;
UPDATE `{#}users` SET `city` = '12008', `city_cache` = 'London' WHERE `id` = 1;

INSERT INTO `{#}users_fields` (`name`, `title`, `hint`, `ordering`, `fieldset`, `type`, `is_in_list`, `is_in_item`, `is_in_filter`, `is_private`, `is_fixed`, `is_fixed_type`, `is_system`, `values`, `options`, `groups_read`, `groups_edit`) VALUES
('city', 'City', 'Select the city where you live', 3, 'About', 'city', NULL, 1, 1, NULL, NULL, NULL, NULL, NULL, '---\nlabel_in_item: left\nis_required: 1\nis_digits: \nis_alphanumeric: \nis_email: \nis_unique: \n', '---\n- 0\n', '---\n- 0\n');