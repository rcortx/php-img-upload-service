<?php

include 'RedisStore.php';
include 'ProcessImage.php';

use Phalcon\Mvc\Controller;

// loading dependencies via Composer Autoloader
require dirname(dirname(__DIR__)) . '/vendor/autoload.php';

class ImageupController extends Controller{
    public function indexAction(){

    }

    public function uploadAction(){ // Upload an Image. url: "/img-upload-service/imageup/upload"
        $this->response->setContentType('application/json', 'UTF-8');
        $content = array();
        if($this->request->hasFiles()==true){
            $user = $this->request->getClientAddress();
            $img_store = ImgBinStore::getInstance();
            foreach($this->request->getUploadedFiles() as $file){
                $error = $img_store->storeImage($user, $file);
                if (is_string($error)){ 
                    $content["error"] = $error;
                    $this->response->setStatusCode(400, "Bad Request");
                } else{
                    $this->response->setStatusCode(200, "ok");
                }
            }
        }else {
            $content["error"] = "No File found!";
            $this->response->setStatusCode(400, "Bad Request");
        }
        $this->response->setContent(json_encode($content));
        
        return $this->response;
    }

    public function productAction(){ // Post product data. url: "/img-upload-service/imageup/product"
        $this->response->setContentType('application/json', 'UTF-8');
        $content = array();
         if ($this->request->isPost()) {
                $product = new Products();

                $success = $product->save(
                    $this->request->getPost(),
                    [
                        "name",
                        "price",
                    ]
                );

                if ($success) {
                    $this->response->setStatusCode(201, "created");
                    $content["msg"] = "Product has been uploaded!";
                    
                    // Switch to separate thread for async image processing
                    // or use a message brker with background workers to execute this
                    
                    /////// Change to Threaded Async Task using PThread on Linux ////////

                    $img_proc = new ImageProcessor();
                    $user = $this->request->getClientAddress();
                    $img_store = ImgBinStore::getInstance();
                    $image = $img_store->retrieveImageByUserID($user);
                    $img_name = $image[0];
                    $img = $image[1];
                    $ext = substr($image[2], strrpos($image[2], '/')+1);
                    $p_id =  $product->id;
                    $paths = $img_proc->process($img, $p_id, $img_name, $ext);
                    $product->save($paths, ["image", "image_256", "image_512"]);

                    /////////////////////////////////////////////////////////////

                } else {
                    $this->response->setStatusCode(400, "Bad Request");
                    $messages = $product->getMessages();
                    $content["msg"] = array();
                    $count = 0;
                    foreach ($messages as $message) {
                        $content[$count] = $message->getMessage();
                        $count += 1;
                    }
                }
            }else{
                $content["error"] = "Only POST is allowed";
                $this->response->setStatusCode(405, "Method not Allowed");
            }
            $this->response->setContent(json_encode($content));
            return $this->response;
    }

    public function productlistAction(){ // Lists all products. url: "/img-upload-service/imageup/productlist"
        $this->response->setContentType('application/json', 'UTF-8');
        $content = Products::find();
        $this->response->setContent(json_encode($content));
        return $this->response;
    }

    public function testAction(){ // Ignore
        
        $img_proc = new ImageProcessor();
        $user = $this->request->getClientAddress();
        $img_store = ImgBinStore::getInstance();
        $image = $img_store->retrieveImageByUserID($user);
        $img_name = $image[0];
        $img = $image[1];
        $ext = substr($image[2], strrpos($image[2], '/')+1);

    }
    public function testValidation(){ // Ignore
        $img_id = "img";
        
        if($this->request->hasFiles()==true){

            foreach($this->request->getUploadedFiles() as $file){
                $result = (new ImageValidator())->validate($file);
                if(!is_string($result)){
                    echo "Success!!";
                }else{
                    echo "Failed! Error: $result";
                }
            }
        }else {
            echo "No File found!";
        }
    }
}

?>
