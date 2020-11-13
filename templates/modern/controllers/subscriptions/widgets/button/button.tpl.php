<div class="subscriptions_buttons">
<?php foreach ($buttons as $button) { ?>
    <div class="subscriptions_buttons_item <?php if(!empty($no_first)){ ?>mt-3<?php } ?>">
        <div class="subscriptions_buttons_title">
            <?php echo $button['title']; ?>
        </div>
        <div class="subscriptions_buttons_button">
            <?php echo $button['button']; ?>
        </div>
    </div>
    <?php $no_first = true; ?>
<?php } ?>
</div>