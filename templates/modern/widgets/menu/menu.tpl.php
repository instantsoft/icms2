<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#target-<?php echo $widget->options['menu']; ?>">
    <span class="navbar-toggler-icon"></span>
</button>
<div class="collapse navbar-collapse" id="target-<?php echo $widget->options['menu']; ?>">
    <?php
        $this->menu(
            $widget->options['menu'],
            $widget->options['is_detect'],
            (!empty($widget->options['class']) ? $widget->options['class'] : 'navbar-nav'),
            $widget->options['max_items'], true,
            (!empty($widget->options['template']) ? $widget->options['template'] : 'menu'),
            $widget->title
        );
    ?>
</div>