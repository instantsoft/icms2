<?php
    $this->addTplJSNameFromContext('content-bind');
    $ids = array();
?>

<?php if ($field->title) { ?><label for="<?php echo $field->id; ?>"><?php echo $field->title; ?></label><?php } ?>

<ul class="items">
	<?php if ($items) { ?>
		<?php foreach($items as $item) { ?>
			<li data-id="<?php echo $item['id']; ?>">
				<?php echo $item['title']; ?>
                <?php if ($allowed_to_unbind_perm && ($allowed_to_unbind_perm == 'all' || $item['user_id'] == $auth_user_id)) { ?>
                    <a class="delete" href="#" title="<?php echo LANG_DELETE; ?>"></a>
                <?php } ?>
			</li>
		<?php $ids[] = $item['id']; } ?>
	<?php } ?>
</ul>

<?php if ($is_allowed_to_bind) { ?>
	<?php $url = href_to($ctype_name, 'bind_form', array($child_ctype_name, isset($field_item['id']) ? $field_item['id'] : 0, 'parents')); ?>
	<a class="add" href="<?php echo $url; ?>?input_action=<?php echo $input_action; ?>"><?php echo ($input_action === 'bind' ? LANG_ADD : LANG_SELECT); ?></a>
<?php } ?>

<?php echo html_input('hidden', $field->element_name, implode(',', $ids), array('id'=>$field->id)); ?>

<script>
    $(function(){

        <?php if($input_action === 'bind'){ ?>
            var modal_title = '<?php html(sprintf(LANG_CONTENT_BIND_ITEM, $parent_ctype['labels']['create'])); ?>';
        <?php } else { ?>
            var modal_title = '<?php html(sprintf(LANG_CONTENT_SELECT_ITEM, $parent_ctype['labels']['create'])); ?>';
        <?php } ?>

        var $container = $('#<?php echo $field->id; ?>').closest('div.field');

        $container.find('a.add').click(function(e){

            var $link = $(this);

            e.preventDefault();

            icms.contentBind.start({
				modal_title: modal_title,
				url: $link.attr('href')+'&selected='+$('#<?php echo $field->id; ?>').val(),
				callback: function(items){
					var $list = $link.siblings('ul');
					var $input = $link.siblings('input[type=hidden]');
					var newValues = [];
					$.each(items, function(id, title){
						if ($list.find('li[data-id=' + id+']').length > 0) { return; }
						var $item = $('<li></li>').attr('data-id', id).text(title).appendTo($list);
                        var $deleteBtn = $('<a href="#"></a>').appendTo($item);
                        $deleteBtn.click(function(e){
                            e.preventDefault();
                            $item.remove();
                            var newValues = [];
                            $list.find('li').each(function(){
                                var $item = $(this);
                                newValues.push($item.data('id'));
                            });
                            $input.val(newValues.length > 0 ? newValues.join(',') : '');
                        });
						newValues.push(id);
					});
					if (newValues.length > 0){
                        var $currentVal = $input.val();
						$input.val(($currentVal ? $currentVal + ',' : '') + newValues.join(','));
					}

				}
			});
        });

        $container.find('a.delete').click(function(e){
            e.preventDefault();
            var $link = $(this);
            var $item = $link.parents('li');
            var $list = $item.parents('ul');
            var $input = $list.siblings('input[type=hidden]');
            $item.remove();
            var newValues = [];
            $list.find('li').each(function(){
                var $item = $(this);
                newValues.push($item.data('id'));
            });
            $input.val(newValues.length > 0 ? newValues.join(',') : '');
        });

    });
</script>