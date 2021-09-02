<?php $this->addTplJSName('groups'); ?>
<h1><?php echo LANG_GROUPS_EDIT_ROLES ?></h1>

<?php $this->renderChild('group_edit_header', array('group' => $group)); ?>


<div id="group_roles_list" class="striped-list list-32">
    <?php if ($group['roles']){ ?>
        <?php echo $this->renderChild('group_edit_role', array('roles' => $group['roles'])); ?>
    <?php } ?>
</div>

<div id="group_role_add" class="gui-panel">

    <h3><?php echo LANG_GROUPS_ADD_ROLE; ?></h3>

    <div class="field">
        <?php echo html_input('text', 'role', '', array('id'=>'role_input', 'autocomplete'=>'off')); ?>
        <?php echo html_button(LANG_ADD, 'add', 'icms.groups.addRole()', array('id'=>'role-submit', 'disabled'=>'disabled')); ?>
        <div class="loading-icon" style="display:none"></div>
    </div>

</div>

<script>
    $(function(){
        icms.groups.url_submit = '<?php echo $this->href_to($group['slug'], array('edit', 'roles')); ?>';
        icms.groups.url_delete = '<?php echo $this->href_to($group['slug'], array('edit', 'role_delete')); ?>';
        $( "#role-submit" ).prop('disabled', false);
    });
</script>