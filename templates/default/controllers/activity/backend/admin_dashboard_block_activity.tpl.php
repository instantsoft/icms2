<?php $this->addTplJSName('jquery.scrollbar'); ?>
<div id="activity_wrap">
    <div class="activity_wrap scrollbar-macosx">
        <?php if ($items){ ?>
            <ul class="links adb_list">
                <?php foreach ($items as $item) { ?>
                    <li>
                        <a href="<?php echo href_to_profile($item['user']); ?>">
                            <?php html($item['user']['nickname']); ?>
                        </a>
                        <div>
                            <?php if (!empty($item['images'][0]['src'])) { ?>
                                <div class="image">
                                    <img src="<?php echo $item['images'][0]['src']; ?>">
                                </div>
                            <?php } ?>
                            <?php echo $item['description']; ?>
                        </div>
                        <div class="date<?php if(!empty($item['is_new'])){ ?> highlight_new<?php } ?>">
                            <?php echo $item['date_diff']; ?>
                        </div>
                    </li>
                <?php } ?>
            </ul>
        <?php } else { echo LANG_LIST_EMPTY; } ?>
    </div>
</div>
<script>
    $(function() {
        $('#activity_wrap .activity_wrap').scrollbar();
    });
</script>
