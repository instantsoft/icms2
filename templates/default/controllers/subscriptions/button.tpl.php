<?php $this->addJS( $this->getJavascriptFileName('subscriptions') ); ?>
<div class="subscribe_wrap">
    <a href="#" class="subscriber" data-hash="<?php echo $hash; ?>" data-link0="<?php echo $this->href_to('subscribe'); ?>" data-link1="<?php echo $this->href_to('unsubscribe'); ?>" data-text0="<?php echo LANG_USERS_SUBSCRIBE; ?>" data-text1="<?php echo LANG_USERS_UNSUBSCRIBE; ?>" data-issubscribe="<?php echo (int)$user_is_subscribed; ?>" data-target="<?php html(json_encode($target)); ?>"><span></span></a>
    <div class="spinner"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>
    <span title="<?php echo LANG_SBSCR_SUBSCRIBERS; ?>" class="count-subscribers" data-list_link="<?php echo $this->href_to('list_subscribers', $hash); ?>"><?php echo $subscribers_count; ?></span>
</div>