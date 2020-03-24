<div class="card mb-4 widget<?php if ($widget['class_wrap']) { ?> <?php echo $widget['class_wrap'];  } ?>" id="widget_wrapper_<?php echo $widget['id']; ?>">
    <?php if ($widget['title'] && $is_titles){ ?>
    <h4 class="card-header<?php if ($widget['class_title']) { ?> <?php echo $widget['class_title'];  } ?>">
        <?php echo $widget['title']; ?>
        <?php if (!empty($widget['links'])) { ?>
            <div class="links">
                <?php $links = string_parse_list($widget['links']); ?>
                <?php foreach($links as $link){ ?>
                    <a href="<?php html((strpos($link['value'], 'http') === 0) ? $link['value'] : href_to($link['value'])); ?>">
                        <?php html($link['id']); ?>
                    </a>
                <?php } ?>
            </div>
        <?php } ?>
    </h4>
    <?php } ?>
    <div class="card-body<?php if ($widget['class']) { ?> <?php echo $widget['class'];  } ?>">
        <?php echo $widget['body']; ?>
    </div>
</div>