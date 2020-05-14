<?php
class bootstrap4 extends cmsFrontend {

    public function compileScss($path, $vars = []) {

        if(!cmsCore::includeFile('system/libs/scssphp/scss.inc.php')){
            return false;
        }

        $scss_file = $this->cms_template->getTplFilePath($path);

        $data = file_get_contents($scss_file);

        $working_dir = dirname(realpath($scss_file));

        $scss_file_name = basename($scss_file);

        chdir($working_dir);

        $scss = new ScssPhp\ScssPhp\Compiler();

        $scss->setFormatter('ScssPhp\\ScssPhp\\Formatter\\Compressed');

        if($vars){

            $_vars = [];

            foreach ($vars as $key => $value) {
                if(!$value){
                    $_vars[$key] = 'false'; continue;
                }
                if($value === 1){
                    $_vars[$key] = 'true'; continue;
                }
                $_vars[$key] = $value;
            }

            $scss->setVariables($_vars);
        }

        return $scss->compile($data, $scss_file_name);

    }

}
