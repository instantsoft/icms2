<?php

return [
    [
        'intro' => '<h3>Welcome to the widget management page!</h3><p>This is where you actually control how your site will display.<br>Each page of the site is divided into "positions", within which you can display various blocks - widgets.<br>Widgets can show menus, content lists, search form, login form, and many other things.</p> <p>Here you can change the site position scheme and assign widgets to these positions.</p><p><b>Click &laquo;Next&raquo; for a quick tour of widget management.</b><br><span class="text-muted">We recommend doing this if this is your first introduction to InstantCMS 2</span></p>',
        'tooltipClass' => 'introjs-full-width'
    ],
    [
        'element'  => '#intro-step1',
        'scrollTo' => 'tooltip',
        'intro'    => '<p>There are two tabs on the left panel:</p> <ol><li>list of all site pages grouped by type; select the appropriate page type in this tree to see which widgets are linked to them.</li><li>palette with all available widgets; from there, you can drag widgets to the desired position on the site diagram to link them to the selected page type.</li></ol>'
    ],
    [
        'element'  => '#cp-widgets-layout',
        'scrollTo' => 'tooltip',
        'intro'    => 'The center shows the page layout of your site. You can add rows and columns within rows to create the desired page layout.'
    ],
    [
        'element'  => '.gridicon',
        'scrollTo' => 'tooltip',
        'intro'    => 'The page layout may depend on the selected site template. You can switch between different templates using this button.'
    ],
    [
        'element'  => '.add_row',
        'scrollTo' => 'tooltip',
        'intro'    => 'Click here to add another row to the page layout where you can place widgets.'
    ],
    [
        'element'  => '.add-scheme-row',
        'scrollTo' => 'tooltip',
        'intro'    => 'You can add the required number of columns inside a row.'
    ],
    [
        'element'  => '.position',
        'scrollTo' => 'tooltip',
        'intro'    => '<p>Each column forms a new position for widgets.</p><p>Select a widget in the left panel (All widgets tab) and drag it inside the column to link the widget to the current page type.</p>'
    ],
    [
        'element'  => '.breadcrumb-menu',
        'position' => 'left',
        'scrollTo' => 'tooltip',
        'intro'    => 'Working with widgets is described in great detail in our documentation. Just click here if you don\'t understand anything.'
    ]
];
