<?php if ($this->isToolbar()){ ?>
<nav class="cp_toolbar navbar navbar-light bg-light my-3 py-1">
    <?php $this->toolbar(); ?>
</nav>
<?php } ?>

<form action="<?php echo $submit_url; ?>" method="post">

    <div class="datagrid_wrapper perms_grid table-responsive-md">
        <table id="datagrid" class="datagrid table table-striped table-bordered table-hover table-light" cellpadding="0" cellspacing="0" border="0">
            <thead>
                <tr>
                    <th><?php echo LANG_PERM_RULE; ?></th>
                    <?php foreach($groups as $group){ ?>
                        <th class="center"><?php echo $group['title']; ?></th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach($rules as $rule){ ?>
                    <tr>
                        <td>
                            <div>
                            <?php echo $rule['title']; ?>
                            <?php if(!empty($rule['title_hint'])){ ?>
                                <div class="hint"><?php echo $rule['title_hint']; ?></div>
                            <?php } ?>
                            </div>
                        </td>

                        <?php foreach($groups as $group){ ?>

                            <?php if($group['id'] == GUEST_GROUP_ID && empty($rule['show_for_guest_group'])){ ?>
                                <td class="center"></td>
                            <?php continue; } ?>

                            <?php
                                $default =  isset($values[$rule['id']][$group['id']]) ?
                                            $values[$rule['id']][$group['id']] :
                                            null;
                            ?>

                            <td class="center" data-label="<?php html($group['title']); ?>">
                                <?php if ($rule['type'] == 'flag'){ ?>
                                    <label class="switch switch-pill switch-primary m-0 align-middle">
                                        <?php echo html_checkbox("value[{$rule['id']}][{$group['id']}]", $default, 1, array('class' => 'switch-input')); ?>
                                        <span class="switch-slider"></span>
                                    </label>
                                <?php } ?>
                                <?php if ($rule['type'] == 'list'){ ?>
                                    <?php echo html_select("value[{$rule['id']}][{$group['id']}]", $rule['options'], $default); ?>
                                <?php } ?>
                                <?php if ($rule['type'] == 'number'){ ?>
                                    <?php echo html_input('text', "value[{$rule['id']}][{$group['id']}]", $default, array('class'=>'input-number')); ?>
                                <?php } ?>
                            </td>

                        <?php } ?>

                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <div class="buttons  my-3">
        <?php echo html_submit(LANG_SAVE); ?>
    </div>

</form>