<?php foreach ($rows as $row) { ?>
<div class="row no-gutters widgets-layout-scheme align-items-center" data-id="<?php echo $row['id']; ?>">
    <div class="layout-row-title col-12 col-md-2 filled p-2 d-flex justify-content-between">
        {cell:<?php echo $row['title']; ?>}
        <div class="layout-scheme-actions align-items-center">
            <a class="add mr-1 text-success text-decoration-none add-scheme-row ajax-modal" data-toggle="tooltip" data-placement="top" href="<?php echo $this->href_to('widgets', ['col_add', $row['id']]); ?>" title="<?php echo LANG_ADD; ?>">
                <i class="icon-plus icons"></i>
            </a>
            <a class="edit mr-1 text-decoration-none edit-scheme-row ajax-modal" data-toggle="tooltip" data-placement="top" href="<?php echo $this->href_to('widgets', ['row_edit', $row['id']]); ?>" title="<?php echo LANG_EDIT; ?>">
                <i class="icon-pencil icons"></i>
            </a>
            <a class="delete text-danger text-decoration-none delete-scheme-row" data-toggle="tooltip" data-placement="top" href="<?php echo $this->href_to('widgets', ['row_delete', $row['id']]); ?>" title="<?php echo LANG_DELETE; ?>" onclick="return confirm('<?php echo LANG_CP_WIDGETS_ROW_DEL_CONFIRM; ?>')">
                <i class="icon-close icons"></i>
            </a>
        </div>
    </div>
    <div class="w-100 d-md-none"></div>
    <div class="col-12 col-md-10">
        <div class="row no-gutters widgets-layout-scheme-col-wrap">
        <?php foreach ($row['cols'] as $col) { ?>
            <?php if(!empty($col['rows'])){ ?>
                <?php $this->renderChild('widgets_scheme', array('rows'=>$col['rows'])); ?>
            <?php } ?>
            <div class="<?php echo $col['options']['default_col_class']; ?> p-1 bg-white widgets-layout-scheme-col" data-id="<?php echo $col['id']; ?>">
                <div class="layout-col-title d-flex justify-content-between" data-toggle="tooltip" data-placement="top" title="<?php echo $col['name']; ?>">
                    <span><?php echo $col['title']; ?></span>
                    <div class="layout-scheme-actions d-flex align-items-center">
                        <a class="add mr-2 text-white text-decoration-none add-scheme-col ajax-modal" href="<?php echo $this->href_to('widgets', ['row_add', $row['id']]); ?>" title="<?php echo LANG_CP_WIDGETS_ADD_ROW_P; ?>">
                            <i class="icon-plus icons d-block"></i>
                        </a>
                        <a class="edit mr-2 text-white text-decoration-none edit-scheme-col ajax-modal" href="<?php echo $this->href_to('widgets', ['col_edit', $col['id']]); ?>" title="<?php echo LANG_EDIT; ?>">
                            <i class="icon-pencil icons d-block"></i>
                        </a>
                        <a class="delete text-warning text-decoration-none delete-scheme-col" href="<?php echo $this->href_to('widgets', ['col_delete', $col['id']]); ?>" title="<?php echo LANG_DELETE; ?>" onclick="return confirm('<?php echo LANG_CP_WIDGETS_COL_DEL_CONFIRM; ?>')">
                            <i class="icon-close icons d-block"></i>
                        </a>
                    </div>
                </div>
                <?php if($col['is_body']){ ?>
                    {block:LANG_PAGE_BODY}
                <?php } else { ?>
                    {position:<?php echo $col['name']; ?>}
                <?php } ?>
            </div>
        <?php } ?>
        </div>
    </div>
</div>
<?php } ?>