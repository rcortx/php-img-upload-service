<?php

class ImageConstants{
    // Image fields
    const KEY_IMG_NAME = "name";
    const KEY_IMG_TIMESTAMP = "timestamp";
    const KEY_IMG_IMG_CONTENTS = "img_contents";
    const KEY_IMG_MIME = "mime";

    // key refencing set of time windows
    const KEY_SET_TIME_WINDOWS = "time_windows";

    // length of each time window in seconds
    const TIME_WINDOW_LENGTH = 600; // seconds

    const PREPEND_IMG_TYPE = "Content-Type: ";
}

class ImageValidator{
    // Accepted Mimes
    private static $JPG = "jpg";
    private static $PNG = "png";
    private static $JPEG = "jpeg";
    // Array of accepted MIME types
    private static $ACCEPTED_MIMES = NULL; // Initialized in init()
    // Max permissible size of image in bytes
    const MAX_SIZE_IMG_ALLOWED = 2000000; // bytes

    private static $ERROR_CODE_NOT_IMAGE = 1;
    private static $ERROR_MSG_NOT_IMAGE = "File is not an image! Only images are accepted.";

    private static $ERROR_CODE_SIZE_EXCEEDS_MAX = 2;
    private static $ERROR_MSG_SIZE_EXCEEDS_MAX = NULL; // Initialized in init()

    private static $ERROR_CODE_NOT_ACCEPTED_FORMATS = 3;
    private static $ERROR_MSG_NOT_ACCEPTED_FORMATS = NULL; // Initialized in init()

    private static $ERRORS = NULL; // Initialized in init()

    // tracks whether constants have been initialized
    private static $isinit = false;
    
    // methods
    private static function init(){
        if (self::$isinit){
            return;
        }
        self::$ACCEPTED_MIMES = array(self::$JPG, self::$PNG, self::$JPEG);
         
        self::$ERROR_MSG_NOT_ACCEPTED_FORMATS = "Submited image is not an accepted format! Only ".
            implode(", ", self::$ACCEPTED_MIMES)." are accepted.";
        
        self::$ERROR_MSG_SIZE_EXCEEDS_MAX = "Image size exceeds max size! Upload size should be ".
            "less than ".(self::MAX_SIZE_IMG_ALLOWED/1000)." kb";

        self::$ERRORS = array(self::$ERROR_CODE_NOT_IMAGE=>self::$ERROR_MSG_NOT_IMAGE,
            self::$ERROR_CODE_SIZE_EXCEEDS_MAX=>self::$ERROR_MSG_SIZE_EXCEEDS_MAX,
            self::$ERROR_CODE_NOT_ACCEPTED_FORMATS=>self::$ERROR_MSG_NOT_ACCEPTED_FORMATS);
        
        self::$isinit = true;
    }

    public function __construct(){
        self::init();
    }

    private function raiseError($code){

        return self::$ERRORS[$code];
    }

    public function validate($file){
        if(self::isImg($file)){
            if(self::isAcceptableSize($file)){
                if(self::isAcceptedType($file)){
                    return true;
                }else{
                    return self::raiseError(self::$ERROR_CODE_NOT_ACCEPTED_FORMATS);
                }
            }else{
                return self::raiseError(self::$ERROR_CODE_SIZE_EXCEEDS_MAX);
            }
        }else{
            return self::raiseError(self::$ERROR_CODE_NOT_IMAGE);
        }
    }

    public function isAcceptedType($file){
        $imageType = $file->getExtension();
        return in_array($imageType, self::$ACCEPTED_MIMES);
    }

    public function isAcceptableSize($file){
        return $file->getSize() <= self::MAX_SIZE_IMG_ALLOWED;
    }

    public function isImg($file){
        try{
            if(getimagesize($file->getTempName())!==NULL){
                return true;
            }
        } catch(Exception $e){
            return false;
        }
    }
}

// To turn off Error notices
set_error_handler('exceptions_error_handler');

function exceptions_error_handler($severity, $message, $filename, $lineno) {
  if (error_reporting() == 0) {
    return;
  }
  if (error_reporting() & $severity) {
    throw new ErrorException($message, 0, $severity, $filename, $lineno);
  }
}

?>