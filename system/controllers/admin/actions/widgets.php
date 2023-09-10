<?php
/**
 * @property \modelBackendWidgets $model_backend_widgets
 */
class actionAdminWidgets extends cmsAction {

    private $is_dynamic_scheme = false;
    private $rows_titles_pos   = 'left';

    public function run($do = false) {

        // если нужно, передаем управление другому экшену
        if ($do) {
            $this->runExternalAction('widgets_' . $do, array_slice($this->params, 1));
            return;
        }

        cmsCore::loadAllControllersLanguages();

        $controllers = $this->model_backend_widgets->getPagesControllers();

        $widgets_list = $this->model_backend_widgets->getAvailableWidgets();

        $tpls = cmsCore::getTemplates();

        $scroll_to     = strip_tags($this->request->get('scroll_to', ''));
        $template_name = $this->request->get('template_name', '');

        if (!$template_name || !in_array($template_name, $tpls)) {
            $template_name = cmsConfig::get('template');
        }

        cmsCore::loadTemplateLanguage($template_name);

        // Шаблоны для показа виджетов
        $templates = [];
        // Шаблоны, у которых есть динамическая схема
        $templates_dynamic_scheme = [];

        foreach ($tpls as $tpl) {
            $template_path = $this->cms_config->root_path . cmsTemplate::TEMPLATE_BASE_PATH . $tpl;
            $manifest      = cmsTemplate::getTemplateManifest($template_path);
            if ($manifest !== null) {
                if (!empty($manifest['properties']['is_frontend'])) {
                    $templates[$tpl] = !empty($manifest['title']) ? $manifest['title'] : $tpl;
                }
                if (!empty($manifest['properties']['is_dynamic_layout'])) {
                    $templates_dynamic_scheme[$tpl] = !empty($manifest['title']) ? $manifest['title'] : $tpl;
                }
                continue;
            }
            if (file_exists($template_path . '/main.tpl.php')) {
                $templates[$tpl] = $tpl;
            }
        }

        $this->rows_titles_pos = cmsUser::getCookie('rows_titles_pos');
        if (!$this->rows_titles_pos) {
            $this->rows_titles_pos = 'left';
        }

        $scheme_html = $this->getSchemeHTML($template_name);

        // Пошаговое руководство страницы, если есть
        $intro_lang = cmsCore::loadLanguage('templates/' . $this->cms_template->name . '/admin/intro/widgets/widgets');

        return $this->cms_template->render('widgets', [
            'is_dynamic_scheme'        => $this->is_dynamic_scheme,
            'rows_titles_pos'          => $this->rows_titles_pos,
            'intro_lang'               => $intro_lang,
            'controllers'              => $controllers,
            'template_name'            => $template_name,
            'templates'                => $templates,
            'templates_dynamic_scheme' => $templates_dynamic_scheme,
            'widgets_list'             => $widgets_list,
            'scroll_to'                => $scroll_to,
            'scheme_html'              => $scheme_html
        ]);
    }

    public function getSchemeHTML($name = '') {

        $template = new cmsTemplate($name);

        $template->setContext($this);

        $scheme_html = $template->getSchemeHTML();
        if (!$scheme_html) {
            $scheme_html = $this->getDynamicSchemeHTML($template);
            if (!$scheme_html) {
                return false;
            }

            $this->is_dynamic_scheme = true;
        }

        preg_match_all('/{(.+)}/ui', $scheme_html, $matches);

        if (!empty($matches[1])) {
            foreach ($matches[1] as $block) {

                list($type, $value) = explode(':', $block);

                if ($type === 'position') {
                    $replace_html = '<ul class="position" rel="' . html($value, false) . '" id="pos-' . html($value, false) . '"></ul>';
                }

                if ($type === 'block') {
                    if (mb_strpos($value, 'LANG_') === 0) {
                        $value = constant($value);
                    }
                    $replace_html = '<div class="block"><span>' . $value . '</span></div>';
                }

                if ($type === 'cell') {
                    if (mb_strpos($value, 'LANG_') === 0) {
                        $value = constant($value);
                    }
                    $replace_html = '<div class="cell"><span>' . $value . '</span></div>';
                }

                $scheme_html = str_replace("{{$block}}", $replace_html, $scheme_html);
            }
        }

        return $scheme_html;
    }

    private function getDynamicSchemeHTML($template) {

        $this->cms_template->addTplJSName('admin-scheme');

        $rows = $this->model_backend_widgets->getLayoutRows($template->getName());

        return $template->getRenderedChild('widgets_scheme', [
            'rows_titles_pos' => $this->rows_titles_pos,
            'rows'            => $rows
        ]);
    }

}
