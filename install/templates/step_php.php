<h1><?php echo LANG_STEP_PHP_CHECK; ?></h1>

<h2><?php echo LANG_PHP_VERSION; ?></h2>
<p><?php echo LANG_PHP_VERSION_REQ ?></p>
<table class="grid">
    <tr>
        <td><?php echo LANG_PHP_VERSION_DESC; ?></td>
        <td class="value">
            <?php echo html_bool_span($info['php']['version'], $info['php']['valid']); ?>
        </td>
    </tr>
</table>

<h2><?php echo LANG_PHP_EXTENSIONS; ?></h2>
<p><?php echo LANG_PHP_EXTENSIONS_REQ ?></p>

<table class="grid">
    <?php foreach($info['ext'] as $name=>$valid) { ?>
    <tr>
        <td><?php echo $name; ?></td>
        <td class="value">
            <?php if ($valid) { ?>
                <?php echo html_bool_span(LANG_PHP_EXT_INSTALLED, $valid); ?>
            <?php } else { ?>
                <?php echo html_bool_span(LANG_PHP_EXT_NOT_INSTALLED, $valid); ?>
            <?php } ?>
        </td>
    </tr>
    <?php } ?>
</table>

<h2><?php echo LANG_PHP_EXTENSIONS_EXTRA; ?></h2>
<p><?php echo LANG_PHP_EXTENSIONS_EXTRA_REQ ?></p>

<table class="grid">
    <?php foreach($info['ext_extra'] as $name=>$valid) { ?>
    <tr>
        <td><?php echo $name; ?></td>
        <td class="value">
            <?php if ($valid) { ?>
                <span class="positive"><?php echo LANG_PHP_EXT_INSTALLED; ?></span>
            <?php } else { ?>
                <span class="neutral"><?php echo LANG_PHP_EXT_NOT_INSTALLED; ?></span>
            <?php } ?>
        </td>
    </tr>
    <?php } ?>
</table>

<?php if($info['valid']){ ?>
    <div class="buttons">
        <input type="button" name="next" id="btn-next" value="<?php echo LANG_NEXT; ?>" onclick="nextStep()" />
    </div>
<?php } else { ?>
    <p>
        <span class="negative"><?php echo LANG_PHP_CHECK_ERROR; ?></span>
        <?php echo LANG_PHP_CHECK_ERROR_HINT; ?>
    </p>
<?php } ?>