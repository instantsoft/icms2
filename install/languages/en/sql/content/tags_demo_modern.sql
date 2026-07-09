INSERT INTO `{#}tags` (`id`, `tag`, `frequency`) VALUES
(43, 'example', 5),
(44, 'news', 3),
(49, 'article', 2),
(50, 'science', 2),
(55, 'post', 1),
(56, 'robots', 1);

INSERT INTO `{#}tags_bind` (`tag_id`, `target_controller`, `target_subject`, `target_id`) VALUES
(43, 'content', 'news', 6),
(44, 'content', 'news', 6),
(43, 'content', 'news', 4),
(44, 'content', 'news', 3),
(43, 'content', 'news', 3),
(44, 'content', 'news', 2),
(49, 'content', 'articles', 4),
(50, 'content', 'articles', 4),
(43, 'content', 'articles', 1),
(49, 'content', 'articles', 1),
(50, 'content', 'articles', 1),
(43, 'content', 'posts', 5),
(55, 'content', 'posts', 5),
(56, 'content', 'posts', 5);