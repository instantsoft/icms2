<?php
$this->setPageTitle(LANG_PHOTOS_ALL);
$this->addBreadcrumb(LANG_PHOTOS_ALL);
?>

<h1><?php echo LANG_PHOTOS_ALL; ?></h1>
<?php echo $this->renderChild('filter-panel', array(
    'item' => $album,
    'page_url' => href_to('photos'),
)); ?>
<?php echo $photos_html;
