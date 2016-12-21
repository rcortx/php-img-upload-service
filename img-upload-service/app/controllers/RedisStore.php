<?php
include 'ImageHelpers.php';
include 'PredisAdapterTest.php';

class ImgBinStore{
    /*
    *   The way in which data is stored in Redis is as follows:
    *   - Every image entry is stored as a Hashmap with user_id as key
    *   - hashmap's key is user_id. hasmap's fields are: 
    *       - timestamp: value of php time() at the time it was stored
    *       - image_contents: image converted to binary by get_file_contents()
    *       - mime: image's mime type
    *       - name: name of image
    *   - user_id of saved image is also stored in a list referenced by it's Current time window
    *   - With every cycle of TIME_WINDOW_LENGTH time, a new time window and its corresponding lists
    *     to store images saved during this time is created
    *   - Every Time Window is also stored in an ORDERED SET containing all previous time windows
    *   - This set of Time Windows is a Redis set which stores set of time windows Ordered and
    *     interspered by TIME_WINDOW_LENGTH
    *
    */

    // singleton instance of class
    private static $imgBinStoreInstance;
    private static $test_user = "U:1000"; 
    private $store_adapter = NULL;
    private $validator = NULL;

    public static function getInstance(){ // returns instance of class
        if(self::$imgBinStoreInstance == NULL){
            self::$imgBinStoreInstance = new ImgBinStore();
        }
        return self::$imgBinStoreInstance;
    }

    private function __construct(){
        $this->store_adapter = new PredisAdapterTest();
        $this->validator = new ImageValidator();
    }

    public function storeImage($user, $file){
        $result = $this->validator->validate($file);
        if(!is_string($result)){
            // Success!
            $payload = $this->serializeImage($file);
            $this->store_adapter->setMap($user, $payload);
            $this->markTime($user, $payload);
            return true;
        }else{
            // Failed!
            return $result;
        }
    }

    public function retrieveImageByUserID($user){
        return $this->deserializeImage($this->store_adapter->getMap($user));
    }

    public function markTime($user, $payload){
        $time = floor($payload[ImageConstants::KEY_IMG_TIMESTAMP]/
                ImageConstants::TIME_WINDOW_LENGTH);
        if($this->store_adapter->exists($time) == false){
            $this->store_adapter->addToSortedSet(ImageConstants::KEY_SET_TIME_WINDOWS, $time);
        }
        $this->store_adapter->addToList($time, $user);
    }

    public function deleteStaleData(){ // deletes any data older than TIME_WINDOW_LENGTH
        $time = floor(time()/ImageConstants::TIME_WINDOW_LENGTH)-1;
        if($this->store_adapter->exists($time) == true){
            $this->store_adapter->delKeysFromFetchedList($time, 0, -1);
            $this->store_adapter->delRangeFromSet(ImageConstants::KEY_SET_TIME_WINDOWS, $time);
        }
    }

    public function serializeImage($img){
        return array(ImageConstants::KEY_IMG_NAME=>$img->getName(),
                ImageConstants::KEY_IMG_MIME=>ImageConstants::PREPEND_IMG_TYPE.$img->getType(),
                ImageConstants::KEY_IMG_IMG_CONTENTS=>file_get_contents($img->getTempName()),
                ImageConstants::KEY_IMG_TIMESTAMP=>time());
    }

    public function deserializeImage($value){
        $imageBlob = $value[ImageConstants::KEY_IMG_IMG_CONTENTS];
        $imagick = new Imagick();
        $imagick->readImageBlob($imageBlob);
        return array($value[ImageConstants::KEY_IMG_NAME], $imagick, 
            $value[ImageConstants::KEY_IMG_MIME]);
    }
}


?>