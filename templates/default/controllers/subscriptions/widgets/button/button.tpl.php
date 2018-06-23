<div class="subscriptions_buttons">
<?php foreach ($buttons as $button) { ?>
    <div class="subscriptions_buttons_item">
        <div class="subscriptions_buttons_title">
            <?php echo $button['title']; ?>
        </div>
        <div class="subscriptions_buttons_button">
            <?php echo $button['button']; ?>
        </div>
    </div>
<?php } ?>
</div>