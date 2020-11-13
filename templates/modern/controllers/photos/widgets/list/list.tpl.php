<?php
/**
 * Template Name: LANG_CP_LISTVIEW_STYLE_BASIC
 * Template Type: widget
 */

    $this->addTplJSName([
        'photos',
        'jquery-flex-images'
    ]);

    $this->addTplCSS('controllers/photos/styles');

    $photo_wrap_id = 'widget-photos-'.$widget->id;
?>

<div class="album-photos-wrap d-flex flex-wrap m-n1" id="<?php echo $photo_wrap_id; ?>"<?php if ($is_owner) { ?> data-delete-url="<?php echo href_to('photos', 'delete'); ?>"<?php } ?>>
    <?php echo $this->renderControllerChild('photos', 'photos', array(
        'photos'        => $photos,
        'is_owner'      => $is_owner,
        'user'          => $user,
        'photo_wrap_id' => $photo_wrap_id,
        'preset_small'  => $preset_small,
    )); ?>
</div>

<?php ob_start(); ?>
    <script>
        <?php echo $this->getLangJS('LANG_PHOTOS_DELETE_PHOTO_CONFIRM'); ?>
        icms.photos.row_height = '<?php echo $row_height; ?>';
        $(function(){
            icms.photos.initAlbum('#<?php echo $photo_wrap_id; ?>');
            $('.icms-widget__tabbed > .card-header a[data-toggle="tab"]').one('shown.bs.tab', function (e) {
                icms.photos.flexImagesInit('<?php echo (isset($photo_wrap_id) ? '#'.$photo_wrap_id : ''); ?>');
            });
        });
    </script>
<?php $this->addBottom(ob_get_clean()); ?>