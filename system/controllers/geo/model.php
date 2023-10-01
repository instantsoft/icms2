<?php

class modelGeo extends cmsModel {

    public $limit = false;

    public function getCountries() {

        $this->useCache('geo.countries');

        return $this->filterEqual('is_enabled', true)->
                orderByList([
                    ['by' => 'ordering', 'to' => 'asc'],
                    ['by' => 'name', 'to' => 'asc']
                ])->get('geo_countries', function ($item) {
            return $item['name'];
        }) ?: [];
    }

    public function getRegions($country_id = false) {

        $this->useCache('geo.regions');

        if ($country_id) {
            $this->filterEqual('country_id', $country_id);
        }

        return $this->filterEqual('is_enabled', true)->
                orderByList([
                    ['by' => 'ordering', 'to' => 'asc'],
                    ['by' => 'name', 'to' => 'asc']
                ])->get('geo_regions', function ($item) {
            return $item['name'];
        }) ?: [];
    }

    public function getCities($region_id = false) {

        $this->useCache('geo.cities');

        if ($region_id) {
            $this->filterEqual('region_id', $region_id);
        }

        return $this->filterEqual('is_enabled', true)->
                orderByList([
                    ['by' => 'ordering', 'to' => 'asc'],
                    ['by' => 'name', 'to' => 'asc']
                ])->get('geo_cities', function ($item) {
            return $item['name'];
        }) ?: [];
    }

    public function getCityParents($city_id) {

        $this->useCache('geo.cities.parents');

        $this->select('r.id', 'region_id');
        $this->select('c.id', 'country_id');
        $this->select('r.name', 'region_name');
        $this->select('c.name', 'country_name');

        $this->join('geo_regions', 'r', 'r.id = i.region_id');
        $this->join('geo_countries', 'c', 'c.id = r.country_id');

        $this->filterEqual('id', $city_id);

        return $this->getItem('geo_cities');
    }

    public function getRegionParents($region_id) {

        $this->useCache('geo.regions.parents');

        $this->select('i.id', 'region_id');
        $this->select('c.id', 'country_id');
        $this->select('i.name', 'region_name');
        $this->select('c.name', 'country_name');

        $this->join('geo_countries', 'c', 'c.id = i.country_id');

        $this->filterEqual('id', $region_id);

        return $this->getItem('geo_regions');
    }

    public function getCity($id) {

        $this->useCache('geo.city');

        return $this->getItemById('geo_cities', $id);
    }

}
