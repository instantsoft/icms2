INSERT INTO `{#}scheduler_tasks` (`id`, `title`, `controller`, `hook`, `period`, `date_last_run`, `is_active`, `is_new`) VALUES
(NULL, 'Удаление пользователей не прошедших верификацию', 'auth', 'delete_locked', 1440, NULL, 1, 1);