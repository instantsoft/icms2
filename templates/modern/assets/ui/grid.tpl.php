<?php
if(!empty($page_title)) {
    $this->addBreadcrumb($page_title);
    $this->setPageTitle($page_title);
}
?>
<?php if(!empty($h1_title)) { ?>
    <h1><?php echo $h1_title; ?></h1>
<?php } ?>
<?php $this->renderGrid($source_url, $grid);
