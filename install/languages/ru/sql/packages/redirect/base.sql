INSERT INTO `{#}controllers` (`title`, `name`, `is_enabled`, `options`, `author`, `url`, `version`, `is_backend`) VALUES
('Редиректы', 'redirect', 1, '---\nno_redirect_list:\nblack_list:\nis_check_link: null\nwhite_list:\nredirect_time: 10\nis_check_refer: null\n', 'InstantCMS Team', 'https://instantcms.ru', '2.0', 1);

INSERT INTO `{#}events` (`event`, `listener`, `ordering`, `is_enabled`) VALUES
('engine_start', 'redirect', 215, 1);