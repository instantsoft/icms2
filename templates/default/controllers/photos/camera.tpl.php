<?php

    $this->addTplJSName([
        'photos',
        'jquery-flex-images'
    ]);

$this->setPageTitle($page_title);
$this->setPageDescription($page_title);

if ($ctype['options']['list_on']) {
    $this->addBreadcrumb($ctype['title'], href_to($ctype['name']));
}

$this->addBreadcrumb($page_title);

?>

<h1><?php echo $page_title; ?></h1>
<?php if ($hooks_html) { ?>
    <div class="camera_hints">
        <?php echo html_each($hooks_html); ?>
    </div>
<?php } ?>


<div class="album-photos-wrap" id="album-photos-list"<?php if ($is_owner) { ?> data-delete-url="<?php echo $this->href_to('delete'); ?>"<?php } ?>>
    <?php echo $this->renderChild('photos', array(
        'photos'   => $photos,
        'is_owner' => $is_owner,
        'user'     => $user,
        'has_next' => $has_next,
        'preset_small' => $preset_small,
        'page'     => $page
    )); ?>
</div>

<?php if($photos && ($has_next || (!$has_next && $page > 1))){ ?>
<a class="show-more" href="<?php echo $item['base_url'].((strpos($item['base_url'], '?') !== false) ? '&' : '?').'photo_page='.($has_next ? ($page+1) : 1); ?>" onclick="return icms.photos.showMore(this);" data-url="<?php echo href_to('photos', 'more', array($item_type, $item['id'])); ?>" data-url-params="<?php html(json_encode($item['url_params'])); ?>" data-first-page-url="<?php echo $item['base_url']; ?>">
    <span data-to-first="<?php echo LANG_RETURN_TO_FIRST; ?>">
        <?php if($has_next){ echo LANG_SHOW_MORE; } else { echo LANG_RETURN_TO_FIRST; } ?>
    </span>
</a>

<script>
    icms.photos.initial_page = <?php echo $page; ?>;
    icms.photos.init = true;
    icms.photos.mode = 'album';
</script>

<?php } ?>

<script>
    <?php echo $this->getLangJS('LANG_PHOTOS_DELETE_PHOTO_CONFIRM'); ?>
    icms.photos.row_height = '<?php echo $row_height; ?>';
    $(function(){
        icms.photos.initAlbum();
    });
</script>