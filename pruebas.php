
<?php
    require_once 'classes/uploadFile.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?php
            if(!empty($_FILES)){
                uploadFile::MultiUpoad($_FILES, false);
            }else{
                echo "<h1>No ha llegado ningin fichero</h1>" .error_get_last()['message'];
            }
        ?>
    </body>
</html>

