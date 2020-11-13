<?php
/**
 * Template Name: LANG_CP_LISTVIEW_STYLE_SLIDER
 * Template Type: widget
 */

    $this->addTplJSName([
        'photos',
        'vendors/slick/slick.min'
    ]);

    $this->addTplCSSNameFromContext('slick');

    $this->addTplCSS('controllers/photos/styles');

    $photo_wrap_id = 'widget-photos-'.$widget->id;

?>

<div class="album-photos-wrap my-n1" id="<?php echo $photo_wrap_id; ?>"<?php if ($is_owner) { ?> data-delete-url="<?php echo href_to('photos', 'delete'); ?>"<?php } ?>>
    <?php echo $this->renderControllerChild('photos', 'photos', array(
        'photos'        => $photos,
        'is_owner'      => $is_owner,
        'user'          => $user,
        'photo_wrap_id' => $photo_wrap_id,
        'preset_small'  => $preset_small,
        'disable_owner' => true,
        'disable_flex'  => true
    )); ?>
</div>

<?php ob_start(); ?>
<script>
    $(function(){
        icms.photos.initCarousel('#<?php echo $photo_wrap_id; ?>');
    });
</script>
<?php $this->addBottom(ob_get_clean()); ?>