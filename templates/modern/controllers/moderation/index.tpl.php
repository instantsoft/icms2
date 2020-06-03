<h1><?php echo $page_title; ?></h1>

<div id="moderation_content_pills">
    <?php $this->menu('moderation_content_types', true, 'nav nav-pills mb-3 mb-md-4'); ?>
</div>

<div id="moderation_content_list"><?php echo $list_html; ?></div>