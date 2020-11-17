<h1><?php echo LANG_STEP_SITE; ?></h1>

<form id="step-form">

    <fieldset>

        <div class="field">
            <label><?php echo LANG_SITE_SITENAME; ?></label>
            <input autocomplete="off" type="text" class="input input-icon icon-text" name="sitename" value="<?php echo LANG_CFG_SITENAME; ?>" />
        </div>
        <div class="field">
            <label><?php echo LANG_SITE_HOMETITLE; ?></label>
            <input autocomplete="off" type="text" class="input input-icon icon-text" name="hometitle" value="<?php echo LANG_CFG_SITENAME; ?>" />
        </div>
        <div class="field">
            <label><?php echo LANG_SITE_METAKEYS; ?></label>
            <input autocomplete="off" type="text" class="input input-icon icon-text" name="metakeys" value="<?php echo LANG_CFG_METAKEYS; ?>" />
        </div>
        <div class="field">
            <label><?php echo LANG_SITE_METADESC; ?></label>
            <textarea name="metadesc" rows="4"><?php echo LANG_CFG_METADESC; ?></textarea>
        </div>
        <div class="field">
            <label><?php echo LANG_SITE_TEMPLATE; ?></label>
            <select class="input" name="template">
                <?php foreach ($site_tpls as $tpl) { ?>
                    <option value="<?php echo $tpl; ?>" <?php if($default_template == $tpl){?>selected="selected"<?php } ?>><?php echo $tpl; ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="field">
            <label><?php echo LANG_SITE_TEMPLATE_ADMIN; ?></label>
            <select class="input" name="template_admin">
                <?php foreach ($admin_tpls as $tpl) { ?>
                    <option value="<?php echo $tpl; ?>" <?php if($default_atemplate == $tpl){?>selected="selected"<?php } ?>><?php echo $tpl; ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="field">
            <label>
                <input type="checkbox" value="1" checked="checked" name="is_check_updates">
                <?php echo LANG_SITE_CHECK_UPDATE; ?>
            </label>
        </div>

    </fieldset>

</form>

<div class="buttons">
    <input type="button" name="next" id="btn-next" value="<?php echo LANG_NEXT; ?>" onclick="submitStep()" />
</div>