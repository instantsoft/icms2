INSERT INTO `{#}content_datasets` (`ctype_id`, `name`, `title`, `ordering`, `is_visible`, `filters`, `sorting`, `index`, `groups_view`, `groups_hide`) VALUES
(5, 'rating', 'Рейтинг', 5, 1, '---\n- \n  field: rating\n  condition: gt\n  value: 0\n', '---\n- \n  by: rating\n  to: desc\n', 'dataset_rating', '---\n- 0\n', NULL),
(6, 'daily', 'Лучшие за сутки', 2, 1, '---\n- \n  field: date_pub\n  condition: dy\n  value: 1\n', '---\n- \n  by: rating\n  to: desc\n', 'dataset_daily', '---\n- 0\n', NULL),
(6, 'weekly', 'за неделю', 3, 1, '---\n- \n  field: date_pub\n  condition: dy\n  value: 7\n', '---\n- \n  by: rating\n  to: desc\n', 'dataset_daily', '---\n- 0\n', NULL),
(10, 'popular', 'Популярные', 3, 1, NULL, '---\n- \n  by: rating\n  to: desc\n', 'dataset_popular', '---\n- 0\n', NULL);