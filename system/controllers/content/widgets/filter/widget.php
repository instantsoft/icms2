<?php

class widgetContentFilter extends cmsWidget {

    public $is_cacheable = false;

    public function run() {

        $ctype_name = $this->getOption('ctype_name');

        $category = ['id' => 1];
        $item     = [];

        $is_in_item = strpos($this->cms_core->uri, '.html') !== false || strpos($this->cms_core->uri, '/view-') !== false;

        if (!$is_in_item) {

            $current_ctype_category = cmsModel::getCachedResult('current_ctype_category');
            if (!empty($current_ctype_category['id'])) {
                $category = $current_ctype_category;
            }

        } elseif (!$ctype_name) {

            $item = cmsModel::getCachedResult('current_ctype_item');
            if ($item) {
                if (!empty($item['category'])) {
                    $category = $item['category'];
                }
            }
        }

        if (!$ctype_name) {

            $ctype = cmsModel::getCachedResult('current_ctype');
            if (!$ctype) {
                return false;
            }

            $ctype_name = $ctype['name'];

            if ($is_in_item) {
                if (!$item) {
                    return false;
                }
            }

            $page_url = href_to($ctype_name, $category['slug'] ?? '');

            $fields       = cmsModel::getCachedResult('current_ctype_fields');
            $props        = cmsModel::getCachedResult('current_ctype_props');
            $props_fields = cmsModel::getCachedResult('current_ctype_props_fields');

            if ($props_fields === null) {
                $props_fields = cmsCore::getController('content')->getPropsFields($props);
            }

            $current_child_ctype = cmsModel::getCachedResult('current_child_ctype');

            if ($current_child_ctype) {
                $page_url = href_to($ctype['name'], $item['slug'] . '/view-' . $current_child_ctype['name']);
            }

        } else {

            $content_controller = cmsCore::getController('content');

            $ctype  = $content_controller->model->getContentTypeByName($ctype_name);
            $fields = $content_controller->model->getContentFields($ctype_name);
            $props  = $content_controller->model->getContentProps($ctype_name, $category['id']);

            $props_fields = $content_controller->getPropsFields($props);

            $page_url = href_to($ctype_name, $category['slug'] ?? '');
        }

        if (!$fields && !$props) {
            return false;
        }

        $fields_count = 0;

        if ($fields) {
            foreach ($fields as $field) {
                if ($field['is_in_filter'] && (empty($field['filter_view']) || $this->cms_user->isInGroups($field['filter_view']))) {
                    $fields_count++;
                } else {
                    unset($fields[$field['name']]);
                }
            }
        }

        if (!empty($props_fields)) {
            foreach ($props as $prop) {
                if ($prop['is_in_filter']) {
                    $fields_count++;
                } else {
                    unset($props[$prop['id']]);
                }
            }
        }

        if (!$fields_count) {
            return false;
        }

        $filters = [];

        foreach ($fields as $name => $field) {

            $field['handler']->setContext('filter')->
                    setItem(['ctype_name' => $ctype['name'], 'id' => null, 'category' => $category])->
                    setItemList(['ctype' => $ctype, 'category_id' => $category['id']]);

            $field['handler']->id .= '_filter' . $this->id;

            $fields[$name] = $field;

            if (!$this->cms_core->request->has($name)) {
                continue;
            }

            $value = $this->cms_core->request->get($name, false, $field['handler']->getDefaultVarType());

            $value = $field['handler']->storeFilter($value);
            if (is_empty_value($value)) {
                continue;
            }

            $filters[$name] = $value;
        }

        if (!empty($props)) {
            foreach ($props as $key => $prop) {

                $name = 'p' . $prop['id'];

                $prop['handler'] = $props_fields[$prop['id']];

                $prop['handler']->setContext('filter')->setName("p{$prop['id']}")->
                        setItem(['ctype_name' => $ctype['name'], 'id' => null, 'category' => $category])->
                        setItemList(['ctype' => $ctype, 'category_id' => $category['id']]);

                $prop['handler']->id .= '_filter' . $this->id;

                $props[$key] = $prop;

                if (!$this->cms_core->request->has($name)) {
                    continue;
                }

                $value = $this->cms_core->request->get($name, false, $prop['handler']->getDefaultVarType());

                $value = $prop['handler']->storeFilter($value);
                if (is_empty_value($value)) {
                    continue;
                }

                $filters[$name] = $value;
            }
        }

        return [
            'form_id'      => $this->name . '_' . $this->position . $this->id . '_' . $this->bind_id,
            'ctype_name'   => $ctype_name,
            'category'     => $category,
            'page_url'     => $page_url,
            'fields'       => $fields,
            'props_fields' => $props_fields,
            'props'        => $props,
            'filters'      => $filters
        ];
    }

}
