<div class="icms-content-category">
    <div>
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
        <div class="category_description">
            <?php echo $category['description']; ?>
        </div>
    <?php } ?>
</div>