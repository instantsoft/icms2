<?php

	$this->addBreadcrumb(LANG_BILLING_CP_PRICES);
	$this->setPageTitle(LANG_BILLING_CP_PRICES);

    $this->addToolButton([
        'icon'  => 'save',
        'class' => 'save',
        'title' => LANG_SAVE,
        'href'  => 'javascript:icms.forms.submit()'
    ]);

	$this->addToolButton([
		'class' => 'refresh',
		'title' => LANG_BILLING_CP_PRICES_UPDATE,
		'href'  => $this->href_to('prices_update')
	]);

?>

<div class="cp_toolbar navbar navbar-light bg-light my-2 pl-0 py-1">
    <?php $this->toolbar(); ?>
</div>

<form action="" method="post">
    <?php echo html_csrf_token(); ?>
    <div id="prices-list" class="datagrid_wrapper table-responsive dataTables_wrapper dt-bootstrap4">
        <table id="datagrid" class="datagrid table table-striped table-bordered dataTable bg-white m-0">

            <?php foreach($actions as $controller_name => $actions_list){ ?>

                    <thead>
                        <tr>
                            <th><?php echo $controllers_titles[$controller_name]; ?></th>
							<?php foreach($groups as $g) {?>
								<th class="group"><?php html($g['title']); ?></th>
							<?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($actions_list as $a){ ?>
                            <tr>
                                <td class="title"><?php echo html_input('text', "titles[{$a['id']}]", $a['title']); ?></td>
								<?php foreach($groups as $g) {?>
									<td class="group">
										<?php $price = $a['prices'][$g['id']] ?? 0; ?>
										<?php echo html_input('text', "prices[{$a['id']}][{$g['id']}]", round($price, 2)); ?>
									</td>
								<?php } ?>
                            </tr>
                        <?php } ?>
                    </tbody>

            <?php } ?>

        </table>
    </div>

    <div class="buttons my-3">
        <?php echo html_submit(LANG_SAVE); ?>
    </div>

</form>