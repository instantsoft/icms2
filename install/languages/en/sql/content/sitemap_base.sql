INSERT INTO `{#}controllers` (`title`, `name`, `is_enabled`, `options`, `author`, `url`, `version`, `is_backend`) VALUES
('Sitemap and robots.txt generator', 'sitemap', 1, '---\nsources:\n  content|pages: 1\n  content|albums: 1\n  content|articles: 1\n  content|posts: 1\n  content|board: 1\n  content|news: 1\n  frontpage|root: 1\n  groups|profiles: 1\n  users|profiles: 1\nshow_lastmod: 1\nshow_changefreq: 1\ndefault_changefreq: daily\nshow_priority: 1\nrobots: |\n  User-agent: *\r\n  Disallow:\ngenerate_html_sitemap: null\nchangefreq:\n  content:\n    pages:\n    albums:\n    articles:\n    posts:\n    board:\n    news:\n  frontpage:\n    root:\n  groups:\n    profiles:\n  users:\n    profiles:\npriority:\n  content:\n    pages:\n    albums:\n    articles:\n    posts:\n    board:\n    news:\n  frontpage:\n    root: 1.0\n  groups:\n    profiles: 0.8\n  users:\n    profiles: 0.8\n', 'InstantCMS Team', 'https://instantcms.ru', '2.0', 1);

INSERT INTO `{#}events` (`event`, `listener`, `ordering`, `is_enabled`) VALUES
('engine_start', 'sitemap', 141, 1);

INSERT INTO `{#}scheduler_tasks` (`title`, `controller`, `hook`, `period`, `is_strict_period`, `date_last_run`, `is_active`, `is_new`) VALUES
('Sitemap generation', 'sitemap', 'generate', 1440, NULL, NULL, 1, 0);