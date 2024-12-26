<?php
/**
 * Основной класс всех виджетов
 *
 * @property \cmsConfig $cms_config
 * @property \cmsCore $cms_core
 * @property \cmsTemplate $cms_template
 * @property \cmsUser $cms_user
 */
#[\AllowDynamicProperties]
class cmsWidget {

    use icms\traits\corePropertyLoadable;

    /**
     * Некоторые поля таблиц виджетов
     */
    public $name;
    public $controller;
    public $title;
    public $is_title;
    public $position;
    public $groups_view;
    public $groups_hide;
    public $options;
    public $css_class;

    /**
     * Подключать css контроллера
     * @var boolean
     */
    public $insert_controller_css = false;

    /**
     * Флаг кэширования виджета
     * @var ?bool
     */
    public $is_cacheable = null;

    /**
     * Разрешено ли управлять кэшированием из опции в админке
     * @var bool
     */
    private $allow_cacheable_option = true;

    /**
     * Имя шаблона виджета
     * @var ?string
     */
    protected $template;

    /**
     * Шаблон обёртки виджета
     * @var string
     */
    protected $wrapper = '';

    public function __construct(array $widget) {

        cmsCore::loadWidgetLanguage($widget['name'], $widget['controller']);

        foreach ($widget as $field => $value) {
            // кэшированием можно управлять из класса виджета
            // свойство там - приоритетное
            if ($field === 'is_cacheable') {
                if ($this->is_cacheable === null) {
                    $this->is_cacheable = (bool) ($value);
                } else {
                    $this->allow_cacheable_option = false;
                }
                continue;
            }
            $this->{$field} = $value;
        }

        $this->css_class       = $widget['class'];
        $this->css_class_title = $widget['class_title'] ?? '';
        $this->css_class_wrap  = $widget['class_wrap'] ?? '';
        $this->tpl_wrap_custom = $widget['tpl_wrap_custom'] ?? '';
        $this->template        = $this->name;

        if (!empty($widget['tpl_wrap'])) {
            $this->setWrapper($widget['tpl_wrap']);
        }

        if (!empty($widget['tpl_body'])) {
            $this->setTemplate($widget['tpl_body']);
        }

    }

    /**
     * Формирует данные для вывода виджета
     *
     * @param string $html Результат работы виджета
     * @return array
     */
    public function createPositionData(string $html) {

        $wd_data = [];

        foreach ($this as $key => $value) {
            $wd_data[$key] = $value;
        }

        if (!$this->is_title) {
            $wd_data['title'] = '';
        }

        $wd_data['class'] = $this->css_class;
        $wd_data['class_title'] = $this->css_class_title;
        $wd_data['class_wrap'] = ($this->tpl_wrap_style ?? '') . (!empty($this->css_class_wrap) ? ' ' . $this->css_class_wrap : '');
        $wd_data['body'] = $html;

        return $wd_data;
    }

    /**
     * Возвращает значение опции виджета
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getOption(string $key, $default = false) {

        $is_array = strpos($key, ':');

        if ($is_array === false) {
            $value = array_key_exists($key, $this->options) ? $this->options[$key] : $default;
        } else {
            $value = array_value_recursive($key, $this->options);
            if ($value === null) {
                $value = $default;
            }
        }

        return $value;
    }

    /**
     * Возвращает все опции виджета
     *
     * @return array
     */
    public function getOptions() {
        return $this->options;
    }

    /**
     * Устанавливает шаблон виджета
     *
     * @param string $template
     */
    public function setTemplate(string $template) {
        $this->template = $template;
    }

    /**
     * Возвращает имя шаблона виджета
     *
     * @return string
     */
    public function getTemplate() {
        return $this->template;
    }

    /**
     * Устанавливает шаблон обёртки виджета
     *
     * @param string $template
     */
    public function setWrapper(string $template) {
        $this->wrapper = $template;
    }

    /**
     * Возвращает шаблон обёртки виджета
     *
     * @return string
     */
    public function getWrapper() {
        return $this->wrapper;
    }

    /**
     * Выключает кэширование виджета
     */
    public function disableCache() {
        $this->is_cacheable = false;
    }

    /**
     * Включает кэширование виджета
     */
    public function enableCache() {
        $this->is_cacheable = true;
    }

    /**
     * Проверяет, включено ли кэширование виджета
     */
    public function isCacheable() {
        return $this->is_cacheable;
    }

    /**
     * Проверяет, включено ли управление кэшированием через опцию виджета в админке
     */
    public function isAllowCacheableOption() {
        return $this->allow_cacheable_option;
    }

}
