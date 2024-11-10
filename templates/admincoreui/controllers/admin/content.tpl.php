<?php
    $this->addTplJSName([
        'datatree',
        'admin-content'
    ]);
    $this->addTplCSSName('datatree');

    $this->setPageTitle(LANG_CP_SECTION_CONTENT);

    $this->addBreadcrumb(LANG_CP_SECTION_CONTENT, $this->href_to('content'));
    // туда будет подставляться активный пункт дерева
    $this->addBreadcrumb('', $this->href_to('content').'?last');

    $this->addMenuItem('breadcrumb-menu', [
        'title' => LANG_HELP,
        'url'   => LANG_HELP_URL_CONTENT,
        'options' => [
            'target' => '_blank',
            'icon' => 'question-circle'
        ]
    ]);
?>

<div class="row flex-nowrap align-items-stretch mb-4">
    <div class="col-sm col-xl-3 col-xxl-2 quickview-wrapper" id="left-quickview">
        <a class="quickview-toggle close" data-toggle="quickview" data-toggle-element="#left-quickview" href="#"><span>×</span></a>
        <div id="datatree" class="card-body bg-white h-100 pt-3" data-content_url="<?php echo $this->href_to('content'); ?>" data-ctype_id="<?php echo $ctype['id']; ?>" data-key_path="<?php html($key_path); ?>" data-ctype_edit="<?php echo $this->href_to('ctypes', ['edit']); ?>" data-moderation_url="<?php echo $this->href_to('controllers', ['edit', 'moderation', 'logs', 'content']); ?>">
            <ul id="treeData" class="skeleton-tree">
                <?php foreach ($ctypes as $id => $_ctype) { ?>
                    <li id="<?php echo $_ctype['id']; ?>.1" class="lazy folder">
                        <?php echo $_ctype['title']; ?>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="col-sm-12 col-xl-9 col-xxl-10">
        <?php echo $grid_html ?>
    </div>
</div>