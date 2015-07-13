<h1><?php echo LANG_STEP_SITE; ?></h1>

<form id="step-form">

    <fieldset>

        <div class="field">
            <label><?php echo LANG_SITE_SITENAME; ?></label>
            <input type="text" class="input input-icon icon-text" name="sitename" value="<?php echo LANG_CFG_SITENAME; ?>" />
        </div>
        <div class="field">
            <label><?php echo LANG_SITE_HOMETITLE; ?></label>
            <input type="text" class="input input-icon icon-text" name="hometitle" value="<?php echo LANG_CFG_SITENAME; ?>" />
        </div>
        <div class="field">
            <label><?php echo LANG_SITE_METAKEYS; ?></label>
            <input type="text" class="input input-icon icon-text" name="metakeys" value="<?php echo LANG_CFG_METAKEYS; ?>" />
        </div>
        <div class="field">
            <label><?php echo LANG_SITE_METADESC; ?></label>
            <textarea name="metadesc" rows="4"><?php echo LANG_CFG_METADESC; ?></textarea>
        </div>

    </fieldset>

</form>

<div class="buttons">
    <input type="button" name="next" id="btn-next" value="<?php echo LANG_NEXT; ?>" onclick="submitStep()" />
</div>

