<?php $listed = []; $count = count($breadcrumbs); ?>
<nav id="breadcrumb">
    <ol class="breadcrumb text-truncate flex-nowrap position-relative flex-fill">
        <li class="breadcrumb-item">
            <?php if (!$breadcrumbs) { ?>
                <span class="text-muted">
                    <?php html_svg_icon('solid', 'tachometer-alt'); ?>
                    <?php echo LANG_ADMIN_CONTROLLER; ?>
                </span>
            <?php } else { ?>
                <a href="<?php echo $options['home_url']; ?>" title="<?php echo LANG_HOME; ?>">
                    <?php html_svg_icon('solid', 'home'); ?>
                </a>
            <?php } ?>
        </li>
        <?php if ($breadcrumbs) { ?>

            <?php foreach($breadcrumbs as $id => $item){ ?>

                <?php if (in_array($item['href'], $listed)){ continue; } ?>

                <li class="breadcrumb-item<?php if (isset($item['is_last'])){ ?> active<?php if($count > 3) { ?> d-none d-lg-inline-block<?php } ?><?php } ?>">
                    <?php if (!isset($item['is_last'])){ ?>
                        <a href="<?php html($item['href']); ?>">
                            <span><?php html($item['title']); ?></span>
                        </a>
                    <?php } else { ?>
                        <span><?php html($item['title']); ?></span>
                    <?php } ?>
                </li>

                <?php $listed[] = $item['href']; ?>

            <?php } ?>

        <?php } ?>
        <?php if($this->hasMenu('breadcrumb-menu')){ ?>
            <li class="breadcrumb-menu d-sm-down-none">
                <div class="btn-group" role="group">
                    <?php foreach($this->menus['breadcrumb-menu'] as $item){ ?>
                        <a <?php if (isset($item['options']['title'])) { ?>title="<?php html($item['options']['title']); ?>"<?php } ?> <?php if (isset($item['options']['target'])) { ?>target="<?php echo $item['options']['target']; ?>"<?php } ?> class="btn<?php if (!empty($item['options']['class'])) { ?> <?php echo $item['options']['class']; ?><?php } ?>" href="<?php html($item['url']); ?>">
                            <?php if (!empty($item['options']['icon'])) {
                                $icon_params = explode(':', $item['options']['icon']);
                                if(!isset($icon_params[1])){ array_unshift($icon_params, 'solid'); }
                                html_svg_icon($icon_params[0], $icon_params[1]);
                            } ?>
                            <?php html($item['title']); ?>
                        </a>
                    <?php } ?>
                </div>
            </li>
        <?php } ?>
    </ol>
</nav>
