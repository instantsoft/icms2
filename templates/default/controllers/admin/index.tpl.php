<?php
    $this->setPageTitle(LANG_ADMIN_CONTROLLER);
    $this->addJS('templates/default/js/admin-chart.js');
    $this->addJS('templates/default/js/admin-dashboard.js');
    $this->addJS('templates/default/js/jquery-cookie.js');
    $this->addJS('templates/default/js/jquery-ui.js');
    $this->addCSS('templates/default/css/jquery-ui.css');
?>
<h1><?php echo LANG_ADMIN_CONTROLLER; ?></h1>

<div id="dashboard">
    <div class="row">
        <div id="chart" class="col" data-url="<?php echo $this->href_to('index_chart_data'); ?>" data-period="<?php html($defaults['period']); ?>">

            <h3><?php echo LANG_CP_DASHBOARD_STATS; ?></h3>

            <div class="col-body">

                <div class="toolbar">
                    <select>
                        <?php foreach($chart_nav as $section){ ?>
                            <optgroup label="<?php echo $section['title']; ?>">
                                <?php foreach($section['sections'] as $id => $link) { ?>
                                    <?php $is_active = $defaults['controller']==$section['id'] && $defaults['section']==$id; ?>
                                    <option data-ctrl="<?php echo $section['id']; ?>" data-section="<?php echo $id; ?>" <?php if ($is_active) { ?>selected="selected"<?php } ?>>
                                        <?php echo $link['title']; ?>
                                    </option>
                                <?php } ?>
                            </optgroup>
                        <?php } ?>
                    </select>
                    <div class="pills-menu">
                        <ul class="menu">
                            <li <?php if ($defaults['period'] == 7) { ?>class="active"<?php } ?>>
                                <a class="item" href="#" data-period="7"><?php echo LANG_WEEK; ?></a>
                            </li>
                            <li <?php if ($defaults['period'] == 30) { ?>class="active"<?php } ?>>
                                <a class="item" href="#" data-period="30"><?php echo LANG_MONTH; ?></a>
                            </li>
                            <li <?php if ($defaults['period'] == 365) { ?>class="active"<?php } ?>>
                                <a class="item" href="#" data-period="365"><?php echo LANG_YEAR; ?></a>
                            </li>
                        </ul>
                    </div>
                </div>

                <canvas id="chart-canvas"></canvas>

            </div>

        </div>
        <div class="col col-sm">
            <h3><?php echo LANG_CP_DASHBOARD_SYSINFO; ?></h3>
            <div class="col-body">
                <ul id="sysinfo">
                    <?php foreach ($sysinfo as $feature => $value) { ?>
                        <?php if (is_bool($value)) { $value = $value ? LANG_YES : LANG_NO; } ?>
                        <li>
                            <span><?php echo $feature; ?></span> <?php echo $value; ?>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
        <div id="resources" class="col col-sm">
            <h3><?php echo LANG_CP_DASHBOARD_RESOURCES; ?></h3>
            <div class="col-body">

                <div id="lead-sponsor">
                    <div class="hint">
                        <?php echo LANG_CP_DASHBOARD_LEAD_SPONSOR; ?>
                        <a href="http://www.instantcms.ru/sponsorship.html">?</a>
                    </div>
                    <a href="http://web-studio.pro/%D0%B7%D0%B0%D0%BA%D0%B0%D0%B7">
                        <img src="<?php echo href_to('templates/default/images/wsp.png'); ?>">
                    </a>
                </div>

                <ul class="links">
                    <li><a href="http://instantcms.ru/"><?php echo LANG_CP_DASHBOARD_LINKS_SITE; ?></a></li>
                    <li><a href="http://docs.instantcms.ru/"><?php echo LANG_CP_DASHBOARD_LINKS_DOCS; ?></a></li>
                    <li><a href="http://addons.instantcms.ru/"><?php echo LANG_CP_DASHBOARD_LINKS_ADDONS; ?></a></li>
                    <li><a href="http://instantcms.ru/forum/"><?php echo LANG_CP_DASHBOARD_LINKS_FORUMS; ?></a></li>
                </ul>

                <ul class="links">
                    <li><a href="http://instantcms.ru/donate.html"><?php echo LANG_CP_DASHBOARD_LINKS_DONATE; ?></a></li>
                    <li><a href="http://instantcms.ru/sponsorship.html"><?php echo LANG_CP_DASHBOARD_LINKS_SPONSORS; ?></a></li>
                </ul>

                <div class="premium"><?php echo LANG_CP_DASHBOARD_PREMIUM; ?></div>

                <ul class="links premium_list">
                    <li><a class="tooltip" title="<?php echo LANG_CP_DASHBOARD_BILLING_HINT; ?>" href="http://www.instantcms.ru/blogs/InstantSoft/biling-dlja-instantcms-2.html"><?php echo LANG_CP_DASHBOARD_BILLING; ?></a></li>
                    <li><a class="tooltip" title="<?php echo LANG_CP_DASHBOARD_INVIDEO_HINT; ?>" href="https://instantvideo.ru/software/instantvideo2.html">InstantVideo</a></li>
                    <li><a class="tooltip" title="<?php echo LANG_CP_DASHBOARD_INMAPS_HINT; ?>" href="http://www.instantcms.ru/blogs/InstantSoft/instantmaps-dlja-instantcms-2.html">InstantMaps</a></li>
                </ul>
            </div>
        </div>
    </div>
    <?php if($dashboard_blocks) { ?>
        <?php foreach ($dashboard_blocks as $key => $dashboard_block) { ?>
        <div class="row">
            <div class="col col-sm" id="db_<?php echo $key; ?>">
                <h3><?php echo $dashboard_block['title']; ?></h3>
                <div class="col-body"><?php echo $dashboard_block['html']; ?></div>
            </div>
        </div>
        <?php } ?>
    <?php } ?>
</div>
<script>
    $(function() {
        $(document).tooltip({
            items: '.tooltip',
            show: { duration: 0 },
            hide: { duration: 0 },
            position: {
                my: "center",
                at: "top-20"
            }
        });
    });
</script>