<?php 

use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;

class ProcessingSettings{
    public static $toSizes = array("w-512", "w-256");
    
    public static function getPath(){
        return dirname(dirname(__DIR__)) .(DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.
            'img'.DIRECTORY_SEPARATOR);
    }
    public static function getURL(){
        return $_SERVER["SERVER_NAME"].str_replace(dirname(dirname(dirname(__DIR__))), "", dirname(dirname(__DIR__))) .
        '/public/img/';
    }
}

class ImageProcessor{

    private $specs;
    private $filesystem;

    public function __construct(){
        $this->parseSettings();
        $adapter = new Local(ProcessingSettings::getPath());
        $this->filesystem = new Filesystem($adapter);
    }

    public function process($image, $p_id, $name, $ext){
        $paths = array();
        $paths["image"] = $this->save($image, $p_id, "ori", $name, $ext); 
        // full path on server
        foreach($this->specs as $spec){
            $resized = $this->resize($image, $spec);
            $paths["image_".$spec[1]] = $this->save($resized, $p_id, $spec[1], $name, $ext);
            // full path on server
        }
        return $paths;
    }

    public function resize($image, $spec){
        // resize image
        $width = $image->getImageWidth();
        $height = $image->getImageHeight();
        if($spec[0] == 0){
            $newwidth = $spec[1];
            $newheight = ($newwidth/$width)*$height;
        }else{
            $newheight = $spec[1];
            $newwidth = ($newheight/$height)*$width;
        }
         $image->resizeImage($newwidth, $newheight, Imagick::FILTER_BOX, NULL, true);
         return $image;
    }

    public function save($image, $p_id, $spec, $name, $ext){ // change to array arg
        $img_root_dir = ProcessingSettings::getURL();
        $url_path = $img_root_dir.("$p_id/$spec/$name");
        $file_path = ProcessingSettings::getPath() . ("$p_id".DIRECTORY_SEPARATOR."$spec");
        mkdir($file_path, null, true); // creates nested directories in the format of product_id/image_spec/
        $file_path .= DIRECTORY_SEPARATOR."$name";
        $image->writeImage($file_path);
        return str_replace("//", '/', str_replace("\\", '/', $url_path));
    }
    private function parseSettings(){
        $this->specs = new ArrayObject(array());
        $key = array('w'=>0, 'h'=>1);
        foreach(ProcessingSettings::$toSizes as $spec){
            $this->specs->append(array($key[$spec[0]], substr($spec, (int)strrpos($spec, '-')+1)));
        }
    }

    public function test(){
        foreach($this->specs as $spec){
            echo "<br>". $spec[0] . "xxxxxx" . $spec[1] . "<br>";
        }
        echo ProcessingSettings::getPath();
    }

}

?>