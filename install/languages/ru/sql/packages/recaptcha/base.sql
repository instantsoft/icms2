INSERT INTO `{#}controllers` (`title`, `name`, `is_enabled`, `options`, `author`, `url`, `version`, `is_backend`) VALUES
('Капча reCAPTCHA', 'recaptcha', 1, '---\npublic_key:\nprivate_key:\ntheme: light\nlang: ru\nsize: normal\n', 'InstantCMS Team', 'https://instantcms.ru', '2.0', 1);

INSERT INTO `{#}events` (`event`, `listener`, `ordering`, `is_enabled`) VALUES
('captcha_html', 'recaptcha', 85, 1),
('captcha_validate', 'recaptcha', 86, 1),
('captcha_list', 'recaptcha', 87, 1);