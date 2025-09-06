<?php
    $mysqli = new mysqli("localhost","root","crawling23","collector_app");
    if ($mysqli -> connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
    exit();
    }
    $myArray = [];
    $sql = "SELECT * FROM custom_db";
    $result = $mysqli -> query($sql);
    while($row = $result->fetch_array()) {
        $database_list[] = $row;
    }
    $mysqli -> close();
