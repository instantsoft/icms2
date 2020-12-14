<ul class="pagination mb-0 my-3 my-md-4">
<?php if ($prev_url) { ?>
    <li class="page-item page-item-prev">
        <a class="page-link" href="<?php html($prev_url); ?>">
            <?php html_svg_icon('solid', 'arrow-left'); ?>
            <span class="d-none d-sm-inline-block"><?php echo LANG_PAGE_PREV; ?></span>
        </a>
    </li>
<?php } ?>
<?php foreach ($pages as $page) { ?>
    <?php if ($page['url']) { ?>
        <li class="page-item <?php echo ($page['is_current'] ? 'active' : ''); ?>">
            <a class="page-link" href="<?php html($page['url']); ?>">
                <?php html($page['num']); ?>
            </a>
        </li>
    <?php } else { ?>
        <li class="page-item disabled">
            <span class="page-link"><?php html($page['num']); ?></span>
        </li>
    <?php } ?>
<?php } ?>
<?php if ($next_url) { ?>
    <li class="page-item page-item-next">
        <a class="page-link" href="<?php html($next_url); ?>">
            <span class="d-none d-sm-inline-block"><?php echo LANG_PAGE_NEXT; ?></span>
            <?php html_svg_icon('solid', 'arrow-right'); ?>
        </a>
    </li>
<?php } ?>
<?php if ($stat_hint) { ?>
    <li class="page-item disabled d-none d-sm-inline-block">
        <span class="page-link"><?php html($stat_hint); ?></span>
    </li>
<?php } ?>
</ul>
