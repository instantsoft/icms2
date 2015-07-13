<?php if ($field->title) { ?><label for="<?php echo $field->id; ?>"><?php echo $field->title; ?></label><?php } ?>
<?php

	$images = false;

    if ($value){
        $images = is_array($value) ? $value : cmsModel::yamlToArray($value);
    }

	$sizes = $field->getOption('sizes');
	
    $images_controller = cmsCore::getController('images');

    echo $images_controller->getMultiUploadWidget($field->element_name, $images, $sizes);
