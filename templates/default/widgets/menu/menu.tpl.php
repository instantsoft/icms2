<?php

    $this->menu(
        $widget->options['menu'],
        $widget->options['is_detect'],
        (!empty($widget->options['class']) ? $widget->options['class'] : 'menu'),
        $widget->options['max_items'], empty($widget->options['is_detect_strict']),
        (!empty($widget->options['template']) ? $widget->options['template'] : 'menu'),
        $widget->title
    );
