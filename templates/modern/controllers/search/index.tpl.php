<?php
    $this->addTplJSNameFromContext('search');

    $this->addBreadcrumb(LANG_SEARCH_TITLE, $this->href_to(''));
    if($query){
        $this->addBreadcrumb($query);
    }

    $content_menu = [];

    $uri_query = http_build_query([
        'order_by' => $order_by,
        'q'        => $query,
        'type'     => $type,
        'date'     => $date
    ]);

    if ($results){

        foreach($results as $result) {

            $content_menu[] = [
                'title'    => $result['title'],
                'url'      => $this->href_to($result['name']) . '?' . $uri_query,
                'url_mask' => $this->href_to($result['name']),
                'counter'  => $result['count']
            ];

            if($result['items'] || $result['html']) {
                $search_data = $result;
            }
        }

        $this->addMenuItems('results_tabs', $content_menu);
    }
?>

<h1>
    <?php $this->pageH1();?>
</h1>

<?php $this->renderChild('search_form', [
    'show_search_params' => $show_search_params,
    'query'              => $query,
    'type'               => $type,
    'date'               => $date,
    'order_by'           => $order_by
]); ?>

<?php if ($query && empty($search_data)){ ?>
    <?php $this->addHead('<meta name="robots" content="noindex">'); ?>
    <p class="alert alert-info">
        <?php echo LANG_SEARCH_NO_RESULTS; ?>
    </p>
<?php } ?>

<?php if (empty($search_data)){ return; } ?>

<?php $this->menu('results_tabs', true, 'nav nav-pills mb-3 mb-md-4'); ?>

<?php if (!empty($search_data['html'])) { ?>
    <?php echo $search_data['html']; ?>
<?php } else { ?>

    <div id="search_results_list" class="mb-3 mb-md-4">
        <?php foreach($search_data['items'] as $item){ ?>
            <div class="item media mb-3 mb-md-4 mb-lg-5">
                <div class="media-body">
                    <h3 class="mt-0">
                        <a href="<?php echo $item['url']; ?>" target="_blank">
                            <?php echo $item['title']; ?>
                        </a>
                    </h3>
                    <?php if(!empty($item['image'])){ ?>
                        <div class="mb-3">
                            <a href="<?php echo $item['url']; ?>" target="_blank">
                                <?php echo $item['image']; ?>
                            </a>
                        </div>
                    <?php } ?>
                    <?php foreach($item['fields'] as $field=>$value){ ?>
                        <?php if (!$value) { continue; } ?>
                        <div class="field search_field_<?php echo $field; ?> mb-2">
                            <?php echo $value; ?>
                        </div>
                    <?php } ?>
                    <div class="text-muted small">
                        <?php html_svg_icon('solid', 'calendar-alt'); ?>
                        <?php echo html_date_time($item['date_pub']); ?>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>

    <?php echo html_pagebar($page, $perpage, $search_data['count'], $page_url, $uri_query); ?>

<?php } ?>