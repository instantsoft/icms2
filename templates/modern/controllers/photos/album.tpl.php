<?php $this->addTplJSName([
    'photos',
    'jquery-flex-images'
]); ?>

<div class="album-photos-wrap d-flex flex-wrap m-n1" id="album-photos-list"<?php if ($is_owner || !empty($item['is_public'])) { ?> data-delete-url="<?php echo $this->href_to('delete'); ?>"<?php } ?>>
    <?php echo $this->renderChild('photos', [
        'photos'   => $photos,
        'item'     => $item,
        'is_owner' => $is_owner,
        'user'     => $user,
        'has_next' => $has_next,
        'preset_small' => $preset_small,
        'page'     => $page
    ]); ?>
</div>

<?php if($photos && ($has_next || (!$has_next && $page > 1))){ ?>
<a class="btn btn-primary btn-block btn-sm mt-2  mb-3 show-more" href="<?php echo $item['base_url'].((strpos($item['base_url'], '?') !== false) ? '&' : '?').'photo_page='.($has_next ? ($page+1) : 1); ?>" data-url="<?php echo href_to('photos', 'more', [$item_type, $item['id']]); ?>" data-url-params="<?php html(json_encode($item['url_params'])); ?>" data-first-page-url="<?php echo $item['base_url']; ?>" id="album-photos-show-more">
    <span data-to-first="<?php echo LANG_RETURN_TO_FIRST; ?>">
        <?php if($has_next){ echo LANG_SHOW_MORE; } else { echo LANG_RETURN_TO_FIRST; } ?>
    </span>
</a>
<?php ob_start(); ?>
<script>
    icms.photos.initial_page = <?php echo $page; ?>;
    icms.photos.init = true;
    icms.photos.mode = 'album';
    $(function(){
        $('#album-photos-show-more').on('click', function(){
            return icms.photos.showMore(this);
        });
    });
</script>
<?php $this->addBottom(ob_get_clean()); ?>

<?php } ?>
<?php ob_start(); ?>
<script>
    <?php echo $this->getLangJS('LANG_PHOTOS_DELETE_PHOTO_CONFIRM'); ?>
    icms.photos.row_height = '<?php echo $row_height; ?>';
    $(function(){
        icms.photos.initAlbum();
    });
</script>
<?php $this->addBottom(ob_get_clean()); ?>