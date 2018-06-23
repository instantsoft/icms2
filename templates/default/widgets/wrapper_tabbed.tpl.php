<?php

    if(!isset($widgets)){ $widgets = array($widget); }

    $wrap_class = array('widget_tabbed');

    foreach($widgets as $widget) {
        if ($widget['class_wrap']) {
            $wrap_class[] = $widget['class_wrap'];
        }
    }

?>
<div class="<?php echo implode(' ', $wrap_class); ?>">
    <div class="tabs">
        <ul>
            <?php foreach($widgets as $index => $widget) { ?>
                <li class="tab<?php if ($widget['class_title']) { ?> <?php echo $widget['class_title'];  } ?>">
                    <a <?php if ($index==0) { ?>class="active"<?php } ?> data-id="<?php echo $widget['id']; ?>">
                        <?php echo $widget['title'] ? $widget['title'] : ($index+1); ?>
                    </a>
                </li>
            <?php } ?>
            <li class="links">
                <?php foreach($widgets as $index => $widget) { ?>
                    <?php if (!empty($widget['links'])) { ?>
                        <div class="links-wrap" id="widget-links-<?php echo $widget['id']; ?>" <?php if ($index>0) { ?>style="display: none"<?php } ?>>
                            <?php $links = string_parse_list($widget['links']); ?>
                            <?php foreach($links as $link){ ?>
                                <a href="<?php echo (strpos($link['value'], 'http') === 0) ? $link['value'] : href_to($link['value']); ?>">
                                    <?php echo $link['id']; ?>
                                </a>
                            <?php } ?>
                        </div>
                    <?php } ?>
                <?php } ?>
            </li>
        </ul>
    </div>
    <div class="widgets">
        <?php foreach($widgets as $index=>$widget) { ?>
            <div id="widget-<?php echo $widget['id']; ?>" class="body<?php if ($widget['class']) { ?> <?php echo $widget['class'];  } ?>" <?php if ($index>0) { ?>style="display: none"<?php } ?>>
                <?php echo $widget['body']; ?>
                <?php if(cmsUser::isAdmin()){ ?>
                    <?php include 'wrap_edit_links.tpl.php'; ?>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
</div>