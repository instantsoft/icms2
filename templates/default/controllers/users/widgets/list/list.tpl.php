<?php if ($profiles){ ?>

    <div class="widget_profiles_list <?php echo $style; ?>">
        <?php $size = $style == 'list' ? 'micro' : 'small'; ?>

        <?php foreach($profiles as $profile) { ?>

            <?php $url = href_to_profile($profile); ?>

            <div class="item">
                <div class="image">
                    <a href="<?php echo $url; ?>" title="<?php html($profile['nickname']); ?>">
                        <?php echo html_avatar_image($profile['avatar'], $size, $profile['nickname']); ?>
                    </a>
                </div>
                <?php if ($style=='list'){ ?>
                    <div class="info">
                        <div class="name">
                            <a href="<?php echo $url; ?>"><?php html($profile['nickname']); ?></a>
                        </div>
                        <?php if (!empty($profile['fields'])){ ?>
                            <div class="fields">
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
            </div>

        <?php } ?>
    </div>

<?php } ?>
