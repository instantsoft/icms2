<?php

    $this->addTplJSName('users');

    $this->setPageTitle(LANG_USERS_EDIT_PROFILE);

    if($this->controller->listIsAllowed()){
        $this->addBreadcrumb(LANG_USERS, href_to('users'));
    }
    $this->addBreadcrumb($profile['nickname'], href_to_profile($profile));

    $this->addToolButton(array(
        'class' => 'save',
        'title' => LANG_SAVE,
        'href'  => "javascript:icms.forms.submit()"
    ));

    $this->addToolButton(array(
        'class' => 'cancel',
        'title' => LANG_CANCEL,
        'href'  => $cancel_url
    ));

    $this->addBreadcrumb(LANG_USERS_EDIT_PROFILE);

    $this->renderChild('profile_edit_header', array('profile'=>$profile));

    $append_html = '';

    if($allow_delete_profile){

        ob_start(); ?>

        <div class="buttons_delete_profile">
            <?php echo html_button(LANG_USERS_DELETE_PROFILE, 'delete_profile', "icms.users.delete('".href_to_profile($profile, ['delete'])."', '".LANG_USERS_DELETE_PROFILE."');", array('class'=>'delete_profile')); ?>
        </div>

        <?php $append_html = ob_get_clean(); ?>

    <?php } ?>

<?php
    if(!empty($profile['id']) && $profile['slug'] == $profile['id']){ $profile['slug'] = null; }
    $this->renderForm($form, $profile, array(
        'action'      => '',
        'append_html' => $append_html,
        'cancel'      => array('show' => true, 'href' => $cancel_url),
        'method'      => 'post',
        'toolbar'     => false
    ), $errors);
