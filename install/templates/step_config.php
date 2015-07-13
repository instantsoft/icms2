<h1><?php echo LANG_STEP_CONFIG; ?></h1>

<p><?php echo LANG_CONFIG_INFO; ?></p>
<p>
    <?php echo LANG_CONFIG_PATH; ?><br/>
    <b><?php echo $path; ?></b>
</p>

<p><?php echo LANG_CONFIG_MUST_WRITABLE; ?></p>
<p><?php echo LANG_CONFIG_AFTER; ?></p>


<form id="step-form"></form>

<div class="buttons">
    <input type="button" name="next" id="btn-next" value="<?php echo LANG_NEXT; ?>" onclick="submitStep()" />
</div>
