<?php /*
<div id="lead-sponsor">
    <div class="hint">
        <?php echo LANG_CP_DASHBOARD_LEAD_SPONSOR; ?>
        <a href="http://www.instantcms.ru/sponsorship.html">?</a>
    </div>
    <a href="">
        <img src="<?php echo href_to(cmsTemplate::TEMPLATE_BASE_PATH.'default/images/'); ?>">
    </a>
</div>
*/ ?>
<div class="list-group list-group-accent">
    <a class="list-group-item list-group-item-accent-success list-group-item-success" href="https://instantcms.ru/donate.html" target="_blank"><?php echo LANG_CP_DASHBOARD_LINKS_DONATE; ?></a>
    <a class="list-group-item list-group-item-accent-primary list-group-item-primary" href="https://instantcms.ru/sponsorship.html" target="_blank"><?php echo LANG_CP_DASHBOARD_LINKS_SPONSORS; ?></a>
</div>
<div class="list-group">
    <div class="list-group-item list-group-item-action flex-column align-items-start active rounded-0">
        <?php echo LANG_CP_DASHBOARD_PREMIUM; ?>
    </div>
    <a href="https://instantcms.ru/blogs/instantsoft/biling-dlja-instantcms-2.html" class="list-group-item list-group-item-action flex-column align-items-start">
        <div class="d-flex w-100 justify-content-between">
            <h5 class="mb-1"><?php echo LANG_CP_DASHBOARD_BILLING; ?></h5>
        </div>
        <p class="mb-1"><?php echo LANG_CP_DASHBOARD_BILLING_HINT; ?></p>
    </a>
    <a href="https://instantvideo.ru/software/instantvideo2.html" class="list-group-item list-group-item-action flex-column align-items-start">
        <div class="d-flex w-100 justify-content-between">
            <h5 class="mb-1">InstantVideo</h5>
        </div>
        <p class="mb-1"><?php echo LANG_CP_DASHBOARD_INVIDEO_HINT; ?></p>
    </a>
    <a href="https://instantcms.ru/blogs/instantsoft/instantmaps-dlja-instantcms-2.html" class="list-group-item list-group-item-action flex-column align-items-start rounded-0">
        <div class="d-flex w-100 justify-content-between">
            <h5 class="mb-1">InstantMaps</h5>
        </div>
        <p class="mb-1"><?php echo LANG_CP_DASHBOARD_INMAPS_HINT; ?></p>
    </a>
</div>