<h1><?php echo $h1_title; ?></h1>

<?php if ($datasets){ ?>
    <div class="content_datasets">
        <ul class="pills-menu">
            <?php $ds_counter = 0; ?>
            <?php foreach($datasets as $set){ ?>
                <?php $ds_selected = ($dataset_name == $set['name'] || (!$dataset_name && $ds_counter==0)); ?>
                <li <?php if ($ds_selected){ ?>class="active"<?php } ?>>

                    <?php if ($ds_counter > 0) { $ds_url = sprintf(rel_to_href($base_ds_url), $set['name']); } ?>
                    <?php if ($ds_counter == 0) { $ds_url = href_to('groups'); } ?>

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
    <?php if (!empty($dataset['description'])){ ?>
        <div class="content_datasets_description">
            <?php echo $dataset['description']; ?>
        </div>
    <?php } ?>
<?php } ?>

<?php echo $groups_list_html;
