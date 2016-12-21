#!usr/bin/php
<?php 

// Cleaning cronjob run every 10 minutes (As per the 600 second time window)
// crontab -e
// crontab 0,10,20,30,40,50 * * * * ./CronTask.php

include 'RedisStore.php';

// loading dependencies via composer Autoloader
require dirname(dirname(__DIR__)) . '/vendor/autoload.php';

function cleanDirt(){
    ImgBinStore::getInstance()->deleteStaleData();
}

cleanDirt();
?>