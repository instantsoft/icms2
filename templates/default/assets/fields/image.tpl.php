<?php if ($field->title) { ?><label for="<?php echo $field->id; ?>"><?php echo $field->title; ?></label><?php } ?>
<?php

	$paths = false;

    if ($value){
        $paths = is_array($value) ? $value : cmsModel::yamlToArray($value);
    }

	$sizes = $field->getOption('sizes');
	
    $images_controller = cmsCore::getController('images');

    echo $images_controller->getSingleUploadWidget($field->element_name, $paths, $sizes);
