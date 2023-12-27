<?php

class onRedirectEngineStart extends cmsAction {

    public function run(){

        if(empty($this->options['rewrite_json'])){
            return true;
        }

        $found = false;

        $rules = json_decode($this->options['rewrite_json'], true);

        if ($rules) {

            $checked_uri = $this->cms_core->uri_before_remap;
            if ($this->cms_core->uri_query) {
                $checked_uri .= '?' . http_build_query($this->cms_core->uri_query);
            }

            //перебираем правила
            foreach ($rules as $rule) {

                if(!is_array($rule)){
                    continue;
                }

                //небольшая валидация правила
                if (empty($rule['source']) || empty($rule['target']) || empty($rule['action'])) {
                    continue;
                }

                $matches = [];
                //проверяем совпадение выражения source с текущим uri
                if (preg_match($rule['source'], $checked_uri, $matches)) {

                    //перебираем совпавшие сегменты и добавляем их в target
                    //чтобы сохранить параметры из $this->cms_core->uri в новом адресе
                    foreach ($matches as $key => $value) {
                        if (!$key) { continue; }
                        if (!$value) { $value = ''; }
                        if (mb_strstr($rule['target'], '{' . $key . '}')) {
                            $rule['target'] = str_replace('{' . $key . '}', $value, $rule['target']);
                        }
                    }

                    //выполняем действие
                    switch ($rule['action']) {
                        case 'rewrite':
                            $t = parse_url($rule['target']);
                            if(!empty($t['query'])){
                                mb_parse_str($t['query'], $this->cms_core->uri_query);
                            }
                            // разбиваем URL на сегменты
                            $segments = explode('/', $t['path']);
                            // Определяем контроллер из первого сегмента
                            if (isset($segments[0])) { $this->cms_core->uri_controller = $segments[0]; }
                            // Определяем действие из второго сегмента
                            if (isset($segments[1])) { $this->cms_core->uri_action = $segments[1]; }
                            // Определяем параметры действия из всех остальных сегментов
                            if (count($segments)>2){
                                $this->cms_core->uri_params = array_slice($segments, 2);
                            }
                            $this->cms_core->uri = $this->cms_core->uri_before_remap = $rule['target'];
                            $found = true;
                            break;
                        case 'redirect': $this->redirect($rule['target']);
                            break;
                        case 'redirect-301': $this->redirect($rule['target'], 301);
                            break;
                    }
                }

                if ($found) {
                    break;
                }
            }
        }

        return true;
    }

}
