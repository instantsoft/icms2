<div class="input-group mb-0  mt-3 mt-md-4">
    <?php if ($prev_url) { ?>
        <div class="input-group-prepend">
            <a href="<?php html($prev_url); ?>" class="btn btn-outline-secondary">
                <?php html_svg_icon('solid', 'arrow-left'); ?>
                <span class="d-none d-sm-inline-block"><?php echo LANG_PAGE_PREV; ?></span>
            </a>
        </div>
    <?php } ?>
    <button class="btn btn-outline-secondary dropdown-toggle rounded-0" type="button" data-toggle="dropdown"><?php echo $current_page; ?></button>
    <div class="dropdown-menu">
        <?php foreach ($pages as $page) { ?>
            <?php if ($page['url']) { ?>
                <a class="dropdown-item <?php echo ($page['is_current'] ? 'active' : ''); ?>" href="<?php html($page['url']); ?>">
                    <?php echo LANG_PAGE; ?> <?php html($page['num']); ?>
                </a>
            <?php } else { ?>
                <span class="dropdown-item disabled"><?php html($page['num']); ?></span>
            <?php } ?>
        <?php } ?>
    </div>
    <?php if ($next_url) { ?>
        <div class="input-group-append">
            <a href="<?php html($next_url); ?>" class="btn btn-outline-secondary">
                <span class="d-none d-sm-inline-block"><?php echo LANG_PAGE_NEXT; ?></span>
                <?php html_svg_icon('solid', 'arrow-right'); ?>
            </a>
        </div>
    <?php } ?>
</div>