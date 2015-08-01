<?php if ($field->title) { ?><label for="<?php echo $field->id; ?>"><?php echo $field->title; ?></label><?php } ?>
<?php

    if($field->data['is_multiple']){

        echo html_select_multiple($field->element_name, $field->data['items'], $value, $field->data['dom_attr'], $field->data['is_tree']);

    }else{

        echo html_select($field->element_name, $field->data['items'], $value, $field->data['dom_attr']);

    }

?>

<?php if($field->data['parent']){ ?>
	<script>
		$('#<?php echo str_replace(':', '_', $field->data['parent']['list']); ?>').on('change', function(){
			icms.forms.updateChildList('<?php echo $field->id; ?>', '<?php echo $field->data['parent']['url']; ?>', $(this).val());
		});
	</script>
<?php } ?>
