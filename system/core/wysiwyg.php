<?php
/**
 * Базовый класс wysiwyg редакторов
 */
class cmsWysiwyg {

    /**
     * Опции редактора
     *
     * @var array
     */
    protected $options = [];

    /**
     * ID элемента редактора
     *
     * @var ?string
     */
    protected $dom_id = null;

    /**
     * Создаёт и возвращает объект класса редактора $name
     *
     * @param string $name  Имя редактора
     * @param array $config Опции
     * @return \cmsWysiwyg[Name]|\self
     */
    public static function getEditor(string $name, array $config = []) {

        $connector = 'wysiwyg/' . $name . '/wysiwyg.class.php';

        if (!cmsCore::includeFile($connector)) {
            return new self($config);
        }

        cmsCore::loadControllerLanguage($name);

        $class_name = 'cmsWysiwyg' . string_to_camel('_', $name);

        return new $class_name($config);
    }

    public function __construct($config = []) {

        $this->options = array_replace_recursive($this->options, $config);

        $this->setDomId();
    }

    /**
     * Устанавливает ID элемента редактора
     */
    protected function setDomId() {

        $this->dom_id = $this->options['id'] ?? 'wysiwyg-' . uniqid();

        unset($this->options['id']);
    }

    /**
     * Печатает редактор
     *
     * @param string $field_name Имя поля формы
     * @param string $content    Значение
     * @param array $config @deprecated
     */
    public function displayEditor($field_name, $content = '', $config = []) {

        if($this->dom_id) {
            echo html_textarea($field_name, $content, ['id' => $this->dom_id, 'class' => 'wysiwyg_redactor']);
        }

    }

    /**
     * Подготовливает для сохранения значение,
     * полученное от редактора при сабмите формы
     * Вызыввется в поле fieldHtml в методе store
     *
     * @param mixed $value Значение
     * @return mixed
     */
    public function prepareValue($value) {
        return $value;
    }

    /**
     * Возвращает некие параметры редактора
     *
     * @return array
     */
    public function getParams() {
        return [];
    }

}
