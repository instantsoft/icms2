<h1 itemprop="name">
    <?php html($photo['title']); ?>
    <?php if ($photo['is_private'] == 1) { ?>
        <span class="is_private" title="<?php html(LANG_PRIVACY_PRIVATE); ?>"></span>
    <?php } ?>
</h1>
<img data-page-url="<?php echo href_to('photos', $photo['slug'].'.html').(!empty($photos_url_params) ? '?'.$photos_url_params : ''); ?>" src="<?php echo html_image_src($photo['image'], $preset, true, false); ?>" alt="<?php html($photo['title']); ?>" itemprop="contentUrl" />
<div id="fullscreen_photo" class="<?php if ($request->isAjax()) { ?>close<?php } else { ?>disabled-act<?php } ?>"><div></div></div>
<?php if($prev_photo && $prev_photo['slug']){ ?>
    <a href="<?php echo  href_to('photos', $prev_photo['slug'].'.html'); ?>" class="photo_navigation prev_item" title="<?php html($prev_photo['title']); ?>"></a>
<?php } ?>
<?php if($next_photo && $next_photo['slug']){ ?>
    <a href="<?php echo  href_to('photos', $next_photo['slug'].'.html'); ?>" class="photo_navigation next_item" title="<?php html($next_photo['title']); ?>"></a>
<?php } ?>
<?php if (!$request->isAjax()) { ?>
    <div class="fullscreen_click"></div>
<?php } ?>
<script>
    <?php if(!empty($photos_url_params)){ ?>
        $(function(){
            $('.photo_navigation').each(function (){
                $(this).attr('href', $(this).attr('href')+'?<?php echo $photos_url_params; ?>');
            });
        });
    <?php } ?>
</script>