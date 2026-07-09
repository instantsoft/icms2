INSERT INTO `{#}controllers` (`title`, `name`, `is_enabled`, `options`, `author`, `url`, `version`, `is_backend`) VALUES
('Content Security Policy', 'csp', 1, '---\nenable_csp: null\ncsp_str: \"default-src \'self\'; script-src \'unsafe-eval\' \'nonce-{nonce}\' \'strict-dynamic\'; style-src \'self\' data: \'unsafe-inline\' https://fonts.googleapis.com; img-src \'self\' data: https://instantcms.ru; font-src \'self\' data: https://fonts.gstatic.com\"\nis_report_only: 1\nenable_report: 1\n', 'InstantCMS Team', 'https://instantcms.ru', '2.0', 1);

INSERT INTO `{#}events` (`event`, `listener`, `ordering`, `is_enabled`) VALUES
('engine_start', 'csp', 239, 1);

DROP TABLE IF EXISTS `{#}csp_logs`;
CREATE TABLE `{#}csp_logs` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `date_pub` timestamp NOT NULL DEFAULT current_timestamp(),
  `blocked_uri` varchar(255) DEFAULT NULL,
  `referrer` varchar(255) DEFAULT NULL,
  `line_number` smallint(6) DEFAULT NULL,
  `document_uri` varchar(255) DEFAULT NULL,
  `violated_directive` varchar(64) DEFAULT NULL,
  `effective_directive` varchar(64) DEFAULT NULL,
  `status_code` smallint(6) DEFAULT NULL,
  `ip` varbinary(16) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `date_pub` (`date_pub`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='CSP Logs';