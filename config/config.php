<?php

ob_start();

try {
    $con = new PDO("mysql:dbname=citysearch;host=127.0.0.1;", "root", "123456");
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

}
catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}