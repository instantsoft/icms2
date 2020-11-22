<div class="icms-content-header">
    <div class="icms-bg__cover icms-content-header__banner <?php if ($image_is_parallax){ ?>parallax-window<?php } ?>" <?php if ($image_is_parallax){ ?>data-parallax="scroll" data-image-src="<?php echo $image_src; ?>"<?php } else { ?>style="background-image: url(<?php echo $image_src; ?>)"<?php } ?>>
        <div class="container py-5 position-relative">
            <?php foreach ($fields as $field) { ?>
                <div class="icms-content-header__field ft_<?php echo $field['type']; ?> f_<?php echo $field['name']; ?>">
                    <?php echo $field['html']; ?>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php if ($image_is_parallax){
	$this->addTplJSNameFromContext([
        'vendors/parallax/parallax.min'
    ]);
} ?>