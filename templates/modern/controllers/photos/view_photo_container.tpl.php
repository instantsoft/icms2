<h1 itemprop="name">
    <?php html($photo['title']); ?>
    <?php if ($photo['is_private'] == 1) { ?>
        <span class="is_private text-secondary" title="<?php html(LANG_PRIVACY_PRIVATE); ?>">
            <?php html_svg_icon('solid', 'lock'); ?>
        </span>
    <?php } ?>
</h1>
<img class="img-fluid" data-page-url="<?php echo href_to('photos', $photo['slug'].'.html').(!empty($photos_url_params) ? '?'.$photos_url_params : ''); ?>" src="<?php echo html_image_src($photo['image'], $preset, true, false); ?>" alt="<?php html($photo['title']); ?>" itemprop="contentUrl" />
<div id="fullscreen_photo" class="<?php if ($request->isAjax()) { ?>icms-fullscreen__state_expanded<?php } else { ?>d-none<?php } ?>">
    <span class="icms-fullscreen-expand"><?php html_svg_icon('solid', 'expand-alt'); ?></span>
    <span class="icms-fullscreen-compress"><?php html_svg_icon('solid', 'compress-alt'); ?></span>
</div>
<?php if($prev_photo && $prev_photo['slug']){ ?>
    <a href="<?php echo  href_to('photos', $prev_photo['slug'].'.html'); ?>" class="btn text-white d-flex align-items-center justify-content-center photo_navigation prev_item" title="<?php html($prev_photo['title']); ?>">
        <?php html_svg_icon('solid', 'chevron-left'); ?>
    </a>
<?php } ?>
<?php if($next_photo && $next_photo['slug']){ ?>
    <a href="<?php echo  href_to('photos', $next_photo['slug'].'.html'); ?>" class="btn text-white d-flex align-items-center justify-content-center photo_navigation next_item" title="<?php html($next_photo['title']); ?>">
        <?php html_svg_icon('solid', 'chevron-right'); ?>
    </a>
<?php } ?>
<?php if (!$request->isAjax()) { ?>
    <a class="fullscreen_click" href="#"><img></a>
<?php } ?>
<?php ob_start(); ?>
<script>
    <?php if(!empty($photos_url_params)){ ?>
        $(function(){
            $('.photo_navigation').each(function (){
                $(this).attr('href', $(this).attr('href')+'?<?php echo $photos_url_params; ?>');
            });
        });
    <?php } ?>
</script>
<?php $this->addBottom(ob_get_clean()); ?>