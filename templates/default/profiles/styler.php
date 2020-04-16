<?php if (!empty($bg_img)){ ?>
<style>

    body {
        background-image: url("<?php echo $config->upload_root . $bg_img['original']; ?>") !important;
        <?php if (!empty($bg_color)){ ?>
            background-color: <?php echo $bg_color; ?> !important;
        <?php } ?>
        <?php if (!empty($bg_repeat)){ ?>
            background-repeat: <?php echo $bg_repeat; ?> !important;
        <?php } ?>
        <?php if (!empty($bg_pos_x)){ ?>
            background-position-x: <?php echo $bg_pos_x; ?> !important;
        <?php } ?>
        <?php if (!empty($bg_pos_y)){ ?>
            background-position-y: <?php echo $bg_pos_y; ?> !important;
        <?php } ?>
    }

    #body{
        <?php if (!empty($margin_top)){ ?>
            margin-top: <?php echo $margin_top; ?>px !important;
        <?php } ?>
    }

</style>
<?php } ?>
