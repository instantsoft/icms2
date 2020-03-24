<h1><?php echo LANG_SITEMAP_HTML; ?></h1>
<div class="sitemap_wrap">
    <ul>
<?php foreach ($items as $url){ ?>
        <li><a href="<?php echo $url['url']; ?>"><?php echo $url['title']; ?></a></li>
<?php } ?>
    </ul>
</div>
<?php if($show_back) { ?>
<div class="back_button">
    <a href="<?php echo href_to('sitemap'); ?>"><?php echo LANG_BACK; ?></a>
</div>
<?php } ?>