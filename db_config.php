<?php

    // Database connection parameters
    $h_name = "localhost";  // Hostname
    $u_name = "root";       // Username
    $pass = "";              // Password
    $db_name = "php_projects";  // Database name

    // Attempt to establish a connection to the database
    $conn = mysqli_connect($h_name, $u_name, $pass, $db_name);

    // Check if the connection is successful
    if (!$conn) {
        echo "Connection Failed";  // Display an error message if the connection fails
    }

?>
