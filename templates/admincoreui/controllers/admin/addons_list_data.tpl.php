<script>
    has_next = <?php echo $has_next; ?>;
    addons_count = <?php echo $count; ?>;
</script>
<?php if(empty($items)){ ?>
    <p class="alert alert-info mt-4"><?php echo LANG_CP_NO_ADDONS; ?></p>
<?php return; } ?>
<div class="row align-items-stretch">
<?php foreach ($items as $item) { ?>
<?php
    $latest_version = reset($item['versions']);
?>
    <div class="col-xl-6 col-xxl-4 mb-4 addon-card-<?php echo $item['slug']; ?>">
        <div class="card h-100 mb-0">
            <div class="card-header py-2 px-3 row align-items-center mx-0">
                <div class="col-sm-8 p-0">
                    <?php echo $item['type']; ?> <?php echo LANG_FROM; ?>
                    <a target="_blank" href="<?php echo $item['user']['page_url']; ?>">
                        <?php echo $item['user']['nickname']; ?>
                    </a>
                </div>
                <div class="col-sm-4 p-0 rating_stars d-flex align-items-center justify-content-sm-end" title="<?php echo LANG_RATING; ?>: <?php echo round($item['score'], 2); ?>">
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
            </div>
            <div class="card-body p-3 d-flex flex-column h-100">
                <div>
                    <img class="float-left mr-3" src="<?php echo $item['photo']['cover80']; ?>">
                    <h5 class="card-title">
                        <a target="_blank" href="<?php echo $item['page_url']; ?>" class="open-package-details">
                            <?php echo $item['title']; ?>
                        </a>
                    </h5>
                    <p class="card-text clearfix">
                        <a href="#content_<?php echo $item['slug']; ?>" class="ajax-modal full_content_link text-muted" title="<?php html($item['title']); ?>">
                            <?php echo string_short($item['content'], 140, ' ...', 'w'); ?>
                        </a>
                    </p>
                    <div class="d-none">
                        <div class="full_content" id="content_<?php echo $item['slug']; ?>">
                            <?php echo $item['content']; ?>
                        </div>
                    </div>
                </div>
                <p class="card-text card-summary text-muted small mt-auto">
                    <span>
                        <?php if($item['price']){ ?>
                            <?php echo html_spellcount($item['followers_count'], LANG_SUBSCRIBERS_SPELL); ?>
                        <?php } else { ?>
                            <?php echo html_spellcount($item['loads_count'], LANG_DOWNLOAD_SPELL); ?>
                        <?php } ?>
                    </span>
                    <span>
                        <?php echo LANG_CP_LAST_UPDATE; ?>: <?php echo string_date_age_max($latest_version['date_released'], true); ?>
                    </span>
                    <span>
                        <?php if($item['compatibility'] && in_array($core_version, $item['compatibility'])){ ?>
                            <?php echo LANG_CP_INSTALL_COMPATIBILITY_YES; ?>
                        <?php } else { ?>
                            <?php echo LANG_CP_INSTALL_COMPATIBILITY_NO; ?>
                        <?php } ?>
                    </span>
                </p>
            </div>
            <div class="card-footer p-2 text-muted">
                <?php if($item['install']['need_install'] || $item['install']['need_update']){ ?>
                    <?php $btn_class = $item['install']['need_update'] ? 'warning' : 'primary'; ?>
                    <?php if($item['price']){ ?>
                        <?php if ($item['price'] && $item['discount']) {
                            $price = (int)$item['price'] - round((((int)$item['price']*(int)$item['discount'])/100));
                        } else {
                            $price = (int)$item['price'];
                        }
                        ?>
                        <?php if($item['install']['need_install']){ ?>
                            <a class="btn btn-<?php echo $btn_class; ?>" href="<?php echo $item['page_url']; ?>#how-to-buy" target="_blank">
                                <?php echo sprintf(LANG_CP_PACKAGE_BUY, $price); ?> ₽
                            </a>
                        <?php } else { ?>
                            <a class="btn btn-<?php echo $btn_class; ?>" href="<?php echo $item['versions_url']; ?>" target="_blank">
                                <?php echo LANG_CP_PACKAGE_BUY_UPDATE; ?>
                            </a>
                        <?php } ?>
                    <?php } else { ?>
                        <form class="d-none" action="<?php echo $this->href_to('install'); ?>" method="post" enctype="multipart/form-data">
                            <?php echo html_csrf_token(); ?>
                            <input type="hidden" name="addon_id" value="<?php echo $item['id']; ?>">
                            <input type="hidden" name="package" value="<?php echo $item['install']['install_url']; ?>">
                            <input type="submit" name="submit" value="1">
                        </form>
                        <a href="#" class="do_install btn btn-<?php echo $btn_class; ?>">
                            <?php echo $item['install']['install_title']; ?>
                        </a>
                    <?php } ?>
                <?php } else {?>
                    <a class="do_install btn btn-success" href="<?php echo $item['install']['installed_url']; ?>" title="<?php echo sprintf(LANG_CP_PACKAGE_INSTALLED_HINT, $item['type']); ?>">
                        <?php echo LANG_CP_PACKAGE_INSTALLEDT; ?>
                    </a>
                <?php } ?>
                <?php if($item['video']){ ?>
                    <a class="button-video btn btn-brand btn-dropbox ml-2" href="#" data-id="<?php echo $item['video']; ?>">
                        <span><?php echo LANG_CP_PACKAGE_VIDEO; ?></span>
                    </a>
                <?php } ?>
                <?php if($item['demo_url']){ ?>
                    <a class="btn btn-brand btn-dropbox ml-2" href="<?php html($item['demo_url']); ?>" target="_blank">
                        <span><?php echo LANG_CP_PACKAGE_DEMO; ?></span>
                    </a>
                <?php } ?>
            </div>
        </div>
    </div>
<?php } ?>
</div>
