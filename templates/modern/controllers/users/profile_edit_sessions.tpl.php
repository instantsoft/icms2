<?php

    $this->setPageTitle(LANG_USERS_SESSIONS);

    if($this->controller->listIsAllowed()){
        $this->addBreadcrumb(LANG_USERS, href_to('users'));
    }
    $this->addBreadcrumb($profile['nickname'], href_to_profile($profile));
    $this->addBreadcrumb(LANG_USERS_EDIT_PROFILE, href_to_profile($profile, ['edit']));
    $this->addBreadcrumb(LANG_USERS_SESSIONS);

    $this->renderChild('profile_edit_header', ['profile' => $profile]);
?>
<div class="alert alert-info">
    <?php echo LANG_SESSIONS_HINT; ?>
</div>
<div class="table-responsive-sm">
    <table class="table table-hover table-striped">
        <thead>
            <th><?php echo LANG_SESS_TYPE; ?></th>
            <th><?php echo LANG_SESS_LAST_DATE; ?></th>
            <th><?php echo LANG_SESS_IP; ?></th>
            <th class="actions"></th>
        </thead>
        <?php if($sessions){ ?>
            <?php foreach ($sessions as $session) { ?>
                <tr>
                    <td class="align-middle">
                        <?php echo string_lang('LANG_SESS_'.$session['access_type']['type']); ?>
                    </td>
                    <td class="align-middle">
                        <?php echo string_date_age_max($session['date_log'], true); ?>
                    </td>
                    <td class="align-middle">
                        <a rel="noopener noreferrer" target="_blank" href="https://apps.db.ripe.net/db-web-ui/query?searchtext=<?php echo $session['ip']; ?>">
                            <?php echo $session['ip']; ?>
                        </a>
                        <?php if($session['ip_location']) { ?>
                            <div class="text-muted small"><?php echo $session['ip_location']; ?></div>
                        <?php } ?>
                    </td>
                    <td class="align-middle">
                        <a class="text-danger" href="<?php echo href_to_profile($profile, ['edit', 'sessions_delete', $session['id']]).'?csrf_token='.cmsForm::getCSRFToken(); ?>" onclick="if(!confirm('<?php echo LANG_SESS_DROP_CONFIRM; ?>')){ return false; }">
                            <?php echo LANG_SESS_DROP; ?>
                        </a>
                    </td>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr class="table-secondary">
                <td colspan="4">
                    <?php echo LANG_SESS_NOT_FOUND; ?>
                </td>
            </tr>
        <?php } ?>
    </table>
</div>