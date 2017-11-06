<?php

    $this->setPageTitle(sprintf(LANG_TAGS_SEARCH_BY_TAG, $tag));

    if (!empty($ctype['seo_keys'])){ $this->setPageKeywords($ctype['seo_keys']); }
    if (!empty($ctype['seo_desc'])){ $this->setPageDescription($ctype['seo_desc']); }

    $this->addBreadcrumb(sprintf(LANG_TAGS_SEARCH_BY_TAG, $tag));

    $content_menu = array();

    if ($is_results){

        foreach($ctypes as $type){
            if (!in_array($type['name'], $targets['content'])) { continue; }
            $content_menu[] = array(
                'title'    => $type['title'],
                'url'      => $this->href_to('search', array($type['name'])).'?q='.$tag,
                'url_mask' => $this->href_to('search', array($type['name']))
            );
        }

        $content_menu[0]['url']      = $this->href_to('search').'?q='.$tag;
        $content_menu[0]['url_mask'] = $this->href_to('search');

        $this->addMenuItems('results_tabs', $content_menu);

    }

    if (cmsUser::isAdmin()){
        $this->addToolButton(array(
            'class' => 'page_gear',
            'title' => LANG_TAGS_SETTINGS,
            'href'  => href_to('admin', 'controllers', array('edit', 'tags'))
        ));
    }

?>

<h1><?php html(sprintf(LANG_TAGS_SEARCH_BY_TAG, $tag)); ?></h1>

<?php if (!$is_results){ ?>
    <p><?php echo LANG_TAGS_SEARCH_NO_RESULTS; ?></p>
<?php } ?>

<?php if ($is_results){ ?>

    <div id="tags_search_pills">
        <?php $this->menu('results_tabs', true, 'pills-menu-small'); ?>
    </div>

    <div id="tags_search_list"><?php echo $html; ?></div>

<?php } ?>