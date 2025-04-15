<?php
    $this->addTplJSName([
        'photos',
        'jquery-flex-images'
    ]);

    $this->addTplCSS('controllers/photos/styles');

    $this->addBreadcrumb(LANG_SEARCH_TITLE, $this->href_to(''));
    if($query){
        $this->addBreadcrumb($query);
    }

    $content_menu = [];

    $uri_query = http_build_query([
        'q'    => $query,
        'type' => $type,
        'date' => $date
    ]);

    if ($results){

        foreach($results as $result){

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
    <p id="search_no_results" class="alert alert-info">
        <?php echo LANG_SEARCH_NO_RESULTS; ?>
    </p>
<?php } ?>

<?php if (empty($search_data)){ return; } ?>

<?php $this->menu('results_tabs', true, 'nav nav-pills mb-3 mb-md-4'); ?>

<?php if (!empty($search_data['html'])) { ?>
    <?php echo $search_data['html']; ?>
<?php } else { ?>

    <div class="album-photos-wrap d-flex flex-wrap m-n1 mb-3 mb-md-4" id="album-photos-list" data-delete-url="<?php echo href_to('photos', 'delete'); ?>">
        <?php $this->renderControllerChild('photos', 'photos', [
            'photos'        => $search_data['items'],
            'is_owner'      => false,
            'user'          => $user,
            'has_next'      => false,
            'preset_small'  => photos::$preset_small,
            'page'          => 1
        ]); ?>
    </div>

    <?php echo html_pagebar($page, $perpage, $search_data['count'], $page_url, $uri_query); ?>
<?php ob_start(); ?>
<script>
    icms.photos.init = true;
    icms.photos.mode = 'album';
    icms.photos.row_height = '<?php echo photos::$row_height; ?>';
</script>
<?php $this->addBottom(ob_get_clean()); ?>
<?php }