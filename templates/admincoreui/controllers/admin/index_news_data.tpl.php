<div class="list-group list-group-flush">
    <?php foreach ($items as $item) { ?>
    <a target="_blank" href="<?php echo $item['link']; ?>" class="list-group-item list-group-item-action flex-column align-items-start rounded-0">
        <div class="d-flex w-100 justify-content-between">
            <h5 class="mb-1"><?php echo html_strip($item['title'], 50); ?></h5>
            <?php if(!empty($item['pubDate'])){ ?>
                <small class="text-muted">
                    <?php echo html_date_time($item['pubDate']); ?>
                </small>
            <?php } elseif(!empty($item['dc:date'])) { ?>
                <small class="text-muted">
                    <?php echo html_date_time($item['dc:date']); ?>
                </small>
            <?php } ?>
        </div>
        <?php if(!empty($item['description'])){ ?>
            <div class="mb-1">
                <?php if(!empty($item['enclosure'])){ ?>
                    <img src="<?php echo $item['enclosure']; ?>" class="float-left">
                <?php } ?>
                <?php echo string_short($item['description'], 255); ?>
            </div>
        <?php } ?>
    </a>
    <?php } ?>
</div>