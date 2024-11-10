<?php

    $this->addTplJSName([
        'datatree', 'admin-menu'
    ]);

    $this->addTplCSSName('datatree');

    $this->setPageTitle(LANG_CP_SECTION_MENU);

    $this->addBreadcrumb(LANG_CP_SECTION_MENU, $this->href_to('menu'));
    // туда будет подставляться активный пункт дерева
    $this->addBreadcrumb('', $this->href_to('menu').'?last');

    $this->addMenuItem('breadcrumb-menu', [
        'title' => LANG_HELP,
        'url'   => LANG_HELP_URL_MENU,
        'options' => [
            'target' => '_blank',
            'icon' => 'question-circle'
        ]
    ]);
?>

<div class="row align-items-stretch mb-4">
    <div class="col-auto quickview-wrapper" id="left-quickview">
        <a class="quickview-toggle close" data-toggle="quickview" data-toggle-element="#left-quickview" href="#"><span>×</span></a>
        <div id="datatree" class="card-body bg-white h-100 pt-3" data-base_url="<?php echo $this->href_to('menu'); ?>">
            <ul id="treeData" class="skeleton-tree">
                <?php foreach ($menus as $id => $menu) { ?>
                    <li id="<?php echo $menu['id'];?>.0" class="lazy folder">
                        <?php html($menu['title']); ?>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="col">
        <?php echo $grid_html; ?>
    </div>
</div>