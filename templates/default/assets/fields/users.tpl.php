<?php if ($field->title) { ?><label for="<?php echo $field->id; ?>"><?php echo $field->title; ?></label><?php } ?>

<div class="input-users-list">

    <ul>
        <?php foreach($value as $user){ ?>

            <li class="profile">
                <div class="checkbox">
                    <?php echo html_checkbox($field->element_name . '[]', false, $user['id'], array('id' => "cb-user-{$user['id']}")); ?>
                </div>
                <div class="item">
                    <label for="cb-user-<?php echo $user['id']; ?>">
                        <div class="avatar">
                            <?php echo html_avatar_image($user['avatar'], 'micro', $user['nickname']); ?>
                        </div>
                        <div class="name">
                            <?php html($user['nickname']); ?>
                        </div>
                    </label>
                </div>
            </li>

        <?php } ?>
    </ul>

</div>
