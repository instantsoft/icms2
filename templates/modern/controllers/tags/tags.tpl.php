<?php
    $this->setPageTitle(LANG_TAGS);
    $this->addBreadcrumb(LANG_TAGS);
?>
<h1><?php echo LANG_TAGS; ?></h1>
<?php if ($tags){ ?>
    <div class="widget_tags_cloud">
        <ul class="<?php if ($style=='cloud'){ ?>list-unstyled m-0<?php } else { ?>list-group list-group-flush<?php } ?> tags_as_<?php echo $style; ?>">
            <?php foreach($tags as $tag) { ?>
                <?php
                    $color = false;
                    if($colors){
                        $color = current($colors);
                        if(!next($colors)){reset($colors);}
                    }
                ?>
                <?php if ($style=='cloud'){ ?>
                    <?php
                        $size_percent = round(($tag['frequency'] * 100) / $max_freq);
                        $step = 0;
                        if($size_percent){
                            $portion = round(100 / $size_percent, 2);
                            $step = round(($max_fs - $min_fs) / $portion);
                        }
                        $fs = $min_fs + $step;
                    ?>
                    <li class="d-inline-block mr-2 mb-2">
                        <a class="btn btn-outline-secondary icms-btn-tag <?php if($color){ echo 'colored'; } ?>" style="font-size: <?php echo round($fs/12, 3); ?>rem;<?php if($color){ echo ' color: '.$color; } ?>" href="<?php echo href_to('tags').'/'.urlencode($tag['tag']); ?>">
                            <?php html($tag['tag']); ?>
                        </a>
                    </li>
                <?php } ?>

                <?php if ($style=='list'){ ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <a class="icms-btn-tag text-decoration-none" <?php if($color){ echo 'style="color: '.$color.'"'; } ?> href="<?php echo href_to('tags').'/'.urlencode($tag['tag']); ?>">
                            <?php html($tag['tag']); ?>
                        </a>
                        <span class="badge badge-primary badge-pill"><?php html($tag['frequency']); ?></span>
                    </li>
                <?php } ?>
            <?php } ?>
        </ul>
    </div>
<?php } ?>