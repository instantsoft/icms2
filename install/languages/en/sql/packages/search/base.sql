INSERT INTO `{#}controllers` (`title`, `name`, `is_enabled`, `options`, `author`, `url`, `version`, `is_backend`) VALUES
('Search', 'search', 1, '---\nperpage: 15\ntypes:\n  - pages\n  - articles\n  - posts\n  - albums\n  - board\n  - news\n  - groups\n  - photos\nis_hash_tag: null\norder_by: fsort\nseo_h1: \'{query?|Search}{query?Search results for «%s»}\'\nseo_title: \'{query?|Search}{query?Search results for «%s»}\'\nseo_keys: \"\"\nseo_desc: \'{target_title}: {query?|Search}{query?Search results for «%s»}\'\n', 'InstantCMS Team', 'https://instantcms.ru', '2.0', 1);

INSERT INTO `{#}events` (`event`, `listener`, `ordering`, `is_enabled`) VALUES
('photos_before_item', 'search', 95, 1),
('content_before_list', 'search', 96, 1),
('content_before_item', 'search', 97, 1),
('before_print_head', 'search', 98, 1);

INSERT INTO `{#}widgets` (`controller`, `name`, `title`, `author`, `url`, `version`, `is_external`) VALUES
('search', 'search', 'Search', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL);