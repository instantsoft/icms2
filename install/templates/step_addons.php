<h1><?php echo LANG_STEP_ADDONS; ?></h1>

<p class="warning"><?php echo LANG_STEP_ADDONS_WARN; ?></p>

<p><?php echo LANG_STEP_ADDONS_HINT; ?></p>
<form id="step-form">
<ul>
    <?php foreach ($addons as $addon) { ?>
        <li>
            <?php echo htmlspecialchars($addon['title']); ?> v<?php echo htmlspecialchars($addon['version']); ?>
            <?php if ($addon['author']) { ?>
                <?php echo LANG_FROM; ?>
                <?php if ($addon['url']) { ?>
                    <a href="<?php echo htmlspecialchars($addon['url']); ?>" target="_blank"><?php echo htmlspecialchars($addon['author']); ?></a>
                <?php } else {?>
                    <?php echo htmlspecialchars($addon['author']); ?>
                <?php } ?>
            <?php } ?>
        </li>
    <?php } ?>
</ul>
</form>

<div class="buttons">
    <input type="button" name="next" id="btn-next" value="<?php echo LANG_NEXT; ?>" onclick="submitStep('steps/addons_install.php')" />
</div>