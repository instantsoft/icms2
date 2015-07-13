<div class="widget_tabbed">

    <div class="tabs">
        <ul>
            <?php foreach($widgets as $index=>$widget) { ?>
                <li class="tab">
                    <a <?php if ($index==0) { ?>class="active"<?php } ?> data-id="<?php echo $widget['id']; ?>"><?php echo $widget['title'] ? $widget['title'] : ($index+1); ?></a>
                </li>
            <?php } ?>

            <li class="links">
                <?php foreach($widgets as $index=>$widget) { ?>
                    <div class="links-wrap" id="widget-links-<?php echo $widget['id']; ?>" <?php if ($index>0) { ?>style="display: none"<?php } ?>>
                        <?php if (!empty($widget['links'])) { ?>
                            <?php $links = string_parse_list($widget['links']); ?>
                            <?php foreach($links as $link){ ?>
                                <a href="<?php echo (mb_strpos($link['value'], 'http://')===0) ? $link['value'] : href_to($link['value']); ?>"><?php echo $link['id']; ?></a>
                            <?php } ?>
                        <?php } ?>
                    </div>
                <?php } ?>
            </li>

        </ul>
    </div>

    <div class="widgets">

        <?php foreach($widgets as $index=>$widget) { ?>

            <div id="widget-<?php echo $widget['id']; ?>" class="body<?php if ($widget['class']) { ?> <?php echo $widget['class'];  } ?>" <?php if ($index>0) { ?>style="display: none"<?php } ?>>
                <?php echo $widget['body']; ?>
            </div>

        <?php } ?>

    </div>

</div>
