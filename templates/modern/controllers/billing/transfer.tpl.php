<?php
    $this->setPageTitle($title);

    $this->addBreadcrumb(LANG_USERS, href_to('users'));
    $this->addBreadcrumb($receiver['nickname'], $receiver_url);
    $this->addBreadcrumb($title);

    $this->addTplJSName('billing');
?>

<h1><?php echo $title; ?></h1>

<?php if ($balance <= 0) { ?>
    <div class="alert alert-warning mt-3 mt-lg-4">
        <h4 class="alert-heading"><?php echo LANG_BILLING_TRANSFER_LOW_BALANCE; ?></h4>
        <p class="mb-0 mt-3">
            <?php if ($this->controller->options['in_mode'] === 'enabled') { ?>
                <a class="btn btn-success pay" href="<?php echo href_to('billing', 'deposit'); ?>">
                    <?php echo LANG_BILLING_BALANCE_ADD; ?>
                </a>
            <?php } ?>
            <a class="btn btn-link cancel" href="<?php echo $receiver_url; ?>">
                <?php echo LANG_CANCEL; ?>
            </a>
        </p>
    </div>
<?php return; } ?>

<div class="billing-transfer row mt-3 mt-lg-4">
    <div class="billing-transfer-form col-lg-8">
        <div class="card">
            <div class="card-body">
                <form action="" method="post">
                    <?php echo html_csrf_token(); ?>
                    <div class="form-group row">
                        <label class="font-weight-bolder col-sm-4 col-form-label"><?php echo LANG_BILLING_TRANSFER_RECEIVER; ?></label>
                        <div class="col-sm-8">
                            <div class="d-flex align-items-center">
                                <a href="<?php echo $receiver_url; ?>" class="icms-user-avatar small mr-2 <?php if (!empty($receiver['is_online'])){ ?>peer_online<?php } else { ?>peer_no_online<?php } ?>">
                                    <?php if($receiver['avatar']){ ?>
                                        <?php echo html_avatar_image($receiver['avatar'], 'micro', $receiver['nickname']); ?>
                                    <?php } else { ?>
                                        <?php echo html_avatar_image_empty($receiver['nickname'], 'avatar__mini'); ?>
                                    <?php } ?>
                                </a>
                                <?php echo html_link($receiver['nickname'], $receiver_url); ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="font-weight-bolder col-sm-4 col-form-label"><?php echo LANG_BILLING_DEPOSIT_TICKET_BALANCE; ?></label>
                        <div class="col-sm-8">
                            <div class="form-control-plaintext">
                                <?php html_svg_icon('solid', 'coins'); ?>
                                <span class="ml-1"><?php echo html_spellcount($balance, $b_spellcount); ?></span>
                                <a href="#all" class="text-muted ml-2" id="billing_transfer_all" data-balance="<?php echo $balance; ?>">
                                    <?php echo mb_strtolower(LANG_BILLING_TRANSFER_ALL); ?>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="font-weight-bolder col-sm-4 col-form-label"><?php echo LANG_BILLING_TRANSFER_AMOUNT; ?></label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <?php echo html_input('text', 'amount', $amount, ['autocomplete' => 'off', 'required' => true, 'id' => 'trf-amount']); ?>
                                <div class="input-group-append">
                                    <span class="input-group-text"><?php echo $b_spellcount_arr[2]; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="font-weight-bolder col-sm-4 col-form-label"><?php echo LANG_BILLING_TRANSFER_DESC; ?></label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <?php echo html_input('text', 'description', $description, ['maxlength' => 255]); ?>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 mt-lg-4">
                        <?php echo html_submit(LANG_BILLING_TRANSFER_SUBMIT); ?>
                        <a class="ml-2 btn btn-link" href="<?php echo $receiver_url; ?>"><?php echo LANG_CANCEL; ?></a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>