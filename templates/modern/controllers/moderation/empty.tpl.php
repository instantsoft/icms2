<?php
    $this->setPageTitle($page_title);
    $this->addBreadcrumb($page_title);
?>

<h1><?php echo $page_title; ?></h1>

<p id="moderation_content_list" class="alert alert-info mt-4" role="alert">
    <?php echo $empty_hint; ?>
</p>
