<?php $this->addBreadcrumb(LANG_OPTIONS); ?>
<div class="alert alert-info">
    <p><?php printf(LANG_SITEMAP_INFO_CRON, href_to('admin', 'settings', 'scheduler')); ?></p>
    <p><?php printf(LANG_SITEMAP_INFO_URL, href_to('sitemap.xml'), href_to_abs('sitemap.xml')); ?></p>
    <p><?php printf(LANG_SITEMAP_INFO_HTML, href_to('sitemap'), href_to_abs('sitemap')); ?></p>
    <p class="mb-0"><?php printf(LANG_SITEMAP_INFO_ROBOTS, href_to('robots.txt'), href_to_abs('robots.txt')); ?></p>
</div>
<?php
    $this->renderForm($form, $options, [
        'action'      => '',
        'method'      => 'post'
    ], $errors);
