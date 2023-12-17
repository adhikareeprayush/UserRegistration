<?php

    $h_name = "localhost";
    $u_name = "root";
    $pass = "";
    $db_name = "php_projects";

    $conn = mysqli_connect($h_name, $u_name, $pass, $db_name);

    if(!$conn){
        echo "Connection Failed";
    }


?>