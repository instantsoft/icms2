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

    <div id="group_staff_list" class="striped-list list-32">
        <?php foreach($staff as $member) { ?>
            <?php echo $this->renderChild('group_edit_staff_item', array('member'=>$member, 'group'=>$group)); ?>
        <?php } ?>
    </div>

<?php } ?>

<div id="group_staff_add" class="gui-panel">

    <h3><?php echo LANG_GROUPS_ADD_STAFF; ?></h3>
    <div class="hint"><?php echo LANG_GROUPS_ADD_STAFF_HINT; ?></div>

    <div class="field">
        <?php echo html_input('text', 'username', '', array('id'=>'staff-username', 'autocomplete'=>'off')); ?>
        <?php echo html_button(LANG_ADD, 'add', 'icms.groups.addStaff()', array('id'=>'staff-submit', 'disabled'=>'disabled')); ?>
        <div class="loading-icon" style="display:none"></div>
    </div>

</div>

<script>

    <?php
        $list = array();
        if (is_array($members)){
            foreach($members as $member){
                $list[] = $member['email'];
            }
        }
    ?>

    $(document).ready(function(){

        icms.groups.url_submit = '<?php echo $this->href_to($group['slug'], array('edit', 'staff')); ?>';
        icms.groups.url_delete = '<?php echo $this->href_to($group['slug'], array('edit', 'staff_delete')); ?>';

        var members_list = <?php echo $list ? json_encode($list) : '[]'; ?>;

        $( "#staff-username" ).autocomplete({
            source: members_list
        });

        $( "#staff-submit" ).prop('disabled', false);

    });

</script>