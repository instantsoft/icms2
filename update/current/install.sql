ALTER TABLE `{#}scheduler_tasks` CHANGE `period` `period` DECIMAL(7,2) UNSIGNED NULL DEFAULT NULL;

INSERT INTO `{#}images_presets` (`name`, `title`, `width`, `height`, `is_square`, `is_watermark`, `wm_image`, `wm_origin`, `wm_margin`, `is_internal`, `quality`, `gamma_correct`, `crop_position`, `allow_enlarge`, `gif_to_gif`, `convert_format`)
SELECT 'user_cover', 'Обложка профиля', 1200, 200, 1, NULL, NULL, 'top-left', 0, NULL, 87, NULL, 2, NULL, NULL, 'webp'
WHERE NOT EXISTS (
    SELECT 1 FROM `{#}images_presets` WHERE `name` = 'user_cover'
);

DROP TABLE IF EXISTS `{#}upload_rate_limits`;
CREATE TABLE `{#}upload_rate_limits` (
  `user_id` int(11) UNSIGNED NOT NULL COMMENT 'ID пользователя',
  `tokens` decimal(10,4) NOT NULL DEFAULT 0.0000 COMMENT 'Текущее количество токенов',
  `date_updated` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Время последнего обновления корзины',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Ограничение частоты загрузки файлов';