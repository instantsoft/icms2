<?php
function routes_content(){

    return array(

        array(
            'pattern'   => '/^([a-z0-9\-_]+)\/from_friends$/i',
            'action'    => 'items_from_friends',
            1           => 'ctype_name'
        ),

        array(
            'pattern'   => '/^([a-z0-9\-_]+)\/bind_form\/([a-z0-9\-_]+)\/([0-9]+)$/i',
            'action'    => 'item_bind_form',
            1           => 'ctype_name',
            2           => 'child_ctype_name',
            3           => 'id'
        ),

        array(
            'pattern'   => '/^([a-z0-9\-_]+)\/bind_form\/([a-z0-9\-_]+)\/([0-9]+)\/(childs|parents|unbind)$/i',
            'action'    => 'item_bind_form',
            1           => 'ctype_name',
            2           => 'child_ctype_name',
            3           => 'id',
			4			=> 'mode'
        ),

        array(
            'pattern'   => '/^([a-z0-9\-_]+)\/bind\/([a-z0-9\-_]+)$/i',
            'action'    => 'item_bind',
            1           => 'ctype_name',
            2           => 'child_ctype_name',
        ),

        array(
            'pattern'   => '/^([a-z0-9\-_]+)\/unbind\/([a-z0-9\-_]+)$/i',
            'action'    => 'item_unbind',
            1           => 'ctype_name',
            2           => 'child_ctype_name',
        ),

        array(
            'pattern'   => '/^([a-z0-9\-_]+)\/bind_list\/([a-z0-9\-_]+)\/([0-9]+)$/i',
            'action'    => 'item_bind_list',
            1           => 'ctype_name',
            2           => 'child_ctype_name',
            3           => 'id'
        ),

        array(
            'pattern'   => '/^([a-z0-9\-_]+)\/bind_list\/([a-z0-9\-_]+)$/i',
            'action'    => 'item_bind_list',
            1           => 'ctype_name',
            2           => 'child_ctype_name',
        ),

        array(
            'pattern'   => '/^([a-z0-9\-_]+)\/add\/([0-9]+)$/i',
            'action'    => 'item_add',
            1           => 'ctype_name',
            2           => 'to_id'
        ),

        array(
            'pattern'   => '/^([a-z0-9\-_]+)\/add$/i',
            'action'    => 'item_add',
            1           => 'ctype_name'
        ),

        array(
            'pattern'   => '/^([a-z0-9\-_]+)\/edit\/([0-9]+)$/i',
            'action'    => 'item_edit',
            1           => 'ctype_name',
            2           => 'id'
        ),

        array(
            'pattern'   => '/^([a-z0-9\-_]+)\/approve\/([0-9]+)$/i',
            'action'    => 'item_approve',
            1           => 'ctype_name',
            2           => 'id'
        ),

        array(
            'pattern'   => '/^([a-z0-9\-_]+)\/return_for_revision\/([0-9]+)$/i',
            'action'    => 'item_return_for_revision',
            1           => 'ctype_name',
            2           => 'id'
        ),

        array(
            'pattern'   => '/^([a-z0-9\-_]+)\/return\/([0-9]+)$/i',
            'action'    => 'item_return',
            1           => 'ctype_name',
            2           => 'id'
        ),

        array(
            'pattern'   => '/^([a-z0-9\-_]+)\/props\/([0-9]+)$/i',
            'action'    => 'item_props_fields',
            1           => 'ctype_name',
            2           => 'category_id'
        ),

        array(
            'pattern'   => '/^([a-z0-9\-_]+)\/delete\/([0-9]+)$/i',
            'action'    => 'item_delete',
            1           => 'ctype_name',
            2           => 'id'
        ),

        array(
            'pattern'   => '/^([a-z0-9\-_]+)\/trash_put\/([0-9]+)$/i',
            'action'    => 'item_trash_put',
            1           => 'ctype_name',
            2           => 'id'
        ),

        array(
            'pattern'   => '/^([a-z0-9\-_]+)\/trash_remove\/([0-9]+)$/i',
            'action'    => 'item_trash_remove',
            1           => 'ctype_name',
            2           => 'id'
        ),

        array(
            'pattern'   => '/^([a-z0-9\-_]+)\/addcat\/([0-9]+)$/i',
            'action'    => 'category_add',
            1           => 'ctype_name',
            2           => 'to_id'
        ),

        array(
            'pattern'   => '/^([a-z0-9\-_]+)\/addcat$/i',
            'action'    => 'category_add',
            1           => 'ctype_name',
            'to_id'     => 0
        ),

        array(
            'pattern'   => '/^([a-z0-9\-_]+)\/editcat\/([0-9]+)$/i',
            'action'    => 'category_edit',
            1           => 'ctype_name',
            2           => 'id'
        ),

        array(

            'pattern'   => '/^([a-z0-9\-_]+)\/delcat\/([0-9]+)$/i',
            'action'    => 'category_delete',
            1           => 'ctype_name',
            2           => 'id'
        ),

        array(
            'pattern'   => '/^([a-z0-9\-_]+)\/editfolder\/([0-9]+)$/i',
            'action'    => 'folder_edit',
            1           => 'ctype_name',
            2           => 'id'
        ),

        array(
            'pattern'   => '/^([a-z0-9\-_]+)\/delfolder\/([0-9]+)$/i',
            'action'    => 'folder_delete',
            1           => 'ctype_name',
            2           => 'id'
        ),

        array(
            'pattern'   => '/^([a-z0-9\-_]+)\/([a-z0-9\-\/]+).html$/i',
            'action'    => 'item_view',
            1           => 'ctype_name',
            2           => 'slug'
        ),

        array(
            'pattern'   => '/^([a-z0-9\-\/]+).html$/i',
            'action'    => 'item_view',
            'ctype_name' => cmsConfig::get('ctype_default'),
            1           => 'slug'
        ),

        array(
            'pattern'   => '/^([a-z0-9\-_]+)\/([a-z0-9\-\/]+)\/view\-([a-z0-9\-_]+)\/?([a-z0-9_]*)$/i',
            'action'    => 'item_view',
            1           => 'ctype_name',
            2           => 'slug',
            3           => 'child_ctype_name',
            4           => 'dataset'
        ),

        array(
            'pattern'    => '/^([a-z0-9\-\/]+)\/view\-([a-z0-9\-_]+)\/?([a-z0-9_]*)$/i',
            'action'     => 'item_view',
            'ctype_name' => cmsConfig::get('ctype_default'),
            1            => 'slug',
            2            => 'child_ctype_name',
            3           => 'dataset'
        ),

        array(
            'pattern'   => '/^([a-z0-9_]+)\-([a-z0-9_]+)\/([a-z0-9\-\/]+)$/i',
            'action'    => 'category_view',
            1           => 'ctype_name',
            2           => 'dataset',
            3           => 'slug'
        ),

        array(
            'pattern'   => '/^([a-z0-9_]+)\/([a-z0-9\-\/]+)$/i',
            'action'    => 'category_view',
            1           => 'ctype_name',
            2           => 'slug'
        ),

        array(
            'pattern'   => '/^([a-z0-9_]+)\-([a-z0-9_]+)$/i',
            'action'    => 'category_view',
            1           => 'ctype_name',
            2           => 'dataset',
            'slug'      => 'index'
        ),

        array(
            'pattern'   => '/^([a-z0-9_]+)$/i',
            'action'    => 'category_view',
            1           => 'ctype_name',
            'slug'      => 'index'
        )

    );

}
