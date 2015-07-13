<?php
    $this->addJS('templates/default/js/geo.js');
    $city_id = isset($value['id']) ? $value['id'] : null;
    $city_name = isset($value['name']) ? $value['name'] : null;
?>

<?php if ($field->title) { ?><label><?php echo $field->title; ?></label><?php } ?>

<div id="geo-widget-<?php echo $field->element_name; ?>" class="city-input">

    <?php echo html_input('hidden', $field->element_name, $city_id, array('class'=>'city-id')); ?>

    <span class="city-name" <?php if (!$city_name){ ?>style="display:none"<?php } ?>><?php echo $city_name; ?></span>

    <a class="ajax-modal" href="<?php echo href_to('geo', 'widget', array($field->element_name, $city_id)); ?>"><?php echo LANG_SELECT; ?></a>

</div>
