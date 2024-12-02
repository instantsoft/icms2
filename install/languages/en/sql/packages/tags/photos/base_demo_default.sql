INSERT INTO `{#}tags` (`tag`, `frequency`) VALUES
('photo', 1);
SET @albums_tag_id = LAST_INSERT_ID();

INSERT INTO `{#}tags_bind` (`tag_id`, `target_controller`, `target_subject`, `target_id`) VALUES
(43, 'content', 'albums', 16),
(@albums_tag_id, 'content', 'albums', 16);

UPDATE `{#}tags` SET `frequency` = 6 WHERE `id` = 43;