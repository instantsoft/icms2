<?php

    $this->setPageTitle($tab['title'], $profile['nickname']);
    $this->setPageDescription($profile['nickname'].' — '.$tab['title']);

    if($this->controller->listIsAllowed()){
        $this->addBreadcrumb(LANG_USERS, href_to('users'));
    }
    $this->addBreadcrumb($profile['nickname'], href_to_profile($profile));
    $this->addBreadcrumb($tab['title']);

?>

<div id="user_profile_header">
    <?php $this->renderChild('profile_header', ['profile'=>$profile, 'meta_profile' => $meta_profile, 'tabs'=>$tabs]); ?>
</div>

<div id="users_karma_log_window">

    <?php if ($log){ ?>

        <div id="users_karma_log_list" class="striped-list list-32">

            <?php foreach($log as $entry){ ?>

                <div class="item">

                    <div class="icon">
                        <?php echo html_avatar_image($entry['user']['avatar'], 'micro', $entry['user']['nickname']); ?>
                    </div>

                    <div class="value <?php echo html_signed_class($entry['points']); ?>">
                        <span>
                            <?php echo html_signed_num($entry['points']); ?>
                        </span>
                    </div>

                    <div class="title<?php if ($entry['comment']){ ?>-multiline<?php } ?>">

                        <a href="<?php echo href_to_profile($entry['user']); ?>"><?php html($entry['user']['nickname']); ?></a>
                        <span class="date"><?php echo string_date_age_max($entry['date_pub'], true); ?></span>

                        <?php if ($entry['comment']){ ?>
                            <div class="comment">
                                <?php html($entry['comment']); ?>
                            </div>
                        <?php } ?>
                    </div>

                </div>

            <?php } ?>

        </div>

    <?php } ?>

    <?php if (!$log){ ?>
        <p><?php echo LANG_USERS_KARMA_LOG_EMPTY; ?></p>
    <?php } ?>

</div>

<?php if ($perpage < $total) { ?>
    <?php echo html_pagebar($page, $perpage, $total); ?>
<?php } ?>