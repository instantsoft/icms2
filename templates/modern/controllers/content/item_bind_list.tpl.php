<?php if ($total) { ?>
<ul class="list-unstyled list-bind-item">
    <?php foreach($items as $item) { ?>
        <?php
            $url = $mode == 'childs' ?
                    href_to($child_ctype['name'], $item['slug'].'.html') :
                    href_to($ctype['name'], $item['slug'].'.html');
        ?>
        <li class="media align-items-center mb-2" data-id="<?php echo $item['id']; ?>">
            <div class="media-body">
                <b class="mt-0 mb-1 title">
                    <a href="<?php echo $url; ?>" target="_blank"><?php html($item['title']); ?></a>
                </b>
                <div class="small">
                    <a href="<?php echo href_to_profile($item['user']); ?>" class="mr-2">
                    <?php html_svg_icon('solid', 'user'); ?>
                    <?php html($item['user']['nickname']); ?>
                </a>
                <span class="text-muted">
                    <?php html_svg_icon('solid', 'calendar'); ?>
                    <?php echo html_date_time($item['date_pub']); ?>
                </span>
                </div>
            </div>
            <div class="add">
                <input type="button" class="button btn btn-primary" value="<?php if ($mode == 'unbind') { ?>X<?php } else { ?>+<?php } ?>">
            </div>
        </li>
    <?php } ?>
</ul>
<?php } else { ?>
    <p class="alert alert-info mt-3 mb-0 list-bind-item" role="alert">
        <?php echo LANG_LIST_EMPTY; ?>
    </p>
<?php } ?>