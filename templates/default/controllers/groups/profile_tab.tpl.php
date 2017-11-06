<?php

    $this->setPageTitle($tab['title'], $profile['nickname']);

    $this->addBreadcrumb(LANG_USERS, href_to('users'));
    $this->addBreadcrumb($profile['nickname'], href_to_profile($profile));
    $this->addBreadcrumb($tab['title']);

?>
<?php if (!empty($datasets)){
    $this->renderAsset('ui/datasets-panel', array(
        'datasets'        => $datasets,
        'dataset_name'    => $dataset,
        'current_dataset' => $current_dataset,
        'base_ds_url'     => $base_ds_url
    ));
} ?>
<?php echo $html; ?>