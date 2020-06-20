<?php foreach ($roles as $role_id => $title) { ?>
    <div class="item media mb-3 align-items-center" id="role-<?php echo $role_id; ?>">
        <div class="media-body">
            <h5 class="role_title my-1">
                <?php html($title); ?>
            </h5>
            <div class="role_title_edit form-inline d-none">
                <?php echo html_input('text', 'role_title', $title, ['class' => 'form-control-sm']); ?>
                <button type="button" class="button btn btn-primary btn-sm ml-2 icms-group-roles__save" name="" value="" onclick="icms.groups.submitRole(<?php echo $role_id; ?>)">
                    <span><?php echo LANG_SAVE; ?></span>
                </button>
            </div>
        </div>
        <div class="actions">
            <a class="ajaxlink btn btn-secondary btn-sm icms-group-roles__edit" href="#" onclick="icms.groups.editRole(<?php echo $role_id; ?>)">
                <?php html_svg_icon('solid', 'pen'); ?>
                <?php echo LANG_EDIT; ?>
            </a>
            <a class="ajaxlink btn btn-danger btn-sm icms-group-roles__delete" href="#" onclick="icms.groups.deleteRole(<?php echo $role_id; ?>)">
                <?php html_svg_icon('solid', 'window-close'); ?>
                <span class="d-none d-md-inline-block"><?php echo LANG_DELETE; ?></span>
            </a>
        </div>
    </div>
<?php } ?>