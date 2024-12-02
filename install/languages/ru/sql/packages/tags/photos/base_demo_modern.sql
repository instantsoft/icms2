INSERT INTO `{#}tags` (`tag`, `frequency`) VALUES
('фото', 1);
SET @albums_tag_id = LAST_INSERT_ID();

INSERT INTO `{#}tags_bind` (`tag_id`, `target_controller`, `target_subject`, `target_id`) VALUES
(1, 'content', 'albums', 16),
(@albums_tag_id, 'content', 'albums', 16);

UPDATE `{#}tags` SET `frequency` = 6 WHERE `id` = 1;