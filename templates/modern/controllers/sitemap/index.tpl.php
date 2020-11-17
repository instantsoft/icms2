<?php $disable_auto_insert_css = true; ?>
<h1><?php echo LANG_SITEMAP_HTML; ?></h1>
<div class="row my-3">
<?php foreach ($items as $url){ ?>
    <div class="col-sm-6 col-lg-4">
        <a href="<?php echo $url['url']; ?>" class="my-1 d-inline-block">
            <?php echo $url['title']; ?>
        </a>
    </div>
<?php } ?>
</div>
<?php if($show_back) { ?>
<div class="back_button">
    <a href="<?php echo href_to('sitemap'); ?>" class="btn btn-primary">
        <?php echo LANG_BACK; ?>
    </a>
</div>
<?php } ?>