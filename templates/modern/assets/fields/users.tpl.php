<?php if ($field->title) { ?><label for="<?php echo $field->id; ?>"><?php echo $field->title; ?></label><?php } ?>
<div class="input-users-list">
    <?php foreach($value as $user){ ?>
        <div class="custom-control custom-checkbox d-flex align-items-center mb-3">
            <input type="checkbox" name="<?php echo $field->element_name; ?>[]" class="custom-control-input" id="cb-user-<?php echo $user['id']; ?>">
            <label class="custom-control-label d-flex align-items-center" for="cb-user-<?php echo $user['id']; ?>">
                <div class="avatar icms-user-avatar mr-2">
                    <?php if($user['avatar']){ ?>
                        <?php echo html_avatar_image($user['avatar'], 'micro', $user['nickname']); ?>
                    <?php } else { ?>
                        <?php echo html_avatar_image_empty($user['nickname'], 'avatar__mini'); ?>
                    <?php } ?>
                </div>
                <div class="name">
                    <?php html($user['nickname']); ?>
                </div>
            </label>
        </div>
    <?php } ?>
</div>