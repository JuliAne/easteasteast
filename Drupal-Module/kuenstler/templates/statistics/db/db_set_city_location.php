<?php

$response = array();

if(isset($_POST['name']) && isset($_POST['lat']) && isset($_POST['lng'])) {

    $name = $_POST['name'];
    $lat = $_POST['lat'];
    $lng = $_POST['lng'];

    require_once __DIR__ . '/db_connect.php';

    $db = new DB_CONNECT();
    $result = mysql_query("set names utf8");
    $result = mysql_query("INSERT INTO `localisations`(`name`, `lng`, `lat`) VALUES ('$name', '$lat', '$lng')") or die(mysql_error());

    //Check if successful
    if($result) {
            $response["success"] = 1;
    } else {
            //not successfull
            $response["success"] = 0;
            $response["error"]= "Couldn insert. " . mysql_error();
    }
    echo json_encode($response);

} else {
    $response["success"]= 0;
    $response["error"] = "No params";
    echo json_encode($response);
}
