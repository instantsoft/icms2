<?php

    $this->setPageTitle($dataset ? LANG_ACTIVITY . ' - ' . $dataset['title'] : LANG_ACTIVITY);

    $base_url = $this->controller->name;
    $base_ds_url = $this->controller->name . '/index/%s';

    $this->addBreadcrumb(LANG_ACTIVITY, href_to($base_url));

?>

<h1><?php echo LANG_ACTIVITY; ?></h1>

<?php if (sizeof($datasets)>1){ ?>
    <div class="content_datasets">
        <ul class="pills-menu">
            <?php $ds_counter = 0; ?>
            <?php foreach($datasets as $set){ ?>
                <?php $ds_selected = ($dataset_name == $set['name'] || (!$dataset_name && $ds_counter==0)); ?>
                <li <?php if ($ds_selected){ ?>class="active"<?php } ?>>

                    <?php if ($ds_counter > 0) { $ds_url = sprintf(href_to($base_ds_url), $set['name']); } ?>
                    <?php if ($ds_counter == 0) { $ds_url = href_to($base_url); } ?>

                    <?php if ($ds_selected){ ?>
                        <div><?php echo $set['title']; ?></div>
                    <?php } else { ?>
                        <a href="<?php echo $ds_url; ?>"><?php echo $set['title']; ?></a>
                    <?php } ?>

                </li>
                <?php $ds_counter++; ?>
            <?php } ?>
        </ul>
    </div>
<?php } ?>

<?php echo $items_list_html; ?>