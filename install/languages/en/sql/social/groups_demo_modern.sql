INSERT INTO `{#}content_datasets` (`ctype_id`, `name`, `title`, `description`, `ordering`, `is_visible`, `filters`, `sorting`, `index`, `groups_view`, `groups_hide`, `seo_keys`, `seo_desc`, `seo_title`, `cats_view`, `cats_hide`, `max_count`, `target_controller`) VALUES
(NULL, 'rating', 'Top Groups', NULL, 3, 1, NULL, '---\n- \n  by: rating\n  to: desc\n', 'rating', '---\n- 0\n', NULL, NULL, NULL, NULL, NULL, NULL, 0, 'groups'),
(NULL, 'all', 'New Groups', NULL, 2, 1, NULL, '---\n- \n  by: date_pub\n  to: desc\n', 'date_pub', '---\n- 0\n', NULL, NULL, NULL, NULL, NULL, NULL, 0, 'groups'),
(NULL, 'popular', 'Popular', NULL, 1, 1, NULL, '---\n- \n  by: members_count\n  to: desc\n', 'members_count', '---\n- 0\n', NULL, NULL, NULL, NULL, NULL, NULL, 0, 'groups');

INSERT INTO `{#}groups` (`id`, `owner_id`, `date_pub`, `title`, `description`, `logo`, `rating`, `members_count`, `join_policy`, `edit_policy`, `wall_policy`, `is_closed`) VALUES (1, 1, CURRENT_TIMESTAMP, 'Robot Fans', 'The group is dedicated to robots, machine-building and all that is connected with it.', '---\nsmall: 000/u1/7/3/robototehnika-logo-small.png\nmicro: 000/u1/e/3/robototehnika-logo-micro.png\n', 0, 1, 0, 0, 0, 0);

UPDATE `{#}con_posts` SET `parent_id` = 1, `parent_type` = 'group', `parent_title` = 'Fans of Robots', `parent_url` = 'groups/1/content/posts' WHERE `id` = 5;

INSERT INTO `{#}uploaded_files` (`path`, `name`, `size`, `counter`, `type`, `target_controller`, `target_subject`, `target_id`, `user_id`) VALUES
('000/u1/7/3/robototehnika-logo-small.png', 'robototehnika-logo-small.png', 5259, 0, 'image', 'groups', '1', 0, 1),
('000/u1/e/3/robototehnika-logo-micro.png', 'robototehnika-logo-micro.png', 1873, 0, 'image', 'groups', '1', 0, 1);

INSERT INTO `{#}groups_members` (`id`, `group_id`, `user_id`, `role`, `date_updated`) VALUES
(1, 1, 1, 2, CURRENT_TIMESTAMP);