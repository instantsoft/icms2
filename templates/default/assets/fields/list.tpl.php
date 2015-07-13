<?php if ($field->title) { ?><label for="<?php echo $field->id; ?>"><?php echo $field->title; ?></label><?php } ?>
<?php

    $items = $field->getListItems();
    
    $is_multiple = $field->getProperty('is_multiple');
    $is_tree = $field->getProperty('is_tree');
	$parent = $field->getProperty('parent');
	
	$dom_attr = array();	
	$dom_attr['id'] = $field->id;
	
    if (!$is_multiple){
    
        echo html_select($field->element_name, $items, $value, $dom_attr);
        
    } else {
        
        echo html_select_multiple($field->element_name, $items, $value, $dom_attr, $is_tree);
        
    }

?>

<?php if ($parent){ ?>
	<?php $p_id = str_replace(':', '_', $parent['list']); ?>
	<script>
		$('#<?php echo $p_id; ?>').on('change', function(){
			icms.forms.updateChildList('<?php echo $field->id; ?>', '<?php echo $parent['url']; ?>', $(this).val()); 
		});
	</script>
<?php } ?>
