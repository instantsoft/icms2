<?php
/******************************************************************************/
//                                                                            //
//                             InstantMedia 2015                              //
//	 		  http://www.instantmedia.ru/, support@instantmedia.ru            //
//                               written by Fuze                              //
//                  http://www.instantvideo.ru/copyright.html                 //
//                         © ИП Бессмельцев И. В., 2015                       //
//                                                                            //
/******************************************************************************/
class onContentRssContentControllerForm extends cmsAction {

    public function run($data){

		list($form, $feed) = $data;

        $fields = $this->model->getContentFields($feed['ctype_name']);
        $fields = array(''=>'') + array_collection_to_list($fields, 'name', 'title');

        $form->addFieldset(LANG_RSS_FEED_MAPPING, 'mapping', array(
            'childs' => array(

                new fieldList('mapping:title', array(
                    'title' => LANG_RSS_FEED_MAP_TITLE,
                    'items' => $fields
                )),

                new fieldList('mapping:description', array(
                    'title' => LANG_RSS_FEED_MAP_DESC,
                    'items' => $fields
                )),

                new fieldList('mapping:pubDate', array(
                    'title' => LANG_RSS_FEED_MAP_DATE,
                    'items' => $fields
                )),

                new fieldList('mapping:image', array(
                    'title' => LANG_RSS_FEED_MAP_IMAGE,
                    'items' => $fields
                )),

                new fieldList('mapping:image_size', array(
                    'title' => LANG_RSS_FEED_MAP_IMAGE_SIZE,
                    'generator' => function($item) {
                        return array('original' => LANG_PARSER_IMAGE_SIZE_ORIGINAL) + cmsCore::getModel('images')->getPresetsList();
                    }
                ))

            )
        ));

        return array($form, $feed);

    }

}
