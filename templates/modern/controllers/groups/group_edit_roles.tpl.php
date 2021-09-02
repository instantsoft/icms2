<?php $this->addTplJSName('groups'); ?>
<h1><?php echo LANG_GROUPS_EDIT_ROLES ?></h1>

<?php $this->renderChild('group_edit_header', array('group' => $group)); ?>


<div id="group_roles_list" class="striped-list mt-3 mt-md-4">
    <?php if ($group['roles']){ ?>
        <?php echo $this->renderChild('group_edit_role', array('roles' => $group['roles'])); ?>
    <?php } ?>
</div>

<div id="group_role_add" class="gui-panel my-3 my-md-4 bg-light p-3 rounded">

    <h3><?php echo LANG_GROUPS_ADD_ROLE; ?></h3>

    <div class="form-row align-items-center">
        <div class="col-auto">
            <?php echo html_input('text', 'role', '', array('id'=>'role_input', 'autocomplete'=>'off')); ?>
        </div>
        <?php echo html_button(LANG_ADD, 'add', 'icms.groups.addRole()', ['id'=>'role-submit', 'disabled'=>'disabled', 'class' => 'btn-primary']); ?>
    </div>
</div>
<?php ob_start(); ?>
<script>
    $(function(){
        icms.groups.url_submit = '<?php echo $this->href_to($group['slug'], array('edit', 'roles')); ?>';
        icms.groups.url_delete = '<?php echo $this->href_to($group['slug'], array('edit', 'role_delete')); ?>';
        $( "#role-submit" ).prop('disabled', false);
    });
</script>
<?php $this->addBottom(ob_get_clean()); ?>
