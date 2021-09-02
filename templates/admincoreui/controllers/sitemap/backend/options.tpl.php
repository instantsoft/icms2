<?php

    $this->addBreadcrumb(LANG_OPTIONS);

    $this->addToolButton(array(
        'class' => 'save',
        'title' => LANG_SAVE,
        'href'  => "javascript:icms.forms.submit()"
    ));

?>

<?php ob_start(); ?>
<div class="field" id="sitemap_info">
<hr>
    <p><?php printf(LANG_SITEMAP_INFO_CRON, href_to('admin', 'settings', 'scheduler')); ?></p>
    <p><?php printf(LANG_SITEMAP_INFO_URL, href_to('sitemap.xml'), href_to_abs('sitemap.xml')); ?></p>
    <p><?php printf(LANG_SITEMAP_INFO_HTML, href_to('sitemap'), href_to_abs('sitemap')); ?></p>
    <p><?php printf(LANG_SITEMAP_INFO_ROBOTS, href_to('robots.txt'), href_to_abs('robots.txt')); ?></p>
</div>

<?php $append_html = ob_get_clean(); ?>

<?php
    $this->renderForm($form, $options, array(
        'action'       => '',
        'method'       => 'post',
        'append_html'  => $append_html
    ), $errors);
?>

<script>
    $(function(){
        $('#sitemap_info').appendTo('#fset_params');
    });
</script>
