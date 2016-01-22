<?php

    $this->setPageTitle(LANG_SEARCH_TITLE);
    $this->addBreadcrumb(LANG_SEARCH_TITLE);

    $content_menu = array();

    $uri_query = http_build_query(array(
        'q' => $query,
        'type' => $type,
        'date' => $date
    ));

    if ($results){

        foreach($results as $ctype){
            $content_menu[] = array(
                'title' => $ctype['title'],
                'url' => $this->href_to('index', array($ctype['name'])) . "?" . $uri_query,
                'url_mask' => $this->href_to('index', array($ctype['name'])),
                'counter' => $ctype['count']
            );
        }

        $content_menu[0]['url'] = href_to('search') . "?" . $uri_query;
        $content_menu[0]['url_mask'] = href_to('search');

        $this->addMenuItems('results_tabs', $content_menu);

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

<?php if ($query && empty($results)){ ?>
    <p id="search_no_results"><?php echo LANG_SEARCH_NO_RESULTS; ?></p>
<?php } ?>

<?php if ($results){ ?>

    <div id="search_results_pills">
        <?php $this->menu('results_tabs', true, 'pills-menu-small'); ?>
    </div>

    <?php foreach($results as $ctype){ ?>
        <?php if ($ctype['name'] != $ctype_name) { continue; } ?>
        <div id="search_results_list">
            <?php foreach($ctype['items'] as $item){ ?>
                <div class="item">
                    <div class="title">
                        <a href="<?php echo $item['url']; ?>" target="_blank"><?php html($item['title']); ?></a>
                    </div>
                    <?php foreach($item as $field=>$value){ ?>
                        <?php if (in_array($field, array('id', 'title', 'slug', 'date_pub', 'url'))) { continue; } ?>
                        <?php if (!$value) { continue; } ?>
                        <div class="field search_field_<?php echo $field; ?>"><?php echo $value; ?></div>
                    <?php } ?>
                    <div class="info"><span class="date"><?php echo html_date_time($item['date_pub']); ?></span></div>
                </div>
            <?php } ?>
        </div>
        <?php if ($ctype['count'] > $perpage){ ?>
            <?php echo html_pagebar($page, $perpage, $ctype['count'], $page_url, $uri_query); ?>
        <?php } ?>
    <?php } ?>

<?php } ?>
