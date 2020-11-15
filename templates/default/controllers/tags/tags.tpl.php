<?php
    $this->setPageTitle(LANG_TAGS);
    $this->addBreadcrumb(LANG_TAGS);
?>
<h1><?php echo LANG_TAGS; ?></h1>
<?php if ($tags){ ?>
    <div class="widget_tags_cloud">
        <ul class="tags_as_<?php echo $style; ?>">
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
                        if ($max_freq == $min_freq) {
                            $fs = round(($min_fs + $max_fs) / 2);
                        } else  {
                            $step = (($max_fs - $min_fs) * ($tag['frequency'] - $min_freq)) / ($max_freq - $min_freq);
                            $fs = round($min_fs + $step);
                        }
                    ?>
                    <li <?php if($color){ echo 'class="colored"'; } ?> style="font-size: <?php echo $fs; ?>px;<?php if($color){ echo ' color: '.$color; } ?>">
                        <?php echo html_tags_bar($tag['tag']); ?>
                    </li>
                <?php } ?>

                <?php if ($style=='list'){ ?>
                    <li <?php if($color){ echo 'class="colored" style="color: '.$color.'"'; } ?>>
                        <?php echo html_tags_bar($tag['tag']); ?>
                        <span class="counter"><?php html($tag['frequency']); ?></span>
                    </li>
                <?php } ?>
            <?php } ?>
        </ul>
    </div>
<?php } ?>