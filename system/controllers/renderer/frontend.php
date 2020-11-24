<?php
class renderer extends cmsFrontend {

    private $middleware;

	public function __construct($request){

        parent::__construct($request);

        $this->middleware = $this->request->get('middleware');
    }

    public function get() {

        $event_name = 'middleware_'.$this->middleware;

        $class_name = 'on' . string_to_camel('_', $this->name) . string_to_camel('_', $event_name);

        if (!class_exists($class_name, false)) {

            $hook_file = $this->root_path . 'hooks/' . $event_name . '.php';

            if (!is_readable($hook_file)) {
                cmsCore::error(ERR_FILE_NOT_FOUND . ': ' . str_replace(PATH, '', $hook_file));
            }

            include_once $hook_file;
        }

        return new $class_name($this);
    }

    public function render() {

        $params = func_get_args();

        return call_user_func_array([$this->get(), 'run'], $params);
    }

}
