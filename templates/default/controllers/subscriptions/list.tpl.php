<?php if ($items){ ?>
    <?php if(!$is_ajax){ ?>
        <div class="list_subscriptions_wrap striped-list list-32">
    <?php } ?>
        <?php foreach($items as $item){ ?>
            <div class="item">
                <div class="icon">
                    <a href="<?php echo href_to_profile($item['user']); ?>" <?php if (!empty($item['user']['is_online'])){ ?>class="peer_online" title="<?php echo LANG_ONLINE; ?>"<?php } else { ?> class="peer_no_online"<?php } ?>>
                        <?php echo html_avatar_image($item['user']['avatar'], $fields['avatar']['options']['size_teaser'], $item['user']['nickname'], $item['user']['is_deleted']); ?>
                    </a>
                </div>
                <div class="title">
                    <span>
                        <?php html($item['user']['nickname']); ?>
                    </span>
                    <div class="fields">
                        <a href="<?php echo rel_to_href($item['subject_url']); ?>"><?php echo $item['title']; ?></a>
                    </div>
                </div>
                <?php if($item['user']['id'] == $user->id){ ?>
                    <div class="actions">
                        <div class="subscribe_wrap">
                            <a href="#" class="subscriber" data-hash="<?php echo $item['hash']; ?>" data-link0="<?php echo $this->href_to('subscribe'); ?>" data-link1="<?php echo $this->href_to('unsubscribe'); ?>" data-text0="<?php echo LANG_USERS_SUBSCRIBE; ?>" data-text1="<?php echo LANG_USERS_UNSUBSCRIBE; ?>" data-issubscribe="1" data-target="<?php html(json_encode($item['target'])); ?>"><span></span></a>
                            <div class="spinner"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>
                            <span class="count-subscribers" title="<?php echo LANG_SBSCR_SUBSCRIBERS; ?>" data-list_link="<?php echo $this->href_to('list_subscribers', $item['hash']); ?>"><?php echo $item['subscribers_count']; ?></span>
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="actions">
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
