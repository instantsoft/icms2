<h1><?php echo LANG_USER_GROUP; ?>: <span><?php echo $group['title']; ?></span></h1>

<?php

    $this->setPageTitle(LANG_USER_GROUP.': '.$group['title']);

    $this->addBreadcrumb(LANG_CP_SECTION_USERS, $this->href_to('users'));
    $this->addBreadcrumb($group['title']);

    $this->addToolButton(array(
        'class' => 'save',
        'title' => LANG_SAVE,
        'href'  => "javascript:icms.forms.submit()"
    ));

    $this->addToolButton(array(
        'class' => 'cancel',
        'title' => LANG_CANCEL,
        'href'  => $this->href_to('users')
    ));

	$this->addToolButton(array(
		'class' => 'help',
		'title' => LANG_HELP,
		'target' => '_blank',
		'href'  => LANG_HELP_URL_USERS_GROUP
	));

    $this->setMenuItems('users_group', $menu);

?>

<div class="pills-menu">
    <?php $this->menu('users_group'); ?>
</div>

<div class="cp_toolbar">
    <?php $this->toolbar(); ?>
</div>

<form action="<?php echo $this->href_to('users', 'group_perms_save'); ?>" method="post">

    <?php echo html_input('hidden', 'group_id', $group['id']); ?>

    <div class="datagrid_wrapper">
        <table id="datagrid" class="datagrid" cellpadding="0" cellspacing="0" border="0">

            <?php foreach($owners as $controller_name=>$controller){ ?>

                <?php $rules = $controller['rules']; ?>

                <?php foreach($controller['subjects'] as $subject){ ?>

                    <?php $values = $controller['values'][$subject['name']]; ?>

                    <thead class="list_thead">
                        <tr>
                            <th colspan="2"><?php echo $subject['title']; ?></th>
                        </tr>
                    </thead>
                    <tbody class="list_tbody">
                        <?php foreach($rules as $rule){ ?>
                            <tr>
                                <td>
                                    <?php echo $rule['title']; ?>
                                    <?php if(!empty($rule['title_hint'])){ ?>
                                        <div class="hint"><?php echo $rule['title_hint']; ?></div>
                                    <?php } ?>
                                </td>

                                <?php
                                    $default =  isset($values[$rule['id']][$group['id']]) ?
                                                $values[$rule['id']][$group['id']] :
                                                null;
                                ?>

                                <td class="center" data-label="<?php html($rule['title']); ?>">
                                    <div>
                                    <?php if ($rule['type'] == 'flag'){ ?>
                                        <?php echo html_checkbox("value[{$rule['id']}][{$subject['name']}]", $default); ?>
                                    <?php } ?>
                                    <?php if ($rule['type'] == 'list'){ ?>
                                        <?php echo html_select("value[{$rule['id']}][{$subject['name']}]", $rule['options'], $default); ?>
                                    <?php } ?>
                                    <?php if ($rule['type'] == 'number'){ ?>
                                    <?php echo html_input('text', "value[{$rule['id']}][{$subject['name']}]", $default, array('class'=>'input-number')); ?>
                                <?php } ?>
                                    </div>
                                </td>

                            </tr>
                        <?php } ?>
                    </tbody>

                <?php } ?>

            <?php } ?>

        </table>
    </div>

    <div class="buttons">
        <?php echo html_submit(LANG_SAVE); ?>
    </div>

</form>
