<div class="dropdown">
    <button class="btn btn-light" type="button" data-toggle="dropdown">
        <?php if($widget->is_title){ ?>
            <span class="d-none d-md-inline-block"><?php echo $widget->title; ?></span>
        <?php } ?>
        <?php html_svg_icon('solid', 'ellipsis-v'); ?>
    </button>
    <?php
        $this->menu(
            $widget->options['menu'],
            $widget->options['is_detect'],
            (!empty($widget->options['class']) ? $widget->options['class'] : 'dropdown-menu dropdown-menu-right'),
            $widget->options['max_items'], empty($widget->options['is_detect_strict']),
            (!empty($widget->options['template']) ? $widget->options['template'] : 'menu'),
            $widget->title
        );
    ?>
</div>
