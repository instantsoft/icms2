<ul class="links adb_list">
    <?php foreach ($items as $item) { ?>
        <li>
            <a target="_blank" href="<?php echo $item['link']; ?>"><?php echo html_strip($item['title'], 50); ?></a>
            <?php if(!empty($item['description'])){ ?>
                <div>
                    <?php if(!empty($item['enclosure'])){ ?>
                        <div class="image">
                            <img src="<?php echo $item['enclosure']; ?>">
                        </div>
                    <?php } ?>
                    <?php echo string_short($item['description'], 255); ?>
                </div>
            <?php } ?>
            <?php if(!empty($item['pubDate'])){ ?>
                <div class="date">
                    <?php echo html_date_time($item['pubDate']); ?>
                </div>
            <?php } elseif(!empty($item['dc:date'])) { ?>
                <div class="date">
                    <?php echo html_date_time($item['dc:date']); ?>
                </div>
            <?php } ?>
        </li>
    <?php } ?>
</ul>