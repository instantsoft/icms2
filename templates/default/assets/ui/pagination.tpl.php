<div class="pagebar">
    <span class="pagebar_nav">
        <?php if ($prev_url) { ?>
            <a class="pagebar_page pagebar_prev_btn" href="<?php html($prev_url); ?>">
                ← <?php echo LANG_PAGE_PREV; ?>
            </a>
        <?php } else { ?>
            <span class="pagebar_page pagebar_prev_btn disabled">
                ← <?php echo LANG_PAGE_PREV; ?>
            </span>
        <?php } ?>
        <?php if ($next_url) { ?>
            <a class="pagebar_page pagebar_next_btn" href="<?php html($next_url); ?>">
                <?php echo LANG_PAGE_NEXT; ?> →
            </a>
        <?php } else { ?>
            <span class="pagebar_page pagebar_next_btn disabled">
                <?php echo LANG_PAGE_NEXT; ?> →
            </span>
        <?php } ?>
    </span>
    <span class="pagebar_pages">
    <?php foreach ($pages as $page) { ?>
        <?php if ($page['url']) { ?>
            <?php if ($page['is_current']) { ?>
                <span class="pagebar_current"><?php html($page['num']); ?></span>
            <?php } else { ?>
                <a class="pagebar_page" href="<?php html($page['url']); ?>">
                    <?php html($page['num']); ?>
                </a>
            <?php } ?>
        <?php } else { ?>
            <span class="pagebar_page disabled"><?php html($page['num']); ?></span>
        <?php } ?>
    <?php } ?>
    </span>
<?php if ($stat_hint) { ?>
    <div class="pagebar_notice"><?php html($stat_hint); ?></div>
<?php } ?>
</div>
