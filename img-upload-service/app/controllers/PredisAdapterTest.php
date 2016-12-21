<?php 

class PredisAdapterTest{
    
    private $client;

    function __construct(){
        Predis\Autoloader::register();
        $this->client = new Predis\Client();
    }
    public function set($key, $val){
        $this->client->set($key, $val); 
    }
    public function setMap($key, $hash){
        $res = array('HMSET', $key);
        foreach($hash as $k=>$v){
            $res = array_merge($res, array($k, $v));
        }
        $this->client->executeRaw($res);  
    }
    public function get($key, $cmd="GET"){
        return $this->client->executeRaw([$cmd , $key]);

    }
    public function getMap($key){
        $res = $this->get($key, 'HGETALL');
        $asc = array();
        foreach($res as $k=>$v){
            if($k%2 == 0){
                $asc[$v] = $res[$k+1];
            }
        }
        return $asc;
    }
    public function del($key){
        $this->client->executeRaw(['DEL', $key]);
    }

    public function exists($key){
        return $this->client->executeRaw(['EXISTS', $key]);
    }

    public function addToList($key, $val){
        $this->client->executeRaw(['RPUSH', $key, $val]);
    }

    public function addToSortedSet($key, $val){
        $this->client->executeRaw(['ZADD', $key, $val]);
    }

    public function delRangeFromSet($key, $end, $start=0){ //default start is 0
        $this->client->executeRaw(['ZREMRANGEDBYLEX', $key, $start, $end]);
    }

    public function delKeysFromFetchedList($key, $start_i, $end_i){ // deletes all elements in list
        $list = $this->client->executeRaw(['LRANGE', $key, $start_i, $end_i]); //  and the list itself 
        $this->client->executeRaw(array_merge(['DEL', $key], $list));
    }

    //public function 



}

?>