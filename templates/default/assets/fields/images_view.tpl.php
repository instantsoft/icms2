<div id="<?php echo $block_id; ?>">
<?php foreach ($images as $key => $image) { ?>
    <?php
        $current_img_attr = $img_attr;
        $current_img_attr['class'] = ($current_img_attr['class'] ?? '') . ' ' . $image['img_class'];
    ?>
    <?php if($image['is_gif']){ ?>
        <?php echo html_gif_image($image['paths'], $image['small_preset'], $image['title'] . ' ' . $key, $current_img_attr); ?>
    <?php } else { ?>
        <a title="<?php html($image['title']); ?>" class="<?php html($image['link_class']); ?>" href="<?php echo html_image_src($image['paths'], $image['big_preset'], true); ?>">
            <?php echo html_image($image['paths'], $image['small_preset'], $image['title'] . ' ' . $key, $current_img_attr); ?>
        </a>
    <?php } ?>
<?php } ?>
</div>

<?php if($images->getReturn()) { ?>
    <?php $this->addBottom('<script>$(function() { icms.modal.bindGallery(".img-' . $field->getName() . '"); });</script>'); ?>
    <?php if($field->getOption('view_as_slider')) { ?>
        <?php
        $this->addTplJSNameFromContext('vendors/slick/slick.min');
        $this->addTplCSSNameFromContext('slick');
        $this->addBottom('<script>$(function() {$("#' . $block_id . '").slick(' . json_encode($slider_params) . ');});</script>');
        ?>
    <?php } ?>
<?php } ?>