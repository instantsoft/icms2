<h1><?php echo LANG_STEP_LANGUAGE; ?></h1>

<p><?php echo LANG_LANGUAGE_SELECT_RU; ?></p>
<p><?php echo LANG_LANGUAGE_SELECT_EN; ?></p>

<form id="step-form" action="" method="post">

    <select name="lang">
        <?php foreach($langs as $id=>$name){ ?>
            <option value="<?php echo $id; ?>"><?php echo $name . ' (' . mb_strtoupper($id) . ')'; ?></option>
        <?php } ?>
    </select>

    <p><br/></p>
    <p><br/></p>
    <p><br/></p>

    <div class="buttons">
        <input type="submit" name="next" id="btn-next" value="<?php echo LANG_NEXT; ?>" />
    </div>

</form>
