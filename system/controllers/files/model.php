<?php

class modelFiles extends cmsModel{

//============================================================================//
//============================================================================//

    public function registerFile($path, $name){

        $url_key = md5(md5(implode(':', array(microtime(true), $name, $path, rand(0, time()/1000)))));
        $url_key = substr($url_key, rand(0, 23), 8);

        $id = $this->insert('uploaded_files', array(
            'url_key' => $url_key,
            'path' => $path,
            'name' => $name
        ));

        return array(
            'id' => $id,
            'url_key' => $url_key
        );

    }

    public function deleteFile($id){

        return $this->delete('uploaded_files', $id);

    }

    public function getFile($id){

        return $this->getItemById('uploaded_files', $id);

    }

    public function incrementDownloadsCounter($file_id){

        $this->filterEqual('id', $file_id)->increment('uploaded_files', 'counter');

    }

//============================================================================//
//============================================================================//

}
