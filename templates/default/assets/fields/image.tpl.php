<?php if ($field->title) { ?><label for="<?php echo $field->id; ?>"><?php echo $field->title; ?></label><?php } ?>
<?php echo $field->data['images_controller']->getSingleUploadWidget($field->element_name, $field->data['paths'], $field->data); ?>
