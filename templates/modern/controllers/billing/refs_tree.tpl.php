<div class="billing-ref-tree row no-gutters m-n1">
    <?php $last_level = false; ?>
    <?php foreach (array_reverse($refs) as $ref) { ?>
        <?php $level = $ref['level']; ?>
        <?php if ($last_level === false || $last_level != $level) { ?>
            <?php $last_level = $level; ?>
            <div class="w-100"></div>
        <?php } ?>
            <div class="col">
                <a href="<?php echo href_to_profile($ref); ?>" class="bg-light text-dark text-center d-block m-1 p-2 rounded">
                    <?php html($ref['nickname']); ?>
                </a>
            </div>
    <?php } ?>
</div>