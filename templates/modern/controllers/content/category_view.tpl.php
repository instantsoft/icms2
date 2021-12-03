<?php
if (!$is_frontpage){
    if (cmsUser::isAllowed($ctype['name'], 'add')) {

        if (!$category['id'] || $user->isInGroups($category['allow_add'])){

            $href = href_to($ctype['name'], 'add', isset($category['path']) ? $category['id'] : '');

            $this->addToolButton(array(
                'icon' => 'plus-circle',
                'title' => sprintf(LANG_CONTENT_ADD_ITEM, $ctype['labels']['create']),
                'href'  => $href
            ));

        }

    }

    if ($ctype['is_cats']){
        if (cmsUser::isAllowed($ctype['name'], 'add_cat')) {
            $this->addToolButton(array(
                'icon' => 'folder-plus',
                'title' => LANG_ADD_CATEGORY,
                'href'  => href_to($ctype['name'], 'addcat', $category['id'])
            ));
        }

        if ($category['id']){

            if (cmsUser::isAllowed($ctype['name'], 'edit_cat')) {
                $this->addToolButton(array(
                    'icon'  => 'edit',
                    'title' => LANG_EDIT_CATEGORY,
                    'href'  => href_to($ctype['name'], 'editcat', $category['id'])
                ));
            }
            if (cmsUser::isAllowed($ctype['name'], 'delete_cat')) {
                $this->addToolButton(array(
                    'icon' => 'folder-minus',
                    'title' => LANG_DELETE_CATEGORY,
                    'href'  => href_to($ctype['name'], 'delcat', $category['id']),
                    'onclick' => "if(!confirm('".LANG_DELETE_CATEGORY_CONFIRM."')){ return false; }"
                ));
            }

        }
    }

    if (cmsUser::isAdmin()){
        $this->addToolButton(array(
            'icon' => 'wrench',
            'title' => sprintf(LANG_CONTENT_TYPE_SETTINGS, mb_strtolower($ctype['title'])),
            'href'  => href_to('admin', 'ctypes', array('edit', $ctype['id']))
        ));
    }
}

?>
<?php ob_start(); ?>
    <?php if ($show_h1){  ?>
        <h1>
            <?php $this->pageH1(); ?>
            <?php if (!empty($ctype['options']['is_rss']) && $this->controller->isControllerEnabled('rss')){ ?>
                <sup>
                    <a class="inline_rss_icon d-none d-lg-inline-block" title="RSS" href="<?php echo href_to('rss', 'feed', $ctype['name']) . $rss_query; ?>">
                        <?php html_svg_icon('solid', 'rss'); ?>
                    </a>
                </sup>
            <?php } ?>
        </h1>
    <?php } ?>
    <?php if (!empty($list_styles)){ ?>
        <?php $list_icons_mapping = ['' => 'list', 'featured' => 'newspaper', 'table' => 'table', 'tiles' => 'th']; ?>
        <div class="icms-content-list__styles_btn">
            <?php foreach ($list_styles as $list_style) { ?>
            <a data-toggle="tooltip" data-placement="top" rel="nofollow" href="<?php echo $list_style['url']; ?>" class="btn btn-light btn-responsive icms-content-list__<?php echo $list_style['class']; ?>" title="<?php html($list_style['title']); ?>">
                <?php html_svg_icon('solid', $list_icons_mapping[$list_style['style']]); ?>
            </a>
            <?php } ?>
        </div>
    <?php } ?>
<?php $this->addToBlock('before_body', ob_get_clean(), true); ?>

<?php if ($datasets && !$is_hide_items){
    $this->renderAsset('ui/datasets-panel', [
        'datasets'        => $datasets,
        'dataset_name'    => $dataset,
        'current_dataset' => $current_dataset,
        'wrap_class'      => ($show_h1 ? 'my-3 my-md-4' : 'mb-3 mb-md-4').' content_datasets_'.$ctype['name'],
        'ds_prefix'       => '-',
        'base_ds_url'     => rel_to_href($base_ds_url)
    ]);
} ?>

<?php if (!empty($category['description'])){?>
    <div class="category_description"><?php echo $category['description']; ?></div>
<?php } ?>

<?php if ($subcats){ ?>
    <?php if($ctype['options']['cover_preset']){ ?>
        <div class="row mb-n3">
            <?php foreach($subcats as $c){ ?>
                <div class="col-md-4 col-sm-6 mb-3 mb-md-4 <?php echo $c['list_params']['class']; ?>">
                    <div class="card border-0 bg-light">
                        <?php if($c['list_params']['cover_img']){ ?>
                            <a class="d-block overflow-hidden rounded-lg" href="<?php echo $c['list_params']['href']; ?>">
                                <img class="d-block w-100" src="<?php echo $c['list_params']['cover_img']; ?>" alt="<?php html($c['title']); ?>">
                            </a>
                        <?php } ?>
                        <div class="card-body">
                            <h2 class="h5 m-0">
                                <a href="<?php echo $c['list_params']['href']; ?>">
                                    <?php echo $c['title']; ?>
                                </a>
                            </h2>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    <?php } else { ?>
        <ul class="list-inline icms-content-subcats">
            <?php foreach($subcats as $c){ ?>
                <li class="list-inline-item h4 text-warning mr-3 <?php echo $c['list_params']['class']; ?>">
                    <?php html_svg_icon('solid', 'folder'); ?>
                    <a href="<?php echo $c['list_params']['href']; ?>">
                        <?php echo $c['title']; ?>
                    </a>
                </li>
            <?php } ?>
        </ul>
    <?php } ?>
<?php } ?>

<?php $this->block('before_content_items_list_html'); ?>

<?php echo $items_list_html; ?>

<?php if ($hooks_html) { ?>
    <div class="sub_items_list">
        <?php echo html_each($hooks_html); ?>
    </div>
<?php } ?>