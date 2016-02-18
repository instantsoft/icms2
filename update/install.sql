INSERT INTO `cms_widgets_pages` (`id`, `controller`, `name`, `title_const`, `title_subject`, `title`, `url_mask`, `url_mask_not`) VALUES
(143, 'content', 'pages.all', 'LANG_WP_CONTENT_ALL_PAGES', 'Страницы', NULL, 'pages\npages-*\npages/*', NULL),
(144, 'content', 'pages.list', 'LANG_WP_CONTENT_LIST', 'Страницы', NULL, 'pages\npages-*\npages/*', 'pages/*.html\npages/add\npages/edit/*'),
(145, 'content', 'pages.item', 'LANG_WP_CONTENT_ITEM', 'Страницы', NULL, 'pages/*.html', NULL),
(146, 'content', 'pages.edit', 'LANG_WP_CONTENT_ITEM_EDIT', 'Страницы', NULL, 'pages/add\npages/edit/*', NULL);
