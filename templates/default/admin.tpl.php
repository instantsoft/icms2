<!DOCTYPE html>
<html>
<head>
	<title><?php $this->title(); ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo cmsForm::getCSRFToken(); ?>" />
    <?php $this->head(false); ?>
</head>
<body>

    <section>
        <h1>😞</h1>
        <p>Шаблон больше не поддерживается</p>
        <p>The template is no longer supported</p>
        <a href="<?php echo href_to('admin', 'settings', ['switch_template', 'admincoreui']); ?>">
            Нажмите, чтобы переключиться на CoreUI
        </a>
    </section>

    <?php $this->bottom(); ?>
</body>
</html>