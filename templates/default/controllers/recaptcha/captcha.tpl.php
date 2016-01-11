<fieldset class="captcha_data">
    <legend><?php echo LANG_CAPTCHA_CODE; ?></legend>
    <div class="field">
        <div class="g-recaptcha" data-theme="<?php echo $this->controller->options['theme']; ?>" data-size="<?php echo $this->controller->options['size']; ?>" data-callback="captcha_ready" data-sitekey="<?php echo $this->controller->options['public_key']; ?>"></div>
        <script async defer type="text/javascript" src="https://www.google.com/recaptcha/api.js?hl=<?php echo $this->controller->options['lang']; ?>"></script>
    </div>
</fieldset>