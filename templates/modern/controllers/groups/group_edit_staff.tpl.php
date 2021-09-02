<?php
$this->addTplJSName([
    'groups',
    'jquery-ui'
]);
$this->addTplCSSName('jquery-ui');
?>

<h1><?php echo LANG_GROUPS_EDIT ?></h1>

<?php $this->renderChild('group_edit_header', array('group' => $group)); ?>

<?php if ($staff){ ?>

    <div id="group_staff_list" class="striped-list mt-3 mt-md-4">
        <?php foreach($staff as $member) { ?>
            <?php echo $this->renderChild('group_edit_staff_item', array('member'=>$member, 'group'=>$group)); ?>
        <?php } ?>
    </div>

<?php } ?>

<div id="group_staff_add" class="gui-panel my-3 my-md-4 bg-light p-3 rounded">

    <h3><?php echo LANG_GROUPS_ADD_STAFF; ?></h3>
    <div class="hint text-muted mb-1"><?php echo LANG_GROUPS_ADD_STAFF_HINT; ?></div>

    <div class="form-row align-items-center">
        <div class="col-auto">
            <?php echo html_input('text', 'username', '', array('id'=>'staff-username', 'autocomplete'=>'off')); ?>
        </div>
        <?php echo html_button(LANG_ADD, 'add', 'icms.groups.addStaff()', ['id'=>'staff-submit', 'disabled'=>'disabled', 'class' => 'btn-primary']); ?>
    </div>

</div>
<?php ob_start(); ?>
<script>

    <?php
        $list = array();
        if (is_array($members)){
            foreach($members as $member){
                $list[] = $member['email'];
            }
        }
    ?>

    $(function(){

        icms.groups.url_submit = '<?php echo $this->href_to($group['slug'], array('edit', 'staff')); ?>';
        icms.groups.url_delete = '<?php echo $this->href_to($group['slug'], array('edit', 'staff_delete')); ?>';

        var members_list = <?php echo $list ? json_encode($list) : '[]'; ?>;

        $( "#staff-username" ).autocomplete({
            source: members_list
        });

        $( "#staff-submit" ).prop('disabled', false);

    });

</script>
<?php $this->addBottom(ob_get_clean()); ?>
