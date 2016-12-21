<?php Tests.php

// IGNORE THIS FILE

if ($_SERVER["REQUEST_METHOD"] == "POST"){

$img_id = "img";
test1();

}

function test1(){
    include 'ImageHelpers.php';
    
    $result = ImageValidator::validate($_FILES[$img_id]);
    if($result == true){
        echo "Success!!";
    }else{
        echo "Failed! Error was: $result";
    }
}


?>
<html>
    <head>
    </head>
    <body>
        <h1>Testing...</h1>
        <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post" enctype="multipart/form-data">
            Select image to upload:<br>
            <input type="file" name="img" id="img"><br>
            <input type="submit" name="submit" value="UpImg">
        </form>
    </body>
</html>


