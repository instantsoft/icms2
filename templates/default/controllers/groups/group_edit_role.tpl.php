<?php foreach ($roles as $role_id => $title) { ?>
    <div class="item" id="role-<?php echo $role_id; ?>">
        <div class="title">
            <span class="role_title"><?php html($title); ?></span>
            <span class="role_title_edit">
                <?php echo html_input('text', 'role_title', $title); ?>
                <input type="button" class="button inline_submit" name="" value="<?php echo LANG_SAVE; ?>" onclick="icms.groups.submitRole(<?php echo $role_id; ?>)" />
            </span>
        </div>
        <div class="actions">
            <a class="ajaxlink" href="javascript:" onclick="icms.groups.editRole(<?php echo $role_id; ?>)"><?php echo LANG_EDIT; ?></a>
            <a class="ajaxlink" href="javascript:" onclick="icms.groups.deleteRole(<?php echo $role_id; ?>)"><?php echo LANG_DELETE; ?></a>
            <div class="loading-icon" style="display:none"></div>
        </div>
    </div>
<?php } ?>