<?php

    if(!isset($widgets)){ $widgets = [$widget]; }

    $wrap_class = ['icms-widget__tabbed'];
    $widget_links = [];
    $widgets_id = 'icms-widget__tabbed';

    foreach($widgets as $widget) {
        $widgets_id .= '_'.$widget['id'];
        if ($widget['class_wrap'] && !in_array($widget['class_wrap'], $wrap_class)) {
            $wrap_class[] = $widget['class_wrap'];
        }
        if (!empty($widget['links'])) {
            $widget_links[] = [
                'id' => 'widget-links-'.$widget['id'],
                'links' => string_parse_list($widget['links'])
            ];
        }
    }

?>
<div class="card mb-3 mb-md-4 <?php echo implode(' ', $wrap_class); ?>" id="<?php echo $widgets_id; ?>">
    <div class="card-header h5 py-0 pl-0 d-flex align-items-center<?php if ($widget['class_title']) { ?> <?php echo $widget['class_title'];  } ?>">
        <ul class="nav nav-tabs border-0" id="<?php echo $widgets_id; ?>_tabs">
            <?php foreach($widgets as $index => $widget) { ?>
                <li class="nav-item<?php if ($widget['class_title']) { ?> <?php echo $widget['class_title'];  } ?>">
                    <a class="nav-link px-2 px-lg-3<?php if ($index==0) { ?> active<?php } ?>" data-toggle="tab" data-id="<?php echo $widget['id']; ?>" href="#widget-<?php echo $widget['id']; ?>">
                        <?php echo $widget['title'] ? string_replace_svg_icons($widget['title']) : ($index+1); ?>
                    </a>
                </li>
            <?php } ?>
        </ul>
        <?php if ($widget_links) { ?>
            <div class="links ml-auto">
                <?php foreach($widget_links as $index => $widget_link) { ?>
                    <div class="links-wrap" id="<?php echo $widget_link['id']; ?>" <?php if ($index>0) { ?>style="display: none"<?php } ?>>
                    <?php if($device_type !== 'desktop'){ ?>
                        <div class="dropdown">
                            <button class="btn btn-light" type="button" data-toggle="dropdown">
                                <?php html_svg_icon('solid', 'ellipsis-v'); ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                            <?php foreach($widget_link['links'] as $link){ ?>
                                <li class="nav-item">
                                    <a class="nav-link text-nowrap" href="<?php echo (strpos($link['value'], 'http') === 0) ? $link['value'] : href_to($link['value']); ?>">
                                        <?php echo $link['id']; ?>
                                    </a>
                                </li>
                            <?php } ?>
                            </ul>
                        </div>
                    <?php } else { ?>
                        <?php foreach($widget_link['links'] as $link){ ?>
                            <a class="btn btn-outline-info btn-sm" href="<?php echo (strpos($link['value'], 'http') === 0) ? $link['value'] : href_to($link['value']); ?>">
                                <?php echo $link['id']; ?>
                            </a>
                        <?php } ?>
                    <?php } ?>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
    <div class="icms-widgets tab-content">
        <?php foreach($widgets as $index=>$widget) { ?>
            <div id="widget-<?php echo $widget['id']; ?>" class="card-body tab-pane<?php if ($index==0) { ?> active<?php } ?><?php if ($widget['class']) { ?> <?php echo $widget['class'];  } ?>" role="tabpanel">
                <?php echo $widget['body']; ?>
                <?php if(cmsUser::isAdmin()){ ?>
                    <?php $this->addTplJSName('widgets'); ?>
                    <?php include 'wrap_edit_links.tpl.php'; ?>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
</div>