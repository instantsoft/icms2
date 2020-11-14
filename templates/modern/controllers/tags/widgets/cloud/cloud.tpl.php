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
                        // WebMan: Более красивый подбор размеров шрифта
                        if ($max_freq == $min_freq) {
                            // Если мин. и макс. частоты одинаковы, то выбираем средний размер шрифта
                            $fs = round(($min_fs + $max_fs) / 2);
                        } else  {
                            // В противном случае, вычисляем размер шрифта
                            $step = (($max_fs - $min_fs) * ($tag['frequency'] - $min_freq)) / ($max_freq - $min_freq);
                            $fs = round($min_fs + $step);
                        }
                    ?>
                    <li class="d-inline-block">
                        <a class="btn btn-outline-light mr-1 my-1 icms-btn-tag" style="font-size: <?php echo round($fs/14, 3); ?>rem;<?php if($color){ echo ' color: '.$color; } ?>" href="<?php echo href_to('tags').'/'.urlencode($tag['tag']); ?>">
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
