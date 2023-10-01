<h1><?php echo LANG_STEP_PATHS; ?></h1>

<p>
    <?php printf(LANG_PATHS_ROOT_INFO, $doc_root); ?>
</p>

<form id="step-form">

    <fieldset>

        <legend><?php echo LANG_PATHS_ROOT; ?></legend>

        <div class="field">
            <label><?php echo LANG_PATHS_ROOT_PATH; ?></label>
            <input autocomplete="off" type="text" class="input input-icon icon-folder" name="paths[root]" value="<?php echo htmlspecialchars($paths['root']); ?>" />
        </div>

        <div class="field">
            <label><?php echo LANG_PATHS_ROOT_HOST; ?></label>
            <input autocomplete="off" type="text" class="input input-icon icon-url" name="hosts[root]" value="<?php echo htmlspecialchars($hosts['root']); ?>" />
        </div>

    </fieldset>

    <fieldset>

        <legend><?php echo LANG_PATHS_UPLOAD; ?></legend>

        <div class="field">
            <div class="hint"><?php echo LANG_PATHS_MUST_WRITABLE; ?></div>
            <label><?php echo LANG_PATHS_UPLOAD_PATH; ?></label>
            <input autocomplete="off" type="text" class="input input-icon icon-folder" name="paths[upload]" value="<?php echo htmlspecialchars($paths['upload']); ?>" />
        </div>

        <div class="field">
            <label><?php echo LANG_PATHS_UPLOAD_HOST; ?></label>
            <input autocomplete="off" type="text" class="input input-icon icon-url" name="hosts[upload]" value="<?php echo htmlspecialchars($hosts['upload']); ?>" />
        </div>

    </fieldset>

    <fieldset>

        <legend><?php echo LANG_PATHS_CACHE; ?></legend>

        <div class="field">
            <div class="hint"><?php echo LANG_PATHS_MUST_WRITABLE; ?></div>
            <label><?php echo LANG_PATHS_CACHE_PATH; ?></label>
            <input autocomplete="off" type="text" class="input input-icon icon-folder" name="paths[cache]" value="<?php echo htmlspecialchars($paths['cache']); ?>" />
        </div>

    </fieldset>

    <fieldset>

        <legend><?php echo LANG_PATHS_SESSION; ?></legend>

        <div class="field">
            <div class="hint"><?php echo LANG_PATHS_MUST_WRITABLE; ?></div>
            <label><?php echo LANG_PATHS_SESSION_PATH; ?></label>
            <input autocomplete="off" type="text" class="input input-icon icon-folder" name="paths[session_save_path]" value="<?php echo htmlspecialchars($paths['session_save_path']); ?>" />
            <?php if($open_basedir_hint){ ?>
                <div class="hint"><?php echo $open_basedir_hint; ?></div>
            <?php } ?>
        </div>

    </fieldset>

</form>

<p><?php echo LANG_PATHS_CHANGE_INFO ?></p>

<div class="buttons">
    <input type="button" name="next" id="btn-next" value="<?php echo LANG_NEXT; ?>" onclick="submitStep()" />
</div>