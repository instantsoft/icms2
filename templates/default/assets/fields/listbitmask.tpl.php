<?php if ($field->title) { ?><label for="<?php echo $field->id; ?>"><?php echo $field->title; ?></label><?php } ?>
<?php

    $users_model = cmsCore::getModel('users');

	$items = $field->getListItems();
    
	$selected = array();
	
    if ($value) {
		if (!is_array($value)){
			$pos = 0;			
			foreach($items as $key => $title){				
				if (mb_substr($value, $pos, 1) == 1){
					$selected[] = $key;
				}
				$pos++;
				if ($pos+1 > mb_strlen($value)) { break; }
			}
		} else {
			$selected = $value;
		}
	}

    echo html_select_multiple($field->element_name, $items, $selected, array('id'=>$field->id));
