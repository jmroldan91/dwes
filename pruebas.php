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
                $file = new uploadFile($_FILES['file'],'./doc/',$_FILES['file']['size']);
                //echo $file;
                echo "subiendo...<br/>";
                if($file->upload(true)){
                    echo "Archivo subido";
                }else{
                    echo "error subiendo el archivo: " . $file->getError_message();
                }
                echo "<br/>$file";
            }else{
                echo "<h1>No ha llegado ningin fichero</h1>" .error_get_last()['message'];
            }
        ?>
    </body>
</html>
