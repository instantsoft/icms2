<?php
function routes_photos(){

    return array(

        array(
            'pattern' => '/^photos\/([a-z0-9\-\/]+).html$/i',
            'action'  => 'view',
            1         => 'slug'
        ),

        array(
            'pattern' => '/^photos\/camera\-(.+)$/i',
            'action'  => 'camera',
            1         => 'name'
        )

    );

}
