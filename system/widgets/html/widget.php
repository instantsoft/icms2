<?php

class widgetHtml extends cmsWidget {

    public function run() {

        $template = cmsTemplate::getInstance();

        if(!empty($this->options['css_files'])){

            $files = explode("\n", $this->options['css_files']);

            $template->addTplCSSName($files);
        }

        if(!empty($this->options['js_files'])){

            $files = explode("\n", $this->options['js_files']);

            $template->addTplJSName($files);
        }

        if(!empty($this->options['js_inline_scripts'])){
            ob_start(); ?>
            <script>
                <?php echo $this->options['js_inline_scripts']; ?>
            </script>
            <?php $template->addBottom(ob_get_clean());
        }

        return [];
    }

}
