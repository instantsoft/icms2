<script>
    has_next = <?php echo $has_next; ?>;
    addons_count = <?php echo $count; ?>;
</script>
<?php if(empty($items)){ ?>
    <p><?php echo LANG_CP_NO_ADDONS; ?></p>
<?php return; } ?>
<?php foreach ($items as $item) { ?>
<?php
    $latest_version = reset($item['versions']);
?>
<div class="addon-card addon-card-<?php echo $item['slug']; ?>">
    <div class="addon-card-top">
        <div class="name column-name">
            <h3>
                <a target="_blank" href="<?php echo $item['page_url']; ?>" class="open-package-details">
                    <?php echo $item['title']; ?>
                    <img src="<?php echo $item['photo']['cover80']; ?>" class="addon-icon" alt="">
                </a>
            </h3>
        </div>
        <div class="desc column-description">
            <p>
                <a href="#content_<?php echo $item['slug']; ?>" class="ajax-modal full_content_link">
                    <?php echo string_short($item['content'], 125); ?>
                </a>
            </p>
            <div class="full_content" id="content_<?php echo $item['slug']; ?>">
                <div class="full_content_wrap"><?php echo $item['content']; ?></div>
            </div>
            <p class="authors">
                <?php echo $item['type']; ?> <?php echo LANG_FROM; ?>
                <a target="_blank" href="<?php echo $item['user']['page_url']; ?>">
                    <?php echo $item['user']['nickname']; ?>
                </a>
            </p>
        </div>
    </div>
    <div class="addon-card-middle">
        <div>
            <div class="rating_stars" title="<?php echo LANG_RATING; ?>: <?php echo round($item['score'], 2); ?>">
                <?php
                    $value = $item['score'];
                    $value *= 10; $step = 0.5 * 10;
                    $value = (round($value)%$step === 0) ? round($value) : round(($value+$step/2)/$step)*$step;
                    $value /= 10;
                ?>
                <div class="rating">
                <?php for($s=1; $s<=5; $s++) { ?>
                    <?php
                        if ($value >= 1) { $class = 'full'; $value -= 1; } else
                        { $class = 'empty'; $value -= 0.5; }
                    ?>
                    <span class="<?php echo $class; ?>">☆</span>
                <?php } ?>
                </div>
            </div>
            <div class="popularity">
                <?php if($item['price']){ ?>
                    <?php echo html_spellcount($item['followers_count'], LANG_SUBSCRIBERS_SPELL); ?>
                <?php } else { ?>
                    <?php echo html_spellcount($item['loads_count'], LANG_DOWNLOAD_SPELL); ?>
                <?php } ?>
            </div>
        </div>
        <div>
            <div class="update_wrap">
                <?php echo LANG_CP_LAST_UPDATE; ?>: <?php echo string_date_age_max($latest_version['date_released'], true); ?>
            </div>
            <div class="compatibility_wrap">
                <?php if($item['compatibility'] && in_array($core_version, $item['compatibility'])){ ?>
                    <?php echo LANG_CP_INSTALL_COMPATIBILITY_YES; ?>
                <?php } else { ?>
                    <?php echo LANG_CP_INSTALL_COMPATIBILITY_NO; ?>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="addon-card-bottom">
        <?php if($item['video']){ ?>
            <div class="button-video">
                <a href="#" data-id="<?php echo $item['video']; ?>">
                    <?php echo LANG_CP_PACKAGE_VIDEO; ?>
                </a>
            </div>
        <?php } ?>
        <?php if($item['demo_url']){ ?>
            <div class="button-demo">
                <a href="<?php html($item['demo_url']); ?>" target="_blank">
                    <?php echo LANG_CP_PACKAGE_DEMO; ?>
                </a>
            </div>
        <?php } ?>
        <?php if($item['install']['need_install'] || $item['install']['need_update']){ ?>
            <div class="button-install <?php if($item['install']['need_update']){ ?>need_update<?php } ?>">
                <?php if($item['price']){ ?>
                    <?php if ($item['price'] && $item['discount']) {
                        $price = (int)$item['price'] - round((((int)$item['price']*(int)$item['discount'])/100));
                    } else {
                        $price = (int)$item['price'];
                    }
                    ?>

                    <?php if($item['install']['need_install']){ ?>
                        <a href="<?php echo $item['page_url']; ?>#how-to-buy" target="_blank">
                            <?php echo sprintf(LANG_CP_PACKAGE_BUY, $price); ?> ₽
                        </a>
                    <?php } else { ?>
                        <a href="<?php echo $item['versions_url']; ?>" target="_blank">
                            <?php echo LANG_CP_PACKAGE_BUY_UPDATE; ?>
                        </a>
                    <?php } ?>
                <?php } else { ?>
                    <form action="<?php echo $this->href_to('install'); ?>" method="post" enctype="multipart/form-data">
                        <?php echo html_csrf_token(); ?>
                        <input type="hidden" name="addon_id" value="<?php echo $item['id']; ?>">
                        <input type="hidden" name="package" value="<?php echo $item['install']['install_url']; ?>">
                        <input type="submit" name="submit" value="1">
                    </form>
                    <a href="#" class="do_install">
                        <?php echo $item['install']['install_title']; ?>
                    </a>
                <?php } ?>
            </div>
        <?php } else {?>
            <div class="button-installed">
                <a href="<?php echo $item['install']['installed_url']; ?>" title="<?php echo sprintf(LANG_CP_PACKAGE_INSTALLED_HINT, $item['type']); ?>">
                    <?php echo LANG_CP_PACKAGE_INSTALLEDT; ?>
                </a>
            </div>
        <?php } ?>
    </div>
</div>
<?php } ?>
