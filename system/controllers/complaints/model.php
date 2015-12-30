<?php

class modelComplaints extends cmsModel{ 

    public function addComplaints($complaint){ 
       return $this->insert('complaints', $complaint);
    }
        
    public function getLastComplaintTime($ip){
            $time = $this->
                        filterEqual('author_url', $ip)->
                        orderBy('date', 'desc')->
                        getFieldFiltered('complaints', 'date_pub');

    return strtotime($time);

    }

    public function getComplaints(){        
        return $this->get('complaints');            
    }

    public function getComplaintsCount(){        
        return $this->getCount('complaints');            
    }   

    public function deleteComplaints($id){        
        return $this->delete('complaints', $id);            
    } 
}	