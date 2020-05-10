<?php
$points_data = cmsDebugging::getPointsData();
$points_tab = cmsDebugging::getPointsTargets();
$active_tab = false;
?>
<?php if(empty($hide_short_info)){ ?>
<div class="container">
    <span class="item">
        <?php echo LANG_POWERED_BY_INSTANTCMS; ?>
    </span>
    <span class="item">
        <a href="#debug_block" title="<?php echo LANG_DEBUG; ?>" class="ajax-modal"><?php echo LANG_DEBUG; ?></a>
    </span>
    <span class="item">
        Time: <?php echo cmsDebugging::getTime('cms', 4); ?> s
    </span>
    <span class="item">
        Mem: <?php echo round(memory_get_usage(true)/1024/1024, 2); ?> Mb
    </span>
</div>
<?php } ?>
<div id="debug_block" class="d-none">
    <ul class="nav nav-tabs" role="tablist">
        <?php foreach($points_tab as $tab_name => $tab) { ?>
            <li class="nav-item">
                <a class="nav-link<?php if(!$active_tab){ $active_tab = $tab_name; ?> active<?php } ?>" href="#tab-<?php echo $tab_name; ?>" data-toggle="tab" role="tab">
                    <?php echo $tab['title']; ?> <?php echo $tab['count'] ? '<span class="badge badge-pill badge-light">'.$tab['count'].'</span>' : ''; ?>
                </a>
            </li>
        <?php } ?>
    </ul>
    <div class="tab-content">
    <?php foreach($points_data as $tab_name => $data) { ?>
        <div id="tab-<?php echo $tab_name; ?>" class="tab-pane<?php if($active_tab == $tab_name){ ?> active<?php } ?>" role="tabpanel">
            <div class="queries_wrap">
                <?php foreach($data as $query) { ?>
                    <div class="query py-3 border-bottom">
                        <div class="src text-muted small">
                            <?php echo $query['src']; ?>
                        </div>
                        <?php if($query['data']){ ?>
                            <div class="debug_data mt-2 p-2 bg-light">
                                <?php echo isset($query['data_callback']) ? $query['data_callback']($query['data']) : nl2br(htmlspecialchars($query['data'])); ?>
                            </div>
                        <?php } ?>
                        <?php if($query['time']){ ?>
                            <div class="query_time text-muted small mt-2">
                                <?php echo LANG_DEBUG_QUERY_TIME; ?>
                                <span class="<?php echo (($query['time']>=0.1) ? 'text-danger' : 'text-success'); ?>">
                                    <?php echo $query['time']; ?>
                                </span>
                                <?php echo LANG_SECOND10 ?>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    <?php } ?>
    </div>
</div>