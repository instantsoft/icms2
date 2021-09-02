<?php if ($items){ ?>
    <?php if(!$is_ajax){ ?>
        <div class="list_subscriptions_wrap striped-list list-32">
    <?php } ?>
        <?php foreach($items as $item){ ?>
            <div class="item media mb-3 align-items-center">
                <a href="<?php echo href_to_profile($item['user']); ?>" class="icms-user-avatar mr-3 <?php if (!empty($item['user']['is_online'])){ ?>peer_online<?php } else { ?>peer_no_online<?php } ?>">
                    <?php if($item['user']['avatar']){ ?>
                        <?php echo html_avatar_image($item['user']['avatar'], $fields['avatar']['options']['size_teaser'], $item['user']['nickname'], $item['user']['is_deleted']); ?>
                    <?php } else { ?>
                        <?php echo html_avatar_image_empty($item['user']['nickname'], 'avatar__inlist'); ?>
                    <?php } ?>
                </a>
                <div class="media-body text-truncate">
                    <span>
                        <?php html($item['user']['nickname']); ?>
                    </span>
                    <div class="fields">
                        <a href="<?php echo rel_to_href($item['subject_url']); ?>"><?php echo $item['title']; ?></a>
                    </div>
                </div>
                <?php if($item['user']['id'] == $user->id){ ?>
                    <div class="actions">
                        <div class="subscribe_wrap float-right position-relative ml-2 d-flex">
                            <a href="#" class="btn subscriber" data-hash="<?php echo $item['hash']; ?>" data-link0="<?php echo $this->href_to('subscribe'); ?>" data-link1="<?php echo $this->href_to('unsubscribe'); ?>" data-text0="<?php echo LANG_USERS_SUBSCRIBE; ?>" data-text1="<?php echo LANG_USERS_UNSUBSCRIBE; ?>" data-issubscribe="1" data-target="<?php html(json_encode($item['target'])); ?>">
                                <b class="icon-bell">
                                    <?php html_svg_icon('solid', 'bell'); ?>
                                </b>
                                <b class="icon-bell-slash">
                                    <?php html_svg_icon('solid', 'bell-slash'); ?>
                                </b>
                                <span class="d-none d-sm-inline-block"></span>
                            </a>
                            <span title="<?php echo LANG_SBSCR_SUBSCRIBERS; ?>" class="count-subscribers btn btn-outline-secondary position-relative ml-2" data-list_link="<?php echo $this->href_to('list_subscribers', $item['hash']); ?>">
                                <?php echo $item['subscribers_count']; ?>
                            </span>
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="actions text-muted">
                        <?php echo html_spellcount($item['subscribers_count'], LANG_SUBSCRIBERS_SPELL); ?>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    <?php if(!$is_ajax){ ?>
        </div>
    <?php } ?>
    <?php if(!$is_ajax){ ?>
        <?php $this->addTplJSName('subscriptions'); ?>
        <?php $this->renderAsset('ui/pagebar', array('has_next' => $has_next, 'page' => $page, 'base_url' => $base_url)); ?>
    <?php } ?>
<?php }
