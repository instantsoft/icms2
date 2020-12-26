<h1><?php echo LANG_STEP_DATABASE; ?></h1>

<p><?php echo LANG_DATABASE_INFO; ?></p>

<form id="step-form">

    <fieldset>

        <div class="field">
            <label><?php echo LANG_DATABASE_HOST; ?></label>
            <input type="text" autocomplete="off" class="input input-icon icon-db-server" name="db[host]" value="<?php echo !empty($cfg['db_host']) ? htmlspecialchars($cfg['db_host']) : 'localhost'; ?>" />
        </div>

        <div class="field">
            <label><?php echo LANG_DATABASE_USER; ?></label>
            <input type="text" autocomplete="off" class="input input-icon icon-user" name="db[user]" value="<?php echo !empty($cfg['db_user']) ? htmlspecialchars($cfg['db_user']) : ''; ?>" />
        </div>

        <div class="field">
            <label><?php echo LANG_DATABASE_PASS; ?></label>
            <input type="password" autocomplete="off" class="input input-icon icon-password" name="db[pass]" value="<?php echo !empty($cfg['db_pass']) ? htmlspecialchars($cfg['db_pass']) : ''; ?>" />
        </div>

        <div class="field">
            <?php if(!$db_list){ ?>
                <div class="hint"><label><input type="checkbox" value="1" name="db[create_db]"> <?php echo LANG_DATABASE_BASE_HINT; ?></label></div>
            <?php } ?>
            <label><?php echo LANG_DATABASE_BASE; ?></label>
            <?php if($db_list){ ?>
                <select class="input" name="db[base]">
                    <?php foreach ($db_list as $db_name) { ?>
                        <option value="<?php echo $db_name; ?>" <?php if(@$cfg['db_base'] == $db_name){?>selected="selected"<?php } ?>><?php echo $db_name; ?></option>
                    <?php } ?>
                </select>
            <?php } else { ?>
                <input type="text" autocomplete="off" class="input input-icon icon-db" name="db[base]" value="<?php echo !empty($cfg['db_base']) ? htmlspecialchars($cfg['db_base']) : ''; ?>" />
            <?php } ?>
        </div>

        <div class="field">
            <label><?php echo LANG_DATABASE_CHARSET; ?></label>
            <select class="input" name="db[db_charset]">
                <option value="utf8" <?php if(@$cfg['db_charset'] == 'utf8'){?>selected="selected"<?php } ?>>UTF8</option>
                <option value="utf8mb4" <?php if(@$cfg['db_charset'] == 'utf8mb4'){?>selected="selected"<?php } ?>>UTF8mb4</option>
            </select>
        </div>

        <div class="field">
            <div class="hint"><?php echo LANG_DATABASE_ENGINE_HINT; ?></div>
            <label><?php echo LANG_DATABASE_ENGINE; ?></label>
            <select class="input" name="db[engine]">
                <option value="InnoDB" <?php if(@$cfg['db_engine'] == 'InnoDB'){?>selected="selected"<?php } ?>>InnoDB</option>
                <option value="MyISAM" <?php if(@$cfg['db_engine'] == 'MyISAM'){?>selected="selected"<?php } ?>>MyISAM</option>
            </select>
        </div>

        <div class="field">
            <label><?php echo LANG_DATABASE_PREFIX; ?></label>
            <input autocomplete="off" type="text" class="input input-icon icon-db-prefix" name="db[prefix]" value="<?php echo !empty($cfg['db_prefix']) ? htmlspecialchars($cfg['db_prefix']) : 'cms_'; ?>" />
        </div>

        <div class="field">

            <label><?php echo LANG_DATABASE_USERS_TABLE; ?></label>

            <div class="opt-group">
                <label>
                    <input type="radio" name="db[users_exists]" value="0" checked onclick="$('#users_table').hide()" />
                    <?php echo LANG_DATABASE_USERS_TABLE_NEW; ?>
                </label>

                <label>
                    <input type="radio" name="db[users_exists]" value="1" onclick="$('#users_table').show()"  />
                    <?php echo LANG_DATABASE_USERS_TABLE_OLD; ?>
                </label>
            </div>

            <input autocomplete="off" type="text" class="input input-icon icon-db-table" id="users_table" name="db[users_table]" value="cms_users" style="display:none" />

        </div>

        <div class="field">
            <label>
                <input type="checkbox" value="1" checked="true" name="db[is_install_demo_content]">
                <?php echo LANG_DATABASE_INSTALL_DEMO; ?>
            </label>
        </div>

    </fieldset>

</form>


<div class="buttons">
    <input type="button" name="next" id="btn-next" value="<?php echo LANG_NEXT; ?>" onclick="submitStep()" />
</div>