<fieldset>
    <legend><?php echo LANG_CAPTCHA_CODE; ?></legend>
    <div class="field">
        <script>
            var RecaptchaOptions = {
                theme : '<?php echo $theme; ?>',
                lang : '<?php echo $lang; ?>'
            };
        </script>        
        <?php echo recaptcha_get_html($public_key); ?>
    </div>
</fieldset>