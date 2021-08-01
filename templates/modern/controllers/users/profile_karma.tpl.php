<?php

    $this->setPageTitle($tab['title'], $profile['nickname']);
    $this->setPageDescription($profile['nickname'].' — '.$tab['title']);

    if($this->controller->listIsAllowed()){
        $this->addBreadcrumb(LANG_USERS, href_to('users'));
    }
    $this->addBreadcrumb($profile['nickname'], href_to_profile($profile));
    $this->addBreadcrumb($tab['title']);

?>

<?php $this->renderChild('profile_header', ['profile' => $profile, 'meta_profile' => $meta_profile, 'tabs' => $tabs, 'fields' => $fields]); ?>

<div id="users_karma_log_window">

    <?php if ($log){ ?>

        <div id="users_karma_log_list" class="striped-list my-3 my-md-4">

            <?php foreach($log as $entry){ ?>
                <div class="item media mb-3 align-items-center">

                    <?php if (!empty($fields['avatar']) && $fields['avatar']['is_in_list']){ ?>
                        <a href="<?php echo href_to_profile($entry['user']); ?>" class="icms-user-avatar mr-3 <?php if (!empty($entry['is_online'])){ ?>peer_online<?php } else { ?>peer_no_online<?php } ?>">
                        <?php if($entry['user']['avatar']){ ?>
                            <?php echo html_avatar_image($entry['user']['avatar'], $fields['avatar']['options']['size_teaser'], $entry['user']['nickname']); ?>
                        <?php } else { ?>
                            <?php echo html_avatar_image_empty($entry['user']['nickname'], 'avatar__inlist'); ?>
                        <?php } ?>
                        </a>
                    <?php } ?>

                    <div class="media-body">
                        <?php if (!empty($fields['nickname']) && $fields['nickname']['is_in_list']){ ?>
                            <h5 class="my-0">
                                <a href="<?php echo href_to_profile($entry['user']); ?>">
                                    <?php html($entry['user']['nickname']); ?>
                                </a>
                            </h5>
                        <?php } ?>
                        <?php if ($entry['comment']){ ?>
                            <div class="fields mt-1">
                                <?php html($entry['comment']); ?>
                            </div>
                        <?php } ?>
                    </div>

                    <div class="actions text-muted text-right">
                        <div class="h3 m-0 <?php echo html_signed_class($entry['points']); ?>">
                            <?php echo html_signed_num($entry['points']); ?>
                        </div>
                        <div class="small">
                            <?php echo string_date_age_max($entry['date_pub'], true); ?>
                        </div>
                    </div>

                </div>
            <?php } ?>

        </div>

    <?php } ?>

    <?php if (!$log){ ?>
        <div class="alert alert-info mt-4" role="alert">
            <?php echo LANG_USERS_KARMA_LOG_EMPTY; ?>
        </div>
    <?php } ?>

</div>

<?php echo html_pagebar($page, $perpage, $total); ?>