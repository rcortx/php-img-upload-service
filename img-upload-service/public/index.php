<?php
use Phalcon\Loader;
use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Url as UrlProvider;
use Phalcon\Mvc\Application;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;

error_reporting(E_ALL);

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

try {

    $loader = new Loader();
    $loader->registerDirs(
            [
                "../app/controllers/",
                "../app/models/",
            ]
    );

    $loader->register();

    /**
     * The FactoryDefault Dependency Injector automatically registers
     * the services that provide a full stack framework.
     */
    $di = new FactoryDefault();

    $di->set(
        "view", 
        function(){
            $view = new View();
            $view->setViewsDir("../app/views/");
            return $view;
        }
    );

    $di->set(
            "url",
            function(){
                $url = new UrlProvider();
                $url->setBaseUri("/img-upload-service/");
                return $url;
            }
    );

    $di->set(
            "db",
            function(){
                return new DbAdapter(
                    [
                        "host"=>"localhost",
                        "username"=>"root",
                        "password"=>"root",
                        "db_name"=>"test",
                        'port'=> 3306,
                    ]
                );
            }
    );

    /**
     * Read services
     */
    //include APP_PATH . "/config/services.php";

    /**
     * Get config service for use in inline setup below
     */
    //$config = $di->getConfig();

    /**
     * Include Autoloader
     */
    //include APP_PATH . '/config/loader.php';

    /**
     * Handle the request
     */
    $application = new \Phalcon\Mvc\Application($di);

    // pre-existing default
    //echo $application->handle()->getContent();
    
    $response = $application->handle();
    $response->send();

} catch (\Exception $e) {
    echo $e->getMessage() . '<br>';
    echo '<pre>' . $e->getTraceAsString() . '</pre>';
}
