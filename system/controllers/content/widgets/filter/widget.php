<?php
class widgetContentFilter extends cmsWidget {

    public $is_cacheable = false;

    public function run(){

        $ctype_name = $this->getOption('ctype_name');

        $slug = null;

        if (!$ctype_name){

            $core = cmsCore::getInstance();

            if ($core->controller != 'content'){ return false; }

            $uri_segs = explode('/', $core->uri);

            $ctype_string = $uri_segs[0];

            $slug = !mb_strstr($core->uri, '.html') ? mb_substr($core->uri, mb_strlen($ctype_string)+1) : false;

            if (preg_match('/^([a-z0-9]+)$/', $ctype_string, $matches)){
                $ctype_name = $matches[0];
            } else
            if (preg_match('/^([a-z0-9]+)-([a-z0-9_]+)$/', $ctype_string, $matches)){
                $ctype_name = $matches[1];
            } else {
                return false;
            }

        }

		$core = cmsCore::getInstance();
        $model = cmsCore::getModel('content');
		$content_controller = cmsCore::getController('content');

        $fields = $model->getContentFields($ctype_name);

		$category = array('id' => 1);

		if ($slug) {
			$category = $model->getCategoryBySLUG($ctype_name, $slug);
		}

		$props = $model->getContentProps($ctype_name, $category['id']);
		$props_fields = $content_controller->getPropsFields($props);

		$fields_count = 0;

		foreach($fields as $field){
			if ($field['is_in_filter']) { $fields_count++; break; }
		}

		if (!$fields_count && !empty($props_fields)){
			foreach($props as $prop){
				if ($prop['is_in_filter']) { $fields_count++; break; }
			}
		}

		if (!$fields_count){
			return false;
		}

		$filters = array();

		foreach($fields as $name => $field){

			if (!$field['is_in_filter']) { continue; }
			if (!$core->request->has($name)){ continue; }

			$value = $core->request->get($name);
			if (!$value) { continue; }

			$filters[$name] = $value;

		}

		if (!empty($props)){
			foreach($props as $prop){

				$name = "p{$prop['id']}";

				if (!$prop['is_in_filter']) { continue; }
				if (!$core->request->has($name)){ continue; }

				$value = $core->request->get($name);
				if (!$value) { continue; }

				$filters[$name] = $value;

			}
		}

        return array(
			'ctype_name'   => $ctype_name,
            'page_url'     => $core->uri_absolute,
            'fields'       => $fields,
            'props_fields' => $props_fields,
            'props'        => $props,
            'filters'      => $filters
        );

    }

}