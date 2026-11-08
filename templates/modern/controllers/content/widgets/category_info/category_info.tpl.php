<div class="icms-content-category grid-bg bg-white">
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center">
            <h1>
                <?php $this->pageH1(); ?>
            </h1>
            <?php if($this->hasBlock('before_body')){ ?>
                <div>
                    <?php $this->block('before_body'); $this->blocks['before_body'] = ''; ?>
                </div>
            <?php } ?>
        </div>
        <?php if (!empty($category['description'])) { ?>
            <div class="mt-3 row no-gutters mb-n3">
                <div class="col-sm-7">
                    <?php echo $category['description']; ?>
                </div>
            </div>
        <?php } ?>
    </div>
</div>