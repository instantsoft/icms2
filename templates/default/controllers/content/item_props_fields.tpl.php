<?php foreach($props as $prop) { ?>
    <div class="field">
        <?php
            $value = isset($values[$prop['id']]) ? $values[$prop['id']] : '';
            $field = $fields[$prop['id']];
            echo $field->getInput($value);
        ?>
    </div>
<?php } ?>
