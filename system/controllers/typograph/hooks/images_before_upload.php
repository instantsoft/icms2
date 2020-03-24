<?php

class onTypographImagesBeforeUpload extends cmsAction {

    public function run($data){

        list($name, $cms_uploader) = $data;

        if($this->request->has($name)){

            $mb_link = $this->request->get($name, '');

            // ссылка на YouTube?
            if(preg_match('#(?:youtube\.com\/\S*(?:(?:\/e(?:mbed))?\/|watch\/?\?(?:\S*?&?v\=))|youtu\.be\/)([a-z0-9_-]{6,11})#ui', $mb_link, $matches) && !empty($matches[1])){

                $images = array(
                    'https://img.youtube.com/vi/'.$matches[1].'/maxresdefault.jpg',
                    'https://img.youtube.com/vi/'.$matches[1].'/sddefault.jpg',
                    'https://img.youtube.com/vi/'.$matches[1].'/hqdefault.jpg'
                );

                foreach ($images as $ytimg) {

                    $h = get_headers($ytimg, true);
                    $code = substr($h[0], 9, 3);

                    if((int)$code < 400){
                        $_POST[$name] = $ytimg; break;
                    }

                }

            }

        }

        return $data;

    }

}
