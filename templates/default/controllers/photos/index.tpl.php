<?php $this->addBreadcrumb(LANG_PHOTOS_ALL); ?>

<h1><?php $this->pageH1();?></h1>

<?php echo $this->renderChild('filter-panel', [
    'item' => $album,
    'page_url' => href_to('photos')
]); ?>

<?php echo $photos_html;
