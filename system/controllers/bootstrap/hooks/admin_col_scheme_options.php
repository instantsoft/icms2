<?php

class onBootstrapAdminColSchemeOptions extends cmsAction {

    public function run($data) {

        [$do, $row, $col] = $data;

        $template = new cmsTemplate($row['template']);

        $manifest = $template->getManifest();

        $vendor = $manifest['properties']['vendor'] ?? '';

        if (!$this->isSupportedVendor($vendor)) {
            return false;
        }

        $fields = [
            new fieldCheckbox('options:cut_before', [
                'title'          => LANG_CP_WIDGETS_COL_CUT_BEFORE,
                'visible_depend' => ['type' => ['hide' => ['custom']]]
            ])
        ];

        $visible_depend = ['type' => ['hide' => ['custom']]];

        $cols = [
            [
                'name'    => 'default',
                'prefix'  => 'col-sm',
                'title'   => sprintf(LANG_CP_WIDGETS_COL_WIDTH, '≥576px') . ' (' . LANG_CP_WIDGETS_COL_WIDTH_D . ')',
                'default' => 'col-sm',
                'empty'   => LANG_NO
            ],
            [
                'name'   => 'md',
                'prefix' => 'col-md',
                'title'  => sprintf(LANG_CP_WIDGETS_COL_WIDTH, '≥768px'),
                'empty'  => LANG_BY_DEFAULT
            ],
            [
                'name'   => 'lg',
                'prefix' => 'col-lg',
                'title'  => sprintf(LANG_CP_WIDGETS_COL_WIDTH, '≥992px'),
                'empty'  => LANG_BY_DEFAULT
            ],
            [
                'name'   => 'xl',
                'prefix' => 'col-xl',
                'title'  => sprintf(LANG_CP_WIDGETS_COL_WIDTH, '≥1200px'),
                'empty'  => LANG_BY_DEFAULT
            ]
        ];

        if ($vendor === 'bootstrap5') {
            $cols[] = [
                'name'   => 'xxl',
                'prefix' => 'col-xxl',
                'title'  => sprintf(LANG_CP_WIDGETS_COL_WIDTH, '≥1400px'),
                'empty'  => LANG_BY_DEFAULT
            ];
        }

        $cols[] = [
            'name'   => '',
            'prefix' => 'col',
            'title'  => LANG_CP_WIDGETS_COL_WIDTH_ALL,
            'empty'  => LANG_NO
        ];

        foreach ($cols as $col) {

            if (!$col) {
                continue;
            }

            $items = [
                '' => $col['empty'],
                $col['prefix'] => LANG_AUTO,
            ];

            for ($i = 1; $i <= 12; $i++) {
                $items["{$col['prefix']}-$i"] = "{$col['prefix']}-$i (" . round($i * 100 / 12, 2) . '%)';
            }

            $items["{$col['prefix']}-auto"] = LANG_CP_WIDGETS_COL_AUTO;

            $config = [
                'title'            => $col['title'],
                'items'            => $items,
                'visible_depend'   => $visible_depend
            ];

            if (isset($col['default'])) {
                $config['default'] = $col['default'];
            }

            $field = $col['name']
                ? "options:{$col['name']}_col_class"
                : 'options:col_class';

            $fields[] = new fieldList($field, $config);
        }

        $orders = [
            'default' => LANG_CP_WIDGETS_COL_D_ORDER,
            'sm'      => sprintf(LANG_CP_WIDGETS_COL_ORDER, '≥576px'),
            'md'      => sprintf(LANG_CP_WIDGETS_COL_ORDER, '≥768px'),
            'lg'      => sprintf(LANG_CP_WIDGETS_COL_ORDER, '≥992px'),
            'xl'      => sprintf(LANG_CP_WIDGETS_COL_ORDER, '≥1200px')
        ];

        if ($vendor === 'bootstrap5') {
            $orders['xxl'] = sprintf(LANG_CP_WIDGETS_COL_ORDER, '≥1400px');
        }

        foreach ($orders as $name => $title) {

            $fields[] = new fieldNumber("options:{$name}_order", [
                'title' => $title,
                'options' => [
                    'is_abs'  => true,
                    'is_ceil' => true
                ],
                'rules' => [
                    ['max', 12]
                ],
                'visible_depend' => $visible_depend
            ]);
        }

        return $fields;
    }

}
