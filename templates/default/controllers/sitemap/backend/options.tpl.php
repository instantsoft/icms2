<?php

    $this->addBreadcrumb(LANG_OPTIONS);

    $this->addToolButton(array(
        'class' => 'save',
        'title' => LANG_SAVE,
        'href'  => "javascript:icms.forms.submit()"
    ));

	$this->addToolButton(array(
		'class' => 'help',
		'title' => LANG_HELP,
		'target' => '_blank',
		'href'  => LANG_HELP_URL_COM_SITEMAP
	));

?>

<?php ob_start(); ?>

<?php $sitemap_index_file = 'cache/static/sitemaps/sitemap.xml'; ?>

<fieldset>
    <legend><?php echo LANG_INFORMATION; ?></legend>
    <p>
        <?php printf(LANG_SITEMAP_INFO_CRON, href_to('admin', 'settings', 'scheduler')); ?><br>
        <?php printf(LANG_SITEMAP_INFO_URL, href_to('sitemap.xml'), href_to_abs('sitemap.xml')); ?>
    </p>
</fieldset>

<?php $append_html = ob_get_clean(); ?>

<?php
    $this->renderForm($form, $options, array(
        'action' => '',
        'method' => 'post',
        'append_html' => $append_html,
    ), $errors);
?>
