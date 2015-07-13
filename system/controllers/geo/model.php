<?php
class modelGeo extends cmsModel {

    public function getCountries(){

        $this->useCache("geo.countries");

        return $this->orderBy('ordering, name')->get('geo_countries', function($item){

            return $item['name'];

        });

    }

    public function getRegions($country_id=false){

        $this->useCache("geo.regions");

        if ($country_id){
            $this->filterEqual('country_id', $country_id);
        }

        return $this->orderBy('name')->get('geo_regions', function($item){

            return $item['name'];

        });

    }

    public function getCities($region_id=false){

        $this->useCache("geo.cities");

        if ($region_id){
            $this->filterEqual('region_id', $region_id);
        }

        return $this->orderBy('name')->get('geo_cities', function($item){

            return $item['name'];

        });

    }

    public function getCityParents($city_id){

        $this->useCache("geo.cities.parents");

        $this->select('r.id', 'region_id');
        $this->select('c.id', 'country_id');

        $this->join('geo_regions', 'r', 'r.id = i.region_id');
        $this->join('geo_countries', 'c', 'c.id = r.country_id');

        $this->filterEqual('id', $city_id);

        return $this->getItem('geo_cities');

    }

    public function getCity($id){

        $this->useCache("geo.city");

        return $this->getItemById('geo_cities', $id);

    }

}
