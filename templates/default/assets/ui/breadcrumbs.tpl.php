<?php $listed = []; $position = 2; ?>
<ul itemscope itemtype="https://schema.org/BreadcrumbList">

    <li class="home" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
        <a href="<?php echo $options['home_url']; ?>" title="<?php echo LANG_HOME; ?>" itemprop="item">
            <meta itemprop="name" content="<?php echo LANG_HOME; ?>" />
            <meta itemprop="position" content="1" />
        </a>
    </li>

    <?php if ($breadcrumbs) { ?>

        <li class="sep"></li>

        <?php foreach($breadcrumbs as $id=>$item){ ?>

            <?php if (in_array($item['href'], $listed)){ continue; } ?>

            <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <?php if (!isset($item['is_last'])){ ?>
                    <a href="<?php html($item['href']); ?>" itemprop="item">
                        <span itemprop="name">
                            <?php html($item['title']); ?>
                        </span>
                    </a>
                <?php } else { ?>
                    <span itemprop="name">
                        <?php html($item['title']); ?>
                    </span>
                <?php } ?>
                <meta itemprop="position" content="<?php echo $position++; ?>" />
            </li>

            <?php if ($id < count($breadcrumbs)-1){ ?>
                <li class="sep"></li>
            <?php } ?>

            <?php $listed[] = $item['href']; ?>

        <?php } ?>

    <?php } ?>
</ul>