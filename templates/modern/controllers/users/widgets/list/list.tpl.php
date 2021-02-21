<div class="widget_profiles_list<?php if ($style=='tiles'){ ?> d-flex flex-wrap mr-n2 mb-n2<?php } ?>">
    <?php foreach($profiles as $profile){ ?>
        <?php $url = href_to_profile($profile); ?>

        <?php if ($style=='list'){ ?><div class="item media mb-3 align-items-center"><?php } ?>

            <?php if (!empty($fields['avatar']) && $fields['avatar']['is_in_list']){ ?>
                <a href="<?php echo $url; ?>" class="icms-user-avatar<?php if ($style=='list'){ ?>  mr-3<?php } else { ?> mr-2 mb-2<?php } ?> <?php if (!empty($profile['is_online'])){ ?>peer_online<?php } else { ?>peer_no_online<?php } ?>" title="<?php html($profile['nickname']); ?>">
                <?php if($profile['avatar']){ ?>
                    <?php echo html_avatar_image($profile['avatar'], $fields['avatar']['options']['size_teaser'], $profile['nickname']); ?>
                <?php } else { ?>
                    <?php echo html_avatar_image_empty($profile['nickname'], 'avatar__inlist'); ?>
                <?php } ?>
                </a>
            <?php } ?>
            <?php if ($style=='list'){ ?>
                <div class="media-body text-truncate">
                    <?php if (!empty($fields['nickname']) && $fields['nickname']['is_in_list']){ ?>
                        <h5 class="my-0">
                            <a href="<?php echo $url; ?>">
                                <?php html($profile['nickname']); ?>
                            </a>
                        </h5>
                    <?php } ?>
                    <?php if (!empty($profile['fields'])){ ?>
                    <div class="fields mt-2">
                        <?php foreach($profile['fields'] as $field){ ?>
                            <div class="field ft_<?php echo $field['type']; ?> f_<?php echo $field['name']; ?>">
                                <?php if ($field['label_pos'] != 'none'){ ?>
                                    <div class="title_<?php echo $field['label_pos']; ?>">
                                        <?php echo $field['title'] . ($field['label_pos']=='left' ? ': ' : ''); ?>
                                    </div>
                                <?php } ?>
                                <div class="value">
                                    <?php echo $field['html']; ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <?php } ?>
                </div>
            <?php } ?>
        <?php if ($style=='list'){ ?></div><?php } ?>

    <?php } ?>
</div>