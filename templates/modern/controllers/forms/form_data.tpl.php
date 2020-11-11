<?php if($form_items_titles){ ?>
    <?php foreach ($form_items_titles as $title => $value) { ?>
        <p><b><?php echo $title; ?>:</b> <?php echo $value; ?></p>
    <?php } ?>
<?php } ?>