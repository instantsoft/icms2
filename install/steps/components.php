<?php

function step($is_submit) {

    if ($is_submit) {
        return check_components();
    }

    $data = include PATH . 'data/components.php';

    $install_type = $_SESSION['install']['site']['install_type'] ?? 'standard';
    $selected = $_SESSION['install']['components'] ?? [];
    $is_install_demo = $_SESSION['install']['site']['is_install_demo'] ?? ($install_type !== 'minimal');

    if (empty($selected) && isset($data['install_types'][$install_type])) {
        $selected = $data['install_types'][$install_type]['components'];
    }

    return [
        'html' => render('step_components', [
            'categories'    => $data['categories'],
            'mandatory'     => $data['mandatory'],
            'install_types' => $data['install_types'],
            'selected'      => $selected,
            'install_type'  => $install_type,
            'is_install_demo' => $is_install_demo
        ])
    ];
}

function check_components() {

    $data = include PATH . 'data/components.php';

    $install_type = strip_tags(get_post('install_type'));
    $is_install_demo = (int) get_post('is_install_demo');

    if (!isset($data['install_types'][$install_type])) {
        $install_type = 'standard';
    }

    $type_data = $data['install_types'][$install_type];

    if ($install_type === 'custom') {
        $selected = get_post_array('components');
        $selected = array_unique(array_merge($selected, $data['mandatory']));

        $errors = validate_component_dependencies($selected, $data);

        if ($errors) {
            return [
                'error'   => true,
                'message' => implode("\n", $errors)
            ];
        }
    } else {
        $selected = $type_data['components'];
    }

    $_SESSION['install']['site']['install_type'] = $install_type;
    $_SESSION['install']['site']['is_install_demo'] = $is_install_demo;
    $_SESSION['install']['components'] = $selected;

    return [
        'error' => false
    ];
}

function validate_component_dependencies($selected, $data) {

    $errors = [];

    foreach ($data['categories'] as $cat_id => $cat) {
        foreach ($cat['components'] as $comp_id => $comp) {
            if (in_array($comp_id, $selected) && !empty($comp['deps'])) {
                foreach ($comp['deps'] as $dep) {
                    if (!in_array($dep, $selected)) {
                        $dep_title = constant($dep);
                        foreach ($data['categories'] as $dc => $dcat) {
                            if (isset($dcat['components'][$dep])) {
                                $dep_title = constant($dcat['components'][$dep]['title']);
                                break;
                            }
                        }
                        $comp_title = constant($comp['title']);
                        $errors[] = sprintf(LANG_COMPONENT_REQUIRES, $comp_title, $dep_title);
                    }
                }
            }
        }
    }

    return $errors;
}