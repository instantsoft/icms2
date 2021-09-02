<h1><?php echo $page_title ?></h1>

<?php if ($do=='edit') { $this->renderChild('group_edit_header', array('group' => $group)); } ?>

<?php
    if(!empty($group['id']) && $group['slug'] == $group['id']){ $group['slug'] = null; }
    $this->renderForm($form, $group, array(
        'action'  => '',
        'cancel'  => array('show' => true, 'href' => 'javascript:goBack()'),
        'toolbar' => false,
        'method'  => 'post',
        'hook' => array(
            'event' => 'group_form_html',
            'param' => array(
                'do' => $do,
                'id' => $do=='edit' ? $group['id'] : null
            )
        )
    ), $errors);
?>
<?php if ($is_premoderation) { ?>
    <div class="content_moderation_notice icon-info">
        <?php echo LANG_MODERATION_NOTICE; ?>
    </div>
<?php } ?>
<script>
    $(function (){
        $('select[id ^= "content_policy_"]').each(function (){
            console.log($(this).attr('name'));
            var ctype_name = $(this).attr('name').replace(/content_policy\[(.*)\]/g, '$1');
            $(this).on('change', function (){
                var groups_div = $('#f_content_groups_'+ctype_name);
                var role_div = $('#f_content_roles_'+ctype_name);
                $(groups_div).hide();
                $(role_div).hide();
                if($(this).val() == <?php echo groups::CTYPE_POLICY_GROUPS; ?>){
                    $(groups_div).show();
                } else if($(this).val() == <?php echo groups::CTYPE_POLICY_ROLES; ?>) {
                    $(role_div).show();
                }
            }).triggerHandler('change');
        });
        $('.chosen-container-multi .chosen-choices li.search-field input[type="text"]').width(150);
    });
</script>