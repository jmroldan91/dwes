<?php 
    error_reporting(-1);
?>
<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <form name="form" action="pruebas.php" method="POST" enctype="multipart/form-data" multiple>
            <input type="file" name="file" value="" width="30"/><br/>
            <input type="file" name="file2" value="" width="30"/><br/>
            <input type="file" name="file3" value="" width="30"/><br/>
            <input type="file" name="file4" value="" width="30"/><br/>
            <input type="submit" value="submit" name="go" />
        </form>
    </body>
</html>
