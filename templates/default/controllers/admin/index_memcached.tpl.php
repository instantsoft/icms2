<?php foreach ($stats as $server=>$record) {?>
    <ul class="cache_memcached">
        <li><span><?php echo LANG_CP_DASHBOARD_MEMCACHED_STATS_SERVER; ?>: </span> <?php echo $server;?></span></li>
        <li><span><?php echo LANG_CP_DASHBOARD_MEMCACHED_STATS_VERSION; ?>: </span><?php echo $record['version'];?></li>
        <li><span><?php echo LANG_CP_DASHBOARD_MEMCACHED_STATS_PID; ?>: </span><?php echo $record['pid'];?></li>
        <li><span><?php echo LANG_CP_DASHBOARD_MEMCACHED_STATS_CONNECTIONS_MAX; ?>: </span><?php echo $record['max_connections'];?></li>
        <li><span><?php echo LANG_CP_DASHBOARD_MEMCACHED_STATS_CONNECTIONS_CURRENT; ?>: </span><?php echo $record['curr_connections'];?></li>
        <li><span><?php echo LANG_CP_DASHBOARD_MEMCACHED_STATS_ITEMS_TOTAL; ?>: </span><?php echo $record['total_items'];?></li>
        <li><span><?php echo LANG_CP_DASHBOARD_MEMCACHED_STATS_ITEMS_CURRENT; ?>: </span><?php echo $record['curr_items'];?></li>
        <li><span><?php echo LANG_CP_DASHBOARD_MEMCACHED_STATS_COMMAND_GET; ?>: </span><?php echo $record['cmd_get'];?></li>
        <li><span><?php echo LANG_CP_DASHBOARD_MEMCACHED_STATS_COMMAND_SET; ?>: </span><?php echo $record['cmd_set'];?></li>
    </ul>
<?php } ?>
