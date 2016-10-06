<?php

    $this->addCSS('templates/default/controllers/photos/styles.css');

    $this->setPageTitle(LANG_SEARCH_TITLE);

    $this->addBreadcrumb(LANG_SEARCH_TITLE, $this->href_to(''));
    if($query){
        $this->addBreadcrumb($query);
    }

    $content_menu = array();

    $uri_query = http_build_query(array(
        'q'    => $query,
        'type' => $type,
        'date' => $date
    ));

    if ($results){

        foreach($results as $result){

            $content_menu[] = array(
                'title'    => $result['title'],
                'url'      => $this->href_to('index', array($result['name'])) . '?' . $uri_query,
                'url_mask' => $this->href_to('index', array($result['name'])),
                'counter'  => $result['count']
            );

            if($result['items']){
                $search_data = $result;
            }

        }

        $content_menu[0]['url'] = href_to('search') . '?' . $uri_query;
        $content_menu[0]['url_mask'] = href_to('search');

        $this->addMenuItems('results_tabs', $content_menu);

        $this->setPageTitle($query, $target_title, mb_strtolower(LANG_SEARCH_TITLE));

    }

?>

<h1><?php echo LANG_SEARCH_TITLE; ?></h1>

<div id="search_form">
    <form action="<?php echo href_to('search'); ?>" method="get">
        <?php echo html_input('text', 'q', $query, array('placeholder'=>LANG_SEARCH_QUERY_INPUT)); ?>
        <?php echo html_select('type', array(
            'words' => LANG_SEARCH_TYPE_WORDS,
            'exact' => LANG_SEARCH_TYPE_EXACT,
        ), $type); ?>
        <?php echo html_select('date', array(
            'all' => LANG_SEARCH_DATES_ALL,
            'w' => LANG_SEARCH_DATES_W,
            'm' => LANG_SEARCH_DATES_M,
            'y' => LANG_SEARCH_DATES_Y,
        ), $date); ?>
        <?php echo html_submit(LANG_FIND); ?>
    </form>
</div>

<?php if ($query && empty($search_data)){ ?>
    <p id="search_no_results"><?php echo LANG_SEARCH_NO_RESULTS; ?></p>
<?php } ?>

<?php if (!empty($search_data)){ ?>

    <div id="search_results_pills">
        <?php $this->menu('results_tabs', true, 'pills-menu-small'); ?>
    </div>

    <div id="album-photos-list">
        <?php foreach($search_data['items'] as $item){ ?>
            <div class="photo photo-<?php echo $item['id']; ?>">
                <div class="image">
                    <a href="<?php echo $item['url']; ?>" title="<?php html($item['title']); ?>" target="_blank">
                        <?php echo $item['image']; ?>
                    </a>
                </div>
                <div class="info">
                    <div class="rating <?php echo html_signed_class($item['rating']); ?>">
                        <?php echo html_signed_num($item['rating']); ?>
                    </div>
                    <div class="comments">
                        <span><?php echo $item['comments']; ?></span>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
    <?php if ($search_data['count'] > $perpage){ ?>
        <?php echo html_pagebar($page, $perpage, $search_data['count'], $page_url, $uri_query); ?>
    <?php } ?>

<?php } ?>