<?php $this->addTplJSNameFromContext('geo'); ?>

<?php if ($field->title) { ?><label><?php echo $field->title; ?></label><?php } ?>

<?php if(!is_array($value)){ ?>

    <?php
    $this->addTplJSNameFromContext('jquery-chosen');
    $this->addTplCSSNameFromContext('jquery-chosen');
    ?>

    <div class="location_list location_group_<?php echo $field->data['location_group']; ?>">
        <?php echo html_select($field->element_name, $field->data['items'], $value, $field->data['dom_attr']); ?>
    </div>
    <script>
        <?php if($field->data['location_group']){ ?>
            icms.geo.addToGroup('location_group_<?php echo $field->data['location_group']; ?>', '<?php echo $field->data['location_type']; ?>');
        <?php } ?>
        $(function(){
            $('#<?php echo $field->data['dom_attr']['id']; ?>').chosen({no_results_text: '<?php echo LANG_LIST_EMPTY; ?>', placeholder_text_single: '<?php echo LANG_SELECT; ?>', disable_search_threshold: 8, allow_single_deselect: true, width: '100%', search_placeholder: '<?php echo LANG_BEGIN_TYPING; ?>'});
        });
    </script>

<?php } else { ?>

    <div id="geo-widget-<?php echo $field->id; ?>" class="city-input">

        <?php echo html_input('hidden', $field->element_name, $value['id'], array('class'=>'city-id')); ?>

        <span class="city-name" <?php if (empty($value['name'])){ ?>style="display:none"<?php } ?>><?php echo $value['name']; ?></span>

        <a class="city_clear_link" href="#" <?php if (empty($value['name'])){ ?>style="display:none"<?php } ?>><?php echo LANG_DELETE; ?></a>
        <a class="ajax-modal" href="<?php echo href_to('geo', 'widget', array($field->id, $value['id'])); ?>"><?php echo LANG_SELECT; ?></a>

    </div>

<?php }
