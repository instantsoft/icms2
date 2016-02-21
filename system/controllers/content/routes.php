<?php
function routes_content(){

    return array(

        array(
            'pattern'   => '/^([a-z0-9\-_]+)\/from_friends$/i',
            'action'    => 'items_from_friends',
            1           => 'ctype_name'
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
            'pattern'   => '/^([a-z0-9\-_]+)\/([a-zA-Z0-9\-\/]+).html$/i',
            'action'    => 'item_view',
            1           => 'ctype_name',
            2           => 'slug'
        ),

        array(
            'pattern'   => '/^([a-zA-Z0-9\-\/]+).html$/i',
            'action'    => 'item_view',
            'ctype_name' => cmsConfig::get('ctype_default'),
            1           => 'slug'
        ),

        array(
            'pattern'   => '/^([a-z0-9_]+)\-([a-z0-9_]+)\/([a-zA-Z0-9\-\/]+)$/i',
            'action'    => 'category_view',
            1           => 'ctype_name',
            2           => 'dataset',
            3           => 'slug'
        ),

        array(
            'pattern'   => '/^([a-z0-9_]+)\/([a-zA-Z0-9\-\/]+)$/i',
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
