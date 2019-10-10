<?php $listed = array(); ?>
<nav id="breadcrumb" aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="<?php echo $options['home_url']; ?>" title="<?php echo LANG_HOME; ?>"><i class="icon-home icons"></i></a>
        </li>
        <?php if ($breadcrumbs) { ?>

            <?php foreach($breadcrumbs as $id=>$item){ ?>

                <?php if (in_array($item['href'], $listed)){ continue; } ?>

                <li class="breadcrumb-item<?php if (isset($item['is_last'])){ ?> active<?php } ?>">
                    <?php if (!isset($item['is_last'])){ ?>
                        <a href="<?php html($item['href']); ?>" itemprop="url"><span itemprop="title"><?php html($item['title']); ?></span></a>
                    <?php } else { ?>
                        <?php html($item['title']); ?>
                    <?php } ?>
                </li>

                <?php $listed[] = $item['href']; ?>

            <?php } ?>

        <?php } ?>
    </ol>
</nav>