<?php $listed = []; $position = 2; $count = count($breadcrumbs); ?>
<ol class="breadcrumb mb-0 text-truncate flex-nowrap position-relative flex-fill" itemscope itemtype="https://schema.org/BreadcrumbList">
    <li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
        <a href="<?php echo $options['home_url']; ?>" title="<?php echo LANG_HOME; ?>" itemprop="item">
            <?php html_svg_icon('solid', 'home'); ?>
            <meta itemprop="name" content="<?php echo LANG_HOME; ?>" />
            <meta itemprop="position" content="1" />
        </a>
    </li>
    <?php if ($breadcrumbs) { ?>
        <?php foreach($breadcrumbs as $id => $item){ ?>
            <?php if (in_array($item['href'], $listed)){ continue; } ?>
            <li class="breadcrumb-item<?php if (isset($item['is_last'])){ ?> active <?php if($count > 2) { ?>d-none d-lg-inline-block<?php } ?><?php } ?>" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
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
            <?php $listed[] = $item['href']; ?>
        <?php } ?>
    <?php } ?>
</ol>