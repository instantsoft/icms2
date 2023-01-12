<?php

    $this->addTplJSName('admin-moderators');

    $this->setPageTitle(LANG_MODERATORS, $ctype['title']);

    $this->addBreadcrumb(LANG_MODERATORS);

    $this->addMenuItem('breadcrumb-menu', [
        'title' => LANG_MODERATORATION_OPTIONS,
        'url'   => href_to('admin', 'controllers', ['edit', 'moderation', 'options']),
        'options' => [
            'icon' => 'cog'
        ]
    ]);

    $this->addMenuItem('breadcrumb-menu', [
        'title' => LANG_HELP,
        'url'   => LANG_HELP_URL_CTYPES_MODERATORS,
        'options' => [
            'target' => '_blank',
            'icon' => 'question-circle'
        ]
    ]);

?>

<?php if ($this->isToolbar()){ ?>
    <?php $this->toolbar('menu-toolbar'); ?>
<?php } ?>

<div id="ctype_moderators_list" class="datagrid_wrapper table-responsive-md dataTables_wrapper dt-bootstrap4 mb-2" <?php if (!$moderators){ ?>style="display:none"<?php } ?>>
    <table id="datagrid" class="datagrid table table-striped table-bordered dataTable bg-white mb-0 table-align-middle">
        <thead>
            <tr>
                <th><?php echo LANG_MODERATOR; ?></th>
                <th class="d-none d-lg-table-cell"><?php echo LANG_MODERATOR_ASSIGNED_DATE; ?></th>
                <th><?php echo LANG_MODERATOR_TRASH_LEFT_TIME; ?></th>
                <th class="d-none d-lg-table-cell"><?php echo LANG_MODERATOR_APPROVED_COUNT; ?></th>
                <th class="d-none d-lg-table-cell"><?php echo LANG_MODERATOR_DELETED_COUNT; ?></th>
                <th><?php echo LANG_MODERATOR_IDLE_COUNT; ?></th>
                <th><?php echo LANG_CP_ACTIONS; ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if ($moderators){ ?>
                <?php foreach($moderators as $moderator) { ?>
                    <?php echo $this->renderControllerChild('admin', 'ctypes_moderator', [
                        'moderator' => $moderator,
                        'not_use_trash' => false,
                        'ctype' => $ctype
                    ]); ?>
                <?php } ?>
            <?php } ?>
        </tbody>
    </table>
</div>

<div id="ctype_moderators_add" class="card mt-0"
    data-url_submit="<?php echo $this->href_to('ctypes', ['moderators', $ctype['id'],  'add']); ?>"
    data-url_delete="<?php echo $this->href_to('ctypes', ['moderators', $ctype['id'],  'delete']); ?>"
    data-url_autocomplete="<?php echo $this->href_to('users', 'autocomplete'); ?>"
    >
    <div class="card-body">
        <h4><?php echo LANG_MODERATOR_ADD; ?></h4>
        <div class="hint text-muted"><?php echo LANG_MODERATOR_ADD_HINT; ?></div>
        <div class="field form-inline mt-3">
            <?php echo html_input('text', 'user_email', '', ['id'=>'user_email', 'autocomplete'=>'off', 'class' => 'mr-4']); ?>
            <?php echo html_button(LANG_ADD, 'add', 'return icms.adminModerators.add(this)', ['id'=>'submit', 'class' => 'button-submit btn-primary']); ?>
        </div>
    </div>
</div>