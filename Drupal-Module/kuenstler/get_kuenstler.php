<?php

require_once __DIR__ . '/database/db_connect.php';

$db = new DB_CONNECT();

$response = array();
$query = "SELECT Name, Mail, Genre, Town FROM profile LIMIT 1";
$result = mysql_query("set names utf8");

$result = mysql_query($query);
$errorMessage = mysql_error();

if(mysql_num_rows($result)>0) {
    while($row = mysql_fetch_object($result)) {
        $response['success'] = 1;
        $response['name'] = $row->Name;
        $response['mail'] = $row->Mail;
        $response['genre'] = $row->Genre;
        $response['hometown'] = $row->Town;
        //TODO: Read likes
        $response['likes'] = 12;
    }
    echo json_encode($response);
} else {
    $response['success'] = 0;
    $response['error'] = "Either there is no entry, or we got a mysql error.\n" . $errorMessage;
    
    echo json_encode($response);
}
