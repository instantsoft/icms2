<?php $captcha_id = md5(microtime(true)); ?>
<div class="recaptcha_wrap">
    <div id="<?php echo $captcha_id; ?>" class="g-recaptcha"></div>
</div>
<?php ob_start(); ?>
<script async src="https://www.google.com/recaptcha/api.js?onload=onload<?php echo $captcha_id; ?>Callback&render=explicit&hl=<?php echo $this->controller->options['lang'] ? $this->controller->options['lang'] : cmsCore::getLanguageName(); ?>"></script>
<script>
    var onload<?php echo $captcha_id; ?>Callback = function() {
        grecaptcha.render('<?php echo $captcha_id; ?>', {
            sitekey: '<?php echo $this->controller->options['public_key']; ?>',
            theme: '<?php echo $this->controller->options['theme']; ?>',
            size: '<?php echo $this->controller->options['size']; ?>'
        });
    };
</script>
<?php $this->addBottom(ob_get_clean()); ?>
