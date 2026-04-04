INSERT INTO `{#}content_datasets` (`ctype_id`, `name`, `title`, `description`, `ordering`, `is_visible`, `filters`, `sorting`, `index`, `groups_view`, `groups_hide`, `seo_keys`, `seo_desc`, `seo_title`, `cats_view`, `cats_hide`, `max_count`, `target_controller`) VALUES
(NULL, 'rating', 'Лучшие группы', NULL, 3, 1, NULL, '---\n- \n  by: rating\n  to: desc\n', 'rating', '---\n- 0\n', NULL, NULL, NULL, NULL, NULL, NULL, 0, 'groups'),
(NULL, 'all', 'Новые группы', NULL, 2, 1, NULL, '---\n- \n  by: date_pub\n  to: desc\n', 'date_pub', '---\n- 0\n', NULL, NULL, NULL, NULL, NULL, NULL, 0, 'groups'),
(NULL, 'popular', 'Популярные', NULL, 1, 1, NULL, '---\n- \n  by: members_count\n  to: desc\n', 'members_count', '---\n- 0\n', NULL, NULL, NULL, NULL, NULL, NULL, 0, 'groups');

INSERT INTO `{#}groups` (`id`, `owner_id`, `date_pub`, `title`, `description`, `logo`, `rating`, `members_count`, `join_policy`, `edit_policy`, `wall_policy`, `is_closed`) VALUES
(1, 1, DATE_SUB(NOW(),INTERVAL 10 MINUTE), 'Робототехника', 'Группа посвящена роботам, машиностроению и всему что с этим связано.', '---\noriginal: u1/004/f398ad69.png\nbig: u1/004/f13052e8.png\nnormal: u1/004/de897122.png\nsmall: u1/004/a442fa4b.png\nmicro: u1/004/c8a73161.png\n', 0, 1, 0, 0, 0, 0);

UPDATE `{#}con_posts` SET `parent_id` = 1, `parent_type` = 'group', `parent_title` = 'Робототехника', `parent_url` = 'groups/1/content/posts' WHERE `id` = 5;

INSERT INTO `{#}groups_members` (`id`, `group_id`, `user_id`, `role`, `date_updated`) VALUES
(1, 1, 1, 2, CURRENT_TIMESTAMP);