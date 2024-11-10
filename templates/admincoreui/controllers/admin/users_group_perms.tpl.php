<?php

    $this->setPageTitle(LANG_USER_GROUP.': '.$group['title']);

    $this->addBreadcrumb(LANG_CP_SECTION_USERS, $this->href_to('users'));
    $this->addBreadcrumb(LANG_USER_GROUP.': '.$group['title']);

    $this->addToolButton([
        'class' => 'save process-save',
        'title' => LANG_SAVE,
        'href'  => '#',
        'icon'  => 'save'
    ]);

    $this->addToolButton([
        'class' => 'cancel',
        'title' => LANG_CANCEL,
        'href'  => $this->href_to('users'),
        'icon'  => 'undo'
    ]);

    $this->addMenuItem('breadcrumb-menu', [
        'title' => LANG_HELP,
        'url'   => LANG_HELP_URL_USERS_GROUP,
        'options' => [
            'target' => '_blank',
            'icon' => 'question-circle'
        ]
    ]);

    $this->setMenuItems('admin_toolbar', $menu);

?>

<?php if ($this->isToolbar()){ ?>
    <?php $this->toolbar('menu-toolbar'); ?>
<?php } ?>

<form action="<?php echo $this->href_to('users', 'group_perms_save'); ?>" method="post" id="form-perms">

    <?php echo html_csrf_token(); ?>
    <?php echo html_input('hidden', 'group_id', $group['id']); ?>

    <div class="datagrid_wrapper table-responsive-md dataTables_wrapper dt-bootstrap4">
        <table id="datagrid" class="datagrid table table-striped table-bordered dataTable bg-white">
            <tr class="filter table-align-middle table-secondary">
                <th class="p-2" colspan="2">
                    <?php echo html_select('search', (['' => LANG_ALL]+$subjects), '', ['id'=>'filter-perm-subjects', 'class' => 'custom-select custom-select-sm']); ?>
                </th>
            </tr>
            <?php foreach($owners as $controller_name => $controller){ ?>

                <?php $rules = $controller['rules']; ?>

                <?php foreach($controller['subjects'] as $subject){ ?>

                    <?php $values = $controller['values'][$subject['name']]; ?>
                    <?php $class_name = $controller_name.'_'.$subject['name']; ?>
                    <tbody class="list_tbody <?php echo $class_name; ?>">
                        <tr class="table-info">
                            <td colspan="2"><h5 class="m-0"><?php echo $subject['title']; ?></h5></td>
                        </tr>
                        <?php foreach($rules as $rule){ ?>
                            <tr>
                                <td class="align-middle">
                                    <?php echo $rule['title']; ?>
                                    <?php if(!empty($rule['title_hint'])){ ?>
                                        <div class="hint text-muted small"><?php echo $rule['title_hint']; ?></div>
                                    <?php } ?>
                                </td>

                                <?php $default = $values[$rule['id']][$group['id']] ?? null; ?>

                                <td class="center align-middle text-center" data-label="<?php html($rule['title']); ?>">
                                    <?php if ($rule['type'] == 'flag'){ ?>
                                        <label class="switch switch-pill switch-primary m-0 align-middle">
                                            <?php echo html_checkbox("value[{$rule['id']}][{$subject['name']}]", $default, 1, ['class' => 'switch-input']); ?>
                                            <span class="switch-slider"></span>
                                        </label>
                                    <?php } ?>
                                    <?php if ($rule['type'] == 'list'){ ?>
                                        <?php echo html_select("value[{$rule['id']}][{$subject['name']}]", $rule['options'], $default); ?>
                                    <?php } ?>
                                    <?php if ($rule['type'] == 'number'){ ?>
                                        <?php echo html_input('text', "value[{$rule['id']}][{$subject['name']}]", $default, ['class'=>'input-number']); ?>
                                    <?php } ?>
                                </td>

                            </tr>
                        <?php } ?>
                    </tbody>

                <?php } ?>
            <?php } ?>

        </table>
    </div>
    <div class="buttons my-3">
        <?php echo html_submit(LANG_SAVE); ?>
    </div>
</form>
<?php ob_start(); ?>
<script>
    <?php echo $this->getLangJS('LANG_SUBMIT_NOT_SAVE'); ?>
    $(function (){
        icms.forms.initFormHelpers('form-perms');
        icms.forms.initUnsaveNotice();
        $('#filter-perm-subjects').on('change', function () {
            let val = $(this).val();
            if (val === '') {
                $('.list_tbody').show();
                return;
            }
            $('.list_tbody').hide();
            $('.list_tbody').filter(function () {
                return $(this).hasClass(val);
            }).show();
        });
    });
</script>
<?php $this->addBottom(ob_get_clean()); ?>