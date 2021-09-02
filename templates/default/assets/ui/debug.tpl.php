<?php
$points_data = cmsDebugging::getPointsData();
$points_tab = cmsDebugging::getPointsTargets();
?>
<div id="debug_widget" class="tabs-menu">

    <ul class="tabbed">
        <?php foreach($points_tab as $tab_name => $tab) { ?>
            <li><a href="#tab-<?php echo $tab_name; ?>"><?php echo $tab['title']; ?> <?php echo $tab['count'] ? '('.$tab['count'].')' : ''; ?></a></li>
        <?php } ?>
    </ul>

    <?php foreach($points_data as $tab_name => $data) { ?>
        <div id="tab-<?php echo $tab_name; ?>" class="tab">
            <div class="queries_wrap">
                <?php foreach($data as $query) { ?>
                    <div class="query">
                        <div class="src"><?php echo $query['src']; ?></div>
                        <?php if($query['data']){ ?>
                            <div class="debug_data">
                                <?php echo isset($query['data_callback']) ? $query['data_callback']($query['data']) : nl2br(htmlspecialchars($query['data'])); ?>
                            </div>
                        <?php } ?>
                        <?php if($query['time']){ ?>
                            <div class="query_time">
                                <?php echo LANG_DEBUG_QUERY_TIME; ?>
                                <span class="<?php echo (($query['time']>=0.1) ? 'red_query' : 'green_query'); ?>">
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
<script>
    $(function (){
        initTabs('#debug_widget');
    });
</script>