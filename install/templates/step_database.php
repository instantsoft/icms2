<h1><?php echo LANG_STEP_DATABASE; ?></h1>

<p><?php echo LANG_DATABASE_INFO; ?><br>
<?php echo LANG_DATABASE_CHARSET_INFO; ?></p>

<form id="step-form">

    <fieldset>

        <div class="field">
            <label><?php echo LANG_DATABASE_HOST; ?></label>
            <input type="text" class="input input-icon icon-db-server" name="db[host]" value="localhost" />
        </div>

        <div class="field">
            <label><?php echo LANG_DATABASE_USER; ?></label>
            <input type="text" class="input input-icon icon-user" name="db[user]" value="" />
        </div>

        <div class="field">
            <label><?php echo LANG_DATABASE_PASS; ?></label>
            <input type="password" class="input input-icon icon-password" name="db[pass]" value="" />
        </div>

        <div class="field">
            <label><?php echo LANG_DATABASE_BASE; ?></label>
            <input type="text" class="input input-icon icon-db" name="db[base]" value="" />
        </div>

        <div class="field">
            <div class="hint"><?php echo LANG_DATABASE_ENGINE_HINT; ?></div>
            <label><?php echo LANG_DATABASE_ENGINE; ?></label>
            <select class="input" name="db[engine]">
                <option value="MyISAM">MyISAM</option>
                <option value="InnoDB">InnoDB</option>
            </select>
        </div>

        <div class="field">
            <label><?php echo LANG_DATABASE_PREFIX; ?></label>
            <input type="text" class="input input-icon icon-db-prefix" name="db[prefix]" value="cms_" />
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

            <input type="text" class="input input-icon icon-db-table" id="users_table" name="db[users_table]" value="cms_users" style="display:none" />

        </div>

        <div class="field">
            <label>
                <input type="checkbox" value="1" name="db[is_install_demo_content]">
                <?php echo LANG_DATABASE_INSTALL_DEMO; ?>
            </label>
        </div>

    </fieldset>

</form>


<div class="buttons">
    <input type="button" name="next" id="btn-next" value="<?php echo LANG_NEXT; ?>" onclick="submitStep()" />
</div>

