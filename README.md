# img-upload-service-php
A fast, pain-free service for uploading images through REST API in multiple calls separate from the other data (eg: product details)

<h2>Description</h2>
<p>
Product is uploaded through two consecutive REST calls, first call uploads the image and caches it to Redis.
</p>
<p>
Second call uploads product data to MySQL and links image to the product in DB.
The uploaded images are then resized asynchronously to any specs provided in settings and saves them to permanent storage.
</p>
<p>
It's not advisable to upload the images in the same request since the greater the payload
more the chances of the request being dropped due to slow internet connections.
</p>
<p>
    It get's complicated if you have more than one server as you need access to some fast store shared across the servers so that the second request which carries the product data is able to use the cached images. <strong>The in-memory db Redis comes to the rescue where the image is first stored as a binary string! </strong>
</p>
<p>
    The other option is to ensure that your load balancer redirects both the requests to the same server. This is a bit tricky to get right.
    </p>

<p>
    Also provided is a product fetch API for querying stored products, this should send all the prouducts data along with the iages origianl and resized.
</p>

<h2>Instructions</h2>
    
    **PHP version used is 5.5.12
    
    -1) Setup LAMP on linux or WAMP on windows.
    
    -2) Download and set up PHP Phalcon
    
    -3) Download and setup the following PHP extensions: 
        (Phalcon) extension=php_phalcon.dll
        (Imagick) extension=php_imagick.dll

    -4) Setup Redis
    
    -5) in directory img-upload-service/public/index.php change MySQL DB settigns if applicable. (username, password etc)
    
    -6) Copy directory img-upload-service to (wamp/www) directory in WAMP or it's corresponding one in LAMP
    
    -7) Download and setup composer. on the terminal/cmd, move to root project directory and type "composer install" to automatically download dependencies
    
    -8) Run it! go to localhost/img-upload-service/ and follow instructions for a quick demo!
