<?php

class modelRss extends cmsModel{

    public function getFeedsCount(){

        return $this->getCount('rss_feeds');

    }

    public function getFeeds(){

        return $this->get('rss_feeds');

    }

    public function getFeed($id){

        return $this->getItemById('rss_feeds', $id, function($item, $model){
            $item['mapping'] = cmsModel::yamlToArray($item['mapping']);
            $item['image'] = cmsModel::yamlToArray($item['image']);
            return $item;
        });

    }

    public function getFeedByCtypeId($ctype_id){

        return $this->filterEqual('ctype_id', $ctype_id)->getItem('rss_feeds', function($item, $model){
            $item['mapping'] = cmsModel::yamlToArray($item['mapping']);
            $item['image'] = cmsModel::yamlToArray($item['image']);
            return $item;
        });

    }

    public function getFeedByCtypeName($ctype_name){

        return $this->filterEqual('ctype_name', $ctype_name)->getItem('rss_feeds', function($item, $model){
            $item['mapping'] = cmsModel::yamlToArray($item['mapping']);
            $item['image'] = cmsModel::yamlToArray($item['image']);
            return $item;
        });

    }

    public function updateFeed($id, $feed){

        return $this->update('rss_feeds', $id, $feed);

    }

    public function addFeed($feed){

        return $this->insert('rss_feeds', $feed);

    }

    public function deleteFeed($id){

        return $this->delete('rss_feeds', $id);

    }

}
