<h1><?php echo LANG_STEP_START; ?></h1>

<div class="image">
    <img src="images/install.png" />
</div>

<div id="langs">
    <?php foreach($langs as $id){ ?>
        <a <?php if ($id==$lang) { ?>class="selected"<?php } ?> style="background-image:url('languages/<?php echo $id; ?>/flag.png')" href="?lang=<?php echo $id; ?>"><?php echo mb_strtoupper($id); ?></a>
    <?php } ?>
</div>

<p><?php echo LANG_STEP_START_1; ?></p>

<p><?php echo LANG_STEP_START_2; ?></p>

<p><?php echo LANG_STEP_START_3; ?></p>

<p>
    <?php echo LANG_MANUAL; ?>
</p>

<div class="buttons">
    <input type="button" name="next" id="btn-next" value="<?php echo LANG_NEXT; ?>" onclick="nextStep()" />
</div>
