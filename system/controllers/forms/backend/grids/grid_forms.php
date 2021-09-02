<?php

function grid_forms($controller) {

    $options = array(
        'is_sortable'   => true,
        'is_filter'     => true,
        'is_pagination' => true,
        'is_draggable'  => false,
        'is_selectable' => false,
        'order_by'      => 'id',
        'order_to'      => 'asc',
        'show_id'       => true
    );

    $columns = array(
        'id' => array(
            'title'  => 'id',
            'width'  => 30,
            'filter' => 'exact'
        ),
        'title' => array(
            'title'  => LANG_TITLE,
            'width'  => 150,
            'href'   => href_to($controller->root_url, 'edit', '{id}'),
            'filter' => 'like'
        ),
        'name' => array(
            'title'  => LANG_SYSTEM_NAME,
            'width'  => 150,
            'filter' => 'like'
        )
    );

    if(!empty($controller->options['allow_embed'])){
        $columns['hash'] = array(
            'title'  => LANG_FORMS_CP_FORMS_EMBED,
            'handler' => function ($v, $row){
                return html_input('text', '', '<script src="'.href_to_abs('forms', 'framejs', $v).'"></script>', ['onclick' => '$(this).select();']);
            }
        );
    }

    if(!empty($controller->options['allow_shortcode'])){
        $columns['tpl_form'] = array(
            'title'  => LANG_FORMS_CP_FORMS_SHORTCODE,
            'handler' => function ($v, $row){
                return html_input('text', '', '{forms:'.$row['name'].'}', ['onclick' => '$(this).select();']);
            }
        );
    }

    $actions = array(
        array(
            'title' => LANG_VIEW,
            'class' => 'view ajax-modal',
            'href'  => href_to($controller->root_url, 'view', '{id}')
        ),
        array(
            'title' => LANG_EDIT,
            'class' => 'edit',
            'href'  => href_to($controller->root_url, 'edit', '{id}'),
        ),
        array(
            'title' => LANG_CP_CTYPE_FIELDS,
            'class' => 'fields',
            'href'  => href_to($controller->root_url, 'form_fields', '{id}')
        ),
        array(
            'title' => LANG_FORMS_CP_FORMS_COPY,
            'class' => 'copy',
            'href'  => href_to($controller->root_url, 'copy', array('{id}')),
        ),
        array(
            'title'   => LANG_DELETE,
            'class'   => 'delete',
            'href'    => href_to($controller->root_url, 'delete', array('{id}')),
            'confirm' => LANG_FORMS_CP_FORM_DELETE_CONFIRM
        )
    );

    return array(
        'options' => $options,
        'columns' => $columns,
        'actions' => $actions
    );

}
