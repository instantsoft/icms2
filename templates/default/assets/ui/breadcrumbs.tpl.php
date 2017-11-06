<?php $listed = array(); ?>
<ul>

    <li class="home">
        <a href="<?php echo $options['home_url']; ?>" title="<?php echo LANG_HOME; ?>"></a>
    </li>

    <?php if ($breadcrumbs) { ?>

        <li class="sep"></li>

        <?php foreach($breadcrumbs as $id=>$item){ ?>

            <?php if (in_array($item['href'], $listed)){ continue; } ?>

            <li <?php if (!isset($item['is_last'])){ ?>itemscope itemtype="http://data-vocabulary.org/Breadcrumb"<?php } ?>>
                <?php if (!isset($item['is_last'])){ ?>
                    <a href="<?php html($item['href']); ?>" itemprop="url"><span itemprop="title"><?php html($item['title']); ?></span></a>
                <?php } else { ?>
                    <span><?php html($item['title']); ?></span>
                <?php } ?>
            </li>

            <?php if ($id < sizeof($breadcrumbs)-1){ ?>
                <li class="sep"></li>
            <?php } ?>

            <?php $listed[] = $item['href']; ?>

        <?php } ?>

    <?php } ?>
</ul>