INSERT INTO `{#}tags` (`id`, `tag`, `frequency`) VALUES
(1, 'пример', 5),
(2, 'статья', 2),
(3, 'астрономия', 1),
(9, 'наука', 1),
(36, 'новости', 4),
(37, 'проишествия', 1),
(39, 'пост', 1),
(40, 'роботы', 1);

INSERT INTO `{#}tags_bind` (`tag_id`, `target_controller`, `target_subject`, `target_id`) VALUES
(1, 'content', 'articles', 1),
(2, 'content', 'articles', 1),
(3, 'content', 'articles', 1),
(36, 'content', 'news', 2),
(36, 'content', 'news', 3),
(1, 'content', 'news', 3),
(1, 'content', 'news', 4),
(1, 'content', 'news', 6),
(36, 'content', 'news', 6),
(2, 'content', 'articles', 4),
(9, 'content', 'articles', 4),
(1, 'content', 'posts', 5),
(39, 'content', 'posts', 5),
(40, 'content', 'posts', 5),
(36, 'content', 'news', 1),
(37, 'content', 'news', 1);