<?php

    $this->addTplJSName([
        'admin-users', 'datatree'
    ]);

    $this->addTplCSSName('datatree');

    $this->setPageTitle(LANG_CP_SECTION_USERS);

    $this->addBreadcrumb(LANG_CP_SECTION_USERS, $this->href_to('users'));

    if (cmsController::enabled('messages')) {
        $this->addMenuItem('breadcrumb-menu', [
            'title'   => LANG_CP_USER_PMAILING,
            'url'     => $this->href_to('controllers', ['edit', 'messages', 'pmailing']),
            'options' => [
                'icon' => 'envelope-open-text'
            ]
        ]);
    }

    $this->addMenuItem('breadcrumb-menu', [
        'title'   => LANG_CONFIG,
        'url'     => $this->href_to('controllers', ['edit', 'users']),
        'options' => [
            'icon' => 'cog'
        ]
    ]);

    $this->addMenuItem('breadcrumb-menu', [
        'title'   => LANG_HELP,
        'url'     => LANG_HELP_URL_USERS,
        'options' => [
            'target' => '_blank',
            'icon'   => 'question-circle'
        ]
    ]);
?>
<div class="row flex-nowrap align-items-stretch mb-4">
    <div class="col-auto quickview-wrapper" id="left-quickview">
        <a class="quickview-toggle close" data-toggle="quickview" data-toggle-element="#left-quickview" href="#"><span>×</span></a>
        <div id="datatree" class="bg-white h-100 pt-3 pb-3 pr-3" data-base_url="<?php echo $this->href_to('users'); ?>">
            <ul id="treeData" class="skeleton-tree">
                <?php foreach ($groups as $id => $group) { ?>
                    <li id="<?php echo $group['id'];?>" class="folder">
                        <?php echo $group['title']; ?>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="col">
        <?php echo $grid_html; ?>
    </div>
</div>